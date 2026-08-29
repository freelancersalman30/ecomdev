<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\Request;

class CartController extends Controller
{
    /**
     * View Cart Page
     */
    public function index()
    {
        $cart = session()->get('cart', []);
        $coupon = session()->get('coupon', null);

        $summary = $this->calculateSummary($cart, $coupon);

        return view('frontend.cart', compact('cart', 'coupon', 'summary'));
    }

    /**
     * Get Live Cart Data JSON (for Alpine.js drawer / top header badge)
     */
    public function getJson()
    {
        $cart = session()->get('cart', []);
        $coupon = session()->get('coupon', null);
        $summary = $this->calculateSummary($cart, $coupon);

        return response()->json([
            'success' => true,
            'items' => array_values($cart),
            'count' => count($cart),
            'summary' => $summary,
            'coupon' => $coupon
        ]);
    }

    /**
     * Add Item to Cart
     */
    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'variant_id' => 'nullable|exists:product_variants,id',
            'quantity' => 'nullable|integer|min:1'
        ]);

        $product = Product::findOrFail($request->product_id);
        $variant = $request->variant_id ? ProductVariant::find($request->variant_id) : null;
        $quantity = (int) ($request->quantity ?? 1);

        $cart = session()->get('cart', []);

        $cartKey = $variant ? "{$product->id}_{$variant->id}" : "{$product->id}_0";

        $price = $variant ? (float) $variant->selling_price : (float) $product->effective_price;
        $name = $product->name;
        $variantName = $variant ? $variant->variant_name : null;
        $sku = $variant ? $variant->sku : $product->sku;
        $thumbnail = $product->thumbnail;

        if (isset($cart[$cartKey])) {
            $cart[$cartKey]['quantity'] += $quantity;
            $cart[$cartKey]['subtotal'] = $cart[$cartKey]['quantity'] * $cart[$cartKey]['price'];
        } else {
            $cart[$cartKey] = [
                'cart_key' => $cartKey,
                'product_id' => $product->id,
                'variant_id' => $variant ? $variant->id : null,
                'name' => $name,
                'variant_name' => $variantName,
                'sku' => $sku,
                'thumbnail' => $thumbnail,
                'price' => $price,
                'quantity' => $quantity,
                'subtotal' => $price * $quantity,
                'slug' => $product->slug,
            ];
        }

        session()->put('cart', $cart);

        if ($request->wantsJson()) {
            return $this->getJson();
        }

        return redirect()->back()->with('success', "{$product->name} added to your cart!");
    }

    /**
     * Update Quantity
     */
    public function update(Request $request)
    {
        $cartKey = $request->cart_key;
        $quantity = (int) $request->quantity;

        $cart = session()->get('cart', []);

        if (isset($cart[$cartKey])) {
            if ($quantity <= 0) {
                unset($cart[$cartKey]);
            } else {
                $cart[$cartKey]['quantity'] = $quantity;
                $cart[$cartKey]['subtotal'] = $cart[$cartKey]['quantity'] * $cart[$cartKey]['price'];
            }
            session()->put('cart', $cart);
        }

        if ($request->wantsJson()) {
            return $this->getJson();
        }

        return redirect()->back()->with('success', 'Cart updated successfully.');
    }

    /**
     * Remove Item
     */
    public function remove(Request $request)
    {
        $cartKey = $request->cart_key;
        $cart = session()->get('cart', []);

        if (isset($cart[$cartKey])) {
            unset($cart[$cartKey]);
            session()->put('cart', $cart);
        }

        if ($request->wantsJson()) {
            return $this->getJson();
        }

        return redirect()->back()->with('success', 'Item removed from cart.');
    }

    /**
     * Apply Promo Coupon Code
     */
    public function applyCoupon(Request $request)
    {
        $request->validate(['code' => 'required|string']);

        $code = strtoupper(trim($request->code));
        $coupon = Coupon::where('code', $code)->where('is_active', true)->first();

        if (!$coupon) {
            return response()->json(['success' => false, 'message' => 'Invalid or expired coupon code.'], 422);
        }

        $cart = session()->get('cart', []);
        $subtotal = array_sum(array_column($cart, 'subtotal'));

        if ($coupon->min_order_amount && $subtotal < $coupon->min_order_amount) {
            return response()->json([
                'success' => false, 
                'message' => "Minimum order of ৳{$coupon->min_order_amount} required for this coupon."
            ], 422);
        }

        $discount = 0;
        if ($coupon->discount_type === 'percentage') {
            $discount = ($subtotal * $coupon->discount_value) / 100;
            if ($coupon->max_discount_amount) {
                $discount = min($discount, (float) $coupon->max_discount_amount);
            }
        } else {
            $discount = min((float) $coupon->discount_value, $subtotal);
        }

        $couponData = [
            'code' => $coupon->code,
            'discount_type' => $coupon->discount_type,
            'discount_value' => $coupon->discount_value,
            'calculated_discount' => round($discount, 2)
        ];

        session()->put('coupon', $couponData);

        return response()->json([
            'success' => true,
            'message' => "Coupon {$coupon->code} applied successfully!",
            'coupon' => $couponData,
            'summary' => $this->calculateSummary($cart, $couponData)
        ]);
    }

    /**
     * Remove Coupon
     */
    public function removeCoupon()
    {
        session()->forget('coupon');
        return response()->json(['success' => true, 'message' => 'Coupon removed.']);
    }

    private function calculateSummary(array $cart, ?array $coupon): array
    {
        $subtotal = 0;
        $itemCount = 0;

        foreach ($cart as $item) {
            $subtotal += $item['subtotal'];
            $itemCount += $item['quantity'];
        }

        $discount = $coupon ? ($coupon['calculated_discount'] ?? 0) : 0;
        $payable = max(0, $subtotal - $discount);

        return [
            'subtotal' => round($subtotal, 2),
            'item_count' => $itemCount,
            'discount' => round($discount, 2),
            'payable' => round($payable, 2)
        ];
    }
}
