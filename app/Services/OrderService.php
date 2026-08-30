<?php

namespace App\Services;

use App\Models\Coupon;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\DB;

class OrderService
{
    protected InventoryService $inventoryService;

    protected FraudCheckService $fraudCheckService;

    protected SmsService $smsService;

    protected WarrantyService $warrantyService;

    public function __construct(
        InventoryService $inventoryService,
        FraudCheckService $fraudCheckService,
        SmsService $smsService,
        WarrantyService $warrantyService
    ) {
        $this->inventoryService = $inventoryService;
        $this->fraudCheckService = $fraudCheckService;
        $this->smsService = $smsService;
        $this->warrantyService = $warrantyService;
    }

    /**
     * Create order from checkout or POS
     */
    public function createOrder(array $data, array $items): Order
    {
        return DB::transaction(function () use ($data, $items) {
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

            // Generate sequential order ID
            $orderNo = 'DPCB-'.date('Ymd').'-'.str_pad((string) (Order::count() + 1), 4, '0', STR_PAD_LEFT);

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

            // Sync and issue product warranties
            $this->warrantyService->syncOrderWarranties($order);

            // Create initial status log
            $order->statusLogs()->create([
                'from_status' => null,
                'to_status' => $order->status,
                'note' => 'Order created via '.strtoupper($order->order_type),
                'changed_by' => auth()->id(),
            ]);

            // Update customer CRM metrics
            if ($customer) {
                $customer->recalculateMetrics();
            }

            // Dispatch SMS Notification
            if ($customer && $order->shipping_phone) {
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

            return $order;
        });
    }
}
