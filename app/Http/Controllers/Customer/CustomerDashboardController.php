<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class CustomerDashboardController extends Controller
{
    /**
     * Customer Dashboard Overview
     */
    public function index()
    {
        $customer = Auth::guard('customer')->user();
        $customer->recalculateMetrics();

        // Customer's Recent Orders
        $recentOrders = Order::where(function ($q) use ($customer) {
            $q->where('customer_id', $customer->id)
                ->orWhere('shipping_phone', $customer->phone);
        })
            ->with(['items.product'])
            ->latest()
            ->take(5)
            ->get();

        // Metric Counters
        $totalOrders = Order::where(function ($q) use ($customer) {
            $q->where('customer_id', $customer->id)->orWhere('shipping_phone', $customer->phone);
        })->count();

        $inTransitOrders = Order::where(function ($q) use ($customer) {
            $q->where('customer_id', $customer->id)->orWhere('shipping_phone', $customer->phone);
        })->whereIn('status', ['shipped', 'in_transit'])->count();

        $deliveredOrders = Order::where(function ($q) use ($customer) {
            $q->where('customer_id', $customer->id)->orWhere('shipping_phone', $customer->phone);
        })->where('status', 'completed')->count();

        $totalSpent = Order::where(function ($q) use ($customer) {
            $q->where('customer_id', $customer->id)->orWhere('shipping_phone', $customer->phone);
        })->where('payment_status', 'paid')->sum('grand_total');

        return view('customer.dashboard', compact(
            'customer',
            'recentOrders',
            'totalOrders',
            'inTransitOrders',
            'deliveredOrders',
            'totalSpent'
        ));
    }

    /**
     * Customer Order History
     */
    public function orders(Request $request)
    {
        $customer = Auth::guard('customer')->user();
        $status = $request->get('status', 'all');

        $query = Order::where(function ($q) use ($customer) {
            $q->where('customer_id', $customer->id)
                ->orWhere('shipping_phone', $customer->phone);
        })
            ->with(['items.product', 'consignment']);

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $orders = $query->latest()->paginate(10)->withQueryString();

        return view('customer.orders.index', compact('customer', 'orders', 'status'));
    }

    /**
     * Single Order Details & Live Tracking View
     */
    public function orderDetail($orderNo)
    {
        $customer = Auth::guard('customer')->user();

        $order = Order::where('order_no', $orderNo)
            ->where(function ($q) use ($customer) {
                $q->where('customer_id', $customer->id)
                    ->orWhere('shipping_phone', $customer->phone);
            })
            ->with(['items.product', 'statusLogs', 'consignment'])
            ->firstOrFail();

        return view('customer.orders.show', compact('customer', 'order'));
    }

    /**
     * Customer Profile & Address Book
     */
    public function profile()
    {
        $customer = Auth::guard('customer')->user();

        return view('customer.profile', compact('customer'));
    }

    /**
     * Update Profile Information
     */
    public function updateProfile(Request $request)
    {
        $customer = Auth::guard('customer')->user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255|unique:customers,email,'.$customer->id,
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
        ]);

        $customer->update([
            'name' => $request->name,
            'email' => $request->email,
            'address' => $request->address,
            'city' => $request->city,
            'postal_code' => $request->postal_code,
        ]);

        return redirect()->back()->with('success', 'Profile updated successfully.');
    }

    /**
     * Update Customer Account Password
     */
    public function updatePassword(Request $request)
    {
        $customer = Auth::guard('customer')->user();

        $request->validate([
            'current_password' => 'required|string',
            'password' => 'required|string|min:6|confirmed',
        ]);

        if ($customer->password && ! Hash::check($request->current_password, $customer->password)) {
            return redirect()->back()->withErrors(['current_password' => 'Current password does not match our records.']);
        }

        $customer->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()->back()->with('success', 'Password updated successfully.');
    }

    /**
     * Customer Wishlist Page
     */
    public function wishlist()
    {
        $customer = Auth::guard('customer')->user();

        // Recommended/Wishlist products
        $wishlistProducts = Product::where('is_active', true)
            ->with(['category', 'brand'])
            ->latest()
            ->take(8)
            ->get();

        return view('customer.wishlist', compact('customer', 'wishlistProducts'));
    }
}
