<?php

namespace App\Services;

use App\Models\ApiSetting;
use App\Models\SmsLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SmsService
{
    /**
     * Send SMS with tag replacement & Multi-Gateway Dispatch (Custom Gateway, BulkSMS Dhaka, BulkSMS BD)
     */
    public function send(string $phone, string $template, array $data = [], string $gateway = 'BulkSMS'): bool
    {
        // Replace dynamic tokens
        $message = $template;
        foreach ($data as $key => $val) {
            $message = str_replace("{{$key}}", (string) $val, $message);
        }

        $charCount = mb_strlen($message);
        $smsParts = (int) ceil($charCount / 160) ?: 1;

        // Clean phone number (e.g. 01712345678)
        $cleanPhone = preg_replace('/[^0-9]/', '', $phone);

        // Fetch active SMS provider
        $setting = ApiSetting::where('is_active', true)
            ->whereIn('provider', ['custom_sms', 'bulksmsdhaka', 'bulksms_bd', 'bulksms'])
            ->first();

        // If none is explicitly active, fallback to configured
        if (! $setting) {
            $setting = ApiSetting::whereIn('provider', ['custom_sms', 'bulksmsdhaka', 'bulksms_bd', 'bulksms'])->first();
        }

        $provider = $setting?->provider ?? 'bulksmsdhaka';
        $isActive = $setting ? (bool) $setting->is_active : true;

        $status = 'sent';
        $responseId = 'SMS_'.uniqid();
        $errorMessage = null;
        $gatewayDisplayName = $setting?->title ?? 'Custom SMS Gateway';

        if ($isActive) {
            if ($provider === 'custom_sms') {
                // 1. Universal Custom Bulk SMS Gateway Dispatch
                $endpointUrl = $setting->getCredential('endpoint_url');
                $apiKey = $setting->getCredential('api_key_value') ?: $setting->getCredential('api_key');

                if (app()->environment('testing') || empty($endpointUrl) || str_contains($apiKey ?? '', 'demo')) {
                    $status = 'sent';
                    $responseId = 'SMS_CUSTOM_'.uniqid();
                } else {
                    $result = $this->dispatchCustomSms($setting, $cleanPhone, $message);
                    $status = $result['success'] ? 'sent' : 'failed';
                    $responseId = $result['response_id'] ?? null;
                    $errorMessage = $result['error_message'] ?? null;
                    $gatewayDisplayName = $setting->getCredential('gateway_name') ?: $gatewayDisplayName;
                }
            } elseif ($provider === 'bulksmsdhaka') {
                // 2. Bulk SMS Dhaka API (https://bulksmsdhaka.com / bulksmsdhaka.net)
                $apiKey = $setting->getCredential('api_key') ?? ($setting->getCredential('apikey') ?? $setting->api_key);
                $callerId = $setting->getCredential('caller_id') ?? ($setting->getCredential('sender_id') ?? ($setting->sender_id ?? '1234'));
                $gatewayDisplayName = 'Bulk SMS Dhaka';

                if (app()->environment('testing') || str_contains($apiKey ?? '', 'demo') || str_contains($apiKey ?? '', 'mock')) {
                    $status = 'sent';
                    $responseId = 'SMS_SIM_'.uniqid();
                } else {
                    try {
                        $response = Http::withoutVerifying()->timeout(12)->get('https://bulksmsdhaka.net/api/otpsend', [
                            'apikey' => $apiKey,
                            'callerID' => $callerId,
                            'number' => $cleanPhone,
                            'message' => $message,
                        ]);

                        $resData = $response->json();
                        $statusCode = $resData['Status'] ?? ($resData['status'] ?? null);
                        $isSuccess = ($resData['Success'] ?? '') === 'true' || $statusCode === '1000' || $statusCode === 1000;

                        if ($isSuccess) {
                            $status = 'sent';
                            $responseId = (string) ($resData['message_id'] ?? ('SMS_DHAKA_'.uniqid()));
                        } else {
                            $status = 'failed';
                            $responseId = null;
                            $errorMessage = $resData['Message'] ?? ($resData['error_message'] ?? ($response->body() ?: 'Bulk SMS Dhaka dispatch failed.'));
                            Log::warning('Bulk SMS Dhaka Error: '.$errorMessage);
                        }
                    } catch (\Exception $e) {
                        Log::error('Bulk SMS Dhaka HTTP Error: '.$e->getMessage());
                        $status = 'failed';
                        $responseId = null;
                        $errorMessage = $e->getMessage();
                    }
                }
            } else {
                // 3. BulkSMS BD Gateway (https://bulksmsbd.net)
                $apiKey = $setting?->getCredential('api_key') ?? $setting?->api_key;
                $callerId = $setting?->getCredential('sender_id') ?? ($setting?->sender_id ?? '8809617618999');
                $gatewayDisplayName = 'BulkSMS BD';

                if (app()->environment('testing') || str_contains($apiKey ?? '', 'demo') || str_contains($apiKey ?? '', 'mock')) {
                    $status = 'sent';
                    $responseId = 'SMS_SIM_'.uniqid();
                } else {
                    try {
                        $response = Http::withoutVerifying()->timeout(12)->post('https://bulksmsbd.net/api/smsapi', [
                            'api_key' => $apiKey,
                            'type' => 'text',
                            'number' => $cleanPhone,
                            'senderid' => $callerId,
                            'message' => $message,
                        ]);

                        $resData = $response->json();
                        $responseCode = $resData['response_code'] ?? null;

                        if ($responseCode == 202 || ($response->successful() && isset($resData['success_message']))) {
                            $status = 'sent';
                            $responseId = (string) ($resData['message_id'] ?? ('SMS_BD_'.uniqid()));
                        } else {
                            $status = 'failed';
                            $responseId = null;
                            $errorMessage = $resData['error_message'] ?? ($response->body() ?: 'BulkSMS BD submission failed.');
                            Log::warning('BulkSMS BD Error: '.$errorMessage);
                        }
                    } catch (\Exception $e) {
                        Log::error('BulkSMS BD HTTP Error: '.$e->getMessage());
                        $status = 'failed';
                        $responseId = null;
                        $errorMessage = $e->getMessage();
                    }
                }
            }
        } else {
            Log::info("SMS Gateway is disabled in API Hub. Message to {$phone} skipped from live dispatch.");
            $status = 'sent';
            $responseId = 'SMS_INACTIVE_'.uniqid();
        }

        // Log SMS entry
        try {
            SmsLog::create([
                'gateway' => $gatewayDisplayName,
                'phone' => $phone,
                'message' => $message,
                'character_count' => $charCount,
                'sms_parts' => $smsParts,
                'status' => $status,
                'response_id' => $responseId,
                'error_message' => $errorMessage,
                'sent_at' => Carbon::now(),
            ]);

            return $status === 'sent';
        } catch (\Exception $e) {
            Log::error('SMS Log Storage Error: '.$e->getMessage());

            return false;
        }
    }

    /**
     * Dispatch SMS via Universal Custom Gateway
     */
    public function dispatchCustomSms(ApiSetting|array $config, string $phone, string $message): array
    {
        $get = function ($key, $default = null) use ($config) {
            if ($config instanceof ApiSetting) {
                return $config->getCredential($key, $default);
            }

            return $config[$key] ?? $default;
        };

        $url = trim($get('endpoint_url', ''));
        if (empty($url)) {
            return ['success' => false, 'error_message' => 'API Endpoint URL is missing.'];
        }

        $method = strtoupper($get('http_method', 'GET'));
        $apiKeyParam = trim($get('api_key_param', 'apikey')) ?: 'apikey';
        $apiKeyValue = trim($get('api_key_value', '') ?: $get('api_key', ''));

        $senderParam = trim($get('sender_id_param', 'callerID')) ?: 'callerID';
        $senderValue = trim($get('sender_id_value', '') ?: ($get('caller_id', '') ?: $get('sender_id', '')));

        $phoneParam = trim($get('phone_param', 'number')) ?: 'number';
        $msgParam = trim($get('message_param', 'message')) ?: 'message';

        $successKeyword = trim($get('success_keyword', ''));

        // Prepare request parameters
        $params = [];
        if ($apiKeyParam && $apiKeyValue) {
            $params[$apiKeyParam] = $apiKeyValue;
        }
        if ($senderParam && $senderValue) {
            $params[$senderParam] = $senderValue;
        }
        $params[$phoneParam] = $phone;
        $params[$msgParam] = $message;

        // Parse extra parameters (e.g. type=text, format=json)
        $extraParamsRaw = trim($get('extra_params', ''));
        if (! empty($extraParamsRaw)) {
            $lines = preg_split('/[\r\n,&]+/', $extraParamsRaw);
            foreach ($lines as $line) {
                if (str_contains($line, '=')) {
                    [$k, $v] = explode('=', $line, 2);
                    $params[trim($k)] = trim($v);
                }
            }
        }

        try {
            $http = Http::withoutVerifying()->timeout(15);

            if ($method === 'POST_JSON') {
                $response = $http->post($url, $params);
            } elseif ($method === 'POST' || $method === 'POST_FORM') {
                $response = $http->asForm()->post($url, $params);
            } else {
                $response = $http->get($url, $params);
            }

            $body = $response->body();
            $jsonData = $response->json();

            // Evaluate Success
            $isSuccess = false;
            if (! empty($successKeyword)) {
                $isSuccess = str_contains(strtolower($body), strtolower($successKeyword));
            } elseif (is_array($jsonData)) {
                $status = $jsonData['Status'] ?? ($jsonData['status'] ?? ($jsonData['response_code'] ?? null));
                $isSuccess = ($jsonData['Success'] ?? '') === 'true'
                    || ($jsonData['success'] ?? false) === true
                    || $status === '1000' || $status === 1000 || $status === 202 || $status === 200 || $status === '200' || $status === '202';
            } else {
                $isSuccess = $response->successful();
            }

            if ($isSuccess) {
                $msgId = null;
                if (is_array($jsonData)) {
                    $msgId = $jsonData['message_id'] ?? ($jsonData['msg_id'] ?? ($jsonData['id'] ?? ($jsonData['response_id'] ?? null)));
                }

                return [
                    'success' => true,
                    'response_id' => (string) ($msgId ?: ('SMS_CUSTOM_'.uniqid())),
                    'raw_response' => $body,
                ];
            }

            $err = is_array($jsonData)
                ? ($jsonData['Message'] ?? ($jsonData['message'] ?? ($jsonData['error_message'] ?? ($jsonData['error'] ?? $body))))
                : $body;

            return [
                'success' => false,
                'error_message' => $err ?: 'Failed to dispatch via Custom SMS Gateway.',
                'raw_response' => $body,
            ];
        } catch (\Exception $e) {
            Log::error('Custom SMS Gateway HTTP Error: '.$e->getMessage());

            return [
                'success' => false,
                'error_message' => 'Connection Exception: '.$e->getMessage(),
            ];
        }
    }

    /**
     * Test Custom SMS Gateway Connection & Live Ping
     */
    public function testCustomSms(array $data): array
    {
        $testNumber = $data['test_phone'] ?? '01700000000';
        $testMessage = $data['test_message'] ?? 'Custom SMS Gateway connection verification ping';

        if (empty($data['endpoint_url'])) {
            return [
                'success' => false,
                'message' => 'API Endpoint URL is required.',
            ];
        }

        $result = $this->dispatchCustomSms($data, $testNumber, $testMessage);

        if ($result['success']) {
            return [
                'success' => true,
                'message' => 'Custom SMS Gateway connected successfully! Raw response: '.substr($result['raw_response'] ?? '', 0, 200),
                'raw' => $result['raw_response'] ?? '',
            ];
        }

        return [
            'success' => false,
            'message' => $result['error_message'] ?? 'Failed to connect to Custom SMS Gateway.',
            'raw' => $result['raw_response'] ?? '',
        ];
    }

    /**
     * Test Bulk SMS Dhaka Connection / Ping
     */
    public function checkBulkSmsDhaka(?string $apiKey = null, ?string $callerId = null): array
    {
        $setting = ApiSetting::where('provider', 'bulksmsdhaka')->first();
        $key = $apiKey ?: ($setting?->getCredential('api_key') ?? ($setting?->getCredential('apikey') ?? $setting?->api_key));
        $caller = $callerId ?: ($setting?->getCredential('caller_id') ?? ($setting?->getCredential('sender_id') ?? ($setting?->sender_id ?? '1234')));

        if (! $key) {
            return [
                'success' => false,
                'message' => 'Bulk SMS Dhaka API Key is required.',
            ];
        }

        if (app()->environment('testing') || str_contains($key, 'demo') || str_contains($key, 'mock')) {
            return [
                'success' => true,
                'message' => 'Bulk SMS Dhaka connection active (Demo/Testing Mode)!',
            ];
        }

        try {
            $response = Http::withoutVerifying()->timeout(12)->get('https://bulksmsdhaka.net/api/otpsend', [
                'apikey' => $key,
                'callerID' => $caller,
                'number' => '01700000000',
                'message' => 'API Connection Verification Test',
            ]);

            $data = $response->json();
            $statusCode = $data['Status'] ?? ($data['status'] ?? null);
            $isSuccess = ($data['Success'] ?? '') === 'true' || $statusCode === '1000' || $statusCode === 1000;

            if ($isSuccess) {
                return [
                    'success' => true,
                    'message' => 'Bulk SMS Dhaka connected successfully! Live API is active and operational.',
                ];
            }

            return [
                'success' => false,
                'message' => $data['Message'] ?? ($data['error_message'] ?? ($response->body() ?: 'Invalid Bulk SMS Dhaka API Key or Caller ID.')),
            ];
        } catch (\Exception $e) {
            Log::error('Bulk SMS Dhaka Ping Error: '.$e->getMessage());

            return [
                'success' => false,
                'message' => 'Bulk SMS Dhaka Connection failed: '.$e->getMessage(),
            ];
        }
    }

    /**
     * Check BulkSMS BD Account Balance / Connection
     */
    public function checkBalance(?string $apiKey = null): array
    {
        $setting = ApiSetting::whereIn('provider', ['bulksms_bd', 'bulksms'])->first();
        $key = $apiKey ?: ($setting?->getCredential('api_key') ?? $setting?->api_key);

        if (! $key) {
            return [
                'success' => false,
                'message' => 'BulkSMS BD API Key is not configured.',
            ];
        }

        if (app()->environment('testing') || str_contains($key, 'demo') || str_contains($key, 'mock')) {
            return [
                'success' => true,
                'balance' => '500.00',
                'message' => 'BulkSMS BD connection active (Demo/Testing Mode)! Balance: ৳500.00',
            ];
        }

        try {
            $response = Http::withoutVerifying()->timeout(12)->get('https://bulksmsbd.net/api/getBalanceApi', [
                'api_key' => $key,
            ]);

            $data = $response->json();
            if (($data['response_code'] ?? null) == 202) {
                $balance = $data['balance'] ?? '0.00';

                return [
                    'success' => true,
                    'balance' => $balance,
                    'message' => "BulkSMS BD connected successfully! Current Balance: ৳{$balance}",
                ];
            }

            return [
                'success' => false,
                'message' => $data['error_message'] ?? 'Invalid BulkSMS BD API Key or server unreachable.',
            ];
        } catch (\Exception $e) {
            Log::error('BulkSMS BD Balance Check Error: '.$e->getMessage());

            return [
                'success' => false,
                'message' => 'BulkSMS Connection failed: '.$e->getMessage(),
            ];
        }
    }
}
