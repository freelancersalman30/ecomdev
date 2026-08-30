<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\FraudCheck;
use App\Models\Order;

class FraudCheckService
{
    /**
     * Evaluate risk score for incoming order (0 = safe, 100 = critical risk)
     */
    public function evaluateOrder(string $phone, ?string $ipAddress = null): array
    {
        $score = 0;
        $reasons = [];

        // Check if phone or IP is blacklisted in fraud_checks
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
            ];
        }

        // Check past customer delivery success rate
        $customer = Customer::where('phone', $phone)->first();
        if ($customer) {
            if ($customer->total_orders_count >= 3 && $customer->delivery_success_rate < 40) {
                $score += 60;
                $reasons[] = "Customer delivery success rate is only {$customer->delivery_success_rate}%";
            } elseif ($customer->cancelled_orders_count >= 3) {
                $score += 35;
                $reasons[] = "Multiple ({$customer->cancelled_orders_count}) cancelled previous orders";
            }
        }

        // Check multiple recent orders from same IP or Phone in last 24 hours
        $recentOrdersCount = Order::where('shipping_phone', $phone)
            ->where('created_at', '>=', now()->subDay())
            ->count();

        if ($recentOrdersCount >= 3) {
            $score += 30;
            $reasons[] = "{$recentOrdersCount} orders placed in the last 24 hours";
        }

        $isFraudSuspect = $score >= 50;
        $riskLevel = match (true) {
            $score >= 75 => 'critical',
            $score >= 50 => 'high',
            $score >= 25 => 'medium',
            default => 'low',
        };

        return [
            'score' => $score,
            'is_fraud_suspect' => $isFraudSuspect,
            'risk_level' => $riskLevel,
            'reason' => implode(' | ', $reasons) ?: 'Normal order profile',
            'success_rate' => $customer ? $customer->delivery_success_rate : 100.00,
        ];
    }
}
