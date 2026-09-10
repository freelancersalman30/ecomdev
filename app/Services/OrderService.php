<?php

namespace App\Services;

use App\Models\Coupon;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderService
{
    protected InventoryService $inventoryService;

    protected FraudCheckService $fraudCheckService;

    protected SmsService $smsService;

    protected WarrantyService $warrantyService;

    protected AdminNotificationService $adminNotificationService;

    public function __construct(
        InventoryService $inventoryService,
        FraudCheckService $fraudCheckService,
        SmsService $smsService,
        WarrantyService $warrantyService,
        AdminNotificationService $adminNotificationService
    ) {
        $this->inventoryService = $inventoryService;
        $this->fraudCheckService = $fraudCheckService;
        $this->smsService = $smsService;
        $this->warrantyService = $warrantyService;
        $this->adminNotificationService = $adminNotificationService;
    }

    /**
     * Save or update an incomplete (drop-off) order when customer starts typing at checkout
     */
    public function saveIncompleteOrder(array $data, array $items): ?Order
    {
        $phone = trim($data['shipping_phone'] ?? '');
        $name = trim($data['shipping_name'] ?? '');

        // Require at least a phone number (minimum 6 digits) or a name to record drop-off
        if (empty($phone) && empty($name)) {
            return null;
        }

        if (empty($items)) {
            return null;
        }

        return DB::transaction(function () use ($data, $items, $phone, $name) {
            // Find or create customer record if phone is provided
            $customer = null;
            if (! empty($phone)) {
                $customer = Customer::firstOrCreate(
                    ['phone' => $phone],
                    [
                        'name' => ! empty($name) ? $name : 'Guest Customer',
                        'email' => $data['shipping_email'] ?? null,
                        'address' => $data['shipping_address'] ?? null,
                        'city' => $data['shipping_city'] ?? 'Dhaka',
                    ]
                );
            }

            // Compute subtotal and item prices
            $subtotal = 0;
            $processedItems = [];

            foreach ($items as $item) {
                $product = Product::find($item['product_id']);
                if (! $product) {
                    continue;
                }

                $variant = ! empty($item['variant_id']) ? ProductVariant::find($item['variant_id']) : null;
                $unitCost = $variant ? $variant->purchase_price : $product->purchase_price;
                $unitPrice = $variant ? $variant->effective_price : $product->effective_price;
                $qty = max(1, (int) ($item['quantity'] ?? 1));
                $itemSubtotal = $unitPrice * $qty;
                $subtotal += $itemSubtotal;

                $processedItems[] = [
                    'product_id' => $product->id,
                    'variant_id' => $variant?->id,
                    'product_name' => $product->name,
                    'variant_name' => $variant?->variant_name,
                    'sku' => $variant ? $variant->sku : $product->sku,
                    'unit_cost' => $unitCost,
                    'unit_price' => $unitPrice,
                    'quantity' => $qty,
                    'subtotal' => $itemSubtotal,
                ];
            }

            if (empty($processedItems)) {
                return null;
            }

            // Calculate coupon discount
            $discount = 0;
            if (! empty($data['coupon_code'])) {
                $coupon = Coupon::where('code', $data['coupon_code'])->first();
                if ($coupon) {
                    $discount = $coupon->calculateDiscount($subtotal);
                }
            } elseif (! empty($data['discount'])) {
                $discount = (float) $data['discount'];
            }

            $shippingCharge = (float) ($data['shipping_charge'] ?? 0);
            $tax = (float) ($data['tax'] ?? 0);
            $grandTotal = max(0, ($subtotal - $discount) + $shippingCharge + $tax);
            $paidAmount = 0;
            $dueAmount = $grandTotal;

            // Check if there is an existing incomplete order in session or for this phone in last 24h
            $existingOrderId = session('incomplete_order_id');
            $order = null;

            if ($existingOrderId) {
                $order = Order::where('id', $existingOrderId)->where('status', 'incomplete')->first();
            }

            if (! $order && ! empty($phone)) {
                $order = Order::where('status', 'incomplete')
                    ->where('shipping_phone', $phone)
                    ->where('created_at', '>=', now()->subHours(24))
                    ->latest()
                    ->first();
            }

            if ($order) {
                // Update existing incomplete order
                $order->update([
                    'customer_id' => $customer?->id ?? $order->customer_id,
                    'subtotal' => $subtotal,
                    'discount' => $discount,
                    'coupon_code' => $data['coupon_code'] ?? $order->coupon_code,
                    'shipping_charge' => $shippingCharge,
                    'tax' => $tax,
                    'grand_total' => $grandTotal,
                    'paid_amount' => $paidAmount,
                    'due_amount' => $dueAmount,
                    'payment_method' => $data['payment_method'] ?? 'cash_on_delivery',
                    'shipping_name' => ! empty($name) ? $name : $order->shipping_name,
                    'shipping_phone' => ! empty($phone) ? $phone : $order->shipping_phone,
                    'shipping_email' => $data['shipping_email'] ?? $order->shipping_email,
                    'shipping_address' => $data['shipping_address'] ?? $order->shipping_address,
                    'shipping_city' => $data['shipping_city'] ?? $order->shipping_city,
                    'customer_note' => $data['notes'] ?? $order->customer_note,
                ]);

                // Sync items
                $order->items()->delete();
                foreach ($processedItems as $pItem) {
                    $order->items()->create($pItem);
                }
            } else {
                // Generate collision-proof unique order ID
                $prefix = 'DPCB-'.date('Ymd').'-';
                do {
                    $orderNo = $prefix.str_pad((string) random_int(1001, 9999), 4, '0', STR_PAD_LEFT);
                } while (Order::where('order_no', $orderNo)->exists());

                $order = Order::create([
                    'order_no' => $orderNo,
                    'customer_id' => $customer?->id,
                    'user_id' => $data['user_id'] ?? auth()->id(),
                    'order_type' => $data['order_type'] ?? 'online',
                    'status' => 'incomplete',
                    'subtotal' => $subtotal,
                    'discount' => $discount,
                    'coupon_code' => $data['coupon_code'] ?? null,
                    'shipping_charge' => $shippingCharge,
                    'tax' => $tax,
                    'grand_total' => $grandTotal,
                    'paid_amount' => $paidAmount,
                    'due_amount' => $dueAmount,
                    'payment_method' => $data['payment_method'] ?? 'cash_on_delivery',
                    'payment_status' => 'pending',
                    'shipping_name' => ! empty($name) ? $name : 'Guest Customer',
                    'shipping_phone' => ! empty($phone) ? $phone : '01700000000',
                    'shipping_email' => $data['shipping_email'] ?? null,
                    'shipping_address' => $data['shipping_address'] ?? 'Draft Address',
                    'shipping_city' => $data['shipping_city'] ?? 'Dhaka',
                    'ip_address' => request()->ip(),
                    'customer_note' => $data['notes'] ?? null,
                ]);

                foreach ($processedItems as $pItem) {
                    $order->items()->create($pItem);
                }

                $order->statusLogs()->create([
                    'from_status' => null,
                    'to_status' => 'incomplete',
                    'note' => 'Incomplete checkout recorded (Drop-off draft)',
                    'changed_by' => auth()->id(),
                ]);
            }

            session(['incomplete_order_id' => $order->id]);

            return $order;
        });
    }

    /**
     * Create order from checkout or POS
     */
    public function createOrder(array $data, array $items): Order
    {
        $order = DB::transaction(function () use ($data, $items) {
            // Find or create customer
            $customer = null;
            if (! empty($data['shipping_phone'])) {
                $customer = Customer::firstOrCreate(
                    ['phone' => $data['shipping_phone']],
                    [
                        'name' => $data['shipping_name'] ?? 'Walk-in Customer',
                        'email' => $data['shipping_email'] ?? null,
                        'address' => $data['shipping_address'] ?? null,
                        'city' => $data['shipping_city'] ?? 'Dhaka',
                    ]
                );
            }

            // Run Fraud Check
            $fraudResult = $this->fraudCheckService->evaluateOrder(
                $data['shipping_phone'] ?? '',
                $data['ip_address'] ?? request()->ip()
            );

            // Compute subtotal and item prices
            $subtotal = 0;
            $processedItems = [];

            foreach ($items as $item) {
                $product = Product::findOrFail($item['product_id']);
                $variant = ! empty($item['variant_id']) ? ProductVariant::find($item['variant_id']) : null;

                $unitCost = $variant ? $variant->purchase_price : $product->purchase_price;
                $unitPrice = $variant ? $variant->effective_price : $product->effective_price;
                $qty = (int) $item['quantity'];
                $itemSubtotal = $unitPrice * $qty;
                $subtotal += $itemSubtotal;

                $processedItems[] = [
                    'product_id' => $product->id,
                    'variant_id' => $variant?->id,
                    'product_name' => $product->name,
                    'variant_name' => $variant?->variant_name,
                    'sku' => $variant ? $variant->sku : $product->sku,
                    'unit_cost' => $unitCost,
                    'unit_price' => $unitPrice,
                    'quantity' => $qty,
                    'subtotal' => $itemSubtotal,
                ];

                // Deduct stock
                $this->inventoryService->deductStock($product->id, $variant?->id, $qty);
            }

            // Calculate coupon discount
            $discount = 0;
            if (! empty($data['coupon_code'])) {
                $coupon = Coupon::where('code', $data['coupon_code'])->first();
                if ($coupon) {
                    $discount = $coupon->calculateDiscount($subtotal);
                    $coupon->increment('times_used');
                }
            } elseif (! empty($data['discount'])) {
                $discount = (float) $data['discount'];
            }

            $shippingCharge = (float) ($data['shipping_charge'] ?? 0);
            $tax = (float) ($data['tax'] ?? 0);
            $grandTotal = max(0, ($subtotal - $discount) + $shippingCharge + $tax);
            $paidAmount = (float) ($data['paid_amount'] ?? 0);
            $dueAmount = max(0, $grandTotal - $paidAmount);

            // Check if upgrading an existing incomplete order from this session
            $existingOrderId = session('incomplete_order_id');
            $existingIncompleteOrder = null;
            if ($existingOrderId) {
                $existingIncompleteOrder = Order::where('id', $existingOrderId)->where('status', 'incomplete')->first();
            }

            if ($existingIncompleteOrder) {
                $order = $existingIncompleteOrder;
                $order->update([
                    'customer_id' => $customer?->id ?? $order->customer_id,
                    'user_id' => $data['user_id'] ?? auth()->id(),
                    'order_type' => $data['order_type'] ?? 'online',
                    'status' => $data['status'] ?? 'pending',
                    'subtotal' => $subtotal,
                    'discount' => $discount,
                    'coupon_code' => $data['coupon_code'] ?? null,
                    'shipping_charge' => $shippingCharge,
                    'tax' => $tax,
                    'grand_total' => $grandTotal,
                    'paid_amount' => $paidAmount,
                    'due_amount' => $dueAmount,
                    'payment_method' => $data['payment_method'] ?? 'cash_on_delivery',
                    'payment_status' => $paidAmount >= $grandTotal ? 'paid' : ($paidAmount > 0 ? 'partially_paid' : 'pending'),
                    'account_id' => $data['account_id'] ?? null,
                    'shipping_name' => $data['shipping_name'] ?? 'Guest Customer',
                    'shipping_phone' => $data['shipping_phone'] ?? '01700000000',
                    'shipping_email' => $data['shipping_email'] ?? null,
                    'shipping_address' => $data['shipping_address'] ?? 'Counter Sale',
                    'shipping_city' => $data['shipping_city'] ?? 'Dhaka',
                    'is_fraud_suspect' => $fraudResult['is_fraud_suspect'],
                    'fraud_score' => $fraudResult['score'],
                    'fraud_reason' => $fraudResult['reason'],
                    'ip_address' => request()->ip(),
                    'admin_note' => $data['admin_note'] ?? null,
                    'customer_note' => $data['customer_note'] ?? null,
                    'processed_by' => auth()->id(),
                ]);

                // Replace items
                $order->items()->delete();
                foreach ($processedItems as $pItem) {
                    $order->items()->create($pItem);
                }

                $order->statusLogs()->create([
                    'from_status' => 'incomplete',
                    'to_status' => $order->status,
                    'note' => 'Order completed via '.strtoupper($order->order_type).' checkout',
                    'changed_by' => auth()->id(),
                ]);
            } else {
                // Generate collision-proof unique order ID for today (e.g. DPCB-20260910-5421)
                $prefix = 'DPCB-'.date('Ymd').'-';
                do {
                    $orderNo = $prefix.str_pad((string) random_int(1001, 9999), 4, '0', STR_PAD_LEFT);
                } while (Order::where('order_no', $orderNo)->exists());

                $order = Order::create([
                    'order_no' => $orderNo,
                    'customer_id' => $customer?->id,
                    'user_id' => $data['user_id'] ?? auth()->id(),
                    'order_type' => $data['order_type'] ?? 'online',
                    'status' => $data['status'] ?? 'pending',
                    'subtotal' => $subtotal,
                    'discount' => $discount,
                    'coupon_code' => $data['coupon_code'] ?? null,
                    'shipping_charge' => $shippingCharge,
                    'tax' => $tax,
                    'grand_total' => $grandTotal,
                    'paid_amount' => $paidAmount,
                    'due_amount' => $dueAmount,
                    'payment_method' => $data['payment_method'] ?? 'cash_on_delivery',
                    'payment_status' => $paidAmount >= $grandTotal ? 'paid' : ($paidAmount > 0 ? 'partially_paid' : 'pending'),
                    'account_id' => $data['account_id'] ?? null,
                    'shipping_name' => $data['shipping_name'] ?? 'Guest Customer',
                    'shipping_phone' => $data['shipping_phone'] ?? '01700000000',
                    'shipping_email' => $data['shipping_email'] ?? null,
                    'shipping_address' => $data['shipping_address'] ?? 'Counter Sale',
                    'shipping_city' => $data['shipping_city'] ?? 'Dhaka',
                    'is_fraud_suspect' => $fraudResult['is_fraud_suspect'],
                    'fraud_score' => $fraudResult['score'],
                    'fraud_reason' => $fraudResult['reason'],
                    'ip_address' => request()->ip(),
                    'admin_note' => $data['admin_note'] ?? null,
                    'customer_note' => $data['customer_note'] ?? null,
                    'processed_by' => auth()->id(),
                ]);

                // Save order items
                foreach ($processedItems as $pItem) {
                    $order->items()->create($pItem);
                }

                // Create initial status log
                $order->statusLogs()->create([
                    'from_status' => null,
                    'to_status' => $order->status,
                    'note' => 'Order created via '.strtoupper($order->order_type),
                    'changed_by' => auth()->id(),
                ]);
            }

            session()->forget('incomplete_order_id');

            // Sync and issue product warranties safely
            try {
                $this->warrantyService->syncOrderWarranties($order);
            } catch (\Throwable $e) {
                Log::warning('Warranty sync skipped for #'.$order->order_no.': '.$e->getMessage());
            }

            // Update customer CRM metrics
            if ($customer) {
                try {
                    $customer->recalculateMetrics();
                } catch (\Throwable $e) {
                    // pass
                }
            }

            return $order;
        });

        // Post-transaction notifications (safe from rollback)
        try {
            if ($order->customer && $order->shipping_phone) {
                $this->smsService->send(
                    $order->shipping_phone,
                    'Dear {customer_name}, your order #{order_id} of TK {grand_total} is confirmed! DREAMERS PCB.',
                    [
                        'customer_name' => $order->shipping_name,
                        'order_id' => $order->order_no,
                        'grand_total' => $order->grand_total,
                    ]
                );
            }
        } catch (\Throwable $e) {
            Log::warning('SMS notification skipped for #'.$order->order_no.': '.$e->getMessage());
        }

        try {
            $this->adminNotificationService->notifyNewOrder($order);
        } catch (\Throwable $e) {
            Log::warning('Admin notification skipped for #'.$order->order_no.': '.$e->getMessage());
        }

        try {
            OrderEmailService::sendOrderPlacedNotifications($order);
        } catch (\Throwable $e) {
            Log::warning('Order email skipped for #'.$order->order_no.': '.$e->getMessage());
        }

        return $order;
    }
}
