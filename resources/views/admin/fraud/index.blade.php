@extends('layouts.admin')

@section('title', 'Fraud & Risk Detection')
@section('page-title', 'Fraud & Courier Delivery Success Rate Check')

@section('content')
<div class="space-y-6">

    <!-- Phone Number Risk Lookup & Blacklist Form -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

        <!-- Phone Lookup Card -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 border border-slate-200 dark:border-slate-800 shadow-sm space-y-4">
            <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                <i data-lucide="shield-search" class="w-4 h-4 text-sky-500"></i>
                <span>Courier Delivery Success Rate & Risk Lookup</span>
            </h3>
            <p class="text-xs text-slate-500">Check customer phone numbers against courier delivery histories, return rate scores, and past COD cancellations.</p>
            
            <form method="POST" action="{{ route('admin.fraud.check') }}" class="flex items-center gap-2">
                @csrf
                <div class="relative flex-1">
                    <i data-lucide="phone" class="w-4 h-4 absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400"></i>
                    <input type="text" name="phone" required placeholder="Enter customer phone (e.g. 01900998877)" class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-mono font-bold outline-none focus:ring-2 focus:ring-sky-500">
                </div>
                <button type="submit" class="px-5 py-2.5 bg-sky-600 hover:bg-sky-500 text-white rounded-xl text-xs font-bold shadow-md transition flex items-center gap-1.5">
                    <i data-lucide="search" class="w-4 h-4"></i>
                    <span>Evaluate Risk</span>
                </button>
            </form>
        </div>

        <!-- Manual Blacklist Form -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 border border-slate-200 dark:border-slate-800 shadow-sm space-y-4">
            <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                <i data-lucide="ban" class="w-4 h-4 text-rose-500"></i>
                <span>Add Number / IP to Blacklist</span>
            </h3>
            <p class="text-xs text-slate-500">Explicitly flag known fraudulent buyers to automatically block new incoming COD orders.</p>
            
            <form method="POST" action="{{ route('admin.fraud.blacklist') }}" class="space-y-3">
                @csrf
                <div class="grid grid-cols-2 gap-3">
                    <input type="text" name="phone" required placeholder="Phone number *" class="w-full px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-mono outline-none">
                    <input type="text" name="notes" placeholder="Reason (e.g. Repeated parcel return)" class="w-full px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs outline-none">
                </div>
                <button type="submit" class="w-full py-2 bg-rose-600 hover:bg-rose-500 text-white rounded-xl text-xs font-bold shadow-md transition">
                    + Blacklist Number
                </button>
            </form>
        </div>

    </div>

    <!-- Suspicious Orders Alert List -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
        <div class="p-4 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <i data-lucide="alert-triangle" class="w-5 h-5 text-rose-500"></i>
                <h3 class="text-sm font-bold text-slate-900 dark:text-white">Flagged Suspicious Orders</h3>
            </div>
            <span class="text-xs text-rose-500 font-bold">{{ $suspiciousOrders->count() }} flagged</span>
        </div>

        <div class="divide-y divide-slate-100 dark:divide-slate-800">
            @forelse($suspiciousOrders as $order)
            <div class="p-4 flex items-center justify-between hover:bg-slate-50 dark:hover:bg-slate-800/30 transition">
                <div class="space-y-1">
                    <div class="flex items-center gap-2">
                        <a href="{{ route('admin.orders.show', $order->id) }}" class="font-bold code-font text-xs text-slate-900 dark:text-white hover:text-emerald-500">
                            {{ $order->order_no }}
                        </a>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-500/10 text-rose-500 uppercase">
                            Risk: {{ $order->fraud_score }}%
                        </span>
                    </div>
                    <div class="text-xs text-slate-700 dark:text-slate-300 font-medium">{{ $order->shipping_name }} ({{ $order->shipping_phone }})</div>
                    <div class="text-[11px] text-rose-600 dark:text-rose-400">{{ $order->fraud_reason }}</div>
                </div>
                <div class="text-right space-y-1">
                    <div class="text-xs font-bold text-slate-900 dark:text-white code-font">৳{{ number_format($order->grand_total, 2) }}</div>
                    <a href="{{ route('admin.orders.show', $order->id) }}" class="text-xs text-emerald-600 dark:text-emerald-400 font-semibold hover:underline">
                        Review Order &rarr;
                    </a>
                </div>
            </div>
            @empty
            <div class="p-6 text-center text-slate-400 text-xs">No suspicious orders flagged at this moment.</div>
            @endforelse
        </div>
    </div>

    <!-- Blacklisted Records Table -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
        <div class="p-4 border-b border-slate-200 dark:border-slate-800 font-bold text-sm text-slate-900 dark:text-white">
            Blacklisted Phone Numbers & IP Records
        </div>
        <table class="w-full text-left text-xs">
            <thead class="bg-slate-50 dark:bg-slate-800/50 text-slate-500 font-bold uppercase">
                <tr>
                    <th class="p-3">Phone Number</th>
                    <th class="p-3">IP Address</th>
                    <th class="p-3">Risk Level</th>
                    <th class="p-3">Courier Delivery Rate</th>
                    <th class="p-3">Notes & Reasons</th>
                    <th class="p-3">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                @forelse($fraudRecords as $rec)
                <tr>
                    <td class="p-3 font-mono font-bold text-slate-900 dark:text-white">{{ $rec->phone }}</td>
                    <td class="p-3 font-mono text-slate-400">{{ $rec->ip_address ?? 'N/A' }}</td>
                    <td class="p-3">
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase bg-rose-500/10 text-rose-500">
                            {{ $rec->risk_level }}
                        </span>
                    </td>
                    <td class="p-3 font-bold code-font text-rose-500">{{ $rec->courier_success_rate }}%</td>
                    <td class="p-3 text-slate-500">{{ $rec->notes }}</td>
                    <td class="p-3">
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase bg-red-100 text-red-700 dark:bg-red-950 dark:text-red-300">
                            Blacklisted
                        </span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="p-6 text-center text-slate-400">No blacklist entries recorded.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>
@endsection
