<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $query = Customer::withCount('orders')->latest();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('city', 'like', "%{$search}%");
            });
        }

        $customers = $query->paginate(20)->withQueryString();

        return view('admin.customers.index', compact('customers', 'search'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:50|unique:customers,phone',
            'email' => 'nullable|email|max:255|unique:customers,email',
            'password' => 'nullable|string|min:6',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'loyalty_points' => 'nullable|integer|min:0',
            'notes' => 'nullable|string',
            'is_flagged_fraud' => 'nullable|boolean',
            'fraud_reason' => 'nullable|string|max:255',
            'is_active' => 'nullable|boolean',
        ]);

        $data = [
            'name' => $validated['name'],
            'phone' => $validated['phone'],
            'email' => $validated['email'] ?? null,
            'address' => $validated['address'] ?? null,
            'city' => $validated['city'] ?? 'Dhaka',
            'postal_code' => $validated['postal_code'] ?? null,
            'loyalty_points' => $validated['loyalty_points'] ?? 0,
            'notes' => $validated['notes'] ?? null,
            'is_flagged_fraud' => $request->boolean('is_flagged_fraud'),
            'fraud_reason' => $request->boolean('is_flagged_fraud') ? ($validated['fraud_reason'] ?? null) : null,
            'is_active' => $request->has('is_active') ? $request->boolean('is_active') : true,
            'delivery_success_rate' => 100.00,
        ];

        if (! empty($validated['password'])) {
            $data['password'] = Hash::make($validated['password']);
        }

        Customer::create($data);

        return redirect()->route('admin.customers.index')->with('success', 'Customer created successfully!');
    }

    public function show(Customer $customer)
    {
        $customer->load(['orders.items.product']);

        return view('admin.customers.show', compact('customer'));
    }

    public function update(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:50|unique:customers,phone,'.$customer->id,
            'email' => 'nullable|email|max:255|unique:customers,email,'.$customer->id,
            'password' => 'nullable|string|min:6',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'loyalty_points' => 'nullable|integer|min:0',
            'notes' => 'nullable|string',
            'is_flagged_fraud' => 'nullable|boolean',
            'fraud_reason' => 'nullable|string|max:255',
            'is_active' => 'nullable|boolean',
        ]);

        $data = [
            'name' => $validated['name'],
            'phone' => $validated['phone'],
            'email' => $validated['email'] ?? null,
            'address' => $validated['address'] ?? null,
            'city' => $validated['city'] ?? $customer->city,
            'postal_code' => $validated['postal_code'] ?? null,
            'loyalty_points' => $validated['loyalty_points'] ?? 0,
            'notes' => $validated['notes'] ?? null,
            'is_flagged_fraud' => $request->boolean('is_flagged_fraud'),
            'fraud_reason' => $request->boolean('is_flagged_fraud') ? ($validated['fraud_reason'] ?? null) : null,
            'is_active' => $request->has('is_active') ? $request->boolean('is_active') : true,
        ];

        if (! empty($validated['password'])) {
            $data['password'] = Hash::make($validated['password']);
        }

        $customer->update($data);

        return redirect()->back()->with('success', 'Customer details updated successfully!');
    }

    public function destroy(Customer $customer)
    {
        $customerName = $customer->name;
        $customer->delete();

        return redirect()->route('admin.customers.index')->with('success', "Customer '{$customerName}' deleted successfully!");
    }
}
