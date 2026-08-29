@extends('layouts.admin')

@section('title', 'Purchase PO ' . $purchase->purchase_no)
@section('page-title', 'Purchase Order: ' . $purchase->purchase_no)

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <!-- Top Action Bar -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl p-4 border border-slate-200 dark:border-slate-800 shadow-sm flex items-center justify-between">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.purchases.index') }}" class="p-2 rounded-xl text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-800 transition">
                <i data-lucide="arrow-left" class="w-5 h-5"></i>
            </a>
            <div>
                <h2 class="text-base font-bold text-slate-900 dark:text-white code-font">{{ $purchase->purchase_no }}</h2>
                <div class="text-xs text-slate-500">Date: {{ $purchase->purchase_date->format('d M Y') }} | Created by: {{ $purchase->createdBy->name ?? 'Admin' }}</div>
            </div>
        </div>

        <span class="px-3 py-1 rounded-full text-xs font-bold uppercase {{ $purchase->payment_status === 'paid' ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300' : 'bg-rose-100 text-rose-700 dark:bg-rose-950 dark:text-rose-300' }}">
            {{ $purchase->payment_status }}
        </span>
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
            <div><strong>Total Due on Supplier:</strong> ৳{{ number_format($purchase->supplier->current_due, 2) }}</div>
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
                <div class="flex justify-between text-base font-bold pt-2 border-t text-slate-900 dark:text-white">
                    <span>Grand Total:</span>
                    <span class="text-emerald-500 code-font">৳{{ number_format($purchase->grand_total, 2) }}</span>
                </div>
                <div class="flex justify-between text-emerald-600">
                    <span>Paid Amount:</span>
                    <span class="code-font">৳{{ number_format($purchase->paid_amount, 2) }}</span>
                </div>
                <div class="flex justify-between text-rose-500 font-bold">
                    <span>Due Remaining:</span>
                    <span class="code-font">৳{{ number_format($purchase->due_amount, 2) }}</span>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
