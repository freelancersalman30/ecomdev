<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function index()
    {
        $suppliers = Supplier::withCount('purchases')->latest()->get();
        $totalPayable = Supplier::sum('current_due');

        return view('admin.suppliers.index', compact('suppliers', 'totalPayable'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:50',
            'opening_balance' => 'nullable|numeric|min:0',
        ]);

        $supplier = Supplier::create([
            'name' => $request->name,
            'company' => $request->company,
            'phone' => $request->phone,
            'email' => $request->email,
            'address' => $request->address,
            'opening_balance' => $request->opening_balance ?? 0,
            'current_due' => $request->opening_balance ?? 0,
            'notes' => $request->notes,
            'is_active' => true,
        ]);

        return redirect()->back()->with('success', 'Supplier created successfully!');
    }

    public function show(Supplier $supplier)
    {
        $supplier->load(['purchases.items.product', 'payments']);

        return view('admin.suppliers.show', compact('supplier'));
    }

    public function storePayment(Request $request, Supplier $supplier)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
            'payment_date' => 'required|date',
            'payment_method' => 'required|string',
        ]);

        $supplier->payments()->create([
            'payment_date' => $request->payment_date,
            'amount' => $request->amount,
            'payment_method' => $request->payment_method,
            'reference_no' => $request->reference_no,
            'notes' => $request->notes,
            'created_by' => auth()->id(),
        ]);

        $supplier->recalculateDue();

        return redirect()->back()->with('success', 'Payment of TK '.number_format($request->amount, 2).' recorded for '.$supplier->name);
    }
}
