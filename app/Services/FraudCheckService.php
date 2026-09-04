<?php

namespace App\Services;

use App\Models\ApiSetting;
use App\Models\Customer;
use App\Models\FraudCheck;
use App\Models\Order;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FraudCheckService
{
    /**
     * Normalize Bangladesh phone number to 11 digits (01XXXXXXXXX)
     */
    public function normalizePhone(string $phone): string
    {
        $cleaned = preg_replace('/[^0-9]/', '', $phone);

        if (str_starts_with($cleaned, '8801') && strlen($cleaned) === 13) {
            $cleaned = substr($cleaned, 2);
        } elseif (str_starts_with($cleaned, '88') && strlen($cleaned) === 12) {
            $cleaned = substr($cleaned, 2);
        }

        return $cleaned;
    }

    /**
     * Get active Fraud API setting (Zachaikori, Universal, etc.)
     */
    public function getActiveApiSetting(): ?ApiSetting
    {
        return ApiSetting::where('type', 'fraud')
            ->where('is_active', true)
            ->first();
    }

    /**
     * Query external fraud check API (Zachaikori or Universal Gateway)
     */
    public function checkExternalApi(string $phone, ?ApiSetting $apiSetting = null): array
    {
        $phone = $this->normalizePhone($phone);
        $setting = $apiSetting ?? $this->getActiveApiSetting();

        if (! $setting || ! $setting->is_active) {
            return [
                'success' => false,
                'provider' => 'none',
                'message' => 'No active Fraud API gateway configured.',
            ];
        }

        $credentials = is_array($setting->credentials) ? $setting->credentials : [];
        $provider = $setting->provider;

        try {
            if ($provider === 'zachaikori') {
                return $this->queryZachaikoriApi($phone, $credentials);
            } elseif ($provider === 'universal_fraud') {
                return $this->queryUniversalApi($phone, $credentials);
            } else {
                // Fallback custom provider
                return $this->queryUniversalApi($phone, $credentials);
            }
        } catch (Exception $e) {
            Log::error("Fraud API Check Exception [{$provider}]: ".$e->getMessage());

            return [
                'success' => false,
                'provider' => $provider,
                'error' => $e->getMessage(),
                'message' => 'Failed to connect to fraud check API.',
            ];
        }
    }

    /**
     * Query Zachaikori Fraud API
     */
    protected function queryZachaikoriApi(string $phone, array $credentials): array
    {
        $apiKey = $credentials['api_key'] ?? ($credentials['token'] ?? '');
        $endpoint = $credentials['endpoint_url'] ?? 'https://api.zachaikori.com/api/v1/check';
        $httpMethod = strtoupper($credentials['http_method'] ?? 'GET');
        $phoneParam = $credentials['phone_param'] ?? 'phone';

        if (empty($endpoint)) {
            $endpoint = 'https://api.zachaikori.com/api/v1/check';
        }

        $headers = [
            'Accept' => 'application/json',
            'User-Agent' => 'DreamersEcom-FraudCheck/1.0',
        ];

        if (! empty($apiKey)) {
            $headers['Authorization'] = 'Bearer '.$apiKey;
            $headers['X-API-KEY'] = $apiKey;
        }

        $timeout = (int) ($credentials['timeout'] ?? 6);

        if ($httpMethod === 'GET') {
            $url = $endpoint;
            if (str_contains($url, '{phone}')) {
                $url = str_replace('{phone}', urlencode($phone), $url);
                $response = Http::timeout($timeout)->withHeaders($headers)->get($url);
            } else {
                $response = Http::timeout($timeout)->withHeaders($headers)->get($url, [
                    $phoneParam => $phone,
                ]);
            }
        } elseif ($httpMethod === 'POST_JSON') {
            $response = Http::timeout($timeout)->withHeaders($headers)->post($endpoint, [
                $phoneParam => $phone,
            ]);
        } else {
            // POST Form
            $response = Http::timeout($timeout)->asForm()->withHeaders($headers)->post($endpoint, [
                $phoneParam => $phone,
            ]);
        }

        if (! $response->successful()) {
            return [
                'success' => false,
                'provider' => 'zachaikori',
                'status_code' => $response->status(),
                'error' => 'API returned HTTP '.$response->status(),
                'raw_response' => $response->json() ?: $response->body(),
            ];
        }

        $data = $response->json() ?? [];

        // Extract metrics from Zachaikori JSON format
        $totalParcels = (int) (data_get($data, 'total_parcels')
            ?? data_get($data, 'data.total_parcels')
            ?? data_get($data, 'total_orders')
            ?? data_get($data, 'data.total')
            ?? 0);

        $deliveredParcels = (int) (data_get($data, 'delivered_parcels')
            ?? data_get($data, 'data.delivered_parcels')
            ?? data_get($data, 'delivered')
            ?? data_get($data, 'data.delivered')
            ?? 0);

        $cancelledParcels = (int) (data_get($data, 'cancelled_parcels')
            ?? data_get($data, 'data.cancelled_parcels')
            ?? data_get($data, 'returned_parcels')
            ?? data_get($data, 'data.returned')
            ?? data_get($data, 'returns')
            ?? 0);

        $successRate = data_get($data, 'success_rate')
            ?? data_get($data, 'data.success_rate')
            ?? data_get($data, 'delivery_rate')
            ?? data_get($data, 'data.delivery_rate');

        if ($successRate === null) {
            if ($totalParcels > 0) {
                $successRate = round(($deliveredParcels / $totalParcels) * 100, 2);
            } else {
                $successRate = 100.00;
            }
        } else {
            $successRate = (float) $successRate;
        }

        $riskLevel = data_get($data, 'risk_level')
            ?? data_get($data, 'data.risk_level')
            ?? match (true) {
                $totalParcels > 2 && $successRate < 40 => 'critical',
                $totalParcels > 2 && $successRate < 60 => 'high',
                $totalParcels > 0 && $successRate < 75 => 'medium',
                default => 'low',
            };

        $isBlacklisted = (bool) (data_get($data, 'is_blacklisted')
            ?? data_get($data, 'data.is_blacklisted')
            ?? data_get($data, 'is_fraud')
            ?? false);

        $notes = data_get($data, 'notes')
            ?? data_get($data, 'data.notes')
            ?? data_get($data, 'message')
            ?? "Zachaikori report: {$deliveredParcels}/{$totalParcels} delivered ({$successRate}%)";

        // Cache or update FraudCheck record
        FraudCheck::updateOrCreate(
            ['phone' => $phone],
            [
                'risk_level' => $riskLevel,
                'courier_success_rate' => $successRate,
                'total_parcels' => $totalParcels,
                'delivered_parcels' => $deliveredParcels,
                'cancelled_parcels' => $cancelledParcels,
                'notes' => $notes,
                'is_blacklisted' => $isBlacklisted,
            ]
        );

        return [
            'success' => true,
            'provider' => 'zachaikori',
            'provider_title' => 'Zachaikori Courier Risk API',
            'phone' => $phone,
            'total_parcels' => $totalParcels,
            'delivered_parcels' => $deliveredParcels,
            'cancelled_parcels' => $cancelledParcels,
            'success_rate' => $successRate,
            'risk_level' => $riskLevel,
            'is_blacklisted' => $isBlacklisted,
            'notes' => $notes,
            'raw_response' => $data,
        ];
    }

    /**
     * Query Universal / Custom Fraud Check REST API
     */
    protected function queryUniversalApi(string $phone, array $credentials): array
    {
        $endpoint = $credentials['endpoint_url'] ?? '';
        $httpMethod = strtoupper($credentials['http_method'] ?? 'GET');
        $phoneParam = $credentials['phone_param'] ?? 'phone';
        $timeout = (int) ($credentials['timeout'] ?? 6);

        if (empty($endpoint)) {
            return [
                'success' => false,
                'provider' => 'universal_fraud',
                'error' => 'API Endpoint URL is required.',
            ];
        }

        // Build Custom Headers
        $headers = [
            'Accept' => 'application/json',
            'User-Agent' => 'DreamersEcom-UniversalFraud/1.0',
        ];

        if (! empty($credentials['auth_header_name']) && ! empty($credentials['auth_header_value'])) {
            $headers[$credentials['auth_header_name']] = $credentials['auth_header_value'];
        } elseif (! empty($credentials['api_key'])) {
            $headers['Authorization'] = 'Bearer '.$credentials['api_key'];
        }

        // Parse custom headers lines if provided (Header-Name: value)
        if (! empty($credentials['custom_headers'])) {
            $lines = explode("\n", str_replace("\r", '', $credentials['custom_headers']));
            foreach ($lines as $line) {
                if (str_contains($line, ':')) {
                    [$headerKey, $headerVal] = explode(':', $line, 2);
                    $headers[trim($headerKey)] = trim($headerVal);
                }
            }
        }

        // Perform HTTP request
        if ($httpMethod === 'GET') {
            $url = $endpoint;
            if (str_contains($url, '{phone}')) {
                $url = str_replace('{phone}', urlencode($phone), $url);
                $response = Http::timeout($timeout)->withHeaders($headers)->get($url);
            } else {
                $response = Http::timeout($timeout)->withHeaders($headers)->get($url, [
                    $phoneParam => $phone,
                ]);
            }
        } elseif ($httpMethod === 'POST_JSON') {
            $payload = [$phoneParam => $phone];
            if (! empty($credentials['extra_payload_json'])) {
                $extra = json_decode($credentials['extra_payload_json'], true);
                if (is_array($extra)) {
                    $payload = array_merge($extra, $payload);
                }
            }
            $response = Http::timeout($timeout)->withHeaders($headers)->post($endpoint, $payload);
        } else {
            // POST Form
            $payload = [$phoneParam => $phone];
            $response = Http::timeout($timeout)->asForm()->withHeaders($headers)->post($endpoint, $payload);
        }

        if (! $response->successful()) {
            return [
                'success' => false,
                'provider' => 'universal_fraud',
                'status_code' => $response->status(),
                'error' => 'Universal API returned HTTP '.$response->status(),
                'raw_response' => $response->json() ?: $response->body(),
            ];
        }

        $data = $response->json() ?? [];

        // Key mappings configured by admin
        $successRateKey = $credentials['success_rate_key'] ?? 'success_rate';
        $totalParcelsKey = $credentials['total_orders_key'] ?? 'total_parcels';
        $deliveredParcelsKey = $credentials['delivered_orders_key'] ?? 'delivered_parcels';
        $cancelledParcelsKey = $credentials['cancelled_orders_key'] ?? 'cancelled_parcels';
        $riskLevelKey = $credentials['risk_level_key'] ?? 'risk_level';
        $reasonKey = $credentials['reason_key'] ?? 'notes';

        $totalParcels = (int) (data_get($data, $totalParcelsKey)
            ?? data_get($data, 'total_parcels')
            ?? data_get($data, 'data.total')
            ?? data_get($data, 'total_orders')
            ?? 0);

        $deliveredParcels = (int) (data_get($data, $deliveredParcelsKey)
            ?? data_get($data, 'delivered_parcels')
            ?? data_get($data, 'data.delivered')
            ?? data_get($data, 'delivered')
            ?? 0);

        $cancelledParcels = (int) (data_get($data, $cancelledParcelsKey)
            ?? data_get($data, 'cancelled_parcels')
            ?? data_get($data, 'data.cancelled')
            ?? data_get($data, 'returned_parcels')
            ?? 0);

        $successRate = data_get($data, $successRateKey)
            ?? data_get($data, 'data.success_rate')
            ?? data_get($data, 'delivery_rate');

        if ($successRate === null) {
            if ($totalParcels > 0) {
                $successRate = round(($deliveredParcels / $totalParcels) * 100, 2);
            } else {
                $successRate = 100.00;
            }
        } else {
            $successRate = (float) $successRate;
        }

        $riskLevel = data_get($data, $riskLevelKey)
            ?? data_get($data, 'risk_level')
            ?? data_get($data, 'data.risk_level')
            ?? data_get($data, 'data.risk')
            ?? match (true) {
                $totalParcels > 2 && $successRate < 40 => 'critical',
                $totalParcels > 2 && $successRate < 60 => 'high',
                $totalParcels > 0 && $successRate < 75 => 'medium',
                default => 'low',
            };

        $isBlacklisted = (bool) (data_get($data, 'is_blacklisted')
            ?? data_get($data, 'data.is_blacklisted')
            ?? data_get($data, 'is_fraud')
            ?? data_get($data, 'data.is_fraud')
            ?? false);

        $notes = data_get($data, $reasonKey)
            ?? data_get($data, 'data.message')
            ?? data_get($data, 'message')
            ?? "Universal API report: {$deliveredParcels}/{$totalParcels} delivered ({$successRate}%)";

        // Save to cache/history
        FraudCheck::updateOrCreate(
            ['phone' => $phone],
            [
                'risk_level' => $riskLevel,
                'courier_success_rate' => $successRate,
                'total_parcels' => $totalParcels,
                'delivered_parcels' => $deliveredParcels,
                'cancelled_parcels' => $cancelledParcels,
                'notes' => $notes,
                'is_blacklisted' => $isBlacklisted,
            ]
        );

        return [
            'success' => true,
            'provider' => 'universal_fraud',
            'provider_title' => $credentials['provider_name'] ?? 'Universal Fraud Checker API',
            'phone' => $phone,
            'total_parcels' => $totalParcels,
            'delivered_parcels' => $deliveredParcels,
            'cancelled_parcels' => $cancelledParcels,
            'success_rate' => $successRate,
            'risk_level' => $riskLevel,
            'is_blacklisted' => $isBlacklisted,
            'notes' => $notes,
            'raw_response' => $data,
        ];
    }

    /**
     * Evaluate risk score for incoming order or phone lookup (0 = safe, 100 = critical risk)
     */
    public function evaluateOrder(string $phone, ?string $ipAddress = null): array
    {
        $phone = $this->normalizePhone($phone);
        $score = 0;
        $reasons = [];
        $externalData = null;

        // 1. Check local blacklist in fraud_checks
        $blacklisted = FraudCheck::where('phone', $phone)
            ->orWhere(function ($q) use ($ipAddress) {
                if ($ipAddress) {
                    $q->where('ip_address', $ipAddress);
                }
            })
            ->where('is_blacklisted', true)
            ->first();

        if ($blacklisted) {
            return [
                'score' => 95,
                'is_fraud_suspect' => true,
                'risk_level' => 'critical',
                'reason' => 'Phone or IP is explicitly blacklisted in fraud records',
                'success_rate' => $blacklisted->courier_success_rate,
                'external_data' => [
                    'provider' => 'local_blacklist',
                    'total_parcels' => $blacklisted->total_parcels,
                    'delivered_parcels' => $blacklisted->delivered_parcels,
                    'cancelled_parcels' => $blacklisted->cancelled_parcels,
                    'notes' => $blacklisted->notes,
                ],
            ];
        }

        // 2. Query External API if active
        $apiSetting = $this->getActiveApiSetting();
        if ($apiSetting && $apiSetting->is_active && ! empty($phone)) {
            $apiResult = $this->checkExternalApi($phone, $apiSetting);
            if (! empty($apiResult['success'])) {
                $externalData = $apiResult;
                $courierRate = (float) ($apiResult['success_rate'] ?? 100);
                $totalParcels = (int) ($apiResult['total_parcels'] ?? 0);
                $cancelledParcels = (int) ($apiResult['cancelled_parcels'] ?? 0);

                if (! empty($apiResult['is_blacklisted'])) {
                    $score += 85;
                    $reasons[] = 'Reported blacklisted by '.$apiResult['provider_title'];
                } elseif ($totalParcels >= 3 && $courierRate < 40) {
                    $score += 65;
                    $reasons[] = "Courier delivery success rate is only {$courierRate}% across Bangladesh ({$cancelledParcels} returns)";
                } elseif ($totalParcels >= 2 && $courierRate < 60) {
                    $score += 40;
                    $reasons[] = "Low courier success rate ({$courierRate}%) via {$apiResult['provider_title']}";
                } elseif ($cancelledParcels >= 4) {
                    $score += 35;
                    $reasons[] = "Customer has {$cancelledParcels} parcel returns with couriers";
                } elseif ($totalParcels >= 5 && $courierRate >= 85) {
                    $score = max(0, $score - 15); // Reward positive verified buyer
                }
            }
        }

        // 3. Check past store customer delivery & return history
        $customer = Customer::where('phone', $phone)->first();
        if ($customer) {
            if ($customer->is_flagged_fraud) {
                $score += 70;
                $reasons[] = 'Customer is flagged as high risk in store database';
            } elseif ($customer->total_orders_count >= 3 && $customer->delivery_success_rate < 40) {
                $score += 50;
                $reasons[] = "Store delivery success rate is {$customer->delivery_success_rate}%";
            } elseif ($customer->cancelled_orders_count >= 3) {
                $score += 30;
                $reasons[] = "Multiple ({$customer->cancelled_orders_count}) cancelled previous orders in store";
            }
        }

        // 4. Check multiple rapid orders from same IP or Phone in last 24 hours
        $recentOrdersCount = Order::where('shipping_phone', $phone)
            ->where('created_at', '>=', now()->subDay())
            ->count();

        if ($recentOrdersCount >= 3) {
            $score += 30;
            $reasons[] = "{$recentOrdersCount} orders placed in the last 24 hours (potential bot/spam)";
        }

        $isFraudSuspect = $score >= 50;
        $riskLevel = match (true) {
            $score >= 75 => 'critical',
            $score >= 50 => 'high',
            $score >= 25 => 'medium',
            default => 'low',
        };

        // Determine best success rate to display
        $displaySuccessRate = 100.00;
        if ($externalData && isset($externalData['success_rate'])) {
            $displaySuccessRate = $externalData['success_rate'];
        } elseif ($customer && $customer->total_orders_count > 0) {
            $displaySuccessRate = $customer->delivery_success_rate;
        }

        return [
            'score' => min(100, $score),
            'is_fraud_suspect' => $isFraudSuspect,
            'risk_level' => $riskLevel,
            'reason' => implode(' | ', $reasons) ?: 'Normal order profile & verified buyer',
            'success_rate' => $displaySuccessRate,
            'external_data' => $externalData,
        ];
    }
}
