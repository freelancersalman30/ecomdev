@extends('layouts.admin')

@section('title', 'Supplier Ledger: ' . $supplier->name)
@section('page-title', 'Supplier Ledger & Due Settlement')

@section('content')
<div class="space-y-6">

    <!-- Top Card -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 border border-slate-200 dark:border-slate-800 shadow-sm flex flex-col sm:flex-row items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.suppliers.index') }}" class="p-2 rounded-xl text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-800 transition">
                <i data-lucide="arrow-left" class="w-5 h-5"></i>
            </a>
            <div>
                <h2 class="text-base font-bold text-slate-900 dark:text-white">{{ $supplier->name }}</h2>
                <div class="text-xs text-slate-500">{{ $supplier->company }} | Phone: {{ $supplier->phone }}</div>
            </div>
        </div>

        <div class="flex items-center gap-4">
            <div class="text-right">
                <span class="text-xs text-slate-400">Current Outstanding Due:</span>
                <div class="text-2xl font-black text-rose-500 code-font">৳{{ number_format($supplier->current_due, 2) }}</div>
            </div>
            <button onclick="document.getElementById('paySupplierModal').style.display = 'flex'" class="px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs shadow-md transition flex items-center gap-1.5">
                <i data-lucide="dollar-sign" class="w-4 h-4"></i>
                <span>Record Payment / Settle Due</span>
            </button>
        </div>
    </div>

    <!-- 2 Columns: Purchase History & Payment History -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        <!-- Purchase Orders History -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
            <div class="p-4 border-b border-slate-200 dark:border-slate-800 font-bold text-sm text-slate-900 dark:text-white flex items-center justify-between">
                <span>Purchase Invoices ({{ $supplier->purchases->count() }})</span>
                <span class="text-xs text-slate-500 font-normal">Total: ৳{{ number_format($supplier->total_purchased, 2) }}</span>
            </div>
            <div class="divide-y divide-slate-100 dark:divide-slate-800">
                @forelse($supplier->purchases as $po)
                <div class="p-4 flex items-center justify-between hover:bg-slate-50 dark:hover:bg-slate-800/30 transition">
                    <div>
                        <div class="font-bold text-xs text-slate-900 dark:text-white code-font">{{ $po->purchase_no }}</div>
                        <div class="text-[11px] text-slate-400">{{ $po->purchase_date->format('d M Y') }}</div>
                    </div>
                    <div class="text-right">
                        <div class="text-xs font-bold text-slate-900 dark:text-white code-font">৳{{ number_format($po->grand_total, 2) }}</div>
                        <div class="text-[10px] font-bold uppercase text-rose-500">Due: ৳{{ number_format($po->due_amount, 2) }}</div>
                    </div>
                </div>
                @empty
                <div class="p-6 text-center text-slate-400 text-xs">No purchases recorded for this supplier.</div>
                @endforelse
            </div>
        </div>

        <!-- Payment History Ledger -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
            <div class="p-4 border-b border-slate-200 dark:border-slate-800 font-bold text-sm text-slate-900 dark:text-white flex items-center justify-between">
                <span>Payment Vouchers ({{ $supplier->payments->count() }})</span>
                <span class="text-xs text-slate-500 font-normal">Paid: ৳{{ number_format($supplier->total_paid, 2) }}</span>
            </div>
            <div class="divide-y divide-slate-100 dark:divide-slate-800">
                @forelse($supplier->payments as $pmt)
                <div class="p-4 flex items-center justify-between hover:bg-slate-50 dark:hover:bg-slate-800/30 transition">
                    <div>
                        <div class="font-bold text-xs text-emerald-600 dark:text-emerald-400 code-font">৳{{ number_format($pmt->amount, 2) }}</div>
                        <div class="text-[11px] text-slate-400">{{ $pmt->payment_date->format('d M Y') }} via {{ strtoupper($pmt->payment_method) }}</div>
                        @if($pmt->notes)
                        <div class="text-[10px] text-slate-500">{{ $pmt->notes }}</div>
                        @endif
                    </div>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300 uppercase">
                        Voucher OK
                    </span>
                </div>
                @empty
                <div class="p-6 text-center text-slate-400 text-xs">No payment records yet.</div>
                @endforelse
            </div>
        </div>

    </div>

    <!-- RECORD PAYMENT MODAL -->
    <div id="paySupplierModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/70 backdrop-blur-sm" style="display: none;">
        <div class="bg-white dark:bg-slate-900 rounded-2xl max-w-md w-full p-6 border border-slate-200 dark:border-slate-800 shadow-2xl space-y-4">
            <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-800 pb-3">
                <h3 class="text-sm font-bold text-slate-900 dark:text-white">Record Payment to {{ $supplier->name }}</h3>
                <button onclick="document.getElementById('paySupplierModal').style.display = 'none'" class="text-slate-400 hover:text-white">&times;</button>
            </div>
            
            <form method="POST" action="{{ route('admin.suppliers.pay', $supplier->id) }}" class="space-y-3">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Payment Amount (৳) *</label>
                    <input type="number" step="0.01" name="amount" required max="{{ $supplier->current_due > 0 ? $supplier->current_due : 1000000 }}" placeholder="0.00" class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-bold code-font text-emerald-600 outline-none focus:ring-2 focus:ring-emerald-500">
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
                    <button type="button" onclick="document.getElementById('paySupplierModal').style.display = 'none'" class="px-4 py-2 rounded-xl bg-slate-200 dark:bg-slate-800 text-slate-700 dark:text-slate-300 text-xs font-semibold">
                        Cancel
                    </button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold shadow-md">
                        Record Payment
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
