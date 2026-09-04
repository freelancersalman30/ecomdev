<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use App\Models\Customer;
use App\Models\DeliveryMethod;
use App\Models\Order;
use App\Models\OrderStatusLog;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Setting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class CartCheckoutController extends Controller
{
    /**
     * Validate Cart items stock and latest prices.
     */
    public function validateCart(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.variant_id' => 'nullable|exists:product_variants,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid cart payload',
                'errors' => $validator->errors(),
            ], 422);
        }

        $validatedItems = [];
        $subtotal = 0;
        $hasErrors = false;

        foreach ($request->items as $itemData) {
            $product = Product::find($itemData['product_id']);
            $variant = ! empty($itemData['variant_id']) ? ProductVariant::find($itemData['variant_id']) : null;

            if (! $product || ! $product->is_active) {
                $hasErrors = true;
                $validatedItems[] = [
                    'product_id' => $itemData['product_id'],
                    'available' => false,
                    'error_message' => 'Product is no longer available.',
                ];

                continue;
            }

            $unitPrice = $variant ? (float) $variant->effective_price : (float) $product->effective_price;
            $availableStock = $variant ? (int) $variant->stock_quantity : (int) $product->stock_quantity;
            $qty = (int) $itemData['quantity'];

            $isStockAvailable = $availableStock >= $qty;
            if (! $isStockAvailable) {
                $hasErrors = true;
            }

            $lineTotal = $unitPrice * $qty;
            $subtotal += $lineTotal;

            $validatedItems[] = [
                'product_id' => $product->id,
                'variant_id' => $variant?->id,
                'name' => $product->name,
                'variant_name' => $variant?->variant_name,
                'thumbnail' => $variant?->image ? asset('storage/'.$variant->image) : ($product->thumbnail ? asset('storage/'.$product->thumbnail) : null),
                'unit_price' => $unitPrice,
                'quantity' => $qty,
                'available_stock' => $availableStock,
                'is_stock_available' => $isStockAvailable,
                'line_total' => $lineTotal,
            ];
        }

        return response()->json([
            'success' => ! $hasErrors,
            'subtotal' => $subtotal,
            'items' => $validatedItems,
        ]);
    }

    /**
     * Get Delivery Methods & Calculate Shipping Charges.
     */
    public function deliveryMethods(Request $request): JsonResponse
    {
        $subtotal = (float) $request->get('subtotal', 0);

        $methods = collect();
        if (Schema::hasTable('delivery_methods')) {
            $methods = DeliveryMethod::where('is_active', true)
                ->orderBy('sort_order')
                ->orderBy('id')
                ->get();
        }

        // Fallback default zones if table empty
        if ($methods->isEmpty()) {
            $insideCharge = (float) Setting::get('inside_dhaka_charge', 60);
            $outsideCharge = (float) Setting::get('outside_dhaka_charge', 120);

            $methods = collect([
                (object) [
                    'id' => 1,
                    'name' => 'Inside Dhaka',
                    'code' => 'inside_dhaka',
                    'charge' => $insideCharge,
                    'estimated_days' => Setting::get('inside_dhaka_estimate', '24-48 Hours'),
                    'min_order_for_free_delivery' => null,
                    'is_default' => true,
                ],
                (object) [
                    'id' => 2,
                    'name' => 'Outside Dhaka',
                    'code' => 'outside_dhaka',
                    'charge' => $outsideCharge,
                    'estimated_days' => Setting::get('outside_dhaka_estimate', '2-4 Days'),
                    'min_order_for_free_delivery' => null,
                    'is_default' => false,
                ],
            ]);
        }

        $globalFreeThreshold = Setting::get('free_shipping_threshold');
        $globalFreeThreshold = is_numeric($globalFreeThreshold) ? (float) $globalFreeThreshold : null;

        $formatted = $methods->map(function ($method) use ($subtotal, $globalFreeThreshold) {
            $charge = (float) $method->charge;

            // Check free delivery threshold
            $isFree = false;
            if ($globalFreeThreshold && $subtotal >= $globalFreeThreshold) {
                $charge = 0;
                $isFree = true;
            } elseif (! empty($method->min_order_for_free_delivery) && $subtotal >= (float) $method->min_order_for_free_delivery) {
                $charge = 0;
                $isFree = true;
            }

            return [
                'id' => $method->id,
                'name' => $method->name,
                'code' => $method->code,
                'base_charge' => (float) $method->charge,
                'effective_charge' => $charge,
                'is_free_delivery' => $isFree,
                'estimated_days' => $method->estimated_days,
                'min_order_for_free_delivery' => $method->min_order_for_free_delivery ? (float) $method->min_order_for_free_delivery : null,
                'description' => $method->description ?? null,
                'is_default' => (bool) $method->is_default,
            ];
        });

        return response()->json([
            'success' => true,
            'global_free_shipping_threshold' => $globalFreeThreshold,
            'data' => $formatted,
        ]);
    }

    /**
     * Apply Promo Coupon Code API.
     */
    public function applyCoupon(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required|string|max:50',
            'subtotal' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Coupon code is required.',
            ], 422);
        }

        $subtotal = (float) $request->subtotal;
        $code = trim($request->code);

        $coupon = Coupon::where('code', $code)->where('is_active', true)->first();

        if (! $coupon) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired coupon code.',
            ], 404);
        }

        // Check start and expiry date
        if ($coupon->start_date && now()->lt($coupon->start_date)) {
            return response()->json([
                'success' => false,
                'message' => 'This coupon campaign has not started yet.',
            ], 422);
        }

        if ($coupon->end_date && now()->gt($coupon->end_date)) {
            return response()->json([
                'success' => false,
                'message' => 'This coupon code has expired.',
            ], 422);
        }

        // Check usage limits
        if ($coupon->usage_limit && $coupon->used_count >= $coupon->usage_limit) {
            return response()->json([
                'success' => false,
                'message' => 'Coupon usage limit has been reached.',
            ], 422);
        }

        // Check minimum order amount
        if ($coupon->min_order_amount && $subtotal < (float) $coupon->min_order_amount) {
            return response()->json([
                'success' => false,
                'message' => 'Minimum order amount for this coupon is ৳'.number_format($coupon->min_order_amount, 2),
            ], 422);
        }

        // Calculate discount
        $discount = 0;
        if ($coupon->discount_type === 'percentage') {
            $discount = ($subtotal * (float) $coupon->discount_value) / 100;
            if ($coupon->max_discount_amount && $discount > (float) $coupon->max_discount_amount) {
                $discount = (float) $coupon->max_discount_amount;
            }
        } else {
            $discount = min((float) $coupon->discount_value, $subtotal);
        }

        return response()->json([
            'success' => true,
            'message' => 'Coupon applied successfully!',
            'data' => [
                'code' => $coupon->code,
                'discount_type' => $coupon->discount_type,
                'discount_value' => (float) $coupon->discount_value,
                'discount_amount' => round($discount, 2),
                'new_subtotal' => max(0, round($subtotal - $discount, 2)),
            ],
        ]);
    }

    /**
     * Submit Order from Mobile App.
     */
    public function placeOrder(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:30',
            'email' => 'nullable|email|max:255',
            'address' => 'required|string|max:500',
            'city' => 'nullable|string|max:100',
            'delivery_method_code' => 'nullable|string|max:100',
            'payment_method' => 'required|string|in:cod,online,bkash,nagad,rocket',
            'customer_note' => 'nullable|string|max:500',
            'coupon_code' => 'nullable|string|max:50',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.variant_id' => 'nullable|exists:product_variants,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        return DB::transaction(function () use ($request) {
            // Find or create customer
            $customer = $request->user();
            if (! $customer) {
                $customer = Customer::firstOrCreate(
                    ['phone' => $request->phone],
                    [
                        'name' => $request->name,
                        'email' => $request->email,
                        'address' => $request->address,
                        'city' => $request->city,
                        'is_active' => true,
                    ]
                );
            }

            // Calculate Subtotal & Validate Items
            $subtotal = 0;
            $orderItemsData = [];

            foreach ($request->items as $item) {
                $product = Product::lockForUpdate()->find($item['product_id']);
                $variant = ! empty($item['variant_id']) ? ProductVariant::lockForUpdate()->find($item['variant_id']) : null;

                if (! $product || ! $product->is_active) {
                    return response()->json([
                        'success' => false,
                        'message' => "Product '{$product?->name}' is no longer active.",
                    ], 422);
                }

                $qty = (int) $item['quantity'];
                $availableStock = $variant ? (int) $variant->stock_quantity : (int) $product->stock_quantity;

                if ($availableStock < $qty) {
                    return response()->json([
                        'success' => false,
                        'message' => "Insufficient stock for '{$product->name}'. Available: {$availableStock}",
                    ], 422);
                }

                $unitPrice = $variant ? (float) $variant->effective_price : (float) $product->effective_price;
                $unitCost = $variant ? (float) ($variant->purchase_price ?? 0) : (float) ($product->purchase_price ?? 0);
                $itemSubtotal = $unitPrice * $qty;
                $subtotal += $itemSubtotal;

                // Decrement stock
                if ($variant) {
                    $variant->decrement('stock_quantity', $qty);
                }
                $product->decrement('stock_quantity', $qty);
                $product->increment('sales_count', $qty);

                $orderItemsData[] = [
                    'product_id' => $product->id,
                    'variant_id' => $variant?->id,
                    'product_name' => $product->name,
                    'variant_name' => $variant?->variant_name,
                    'sku' => $variant?->sku ?? $product->sku,
                    'unit_cost' => $unitCost,
                    'unit_price' => $unitPrice,
                    'quantity' => $qty,
                    'subtotal' => $itemSubtotal,
                ];
            }

            // Calculate Coupon Discount
            $discount = 0;
            $appliedCoupon = null;
            if ($request->filled('coupon_code')) {
                $appliedCoupon = Coupon::where('code', trim($request->coupon_code))->where('is_active', true)->first();
                if ($appliedCoupon) {
                    if ($appliedCoupon->discount_type === 'percentage') {
                        $discount = ($subtotal * (float) $appliedCoupon->discount_value) / 100;
                        if ($appliedCoupon->max_discount_amount) {
                            $discount = min($discount, (float) $appliedCoupon->max_discount_amount);
                        }
                    } else {
                        $discount = min((float) $appliedCoupon->discount_value, $subtotal);
                    }
                    $appliedCoupon->increment('used_count');
                }
            }

            // Calculate Shipping Charge
            $deliveryMethod = null;
            if ($request->filled('delivery_method_code') && Schema::hasTable('delivery_methods')) {
                $deliveryMethod = DeliveryMethod::where('code', $request->delivery_method_code)->first();
            }

            if (! $deliveryMethod && Schema::hasTable('delivery_methods')) {
                $deliveryMethod = DeliveryMethod::where('is_default', true)->first() ?? DeliveryMethod::first();
            }

            $shippingCharge = $deliveryMethod ? $deliveryMethod->getEffectiveCharge($subtotal) : (float) Setting::get('inside_dhaka_charge', 60);

            // Check global free shipping threshold
            $globalFreeThreshold = Setting::get('free_shipping_threshold');
            if (is_numeric($globalFreeThreshold) && $subtotal >= (float) $globalFreeThreshold) {
                $shippingCharge = 0;
            }

            $grandTotal = max(0, ($subtotal - $discount) + $shippingCharge);
            $orderNo = 'ORD-'.strtoupper(Str::random(4)).'-'.mt_rand(1000, 9999);

            // Create Order
            $order = Order::create([
                'order_no' => $orderNo,
                'customer_id' => $customer->id,
                'order_type' => 'online',
                'status' => 'pending',
                'subtotal' => $subtotal,
                'discount' => $discount,
                'coupon_code' => $appliedCoupon?->code,
                'shipping_charge' => $shippingCharge,
                'tax' => 0,
                'grand_total' => $grandTotal,
                'paid_amount' => 0,
                'due_amount' => $grandTotal,
                'payment_method' => $request->payment_method,
                'payment_status' => 'pending',
                'shipping_name' => $request->name,
                'shipping_phone' => $request->phone,
                'shipping_email' => $request->email,
                'shipping_address' => $request->address,
                'shipping_city' => $request->city ?? ($deliveryMethod?->name ?? 'Dhaka'),
                'shipping_zone' => $deliveryMethod?->name ?? 'Standard Delivery',
                'courier_name' => Setting::get('default_courier_partner', 'steadfast'),
                'customer_note' => $request->customer_note,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            // Save Order Items
            foreach ($orderItemsData as $itemData) {
                $order->items()->create($itemData);
            }

            // Create Initial Order Status Log
            OrderStatusLog::create([
                'order_id' => $order->id,
                'from_status' => null,
                'to_status' => 'pending',
                'note' => 'Order placed via Mobile Application.',
            ]);

            // Recalculate customer metrics
            $customer->recalculateMetrics();

            return response()->json([
                'success' => true,
                'message' => 'Order placed successfully!',
                'data' => [
                    'order_id' => $order->id,
                    'order_no' => $order->order_no,
                    'status' => $order->status,
                    'grand_total' => (float) $order->grand_total,
                    'subtotal' => (float) $order->subtotal,
                    'discount' => (float) $order->discount,
                    'shipping_charge' => (float) $order->shipping_charge,
                    'payment_method' => $order->payment_method,
                    'payment_status' => $order->payment_status,
                    'created_at' => $order->created_at->toIso8601String(),
                ],
            ], 201);
        });
    }
}
