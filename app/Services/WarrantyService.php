<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Warranty;
use Carbon\Carbon;

class WarrantyService
{
    /**
     * Issue or retrieve warranty for an order item
     */
    public function generateWarrantyForOrderItem(OrderItem $item, Order $order, ?string $serialNumber = null): ?Warranty
    {
        $existing = Warranty::where('order_item_id', $item->id)->first();
        if ($existing) {
            if ($serialNumber && ! $existing->serial_number) {
                $existing->update(['serial_number' => $serialNumber]);
            }

            return $existing;
        }

        $product = $item->product ?: Product::find($item->product_id);
        if (! $product) {
            return null;
        }

        $warrantyPeriod = $product->warranty ?: '1 Year Official Warranty';
        $warrantyDays = Warranty::parseDurationDays($warrantyPeriod);

        $startDate = $order->delivered_at ? Carbon::parse($order->delivered_at) : ($order->created_at ? Carbon::parse($order->created_at) : Carbon::now());
        $endDate = (clone $startDate)->addDays($warrantyDays);

        return Warranty::create([
            'warranty_code' => Warranty::generateCode(),
            'serial_number' => $serialNumber,
            'order_id' => $order->id,
            'order_item_id' => $item->id,
            'customer_id' => $order->customer_id,
            'product_id' => $product->id,
            'customer_name' => $order->shipping_name ?: ($order->customer?->name ?: 'Customer'),
            'customer_phone' => $order->shipping_phone ?: ($order->customer?->phone ?: ''),
            'customer_email' => $order->shipping_email ?: $order->customer?->email,
            'warranty_period' => $warrantyPeriod,
            'warranty_days' => $warrantyDays,
            'start_date' => $startDate->toDateString(),
            'end_date' => $endDate->toDateString(),
            'status' => 'active',
        ]);
    }

    /**
     * Sync and issue warranties for all items in an order
     */
    public function syncOrderWarranties(Order $order): array
    {
        $warranties = [];

        foreach ($order->items as $item) {
            $w = $this->generateWarrantyForOrderItem($item, $order);
            if ($w) {
                $warranties[] = $w;
            }
        }

        return $warranties;
    }

    /**
     * Verify warranty by searching code, serial number, or order number
     */
    public function verifyWarranty(string $query): ?Warranty
    {
        $cleanQuery = trim($query);
        if (empty($cleanQuery)) {
            return null;
        }

        // 1. Direct match on warranty_code
        $warranty = Warranty::with(['product', 'order', 'customer'])
            ->where('warranty_code', $cleanQuery)
            ->first();

        if ($warranty) {
            return $warranty;
        }

        // 2. Direct match on serial_number
        $warranty = Warranty::with(['product', 'order', 'customer'])
            ->where('serial_number', $cleanQuery)
            ->first();

        if ($warranty) {
            return $warranty;
        }

        // 3. Match via order_no
        $warranty = Warranty::with(['product', 'order', 'customer'])
            ->whereHas('order', function ($q) use ($cleanQuery) {
                $q->where('order_no', $cleanQuery);
            })
            ->first();

        if ($warranty) {
            return $warranty;
        }

        // 4. Case-insensitive / partial match fallback
        return Warranty::with(['product', 'order', 'customer'])
            ->where('warranty_code', 'LIKE', "%{$cleanQuery}%")
            ->orWhere('serial_number', 'LIKE', "%{$cleanQuery}%")
            ->first();
    }

    /**
     * Manually register or issue a warranty from Admin
     */
    public function createManualWarranty(array $data): Warranty
    {
        $warrantyDays = ! empty($data['warranty_days'])
            ? (int) $data['warranty_days']
            : Warranty::parseDurationDays($data['warranty_period'] ?? null);

        $startDate = ! empty($data['start_date']) ? Carbon::parse($data['start_date']) : Carbon::now();
        $endDate = ! empty($data['end_date']) ? Carbon::parse($data['end_date']) : (clone $startDate)->addDays($warrantyDays);

        $orderId = ! empty($data['order_id']) ? (int) $data['order_id'] : null;
        $customerId = ! empty($data['customer_id']) ? (int) $data['customer_id'] : null;

        if ($orderId && ! $customerId) {
            $order = Order::find($orderId);
            $customerId = $order?->customer_id;
        }

        if (! $customerId && ! empty($data['customer_phone'])) {
            $customer = Customer::where('phone', $data['customer_phone'])->first();
            $customerId = $customer?->id;
        }

        return Warranty::create([
            'warranty_code' => Warranty::generateCode(),
            'serial_number' => ! empty($data['serial_number']) ? trim($data['serial_number']) : null,
            'order_id' => $orderId,
            'order_item_id' => ! empty($data['order_item_id']) ? (int) $data['order_item_id'] : null,
            'customer_id' => $customerId,
            'product_id' => (int) $data['product_id'],
            'customer_name' => $data['customer_name'] ?? 'Walk-in Customer',
            'customer_phone' => $data['customer_phone'] ?? '',
            'customer_email' => $data['customer_email'] ?? null,
            'warranty_period' => $data['warranty_period'] ?? "{$warrantyDays} Days Warranty",
            'warranty_days' => $warrantyDays,
            'start_date' => $startDate->toDateString(),
            'end_date' => $endDate->toDateString(),
            'status' => $data['status'] ?? 'active',
            'admin_notes' => $data['admin_notes'] ?? null,
        ]);
    }
}
