<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\ApiSetting;
use App\Models\DeliveryMethod;
use App\Models\Order;
use App\Models\Setting;
use App\Services\OrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class CheckoutController extends Controller
{
    protected OrderService $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    /**
     * One-Page Checkout Screen
     */
    public function index()
    {
        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return redirect()->route('shop.index')->with('warning', 'Your shopping cart is empty.');
        }

        $coupon = session()->get('coupon', null);

        $subtotal = array_sum(array_column($cart, 'subtotal'));
        $discount = $coupon ? ($coupon['calculated_discount'] ?? 0) : 0;

        $settings = Setting::pluck('value', 'key')->toArray();
        $insideDhaka = (float) ($settings['inside_dhaka_charge'] ?? 70);
        $outsideDhaka = (float) ($settings['outside_dhaka_charge'] ?? 130);
        $globalFreeThreshold = ! empty($settings['free_shipping_threshold']) ? (float) $settings['free_shipping_threshold'] : null;

        // Fetch active delivery methods from database safely
        $deliveryMethods = collect();
        if (Schema::hasTable('delivery_methods')) {
            try {
                $deliveryMethods = DeliveryMethod::where('is_active', true)->orderBy('sort_order')->orderBy('id')->get();
            } catch (\Throwable $e) {
                $deliveryMethods = collect();
            }
        }

        if ($deliveryMethods->isEmpty()) {
            $deliveryMethods = collect([
                new DeliveryMethod([
                    'name' => 'Inside Dhaka',
                    'code' => 'inside_dhaka',
                    'charge' => $insideDhaka,
                    'estimated_days' => $settings['inside_dhaka_estimate'] ?? '1-2 Days',
                    'is_active' => true,
                    'is_default' => true,
                ]),
                new DeliveryMethod([
                    'name' => 'Outside Dhaka',
                    'code' => 'outside_dhaka',
                    'charge' => $outsideDhaka,
                    'estimated_days' => $settings['outside_dhaka_estimate'] ?? '2-4 Days',
                    'is_active' => true,
                    'is_default' => false,
                ]),
            ]);
        }

        $defaultMethod = $deliveryMethods->firstWhere('is_default', true) ?? $deliveryMethods->first();

        $bkashSetting = ApiSetting::where('provider', 'bkash')->first();
        $bkashActive = (bool) ($bkashSetting?->is_active ?? false);

        return view('frontend.checkout', compact(
            'cart',
            'coupon',
            'subtotal',
            'discount',
            'insideDhaka',
            'outsideDhaka',
            'deliveryMethods',
            'defaultMethod',
            'globalFreeThreshold',
            'bkashActive'
        ));
    }

    /**
     * Process Checkout & Place Order
     */
    public function process(Request $request)
    {
        $request->validate([
            'shipping_name' => 'required|string|max:255',
            'shipping_phone' => 'required|string|max:20',
            'shipping_address' => 'required|string|max:1000',
            'shipping_city' => 'required|string|max:100',
            'shipping_area' => 'required|string|max:100',
            'payment_method' => 'required|in:cash_on_delivery,bkash,nagad,bank_transfer',
            'notes' => 'nullable|string|max:500',
        ]);

        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return redirect()->route('shop.index')->with('warning', 'Your cart is empty.');
        }

        $coupon = session()->get('coupon', null);
        $discount = $coupon ? ($coupon['calculated_discount'] ?? 0) : 0;
        $subtotal = array_sum(array_column($cart, 'subtotal'));

        $settings = Setting::pluck('value', 'key')->toArray();
        $globalFreeThreshold = ! empty($settings['free_shipping_threshold']) ? (float) $settings['free_shipping_threshold'] : null;

        // Calculate dynamic delivery charge safely
        $selectedMethod = null;
        if (Schema::hasTable('delivery_methods')) {
            try {
                $selectedMethod = DeliveryMethod::where('code', $request->shipping_area)->where('is_active', true)->first();
            } catch (\Throwable $e) {
                $selectedMethod = null;
            }
        }

        if ($globalFreeThreshold && $subtotal >= $globalFreeThreshold) {
            $shippingCharge = 0.0;
        } elseif ($selectedMethod) {
            $shippingCharge = $selectedMethod->getEffectiveCharge($subtotal);
        } else {
            $shippingCharge = $request->shipping_area === 'inside_dhaka'
                ? (float) ($settings['inside_dhaka_charge'] ?? 70)
                : (float) ($settings['outside_dhaka_charge'] ?? 130);
        }

        // Format cart items for OrderService
        $cartItems = [];
        foreach ($cart as $item) {
            $cartItems[] = [
                'product_id' => $item['product_id'],
                'variant_id' => $item['variant_id'] ?? null,
                'quantity' => $item['quantity'],
            ];
        }

        $orderData = [
            'customer_name' => $request->shipping_name,
            'customer_phone' => $request->shipping_phone,
            'shipping_name' => $request->shipping_name,
            'shipping_phone' => $request->shipping_phone,
            'shipping_address' => $request->shipping_address,
            'shipping_city' => $request->shipping_city,
            'order_type' => 'online',
            'payment_method' => $request->payment_method,
            'shipping_charge' => $shippingCharge,
            'discount' => $discount,
            'coupon_code' => $coupon ? $coupon['code'] : null,
            'paid_amount' => 0, // COD initially 0
            'order_notes' => $request->notes,
            'cart' => $cartItems,
        ];

        try {
            $order = $this->orderService->createOrder($orderData, $cartItems);

            // Clear session cart & coupon
            session()->forget('cart');
            session()->forget('coupon');

            return redirect()->route('checkout.success', $order->order_no);
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Checkout failed: '.$e->getMessage());
        }
    }

    /**
     * Order Confirmation Screen
     */
    public function success($orderNo)
    {
        $order = Order::where('order_no', $orderNo)->with(['items.product', 'statusLogs'])->firstOrFail();

        return view('frontend.order_success', compact('order'));
    }
}
