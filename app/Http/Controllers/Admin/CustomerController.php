<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $query = Customer::withCount('orders')->latest();

        if ($search) {
            $query->where('name', 'like', "%{$search}%")
                ->orWhere('phone', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%");
        }

        $customers = $query->paginate(20)->withQueryString();

        return view('admin.customers.index', compact('customers', 'search'));
    }

    public function show(Customer $customer)
    {
        $customer->load(['orders.items.product']);

        return view('admin.customers.show', compact('customer'));
    }

    public function update(Request $request, Customer $customer)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:50',
            'loyalty_points' => 'nullable|integer|min:0',
        ]);

        $customer->update($request->only('name', 'phone', 'email', 'address', 'city', 'loyalty_points', 'notes', 'is_flagged_fraud', 'fraud_reason'));

        return redirect()->back()->with('success', 'Customer details updated successfully!');
    }
}
