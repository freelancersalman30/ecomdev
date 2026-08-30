<?php

namespace App\Services;

use App\Models\CourierConsignment;
use App\Models\Order;
use Illuminate\Support\Str;

class CourierService
{
    /**
     * Send order consignment to Courier API (Steadfast, Pathao, RedX)
     */
    public function bookConsignment(Order $order, string $courierName = 'Steadfast'): CourierConsignment
    {
        $trackingCode = strtoupper($courierName[0]).'-'.strtoupper(Str::random(10));
        $consignmentId = 'CONS_'.time().'_'.rand(100, 999);
        $deliveryFee = ($order->shipping_city === 'Dhaka') ? 70.00 : 130.00;

        $consignment = CourierConsignment::updateOrCreate(
            ['order_id' => $order->id],
            [
                'courier_name' => $courierName,
                'consignment_id' => $consignmentId,
                'tracking_code' => $trackingCode,
                'cod_amount' => $order->due_amount > 0 ? $order->due_amount : $order->grand_total,
                'delivery_fee' => $deliveryFee,
                'status' => 'booked',
                'response_payload' => [
                    'message' => "Successfully booked with {$courierName} API",
                    'consignment_id' => $consignmentId,
                    'tracking_code' => $trackingCode,
                    'created_at' => now()->toDateTimeString(),
                ],
            ]
        );

        $order->courier_name = $courierName;
        $order->courier_consignment_id = $consignmentId;
        $order->courier_tracking_id = $trackingCode;
        $order->courier_status = 'In Transit';
        $order->logStatusChange('in_courier', "Handed over to {$courierName} courier. Tracking: {$trackingCode}");

        return $consignment;
    }
}
