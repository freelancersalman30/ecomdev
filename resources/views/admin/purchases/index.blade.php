@extends('layouts.admin')

@section('title', 'Purchases & Supplier Due')
@section('page-title', 'Purchases & Supplier Due Intake')

@section('content')
<div class="space-y-6">

    <!-- Financial Filter Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        
        <!-- Today Total -->
        <a href="{{ route('admin.purchases.index', ['filter' => 'today']) }}" 
           class="p-5 rounded-2xl border transition {{ $timeFilter === 'today' ? 'bg-emerald-500 text-slate-950 border-emerald-400 font-bold shadow-lg shadow-emerald-500/20' : 'bg-white dark:bg-slate-900 border-slate-200 dark:border-slate-800 hover:border-emerald-500' }}">
            <div class="text-xs uppercase tracking-wider {{ $timeFilter === 'today' ? 'text-slate-900' : 'text-slate-500' }}">Today's Purchases</div>
            <div class="text-2xl font-black code-font mt-2">৳{{ number_format($todayTotal, 2) }}</div>
            <div class="text-[11px] mt-1 {{ $timeFilter === 'today' ? 'text-slate-800' : 'text-slate-400' }}">Click to filter today</div>
        </a>

        <!-- This Month -->
        <a href="{{ route('admin.purchases.index', ['filter' => 'this_month']) }}" 
           class="p-5 rounded-2xl border transition {{ $timeFilter === 'this_month' ? 'bg-sky-500 text-white border-sky-400 font-bold shadow-lg shadow-sky-500/20' : 'bg-white dark:bg-slate-900 border-slate-200 dark:border-slate-800 hover:border-sky-500' }}">
            <div class="text-xs uppercase tracking-wider {{ $timeFilter === 'this_month' ? 'text-sky-100' : 'text-slate-500' }}">This Month ({{ now()->format('F') }})</div>
            <div class="text-2xl font-black code-font mt-2">৳{{ number_format($monthTotal, 2) }}</div>
            <div class="text-[11px] mt-1 {{ $timeFilter === 'this_month' ? 'text-sky-100' : 'text-slate-400' }}">Monthly intake volume</div>
        </a>

        <!-- This Year -->
        <a href="{{ route('admin.purchases.index', ['filter' => 'this_year']) }}" 
           class="p-5 rounded-2xl border transition {{ $timeFilter === 'this_year' ? 'bg-purple-600 text-white border-purple-500 font-bold shadow-lg shadow-purple-600/20' : 'bg-white dark:bg-slate-900 border-slate-200 dark:border-slate-800 hover:border-purple-500' }}">
            <div class="text-xs uppercase tracking-wider {{ $timeFilter === 'this_year' ? 'text-purple-200' : 'text-slate-500' }}">This Year ({{ now()->year }})</div>
            <div class="text-2xl font-black code-font mt-2">৳{{ number_format($yearTotal, 2) }}</div>
            <div class="text-[11px] mt-1 {{ $timeFilter === 'this_year' ? 'text-purple-200' : 'text-slate-400' }}">Yearly procurement total</div>
        </a>

        <!-- Total Payable Due -->
        <a href="{{ route('admin.purchases.index', ['filter' => 'due']) }}" 
           class="p-5 rounded-2xl border transition {{ $timeFilter === 'due' ? 'bg-rose-600 text-white border-rose-500 font-bold shadow-lg shadow-rose-600/20' : 'bg-white dark:bg-slate-900 border-slate-200 dark:border-slate-800 hover:border-rose-500' }}">
            <div class="text-xs uppercase tracking-wider {{ $timeFilter === 'due' ? 'text-rose-200' : 'text-rose-500 font-bold' }}">Total Due to Suppliers</div>
            <div class="text-2xl font-black code-font mt-2 text-rose-600 dark:text-rose-400 {{ $timeFilter === 'due' ? 'text-white dark:text-white' : '' }}">৳{{ number_format($totalDue, 2) }}</div>
            <div class="text-[11px] mt-1 {{ $timeFilter === 'due' ? 'text-rose-100' : 'text-slate-400' }}">Payable ledger balance</div>
        </a>

    </div>

    <!-- Action Toolbar -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl p-4 border border-slate-200 dark:border-slate-800 shadow-sm flex flex-col sm:flex-row items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <span class="text-xs font-bold text-slate-500">Filter View:</span>
            <a href="{{ route('admin.purchases.index') }}" class="px-3 py-1.5 rounded-lg text-xs font-semibold {{ $timeFilter === 'all' ? 'bg-slate-800 text-white' : 'text-slate-600 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                Show All
            </a>
        </div>

        <a href="{{ route('admin.purchases.create') }}" class="px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs shadow-md shadow-emerald-600/20 transition flex items-center gap-1.5">
            <i data-lucide="plus-circle" class="w-4 h-4"></i>
            <span>+ Create Purchase Order (PO)</span>
        </a>
    </div>

    <!-- Purchases Table -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 dark:bg-slate-800/50 text-slate-500 text-xs uppercase font-semibold">
                    <tr>
                        <th class="px-4 py-3.5">PO No & Date</th>
                        <th class="px-4 py-3.5">Supplier</th>
                        <th class="px-4 py-3.5">Items Purchased</th>
                        <th class="px-4 py-3.5">Grand Total</th>
                        <th class="px-4 py-3.5">Paid Amount</th>
                        <th class="px-4 py-3.5">Due Amount</th>
                        <th class="px-4 py-3.5">Payment Status</th>
                        <th class="px-4 py-3.5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($purchases as $purchase)
                    <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition">
                        <td class="px-4 py-3.5">
                            <div class="font-bold code-font text-xs text-slate-900 dark:text-white">
                                <a href="{{ route('admin.purchases.show', $purchase->id) }}" class="hover:text-emerald-500">
                                    {{ $purchase->purchase_no }}
                                </a>
                            </div>
                            <div class="text-[11px] text-slate-400">{{ $purchase->purchase_date->format('d M Y') }}</div>
                        </td>
                        <td class="px-4 py-3.5">
                            <div class="font-semibold text-xs text-slate-900 dark:text-white">{{ $purchase->supplier->name ?? 'Unknown Supplier' }}</div>
                            <div class="text-[11px] text-slate-500">{{ $purchase->supplier->phone ?? '' }}</div>
                        </td>
                        <td class="px-4 py-3.5 text-xs text-slate-600 dark:text-slate-400">
                            {{ $purchase->items->count() }} line items
                        </td>
                        <td class="px-4 py-3.5 font-bold code-font text-slate-900 dark:text-white">
                            ৳{{ number_format($purchase->grand_total, 2) }}
                        </td>
                        <td class="px-4 py-3.5 text-emerald-600 dark:text-emerald-400 font-bold code-font">
                            ৳{{ number_format($purchase->paid_amount, 2) }}
                        </td>
                        <td class="px-4 py-3.5 font-bold code-font {{ $purchase->due_amount > 0 ? 'text-rose-500' : 'text-slate-400' }}">
                            ৳{{ number_format($purchase->due_amount, 2) }}
                        </td>
                        <td class="px-4 py-3.5">
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase {{ $purchase->payment_status === 'paid' ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300' : ($purchase->payment_status === 'partial' ? 'bg-amber-100 text-amber-700 dark:bg-amber-950 dark:text-amber-300' : 'bg-rose-100 text-rose-700 dark:bg-rose-950 dark:text-rose-300') }}">
                                {{ $purchase->payment_status }}
                            </span>
                        </td>
                        <td class="px-4 py-3.5 text-right">
                            <a href="{{ route('admin.purchases.show', $purchase->id) }}" class="p-1.5 rounded-lg text-slate-600 dark:text-slate-300 hover:text-emerald-500 hover:bg-slate-100 dark:hover:bg-slate-800 inline-flex">
                                <i data-lucide="eye" class="w-4 h-4"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-12 text-center text-slate-400 text-xs">
                            No purchases recorded for this filter.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-slate-200 dark:border-slate-800">
            {{ $purchases->links() }}
        </div>
    </div>

</div>
@endsection
