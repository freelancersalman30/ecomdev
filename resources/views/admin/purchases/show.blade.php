@extends('layouts.admin')

@section('title', 'Purchase PO ' . $purchase->purchase_no)
@section('page-title', 'Purchase Order: ' . $purchase->purchase_no)

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <!-- Top Action Bar -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl p-4 border border-slate-200 dark:border-slate-800 shadow-sm flex items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.purchases.index') }}" class="p-2 rounded-xl text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-800 transition">
                <i data-lucide="arrow-left" class="w-5 h-5"></i>
            </a>
            <div>
                <h2 class="text-base font-bold text-slate-900 dark:text-white code-font">{{ $purchase->purchase_no }}</h2>
                <div class="text-xs text-slate-500">Date: {{ $purchase->purchase_date->format('d M Y') }} | Created by: {{ $purchase->createdBy->name ?? 'Admin' }}</div>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <span class="px-3 py-1 rounded-full text-xs font-bold uppercase {{ $purchase->payment_status === 'paid' ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300' : ($purchase->payment_status === 'partial' ? 'bg-amber-100 text-amber-700 dark:bg-amber-950 dark:text-amber-300' : 'bg-rose-100 text-rose-700 dark:bg-rose-950 dark:text-rose-300') }}">
                {{ $purchase->payment_status }}
            </span>
            @if($purchase->due_amount > 0)
            <button onclick="document.getElementById('payPoModal').style.display = 'flex'" class="px-3.5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs shadow-md transition flex items-center gap-1.5">
                <i data-lucide="dollar-sign" class="w-4 h-4"></i>
                <span>Pay Due (৳{{ number_format($purchase->due_amount, 2) }})</span>
            </button>
            @endif
        </div>
    </div>

    <!-- Supplier & Invoice Details -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 border border-slate-200 dark:border-slate-800 shadow-sm grid grid-cols-1 sm:grid-cols-2 gap-6 text-xs">
        <div class="space-y-1">
            <span class="text-[10px] font-bold uppercase text-slate-400">Supplier / Vendor:</span>
            <div class="text-sm font-bold text-slate-900 dark:text-white">{{ $purchase->supplier->name }}</div>
            <div class="text-slate-500">{{ $purchase->supplier->company }}</div>
            <div class="font-mono text-emerald-600">{{ $purchase->supplier->phone }}</div>
            <div class="text-slate-400">{{ $purchase->supplier->address }}</div>
        </div>

        <div class="space-y-1 sm:text-right">
            <span class="text-[10px] font-bold uppercase text-slate-400">Invoice Information:</span>
            <div><strong>Supplier Ref:</strong> {{ $purchase->supplier_invoice_no ?? 'N/A' }}</div>
            <div><strong>Total Supplier Ledger Due:</strong> <span class="font-bold text-rose-500">৳{{ number_format($purchase->supplier->current_due, 2) }}</span></div>
            <div><strong>PO Due Remaining:</strong> <span class="font-bold {{ $purchase->due_amount > 0 ? 'text-rose-500' : 'text-emerald-500' }}">৳{{ number_format($purchase->due_amount, 2) }}</span></div>
        </div>
    </div>

    <!-- Items Received Table -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
        <div class="p-4 border-b border-slate-200 dark:border-slate-800 font-bold text-sm text-slate-900 dark:text-white">
            Received Components ({{ $purchase->items->count() }})
        </div>
        <table class="w-full text-left text-xs">
            <thead class="bg-slate-50 dark:bg-slate-800/50 text-slate-500 font-bold uppercase">
                <tr>
                    <th class="p-3">Component / Product</th>
                    <th class="p-3">Batch & Serials</th>
                    <th class="p-3 text-right">Unit Cost</th>
                    <th class="p-3 text-center">Qty</th>
                    <th class="p-3 text-right">Subtotal</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                @foreach($purchase->items as $item)
                <tr>
                    <td class="p-3">
                        <div class="font-bold text-slate-900 dark:text-white">{{ $item->product->name ?? 'Product' }}</div>
                        <div class="text-[10px] text-slate-400 font-mono">{{ $item->product->sku ?? '' }}</div>
                    </td>
                    <td class="p-3 font-mono text-[11px] text-slate-500">
                        {{ $item->batch_no ?? 'N/A' }}
                        @if($item->serial_numbers)
                        <div class="text-[10px] text-emerald-500">{{ implode(', ', $item->serial_numbers) }}</div>
                        @endif
                    </td>
                    <td class="p-3 text-right code-font">৳{{ number_format($item->unit_cost, 2) }}</td>
                    <td class="p-3 text-center font-bold">{{ $item->quantity }}</td>
                    <td class="p-3 text-right font-bold code-font">৳{{ number_format($item->subtotal, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="p-4 bg-slate-50 dark:bg-slate-950/60 border-t border-slate-200 dark:border-slate-800 flex justify-end">
            <div class="w-64 space-y-1.5 text-xs">
                <div class="flex justify-between">
                    <span class="text-slate-500">Subtotal:</span>
                    <span class="font-semibold code-font">৳{{ number_format($purchase->subtotal, 2) }}</span>
                </div>
                @if($purchase->discount > 0)
                <div class="flex justify-between text-amber-600">
                    <span>Discount:</span>
                    <span class="code-font">-৳{{ number_format($purchase->discount, 2) }}</span>
                </div>
                @endif
                @if($purchase->shipping_cost > 0)
                <div class="flex justify-between text-slate-500">
                    <span>Shipping:</span>
                    <span class="code-font">+৳{{ number_format($purchase->shipping_cost, 2) }}</span>
                </div>
                @endif
                <div class="flex justify-between text-base font-bold pt-2 border-t text-slate-900 dark:text-white">
                    <span>Grand Total:</span>
                    <span class="text-emerald-500 code-font">৳{{ number_format($purchase->grand_total, 2) }}</span>
                </div>
                <div class="flex justify-between text-emerald-600 font-semibold">
                    <span>Paid Amount:</span>
                    <span class="code-font">৳{{ number_format($purchase->paid_amount, 2) }}</span>
                </div>
                <div class="flex justify-between font-bold {{ $purchase->due_amount > 0 ? 'text-rose-500' : 'text-slate-400' }}">
                    <span>Due Remaining:</span>
                    <span class="code-font">৳{{ number_format($purchase->due_amount, 2) }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Payments Ledger For This Purchase -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
        <div class="p-4 border-b border-slate-200 dark:border-slate-800 font-bold text-sm text-slate-900 dark:text-white flex items-center justify-between">
            <span>Payment Vouchers ({{ $purchase->payments->count() }})</span>
            <span class="text-xs text-slate-500 font-normal">Total Paid for PO: ৳{{ number_format($purchase->paid_amount, 2) }}</span>
        </div>
        <div class="divide-y divide-slate-100 dark:divide-slate-800">
            @forelse($purchase->payments as $pmt)
            <div class="p-4 flex items-center justify-between hover:bg-slate-50 dark:hover:bg-slate-800/30 transition">
                <div>
                    <div class="font-bold text-xs text-emerald-600 dark:text-emerald-400 code-font">৳{{ number_format($pmt->amount, 2) }}</div>
                    <div class="text-[11px] text-slate-400">{{ $pmt->payment_date->format('d M Y') }} via {{ strtoupper($pmt->payment_method) }} | Recorded by {{ $pmt->createdBy->name ?? 'Admin' }}</div>
                    @if($pmt->reference_no)
                    <div class="text-[10px] text-slate-500 font-mono">Ref: {{ $pmt->reference_no }}</div>
                    @endif
                    @if($pmt->notes)
                    <div class="text-[10px] text-slate-500">{{ $pmt->notes }}</div>
                    @endif
                </div>
                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300 uppercase">
                    Settled
                </span>
            </div>
            @empty
            <div class="p-6 text-center text-slate-400 text-xs">No direct payment vouchers linked to this purchase yet.</div>
            @endforelse
        </div>
    </div>

    <!-- RECORD PAYMENT MODAL -->
    @if($purchase->due_amount > 0)
    <div id="payPoModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/70 backdrop-blur-sm" style="display: none;">
        <div class="bg-white dark:bg-slate-900 rounded-2xl max-w-md w-full p-6 border border-slate-200 dark:border-slate-800 shadow-2xl space-y-4">
            <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-800 pb-3">
                <h3 class="text-sm font-bold text-slate-900 dark:text-white">Pay Due for {{ $purchase->purchase_no }}</h3>
                <button type="button" onclick="document.getElementById('payPoModal').style.display = 'none'" class="text-slate-400 hover:text-white">&times;</button>
            </div>
            
            <form method="POST" action="{{ route('admin.purchases.pay', $purchase->id) }}" class="space-y-3">
                @csrf
                <div>
                    <div class="flex justify-between items-center mb-1">
                        <label class="text-xs font-semibold text-slate-500">Payment Amount (৳) *</label>
                        <span class="text-[11px] text-rose-500 font-bold">Due: ৳{{ number_format($purchase->due_amount, 2) }}</span>
                    </div>
                    <input type="number" step="0.01" name="amount" value="{{ $purchase->due_amount }}" required min="0.01" max="{{ $purchase->due_amount }}" placeholder="0.00" class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-bold code-font text-emerald-600 outline-none focus:ring-2 focus:ring-emerald-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Payment Date *</label>
                    <input type="date" name="payment_date" value="{{ date('Y-m-d') }}" required class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs outline-none">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Payment Method</label>
                    <select name="payment_method" class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs outline-none">
                        <option value="bank">Bank Transfer</option>
                        <option value="cash">Cash In Drawer</option>
                        <option value="bkash">bKash Merchant</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Cheque / Transaction Ref</label>
                    <input type="text" name="reference_no" placeholder="TRX-9812903" class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs outline-none">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Notes</label>
                    <textarea name="notes" rows="2" placeholder="Payment note..." class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs outline-none"></textarea>
                </div>
                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" onclick="document.getElementById('payPoModal').style.display = 'none'" class="px-4 py-2 rounded-xl bg-slate-200 dark:bg-slate-800 text-slate-700 dark:text-slate-300 text-xs font-semibold">
                        Cancel
                    </button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold shadow-md">
                        Record Payment
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif

</div>
@endsection
