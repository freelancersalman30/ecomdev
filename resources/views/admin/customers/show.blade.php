@extends('layouts.admin')

@section('title', 'Customer CRM: ' . $customer->name)
@section('page-title', 'Customer Profile: ' . $customer->name)

@section('content')
<div class="space-y-6">

    <!-- Customer Overview Card -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 border border-slate-200 dark:border-slate-800 shadow-sm flex flex-col sm:flex-row items-center justify-between gap-6">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.customers.index') }}" class="p-2 rounded-xl text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-800 transition">
                <i data-lucide="arrow-left" class="w-5 h-5"></i>
            </a>
            <div class="w-14 h-14 rounded-2xl bg-emerald-500/10 text-emerald-500 flex items-center justify-center font-black text-xl">
                {{ substr($customer->name, 0, 1) }}
            </div>
            <div>
                <h2 class="text-lg font-bold text-slate-900 dark:text-white">{{ $customer->name }}</h2>
                <div class="text-xs text-slate-500 font-mono">{{ $customer->phone }} | {{ $customer->email ?? 'No email' }}</div>
                <div class="text-xs text-slate-400 mt-0.5">{{ $customer->address }}, {{ $customer->city }}</div>
            </div>
        </div>

        <div class="flex items-center gap-4">
            <div class="text-right">
                <span class="text-xs text-slate-400">Total Lifetime Spend:</span>
                <div class="text-2xl font-black text-emerald-500 code-font">৳{{ number_format($customer->total_spent, 2) }}</div>
            </div>
            <div class="text-right">
                <span class="text-xs text-slate-400">Loyalty Points:</span>
                <div class="text-xl font-bold text-amber-500 code-font">★ {{ $customer->loyalty_points }}</div>
            </div>
        </div>
    </div>

    <!-- 3 Metrics Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="p-4 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800">
            <span class="text-xs text-slate-400">Delivery Success Rate</span>
            <div class="text-xl font-black {{ $customer->delivery_success_rate < 50 ? 'text-rose-500' : 'text-emerald-500' }} code-font mt-1">
                {{ $customer->delivery_success_rate }}%
            </div>
        </div>

        <div class="p-4 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800">
            <span class="text-xs text-slate-400">Total Orders Placed</span>
            <div class="text-xl font-black text-slate-900 dark:text-white code-font mt-1">
                {{ $customer->total_orders_count }} Orders
            </div>
        </div>

        <div class="p-4 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800">
            <span class="text-xs text-slate-400">Fraud Flag Status</span>
            <div class="text-xl font-black {{ $customer->is_flagged_fraud ? 'text-rose-500' : 'text-emerald-500' }} mt-1">
                {{ $customer->is_flagged_fraud ? 'Flagged Buyer' : 'Clean / Safe Buyer' }}
            </div>
        </div>
    </div>

    <!-- Orders History Table -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
        <div class="p-4 border-b border-slate-200 dark:border-slate-800 font-bold text-sm text-slate-900 dark:text-white">
            Customer Orders History
        </div>
        <table class="w-full text-left text-xs">
            <thead class="bg-slate-50 dark:bg-slate-800/50 text-slate-500 font-bold uppercase">
                <tr>
                    <th class="p-3">Order No</th>
                    <th class="p-3">Date</th>
                    <th class="p-3">Status</th>
                    <th class="p-3">Grand Total</th>
                    <th class="p-3">Payment</th>
                    <th class="p-3 text-right">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                @forelse($customer->orders as $order)
                <tr>
                    <td class="p-3 font-mono font-bold text-slate-900 dark:text-white">{{ $order->order_no }}</td>
                    <td class="p-3 text-slate-400">{{ $order->created_at->format('d M Y') }}</td>
                    <td class="p-3">
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase bg-slate-100 dark:bg-slate-800">
                            {{ str_replace('_', ' ', $order->order_status) }}
                        </span>
                    </td>
                    <td class="p-3 font-bold code-font text-slate-900 dark:text-white">৳{{ number_format($order->grand_total, 2) }}</td>
                    <td class="p-3 text-slate-500">{{ strtoupper($order->payment_method) }}</td>
                    <td class="p-3 text-right">
                        <a href="{{ route('admin.orders.show', $order->id) }}" class="text-emerald-500 hover:underline">View</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="p-6 text-center text-slate-400">No orders placed yet.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>
@endsection
