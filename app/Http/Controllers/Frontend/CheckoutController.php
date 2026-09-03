<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\ApiSetting;
use App\Models\Order;
use App\Models\Setting;
use App\Services\OrderService;
use Illuminate\Http\Request;

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

        $bkashSetting = ApiSetting::where('provider', 'bkash')->first();
        $bkashActive = (bool) ($bkashSetting?->is_active ?? false);

        return view('frontend.checkout', compact('cart', 'coupon', 'subtotal', 'discount', 'insideDhaka', 'outsideDhaka', 'bkashActive'));
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
            'shipping_area' => 'required|in:inside_dhaka,outside_dhaka',
            'payment_method' => 'required|in:cash_on_delivery,bkash,nagad,bank_transfer',
            'notes' => 'nullable|string|max:500',
        ]);

        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return redirect()->route('shop.index')->with('warning', 'Your cart is empty.');
        }

        $coupon = session()->get('coupon', null);
        $discount = $coupon ? ($coupon['calculated_discount'] ?? 0) : 0;

        $settings = Setting::pluck('value', 'key')->toArray();
        $shippingCharge = $request->shipping_area === 'inside_dhaka'
            ? (float) ($settings['inside_dhaka_charge'] ?? 70)
            : (float) ($settings['outside_dhaka_charge'] ?? 130);

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
