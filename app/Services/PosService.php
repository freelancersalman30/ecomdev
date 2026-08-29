<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;

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
        $orderData = [
            'order_type' => 'pos',
            'status' => 'completed',
            'shipping_name' => $payload['customer_name'] ?? 'Walk-in Customer',
            'shipping_phone' => $payload['customer_phone'] ?? '01700000000',
            'shipping_address' => 'Store Counter - DREAMERS PCB',
            'shipping_city' => 'Store POS',
            'discount' => (float)($payload['discount'] ?? 0),
            'tax' => (float)($payload['tax'] ?? 0),
            'shipping_charge' => (float)($payload['shipping_charge'] ?? 0),
            'paid_amount' => (float)($payload['paid_amount'] ?? 0),
            'payment_method' => $payload['payment_method'] ?? 'pos_cash',
            'account_id' => $payload['account_id'] ?? 1,
            'admin_note' => 'POS Terminal Sale. Cashier: ' . (auth()->user()->name ?? 'Admin'),
        ];

        $items = [];
        foreach ($payload['cart'] as $item) {
            $items[] = [
                'product_id' => $item['product_id'],
                'variant_id' => $item['variant_id'] ?? null,
                'quantity' => (int)$item['quantity'],
            ];
        }

        return $this->orderService->createOrder($orderData, $items);
    }
}
