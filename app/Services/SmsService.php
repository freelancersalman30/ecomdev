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
     * Send SMS with tag replacement & BulkSMS BD API Dispatch
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

        // Fetch BulkSMS configuration
        $setting = ApiSetting::whereIn('provider', ['bulksms_bd', 'bulksms'])->first();
        $isActive = $setting ? (bool) $setting->is_active : true;
        $apiKey = $setting?->getCredential('api_key') ?? $setting?->api_key;
        $senderId = $setting?->getCredential('sender_id') ?? $setting?->sender_id ?? '8809617618999';

        $status = 'sent';
        $responseId = 'SMS_'.uniqid();
        $errorMessage = null;

        if ($isActive && $apiKey) {
            // Check if testing environment or demo keys
            if (app()->environment('testing') || str_contains($apiKey, 'demo') || str_contains($apiKey, 'secret_key')) {
                $status = 'sent';
                $responseId = 'SMS_SIM_'.uniqid();
            } else {
                try {
                    // Clean phone number
                    $cleanPhone = preg_replace('/[^0-9]/', '', $phone);

                    $response = Http::withoutVerifying()->timeout(12)->post('https://bulksmsbd.net/api/smsapi', [
                        'api_key' => $apiKey,
                        'type' => 'text',
                        'number' => $cleanPhone,
                        'senderid' => $senderId,
                        'message' => $message,
                    ]);

                    $resData = $response->json();
                    $responseCode = $resData['response_code'] ?? null;

                    if ($responseCode == 202 || ($response->successful() && isset($resData['success_message']))) {
                        $status = 'sent';
                        $responseId = (string) ($resData['message_id'] ?? ('SMS_'.uniqid()));
                    } else {
                        $status = 'failed';
                        $responseId = null;
                        $errorMessage = $resData['error_message'] ?? ($response->body() ?: 'BulkSMS BD submission failed.');
                        Log::warning('BulkSMS BD Dispatch Failure: '.$errorMessage);
                    }
                } catch (\Exception $e) {
                    Log::error('BulkSMS BD HTTP Connection Error: '.$e->getMessage());
                    $status = 'failed';
                    $responseId = null;
                    $errorMessage = $e->getMessage();
                }
            }
        } elseif (! $isActive) {
            Log::info("BulkSMS is disabled in API settings. Message to {$phone} skipped from live dispatch.");
            $status = 'sent';
            $responseId = 'SMS_INACTIVE_'.uniqid();
        }

        // Log SMS entry
        try {
            SmsLog::create([
                'gateway' => $gateway,
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

        if (app()->environment('testing') || str_contains($key, 'demo') || str_contains($key, 'secret_key')) {
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
