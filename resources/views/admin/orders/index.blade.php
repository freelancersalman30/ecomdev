@extends('layouts.admin')

@section('title', 'Orders Management')
@section('page-title', 'Orders Pipeline Management')

@section('content')
<div x-data="{ selectedOrders: [], bulkStatus: '' }" class="space-y-6">

    <!-- 8-Stage Pipeline Status Tabs -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl p-2 border border-slate-200 dark:border-slate-800 shadow-sm flex items-center gap-2 overflow-x-auto">
        
        @php
            $tabs = [
                'all' => ['label' => 'All Orders', 'icon' => 'layers', 'count' => $counts['all'], 'color' => 'slate'],
                'incomplete' => ['label' => 'Incomplete (Drop-off)', 'icon' => 'shopping-cart', 'count' => $counts['incomplete'], 'color' => 'amber'],
                'pending' => ['label' => 'Pending', 'icon' => 'clock', 'count' => $counts['pending'], 'color' => 'yellow'],
                'processing' => ['label' => 'Processing', 'icon' => 'refresh-cw', 'count' => $counts['processing'], 'color' => 'blue'],
                'on_the_way' => ['label' => 'On the Way', 'icon' => 'navigation', 'count' => $counts['on_the_way'], 'color' => 'indigo'],
                'in_courier' => ['label' => 'In Courier', 'icon' => 'truck', 'count' => $counts['in_courier'], 'color' => 'cyan'],
                'completed' => ['label' => 'Completed', 'icon' => 'check-circle-2', 'count' => $counts['completed'], 'color' => 'emerald'],
                'cancelled' => ['label' => 'Cancelled', 'icon' => 'x-circle', 'count' => $counts['cancelled'], 'color' => 'rose'],
                'returned' => ['label' => 'Returned', 'icon' => 'rotate-ccw', 'count' => $counts['returned'], 'color' => 'red'],
            ];
        @endphp

        @foreach($tabs as $tabKey => $tab)
        <a href="{{ route('admin.orders.index', ['status' => $tabKey, 'search' => $search]) }}" 
           class="flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs font-semibold whitespace-nowrap transition {{ $status === $tabKey ? 'bg-emerald-500 text-slate-950 font-bold shadow-md shadow-emerald-500/20' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
            <i data-lucide="{{ $tab['icon'] }}" class="w-4 h-4"></i>
            <span>{{ $tab['label'] }}</span>
            <span class="px-1.5 py-0.5 rounded-full text-[10px] font-bold {{ $status === $tabKey ? 'bg-slate-950/20 text-slate-950' : 'bg-slate-200 dark:bg-slate-800 text-slate-700 dark:text-slate-300' }}">
                {{ $tab['count'] }}
            </span>
        </a>
        @endforeach

    </div>

    <!-- Search, Filter & Bulk Action Toolbar -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl p-4 border border-slate-200 dark:border-slate-800 shadow-sm flex flex-col sm:flex-row items-center justify-between gap-4">
        
        <!-- Search Input -->
        <form method="GET" action="{{ route('admin.orders.index') }}" class="flex items-center gap-2 w-full sm:w-auto">
            <input type="hidden" name="status" value="{{ $status }}">
            <div class="relative w-full sm:w-80">
                <i data-lucide="search" class="w-4 h-4 absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400"></i>
                <input 
                    type="text" 
                    name="search" 
                    value="{{ $search }}" 
                    placeholder="Search by Order ID, Phone, Customer..." 
                    class="w-full pl-10 pr-4 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs outline-none focus:ring-2 focus:ring-emerald-500">
            </div>
            <button type="submit" class="px-3.5 py-2 rounded-xl bg-slate-800 text-white text-xs font-semibold hover:bg-slate-700 transition">
                Search
            </button>
            @if($search)
            <a href="{{ route('admin.orders.index', ['status' => $status]) }}" class="text-xs text-rose-500 hover:underline">Reset</a>
            @endif
        </form>

        <!-- Bulk Action Form -->
        <div x-show="selectedOrders.length > 0" class="flex items-center gap-2 w-full sm:w-auto bg-emerald-50 dark:bg-emerald-950/40 px-3 py-1.5 rounded-xl border border-emerald-200 dark:border-emerald-800">
            <span class="text-xs font-bold text-emerald-700 dark:text-emerald-300"><span x-text="selectedOrders.length"></span> selected</span>
            <form method="POST" action="{{ route('admin.orders.bulk.status') }}" class="flex items-center gap-2">
                @csrf
                <template x-for="id in selectedOrders" :key="id">
                    <input type="hidden" name="order_ids[]" :value="id">
                </template>
                <select name="status" class="px-2.5 py-1 text-xs rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-200">
                    <option value="processing">Move to Processing</option>
                    <option value="on_the_way">Move to On the Way</option>
                    <option value="in_courier">Move to In Courier</option>
                    <option value="completed">Mark Completed</option>
                    <option value="cancelled">Mark Cancelled</option>
                </select>
                <button type="submit" class="px-3 py-1 bg-emerald-600 hover:bg-emerald-500 text-white rounded-lg text-xs font-semibold">
                    Apply
                </button>
            </form>
        </div>

    </div>

    <!-- Orders Table -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 dark:bg-slate-800/50 text-slate-500 text-xs uppercase font-semibold">
                    <tr>
                        <th class="px-4 py-3.5 w-10 text-center">
                            <input type="checkbox" @click="selectedOrders = ($event.target.checked) ? {{ json_encode($orders->pluck('id')) }} : []" class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                        </th>
                        <th class="px-4 py-3.5">Order ID & Date</th>
                        <th class="px-4 py-3.5">Customer & Phone</th>
                        <th class="px-4 py-3.5">Items Summary</th>
                        <th class="px-4 py-3.5">Grand Total</th>
                        <th class="px-4 py-3.5">Payment</th>
                        <th class="px-4 py-3.5">Courier & Tracking</th>
                        <th class="px-4 py-3.5">Status</th>
                        <th class="px-4 py-3.5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($orders as $order)
                    <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition">
                        <td class="px-4 py-3.5 text-center">
                            <input type="checkbox" :value="{{ $order->id }}" x-model="selectedOrders" class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                        </td>
                        <td class="px-4 py-3.5">
                            <div class="font-bold code-font text-xs text-slate-900 dark:text-white">
                                <a href="{{ route('admin.orders.show', $order->id) }}" class="hover:text-emerald-500">
                                    {{ $order->order_no }}
                                </a>
                            </div>
                            <div class="text-[11px] text-slate-400 mt-0.5">{{ $order->created_at->format('d M Y, h:i A') }}</div>
                            <span class="inline-block px-1.5 py-0.5 rounded text-[9px] font-bold uppercase mt-1 {{ $order->order_type === 'pos' ? 'bg-purple-100 text-purple-700 dark:bg-purple-950 dark:text-purple-300' : 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300' }}">
                                {{ $order->order_type }}
                            </span>
                        </td>
                        <td class="px-4 py-3.5">
                            <div class="font-semibold text-xs text-slate-900 dark:text-white">{{ $order->shipping_name }}</div>
                            <div class="text-[11px] text-slate-500 font-mono">{{ $order->shipping_phone }}</div>
                            <div class="text-[10px] text-slate-400 truncate max-w-[150px]">{{ $order->shipping_city }}</div>
                        </td>
                        <td class="px-4 py-3.5">
                            <div class="text-xs font-medium text-slate-700 dark:text-slate-300">
                                {{ $order->items->count() }} items
                            </div>
                            <div class="text-[11px] text-slate-400 truncate max-w-[180px]">
                                {{ $order->items->pluck('product_name')->implode(', ') }}
                            </div>
                        </td>
                        <td class="px-4 py-3.5 font-bold code-font text-slate-900 dark:text-white">
                            ৳{{ number_format($order->grand_total, 2) }}
                        </td>
                        <td class="px-4 py-3.5">
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase {{ $order->payment_status === 'paid' ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300' : 'bg-amber-100 text-amber-700 dark:bg-amber-950 dark:text-amber-300' }}">
                                {{ $order->payment_status }}
                            </span>
                            <div class="text-[10px] text-slate-500 mt-0.5 uppercase">{{ $order->payment_method }}</div>
                        </td>
                        <td class="px-4 py-3.5 text-xs">
                            @if($order->courier_name)
                            <div class="font-semibold text-slate-900 dark:text-white">{{ $order->courier_name }}</div>
                            <div class="text-[10px] font-mono text-emerald-600 dark:text-emerald-400">{{ $order->courier_tracking_id ?? 'No Tracking' }}</div>
                            @else
                            <span class="text-slate-400 text-xs italic">Unassigned</span>
                            @endif
                        </td>
                        <td class="px-4 py-3.5">
                            @php
                                $statusBadge = [
                                    'pending' => 'bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-300',
                                    'processing' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300',
                                    'on_the_way' => 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900/30 dark:text-indigo-300',
                                    'in_courier' => 'bg-cyan-100 text-cyan-800 dark:bg-cyan-900/30 dark:text-cyan-300',
                                    'completed' => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-300',
                                    'cancelled' => 'bg-rose-100 text-rose-800 dark:bg-rose-900/30 dark:text-rose-300',
                                    'returned' => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300',
                                    'incomplete' => 'bg-slate-100 text-slate-800 dark:bg-slate-800 dark:text-slate-300',
                                ];
                            @endphp
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider {{ $statusBadge[$order->status] ?? 'bg-slate-100 text-slate-800' }}">
                                {{ str_replace('_', ' ', $order->status) }}
                            </span>
                            @if($order->is_fraud_suspect)
                            <span class="block mt-1 text-[9px] font-bold text-rose-500">⚠ High Risk ({{ $order->fraud_score }}%)</span>
                            @endif
                        </td>
                        <td class="px-4 py-3.5 text-right space-x-1 whitespace-nowrap">
                            <a href="{{ route('admin.orders.show', $order->id) }}" class="p-1.5 rounded-lg text-slate-600 dark:text-slate-300 hover:text-emerald-500 hover:bg-slate-100 dark:hover:bg-slate-800 inline-flex" title="View Details">
                                <i data-lucide="eye" class="w-4 h-4"></i>
                            </a>
                            <a href="{{ route('admin.orders.invoice', $order->id) }}" target="_blank" class="p-1.5 rounded-lg text-slate-600 dark:text-slate-300 hover:text-sky-500 hover:bg-slate-100 dark:hover:bg-slate-800 inline-flex" title="Print Invoice">
                                <i data-lucide="file-text" class="w-4 h-4"></i>
                            </a>
                            <a href="{{ route('admin.orders.packing_slip', $order->id) }}" target="_blank" class="p-1.5 rounded-lg text-slate-600 dark:text-slate-300 hover:text-amber-500 hover:bg-slate-100 dark:hover:bg-slate-800 inline-flex" title="Packing Slip">
                                <i data-lucide="package" class="w-4 h-4"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="px-6 py-12 text-center text-slate-400 text-xs">
                            No orders found under this pipeline tab.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="p-4 border-t border-slate-200 dark:border-slate-800">
            {{ $orders->links() }}
        </div>
    </div>

</div>
@endsection
