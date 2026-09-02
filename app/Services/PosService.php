<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\Order;

class PosService
{
    protected OrderService $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    /**
     * Process checkout from POS counter terminal
     */
    public function processPosSale(array $payload): Order
    {
        $customerName = ! empty($payload['customer_name']) ? trim($payload['customer_name']) : 'Walk-in Customer';
        $customerPhone = ! empty($payload['customer_phone']) ? trim($payload['customer_phone']) : '01700000000';
        $customerAddress = ! empty($payload['customer_address']) ? trim($payload['customer_address']) : 'Store Counter - DREAMERS PCB';
        $customerCity = ! empty($payload['customer_city']) ? trim($payload['customer_city']) : 'Store POS';
        $customerEmail = ! empty($payload['customer_email']) ? trim($payload['customer_email']) : null;
        $customerNote = ! empty($payload['customer_note']) ? trim($payload['customer_note']) : null;

        // If an existing customer ID was selected, load/sync details
        $customerId = ! empty($payload['customer_id']) ? (int) $payload['customer_id'] : null;
        if ($customerId) {
            $existingCust = Customer::find($customerId);
            if ($existingCust) {
                if (empty($payload['customer_name'])) {
                    $customerName = $existingCust->name;
                }
                if (empty($payload['customer_phone'])) {
                    $customerPhone = $existingCust->phone;
                }
                if (empty($payload['customer_address']) && ! empty($existingCust->address)) {
                    $customerAddress = $existingCust->address;
                }
                if (empty($payload['customer_city']) && ! empty($existingCust->city)) {
                    $customerCity = $existingCust->city;
                }
                if (empty($payload['customer_email']) && ! empty($existingCust->email)) {
                    $customerEmail = $existingCust->email;
                }
            }
        } elseif (! empty($payload['customer_phone']) && $payload['customer_phone'] !== '01700000000') {
            // Find or create customer record in CRM
            $cust = Customer::firstOrCreate(
                ['phone' => $customerPhone],
                [
                    'name' => $customerName,
                    'address' => $customerAddress !== 'Store Counter - DREAMERS PCB' ? $customerAddress : null,
                    'city' => $customerCity !== 'Store POS' ? $customerCity : 'Dhaka',
                    'email' => $customerEmail,
                    'is_active' => true,
                ]
            );
            $customerId = $cust->id;
        }

        $orderData = [
            'customer_id' => $customerId,
            'order_type' => 'pos',
            'status' => 'completed',
            'shipping_name' => $customerName,
            'shipping_phone' => $customerPhone,
            'shipping_address' => $customerAddress,
            'shipping_city' => $customerCity,
            'shipping_email' => $customerEmail,
            'customer_note' => $customerNote,
            'discount' => (float) ($payload['discount'] ?? 0),
            'tax' => (float) ($payload['tax'] ?? 0),
            'shipping_charge' => (float) ($payload['shipping_charge'] ?? 0),
            'paid_amount' => (float) ($payload['paid_amount'] ?? 0),
            'payment_method' => $payload['payment_method'] ?? 'pos_cash',
            'account_id' => $payload['account_id'] ?? 1,
            'admin_note' => 'POS Terminal Sale. Cashier: '.(auth()->user()->name ?? 'Admin'),
        ];

        $items = [];
        foreach ($payload['cart'] as $item) {
            $items[] = [
                'product_id' => $item['product_id'],
                'variant_id' => $item['variant_id'] ?? null,
                'quantity' => (int) $item['quantity'],
            ];
        }

        return $this->orderService->createOrder($orderData, $items);
    }
}
