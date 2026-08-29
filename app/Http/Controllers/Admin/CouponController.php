<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    public function index()
    {
        $coupons = Coupon::latest()->get();
        return view('admin.coupons.index', compact('coupons'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|string|unique:coupons,code|max:50',
            'discount_type' => 'required|in:fixed,percentage',
            'discount_value' => 'required|numeric|min:0.1',
            'min_order_amount' => 'nullable|numeric|min:0',
        ]);

        Coupon::create([
            'code' => strtoupper(trim($request->code)),
            'discount_type' => $request->discount_type,
            'discount_value' => $request->discount_value,
            'min_order_amount' => $request->min_order_amount ?? 0,
            'max_discount_amount' => $request->max_discount_amount,
            'usage_limit' => $request->usage_limit,
            'usage_per_user' => $request->usage_per_user ?? 1,
            'starts_at' => $request->starts_at,
            'expires_at' => $request->expires_at,
            'is_active' => true,
        ]);

        return redirect()->back()->with('success', 'Coupon code generated successfully!');
    }

    public function destroy(Coupon $coupon)
    {
        $coupon->delete();
        return redirect()->back()->with('success', 'Coupon deleted!');
    }
}
