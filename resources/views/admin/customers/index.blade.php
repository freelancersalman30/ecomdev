@extends('layouts.admin')

@section('title', 'Customer CRM Directory')
@section('page-title', 'Customer CRM & Loyalty Records')

@section('content')
<div class="space-y-6">

    <!-- Header & Search Toolbar -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl p-4 border border-slate-200 dark:border-slate-800 shadow-sm flex flex-col sm:flex-row items-center justify-between gap-4">
        <div>
            <h2 class="text-base font-bold text-slate-900 dark:text-white">Customer Directory</h2>
            <p class="text-xs text-slate-500">Track customer spending, delivery success rate scoring, and loyalty rewards</p>
        </div>

        <form method="GET" action="{{ route('admin.customers.index') }}" class="flex items-center gap-2 w-full sm:w-80">
            <div class="relative flex-1">
                <i data-lucide="search" class="w-4 h-4 absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400"></i>
                <input 
                    type="text" 
                    name="search" 
                    value="{{ $search }}" 
                    placeholder="Search name, phone, email..." 
                    class="w-full pl-10 pr-4 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs outline-none focus:ring-2 focus:ring-emerald-500">
            </div>
            <button type="submit" class="px-3.5 py-2 rounded-xl bg-slate-800 text-white text-xs font-semibold hover:bg-slate-700 transition">
                Search
            </button>
        </form>
    </div>

    <!-- Customers Table -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 dark:bg-slate-800/50 text-slate-500 text-xs uppercase font-semibold">
                    <tr>
                        <th class="px-4 py-3.5">Customer & Phone</th>
                        <th class="px-4 py-3.5">City & Address</th>
                        <th class="px-4 py-3.5">Orders Placed</th>
                        <th class="px-4 py-3.5">Delivery Rate</th>
                        <th class="px-4 py-3.5">Total Spent</th>
                        <th class="px-4 py-3.5">Loyalty Points</th>
                        <th class="px-4 py-3.5">Status</th>
                        <th class="px-4 py-3.5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($customers as $cust)
                    <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition">
                        <td class="px-4 py-3.5">
                            <div class="font-bold text-xs text-slate-900 dark:text-white">
                                <a href="{{ route('admin.customers.show', $cust->id) }}" class="hover:text-emerald-500">
                                    {{ $cust->name }}
                                </a>
                            </div>
                            <div class="text-[11px] text-slate-500 font-mono">{{ $cust->phone }}</div>
                            @if($cust->email)
                            <div class="text-[10px] text-slate-400">{{ $cust->email }}</div>
                            @endif
                        </td>
                        <td class="px-4 py-3.5 text-xs">
                            <div class="font-semibold text-slate-800 dark:text-slate-200">{{ $cust->city ?? 'Dhaka' }}</div>
                            <div class="text-[10px] text-slate-400 truncate max-w-[160px]">{{ $cust->address }}</div>
                        </td>
                        <td class="px-4 py-3.5 font-bold code-font text-xs text-slate-900 dark:text-white">
                            {{ $cust->total_orders_count }}
                        </td>
                        <td class="px-4 py-3.5">
                            <span class="px-2 py-0.5 rounded-full text-xs font-bold code-font {{ $cust->delivery_success_rate < 50 ? 'bg-rose-100 text-rose-700 dark:bg-rose-950 dark:text-rose-300' : 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300' }}">
                                {{ $cust->delivery_success_rate }}%
                            </span>
                        </td>
                        <td class="px-4 py-3.5 font-bold code-font text-xs text-slate-900 dark:text-white">
                            ৳{{ number_format($cust->total_spent, 2) }}
                        </td>
                        <td class="px-4 py-3.5">
                            <span class="px-2 py-0.5 rounded-md text-xs font-bold bg-amber-500/10 text-amber-500 code-font">
                                ★ {{ $cust->loyalty_points }} pts
                            </span>
                        </td>
                        <td class="px-4 py-3.5">
                            @if($cust->is_flagged_fraud)
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase bg-rose-500/10 text-rose-500">
                                Fraud Alert
                            </span>
                            @else
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase bg-emerald-500/10 text-emerald-500">
                                Verified
                            </span>
                            @endif
                        </td>
                        <td class="px-4 py-3.5 text-right">
                            <a href="{{ route('admin.customers.show', $cust->id) }}" class="p-1.5 rounded-lg text-slate-600 dark:text-slate-300 hover:text-emerald-500 hover:bg-slate-100 dark:hover:bg-slate-800 inline-flex">
                                <i data-lucide="eye" class="w-4 h-4"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-12 text-center text-slate-400 text-xs">
                            No customers found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-slate-200 dark:border-slate-800">
            {{ $customers->links() }}
        </div>
    </div>

</div>
@endsection
