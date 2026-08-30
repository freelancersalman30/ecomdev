@extends('layouts.admin')

@section('title', 'Business Reports & Intelligence')
@section('page-title', 'Business Reports Hub')

@section('content')
<div class="space-y-6">

    <!-- Top Report Navigation Bar -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl p-2 border border-slate-200 dark:border-slate-800 shadow-sm flex flex-wrap items-center gap-1.5">
        
        <!-- 1. Order / Sales Report -->
        <a href="{{ route('admin.reports.index', ['type' => 'sales']) }}" 
           class="flex items-center gap-2 px-4 py-2.5 rounded-xl font-bold text-xs transition {{ $reportType === 'sales' ? 'bg-emerald-500 text-slate-950 shadow-md shadow-emerald-500/20' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
            <i data-lucide="shopping-bag" class="w-4 h-4"></i>
            <span>Order Report</span>
        </a>

        <!-- 2. Purchase Report -->
        <a href="{{ route('admin.reports.index', ['type' => 'purchases']) }}" 
           class="flex items-center gap-2 px-4 py-2.5 rounded-xl font-bold text-xs transition {{ $reportType === 'purchases' ? 'bg-emerald-500 text-slate-950 shadow-md shadow-emerald-500/20' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
            <i data-lucide="truck" class="w-4 h-4"></i>
            <span>Purchase Report</span>
        </a>

        <!-- 3. Expense Report -->
        <a href="{{ route('admin.reports.index', ['type' => 'expenses']) }}" 
           class="flex items-center gap-2 px-4 py-2.5 rounded-xl font-bold text-xs transition {{ $reportType === 'expenses' ? 'bg-emerald-500 text-slate-950 shadow-md shadow-emerald-500/20' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
            <i data-lucide="receipt" class="w-4 h-4"></i>
            <span>Expense Report</span>
        </a>

        <!-- 4. Stock Report -->
        <a href="{{ route('admin.reports.index', ['type' => 'stock']) }}" 
           class="flex items-center gap-2 px-4 py-2.5 rounded-xl font-bold text-xs transition {{ $reportType === 'stock' ? 'bg-emerald-500 text-slate-950 shadow-md shadow-emerald-500/20' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
            <i data-lucide="boxes" class="w-4 h-4"></i>
            <span>Stock Report</span>
        </a>

        <!-- 5. Profit & Loss -->
        <a href="{{ route('admin.reports.index', ['type' => 'profit_loss']) }}" 
           class="flex items-center gap-2 px-4 py-2.5 rounded-xl font-bold text-xs transition {{ $reportType === 'profit_loss' ? 'bg-gradient-to-r from-emerald-500 to-teal-400 text-slate-950 shadow-md shadow-emerald-500/20' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
            <i data-lucide="trending-up" class="w-4 h-4"></i>
            <span>Profit & Loss (P&L)</span>
        </a>

        <div class="ml-auto flex items-center gap-2 pr-2">
            <button onclick="window.print()" class="px-3 py-2 rounded-xl text-xs font-bold bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700 transition flex items-center gap-1.5">
                <i data-lucide="printer" class="w-4 h-4"></i>
                <span class="hidden sm:inline">Print / PDF</span>
            </button>
        </div>

    </div>

    <!-- Filter & Date Range Toolbar (Hidden in Print) -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl p-4 sm:p-5 border border-slate-200 dark:border-slate-800 shadow-sm print:hidden">
        <form method="GET" action="{{ route('admin.reports.index') }}" id="reportFilterForm" class="space-y-4">
            <input type="hidden" name="type" value="{{ $reportType }}">

            <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 dark:border-slate-800 pb-3">
                <div class="flex items-center gap-2">
                    <i data-lucide="filter" class="w-4 h-4 text-emerald-500"></i>
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">
                        {{ match($reportType) {
                            'sales' => 'Order & Sales Filter',
                            'purchases' => 'Procurement & Purchases Filter',
                            'expenses' => 'Operating Expenses Filter',
                            'stock' => 'Inventory & Catalog Filter',
                            'profit_loss' => 'Profit & Loss Accounting Period',
                            default => 'Filter Report Data'
                        } }}
                    </span>
                </div>

                <!-- Quick Date Presets -->
                @if($reportType !== 'stock')
                <div class="flex flex-wrap items-center gap-1 text-[11px]">
                    <button type="button" onclick="setDateRange('today')" class="px-2.5 py-1 rounded-lg bg-slate-100 dark:bg-slate-800 hover:bg-emerald-50 dark:hover:bg-emerald-950/40 text-slate-600 dark:text-slate-400 font-semibold transition">Today</button>
                    <button type="button" onclick="setDateRange('week')" class="px-2.5 py-1 rounded-lg bg-slate-100 dark:bg-slate-800 hover:bg-emerald-50 dark:hover:bg-emerald-950/40 text-slate-600 dark:text-slate-400 font-semibold transition">This Week</button>
                    <button type="button" onclick="setDateRange('month')" class="px-2.5 py-1 rounded-lg bg-slate-100 dark:bg-slate-800 hover:bg-emerald-50 dark:hover:bg-emerald-950/40 text-slate-600 dark:text-slate-400 font-semibold transition">This Month</button>
                    <button type="button" onclick="setDateRange('last_month')" class="px-2.5 py-1 rounded-lg bg-slate-100 dark:bg-slate-800 hover:bg-emerald-50 dark:hover:bg-emerald-950/40 text-slate-600 dark:text-slate-400 font-semibold transition">Last Month</button>
                    <button type="button" onclick="setDateRange('year')" class="px-2.5 py-1 rounded-lg bg-slate-100 dark:bg-slate-800 hover:bg-emerald-50 dark:hover:bg-emerald-950/40 text-slate-600 dark:text-slate-400 font-semibold transition">This Year</button>
                </div>
                @endif
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 items-end">
                
                @if($reportType !== 'stock')
                <div>
                    <label class="block text-[11px] font-bold uppercase text-slate-500 mb-1">Start Date</label>
                    <input type="date" name="start_date" id="start_date" value="{{ $startDate }}" class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-semibold outline-none focus:ring-2 focus:ring-emerald-500">
                </div>

                <div>
                    <label class="block text-[11px] font-bold uppercase text-slate-500 mb-1">End Date</label>
                    <input type="date" name="end_date" id="end_date" value="{{ $endDate }}" class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-semibold outline-none focus:ring-2 focus:ring-emerald-500">
                </div>
                @endif

                <!-- Report-Specific Context Dropdowns -->
                @if($reportType === 'sales')
                <div>
                    <label class="block text-[11px] font-bold uppercase text-slate-500 mb-1">Order Status</label>
                    <select name="status" class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-semibold outline-none focus:ring-2 focus:ring-emerald-500">
                        <option value="">All Active Orders</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="processing" {{ request('status') === 'processing' ? 'selected' : '' }}>Processing</option>
                        <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>
                <div>
                    <label class="block text-[11px] font-bold uppercase text-slate-500 mb-1">Payment Method</label>
                    <select name="payment_method" class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-semibold outline-none focus:ring-2 focus:ring-emerald-500">
                        <option value="">All Payment Methods</option>
                        <option value="cod" {{ request('payment_method') === 'cod' ? 'selected' : '' }}>Cash on Delivery (COD)</option>
                        <option value="bkash" {{ request('payment_method') === 'bkash' ? 'selected' : '' }}>bKash</option>
                        <option value="nagad" {{ request('payment_method') === 'nagad' ? 'selected' : '' }}>Nagad</option>
                        <option value="rocket" {{ request('payment_method') === 'rocket' ? 'selected' : '' }}>Rocket</option>
                    </select>
                </div>
                @elseif($reportType === 'purchases')
                <div>
                    <label class="block text-[11px] font-bold uppercase text-slate-500 mb-1">Supplier</label>
                    <select name="supplier_id" class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-semibold outline-none focus:ring-2 focus:ring-emerald-500">
                        <option value="">All Suppliers</option>
                        @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->id }}" {{ request('supplier_id') == $supplier->id ? 'selected' : '' }}>{{ $supplier->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-[11px] font-bold uppercase text-slate-500 mb-1">Purchase Status</label>
                    <select name="status" class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-semibold outline-none focus:ring-2 focus:ring-emerald-500">
                        <option value="">All Statuses</option>
                        <option value="received" {{ request('status') === 'received' ? 'selected' : '' }}>Received</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="ordered" {{ request('status') === 'ordered' ? 'selected' : '' }}>Ordered</option>
                    </select>
                </div>
                @elseif($reportType === 'expenses')
                <div>
                    <label class="block text-[11px] font-bold uppercase text-slate-500 mb-1">Expense Category</label>
                    <select name="category_id" class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-semibold outline-none focus:ring-2 focus:ring-emerald-500">
                        <option value="">All Expense Categories</option>
                        @foreach($expenseCategories as $expCat)
                            <option value="{{ $expCat->id }}" {{ request('category_id') == $expCat->id ? 'selected' : '' }}>{{ $expCat->name }}</option>
                        @endforeach
                    </select>
                </div>
                @elseif($reportType === 'stock')
                <div>
                    <label class="block text-[11px] font-bold uppercase text-slate-500 mb-1">Product Category</label>
                    <select name="category_id" class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-semibold outline-none focus:ring-2 focus:ring-emerald-500">
                        <option value="">All Categories</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-[11px] font-bold uppercase text-slate-500 mb-1">Stock Availability</label>
                    <select name="stock_status" class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-semibold outline-none focus:ring-2 focus:ring-emerald-500">
                        <option value="">All Inventory Levels</option>
                        <option value="in_stock" {{ request('stock_status') === 'in_stock' ? 'selected' : '' }}>In Stock (> 10)</option>
                        <option value="low_stock" {{ request('stock_status') === 'low_stock' ? 'selected' : '' }}>Low Stock Warning (≤ 10)</option>
                        <option value="out_of_stock" {{ request('stock_status') === 'out_of_stock' ? 'selected' : '' }}>Out of Stock (0)</option>
                    </select>
                </div>
                @endif

                <div class="flex items-center gap-2">
                    <button type="submit" class="w-full py-2.5 px-4 rounded-xl bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-bold text-xs transition shadow-md shadow-emerald-500/20 flex items-center justify-center gap-1.5">
                        <i data-lucide="search" class="w-4 h-4"></i>
                        <span>Apply Filter</span>
                    </button>
                    <a href="{{ route('admin.reports.index', ['type' => $reportType]) }}" class="p-2.5 rounded-xl border border-slate-200 dark:border-slate-700 text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-800 transition" title="Reset Filters">
                        <i data-lucide="rotate-ccw" class="w-4 h-4"></i>
                    </a>
                </div>

            </div>
        </form>
    </div>

    <!-- ========================================================================= -->
    <!-- 1. ORDER / SALES REPORT VIEW                                              -->
    <!-- ========================================================================= -->
    @if($reportType === 'sales')
    <div class="space-y-6">

        <!-- Sales KPI Summary Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
            
            <div class="bg-white dark:bg-slate-900 rounded-2xl p-5 border border-slate-200 dark:border-slate-800 shadow-sm space-y-1">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Orders</span>
                <div class="text-2xl font-black text-slate-900 dark:text-white code-font mt-2">{{ number_format($summary['total_orders'] ?? 0) }}</div>
                <div class="text-xs text-slate-400">Within selected range</div>
            </div>

            <div class="bg-white dark:bg-slate-900 rounded-2xl p-5 border border-slate-200 dark:border-slate-800 shadow-sm space-y-1">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Gross Sales Value</span>
                <div class="text-2xl font-black text-emerald-500 code-font mt-2">৳{{ number_format($summary['total_revenue'] ?? 0, 2) }}</div>
                <div class="text-xs text-slate-400">Total invoice amounts</div>
            </div>

            <div class="bg-white dark:bg-slate-900 rounded-2xl p-5 border border-slate-200 dark:border-slate-800 shadow-sm space-y-1">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Collected / Paid</span>
                <div class="text-2xl font-black text-sky-500 code-font mt-2">৳{{ number_format($summary['total_paid'] ?? 0, 2) }}</div>
                <div class="text-xs text-slate-400">Actual received funds</div>
            </div>

            <div class="bg-white dark:bg-slate-900 rounded-2xl p-5 border border-slate-200 dark:border-slate-800 shadow-sm space-y-1">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Coupons & Discounts</span>
                <div class="text-2xl font-black text-amber-500 code-font mt-2">৳{{ number_format($summary['total_discount'] ?? 0, 2) }}</div>
                <div class="text-xs text-slate-400">Promotions deducted</div>
            </div>

            <div class="bg-white dark:bg-slate-900 rounded-2xl p-5 border border-slate-200 dark:border-slate-800 shadow-sm space-y-1">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Avg Order Value (AOV)</span>
                <div class="text-2xl font-black text-purple-500 code-font mt-2">৳{{ number_format($summary['avg_order_value'] ?? 0, 2) }}</div>
                <div class="text-xs text-slate-400">Average ticket size</div>
            </div>

        </div>

        <!-- Orders Table -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
            <div class="p-4 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                        <i data-lucide="shopping-bag" class="w-4 h-4 text-emerald-500"></i>
                        <span>Order Transactions Ledger</span>
                    </h3>
                    <p class="text-xs text-slate-500">Showing all registered customer orders between {{ $startDate }} and {{ $endDate }}</p>
                </div>
                <span class="px-3 py-1 rounded-full bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 text-xs font-bold">
                    {{ count($data) }} Records Found
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 dark:bg-slate-800/50 text-slate-500 font-bold uppercase">
                        <tr>
                            <th class="p-3">Order No</th>
                            <th class="p-3">Date & Time</th>
                            <th class="p-3">Customer</th>
                            <th class="p-3">Payment</th>
                            <th class="p-3">Status</th>
                            <th class="p-3">Items</th>
                            <th class="p-3 text-right">Grand Total</th>
                            <th class="p-3 text-center print:hidden">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @forelse($data as $order)
                        <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition">
                            <td class="p-3">
                                <a href="{{ route('admin.orders.show', $order->id) }}" class="font-mono font-bold text-emerald-600 dark:text-emerald-400 hover:underline">
                                    {{ $order->order_no ?? ('#' . $order->id) }}
                                </a>
                            </td>
                            <td class="p-3 text-slate-500 whitespace-nowrap">
                                {{ $order->created_at->format('M d, Y h:i A') }}
                            </td>
                            <td class="p-3">
                                <div class="font-bold text-slate-900 dark:text-white">{{ $order->customer_name ?? $order->customer?->name ?? 'Guest User' }}</div>
                                <div class="text-[11px] text-slate-400 font-mono">{{ $order->customer_phone ?? $order->customer?->phone ?? '-' }}</div>
                            </td>
                            <td class="p-3">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase {{ $order->payment_status === 'paid' ? 'bg-emerald-100 dark:bg-emerald-950/50 text-emerald-600' : 'bg-amber-100 dark:bg-amber-950/50 text-amber-600' }}">
                                    {{ $order->payment_method ?? 'COD' }} ({{ $order->payment_status ?? 'unpaid' }})
                                </span>
                            </td>
                            <td class="p-3">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase 
                                    {{ match($order->status) {
                                        'completed', 'delivered' => 'bg-emerald-100 dark:bg-emerald-950/50 text-emerald-600',
                                        'processing' => 'bg-sky-100 dark:bg-sky-950/50 text-sky-600',
                                        'pending' => 'bg-amber-100 dark:bg-amber-950/50 text-amber-600',
                                        'cancelled' => 'bg-rose-100 dark:bg-rose-950/50 text-rose-600',
                                        default => 'bg-slate-100 dark:bg-slate-800 text-slate-600'
                                    } }}">
                                    {{ $order->status }}
                                </span>
                            </td>
                            <td class="p-3 text-slate-600 dark:text-slate-400">
                                {{ $order->items ? $order->items->count() : 0 }} items
                            </td>
                            <td class="p-3 text-right font-black code-font text-slate-900 dark:text-white whitespace-nowrap">
                                ৳{{ number_format($order->grand_total, 2) }}
                            </td>
                            <td class="p-3 text-center print:hidden">
                                <a href="{{ route('admin.orders.show', $order->id) }}" class="inline-flex p-1.5 rounded-lg text-slate-400 hover:text-emerald-500 hover:bg-slate-100 dark:hover:bg-slate-800 transition" title="View Order">
                                    <i data-lucide="eye" class="w-4 h-4"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="p-8 text-center text-slate-400">
                                <i data-lucide="inbox" class="w-8 h-8 mx-auto mb-2 opacity-40"></i>
                                <p>No orders found matching the selected period and criteria.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                    @if(count($data) > 0)
                    <tfoot class="bg-slate-50 dark:bg-slate-800/80 font-bold border-t-2 border-slate-200 dark:border-slate-700">
                        <tr>
                            <td colspan="6" class="p-3 text-slate-900 dark:text-white uppercase tracking-wider text-right">Filtered Total Sales:</td>
                            <td class="p-3 text-right font-black code-font text-emerald-600 dark:text-emerald-400 text-sm">৳{{ number_format($summary['total_revenue'] ?? 0, 2) }}</td>
                            <td class="print:hidden"></td>
                        </tr>
                    </tfoot>
                    @endif
                </table>
            </div>
        </div>

    </div>
    @endif

    <!-- ========================================================================= -->
    <!-- 2. PURCHASE / PROCUREMENT REPORT VIEW                                     -->
    <!-- ========================================================================= -->
    @if($reportType === 'purchases')
    <div class="space-y-6">

        <!-- Purchase KPI Summary Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            
            <div class="bg-white dark:bg-slate-900 rounded-2xl p-5 border border-slate-200 dark:border-slate-800 shadow-sm space-y-1">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Purchases</span>
                <div class="text-2xl font-black text-slate-900 dark:text-white code-font mt-2">{{ number_format($summary['total_purchases'] ?? 0) }}</div>
                <div class="text-xs text-slate-400">Procurement batches</div>
            </div>

            <div class="bg-white dark:bg-slate-900 rounded-2xl p-5 border border-slate-200 dark:border-slate-800 shadow-sm space-y-1">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Purchase Cost</span>
                <div class="text-2xl font-black text-rose-500 code-font mt-2">৳{{ number_format($summary['total_cost'] ?? 0, 2) }}</div>
                <div class="text-xs text-slate-400">Total cost invoiced</div>
            </div>

            <div class="bg-white dark:bg-slate-900 rounded-2xl p-5 border border-slate-200 dark:border-slate-800 shadow-sm space-y-1">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Paid to Suppliers</span>
                <div class="text-2xl font-black text-emerald-500 code-font mt-2">৳{{ number_format($summary['total_paid'] ?? 0, 2) }}</div>
                <div class="text-xs text-slate-400">Cleared procurement funds</div>
            </div>

            <div class="bg-white dark:bg-slate-900 rounded-2xl p-5 border border-slate-200 dark:border-slate-800 shadow-sm space-y-1">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Outstanding Supplier Due</span>
                <div class="text-2xl font-black text-amber-500 code-font mt-2">৳{{ number_format($summary['total_due'] ?? 0, 2) }}</div>
                <div class="text-xs text-slate-400">Unsettled supplier balance</div>
            </div>

        </div>

        <!-- Purchases Table -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
            <div class="p-4 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                        <i data-lucide="truck" class="w-4 h-4 text-emerald-500"></i>
                        <span>Supplier Purchases & Stock Inward Ledger</span>
                    </h3>
                    <p class="text-xs text-slate-500">Recorded inventory procurement batches between {{ $startDate }} and {{ $endDate }}</p>
                </div>
                <span class="px-3 py-1 rounded-full bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 text-xs font-bold">
                    {{ count($data) }} Invoices
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 dark:bg-slate-800/50 text-slate-500 font-bold uppercase">
                        <tr>
                            <th class="p-3">Invoice / Ref</th>
                            <th class="p-3">Purchase Date</th>
                            <th class="p-3">Supplier Name</th>
                            <th class="p-3">Status</th>
                            <th class="p-3">Payment Status</th>
                            <th class="p-3 text-right">Total Amount</th>
                            <th class="p-3 text-right">Paid</th>
                            <th class="p-3 text-right">Due</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @forelse($data as $purchase)
                        <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition">
                            <td class="p-3 font-mono font-bold text-slate-900 dark:text-white">
                                {{ $purchase->invoice_no ?? $purchase->reference_no ?? ('PUR-#' . $purchase->id) }}
                            </td>
                            <td class="p-3 text-slate-500 whitespace-nowrap">
                                {{ \Carbon\Carbon::parse($purchase->purchase_date)->format('M d, Y') }}
                            </td>
                            <td class="p-3">
                                <div class="font-bold text-slate-900 dark:text-white">{{ $purchase->supplier?->name ?? 'Direct Import' }}</div>
                                <div class="text-[11px] text-slate-400 font-mono">{{ $purchase->supplier?->phone ?? '-' }}</div>
                            </td>
                            <td class="p-3">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase 
                                    {{ $purchase->status === 'received' ? 'bg-emerald-100 dark:bg-emerald-950/50 text-emerald-600' : 'bg-amber-100 dark:bg-amber-950/50 text-amber-600' }}">
                                    {{ $purchase->status }}
                                </span>
                            </td>
                            <td class="p-3">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase 
                                    {{ match($purchase->payment_status ?? 'paid') {
                                        'paid' => 'bg-emerald-100 dark:bg-emerald-950/50 text-emerald-600',
                                        'partial' => 'bg-sky-100 dark:bg-sky-950/50 text-sky-600',
                                        default => 'bg-rose-100 dark:bg-rose-950/50 text-rose-600'
                                    } }}">
                                    {{ $purchase->payment_status ?? 'paid' }}
                                </span>
                            </td>
                            <td class="p-3 text-right font-black code-font text-slate-900 dark:text-white">
                                ৳{{ number_format($purchase->grand_total, 2) }}
                            </td>
                            <td class="p-3 text-right font-bold code-font text-emerald-600 dark:text-emerald-400">
                                ৳{{ number_format($purchase->paid_amount, 2) }}
                            </td>
                            <td class="p-3 text-right font-bold code-font {{ $purchase->due_amount > 0 ? 'text-rose-500' : 'text-slate-400' }}">
                                ৳{{ number_format($purchase->due_amount, 2) }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="p-8 text-center text-slate-400">
                                <i data-lucide="truck" class="w-8 h-8 mx-auto mb-2 opacity-40"></i>
                                <p>No procurement records found for the selected period.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                    @if(count($data) > 0)
                    <tfoot class="bg-slate-50 dark:bg-slate-800/80 font-bold border-t-2 border-slate-200 dark:border-slate-700">
                        <tr>
                            <td colspan="5" class="p-3 text-slate-900 dark:text-white uppercase tracking-wider text-right">Totals:</td>
                            <td class="p-3 text-right font-black code-font text-rose-500 text-sm">৳{{ number_format($summary['total_cost'] ?? 0, 2) }}</td>
                            <td class="p-3 text-right font-black code-font text-emerald-500 text-sm">৳{{ number_format($summary['total_paid'] ?? 0, 2) }}</td>
                            <td class="p-3 text-right font-black code-font text-amber-500 text-sm">৳{{ number_format($summary['total_due'] ?? 0, 2) }}</td>
                        </tr>
                    </tfoot>
                    @endif
                </table>
            </div>
        </div>

    </div>
    @endif

    <!-- ========================================================================= -->
    <!-- 3. EXPENSE REPORT VIEW                                                    -->
    <!-- ========================================================================= -->
    @if($reportType === 'expenses')
    <div class="space-y-6">

        <!-- Expense KPI Summary Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            
            <div class="bg-white dark:bg-slate-900 rounded-2xl p-5 border border-slate-200 dark:border-slate-800 shadow-sm space-y-1">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Operating Expenses</span>
                <div class="text-2xl font-black text-rose-500 code-font mt-2">৳{{ number_format($summary['total_expenses'] ?? 0, 2) }}</div>
                <div class="text-xs text-slate-400">Electricity, rent, logistics, wages</div>
            </div>

            <div class="bg-white dark:bg-slate-900 rounded-2xl p-5 border border-slate-200 dark:border-slate-800 shadow-sm space-y-1">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Expense Entries</span>
                <div class="text-2xl font-black text-slate-900 dark:text-white code-font mt-2">{{ number_format($summary['total_count'] ?? 0) }}</div>
                <div class="text-xs text-slate-400">Total expense vouchers</div>
            </div>

            <div class="bg-white dark:bg-slate-900 rounded-2xl p-5 border border-slate-200 dark:border-slate-800 shadow-sm space-y-1">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Average per Expense</span>
                <div class="text-2xl font-black text-sky-500 code-font mt-2">৳{{ number_format($summary['avg_expense'] ?? 0, 2) }}</div>
                <div class="text-xs text-slate-400">Average cost per transaction</div>
            </div>

        </div>

        <!-- Expenses Table -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
            <div class="p-4 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                        <i data-lucide="receipt" class="w-4 h-4 text-emerald-500"></i>
                        <span>Operating Expense Vouchers Ledger</span>
                    </h3>
                    <p class="text-xs text-slate-500">All registered expenditures recorded between {{ $startDate }} and {{ $endDate }}</p>
                </div>
                <span class="px-3 py-1 rounded-full bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 text-xs font-bold">
                    {{ count($data) }} Vouchers
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 dark:bg-slate-800/50 text-slate-500 font-bold uppercase">
                        <tr>
                            <th class="p-3">Date</th>
                            <th class="p-3">Expense Category</th>
                            <th class="p-3">Payment Account</th>
                            <th class="p-3">Reference / Voucher</th>
                            <th class="p-3">Note / Description</th>
                            <th class="p-3 text-right">Amount</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @forelse($data as $expense)
                        <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition">
                            <td class="p-3 text-slate-500 whitespace-nowrap">
                                {{ \Carbon\Carbon::parse($expense->expense_date)->format('M d, Y') }}
                            </td>
                            <td class="p-3">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300">
                                    {{ $expense->category?->name ?? 'General Expense' }}
                                </span>
                            </td>
                            <td class="p-3 font-semibold text-slate-800 dark:text-slate-200">
                                {{ $expense->account?->name ?? 'Main Cash / Bank' }}
                            </td>
                            <td class="p-3 font-mono text-slate-500">
                                {{ $expense->reference_no ?? ('EXP-#' . $expense->id) }}
                            </td>
                            <td class="p-3 text-slate-600 dark:text-slate-400 max-w-xs truncate">
                                {{ $expense->note ?? '-' }}
                            </td>
                            <td class="p-3 text-right font-black code-font text-rose-500 text-sm whitespace-nowrap">
                                ৳{{ number_format($expense->amount, 2) }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="p-8 text-center text-slate-400">
                                <i data-lucide="receipt" class="w-8 h-8 mx-auto mb-2 opacity-40"></i>
                                <p>No expense vouchers found for the selected period.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                    @if(count($data) > 0)
                    <tfoot class="bg-slate-50 dark:bg-slate-800/80 font-bold border-t-2 border-slate-200 dark:border-slate-700">
                        <tr>
                            <td colspan="5" class="p-3 text-slate-900 dark:text-white uppercase tracking-wider text-right">Total Operating Expenses:</td>
                            <td class="p-3 text-right font-black code-font text-rose-500 text-sm">৳{{ number_format($summary['total_expenses'] ?? 0, 2) }}</td>
                        </tr>
                    </tfoot>
                    @endif
                </table>
            </div>
        </div>

    </div>
    @endif

    <!-- ========================================================================= -->
    <!-- 4. STOCK / INVENTORY REPORT VIEW                                          -->
    <!-- ========================================================================= -->
    @if($reportType === 'stock')
    <div class="space-y-6">

        <!-- Stock KPI Summary Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            
            <div class="bg-white dark:bg-slate-900 rounded-2xl p-5 border border-slate-200 dark:border-slate-800 shadow-sm space-y-1">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Catalog SKUs & Units</span>
                <div class="text-2xl font-black text-slate-900 dark:text-white code-font mt-2">{{ number_format($summary['total_products'] ?? 0) }} SKUs</div>
                <div class="text-xs text-slate-400">{{ number_format($summary['total_units'] ?? 0) }} total physical units in warehouse</div>
            </div>

            <div class="bg-white dark:bg-slate-900 rounded-2xl p-5 border border-slate-200 dark:border-slate-800 shadow-sm space-y-1">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Inventory Cost Valuation</span>
                <div class="text-2xl font-black text-slate-900 dark:text-white code-font mt-2">৳{{ number_format($summary['cost_value'] ?? 0, 2) }}</div>
                <div class="text-xs text-slate-400">Total invested asset cost</div>
            </div>

            <div class="bg-white dark:bg-slate-900 rounded-2xl p-5 border border-slate-200 dark:border-slate-800 shadow-sm space-y-1">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Potential Retail Valuation</span>
                <div class="text-2xl font-black text-emerald-500 code-font mt-2">৳{{ number_format($summary['retail_value'] ?? 0, 2) }}</div>
                <div class="text-xs text-slate-400">Gross revenue potential</div>
            </div>

            <div class="bg-white dark:bg-slate-900 rounded-2xl p-5 border border-slate-200 dark:border-slate-800 shadow-sm space-y-1">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Stock Health Alerts</span>
                <div class="flex items-center gap-3 mt-2">
                    <span class="text-base font-black code-font text-amber-500">{{ $summary['low_stock_count'] ?? 0 }} Low</span>
                    <span class="text-slate-300 dark:text-slate-700">&bull;</span>
                    <span class="text-base font-black code-font text-rose-500">{{ $summary['out_of_stock_count'] ?? 0 }} Out of Stock</span>
                </div>
                <div class="text-xs text-slate-400">Restock procurement needed</div>
            </div>

        </div>

        <!-- Stock Table -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
            <div class="p-4 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                        <i data-lucide="boxes" class="w-4 h-4 text-emerald-500"></i>
                        <span>Live Stock & Inventory Valuation Report</span>
                    </h3>
                    <p class="text-xs text-slate-500">Real-time stock on hand, individual component cost vs selling valuation</p>
                </div>
                <span class="px-3 py-1 rounded-full bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 text-xs font-bold">
                    {{ count($data) }} Products Listed
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 dark:bg-slate-800/50 text-slate-500 font-bold uppercase">
                        <tr>
                            <th class="p-3">Product Info</th>
                            <th class="p-3">SKU & Code</th>
                            <th class="p-3">Category</th>
                            <th class="p-3 text-right">Cost Price</th>
                            <th class="p-3 text-right">Selling Price</th>
                            <th class="p-3 text-center">Available Stock</th>
                            <th class="p-3 text-right">Total Cost Value</th>
                            <th class="p-3 text-right">Potential Retail</th>
                            <th class="p-3 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @forelse($data as $product)
                        <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition">
                            <td class="p-3">
                                <div class="flex items-center gap-3">
                                    <img src="{{ $product->thumbnail ?? 'https://placehold.co/80x80/1e293b/ffffff?text=PCB' }}" 
                                         alt="{{ $product->name }}" 
                                         class="w-9 h-9 rounded-lg object-cover border border-slate-200 dark:border-slate-700">
                                    <div>
                                        <a href="{{ route('admin.products.edit', $product->id) }}" class="font-bold text-slate-900 dark:text-white hover:text-emerald-500 transition line-clamp-1">
                                            {{ $product->name }}
                                        </a>
                                        <span class="text-[10px] text-slate-400 font-mono">{{ $product->brand?->name ?? 'Generic' }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="p-3 font-mono text-slate-500 font-semibold">
                                {{ $product->sku ?? ('SKU-' . $product->id) }}
                            </td>
                            <td class="p-3 text-slate-600 dark:text-slate-400">
                                {{ $product->category?->name ?? 'Uncategorized' }}
                            </td>
                            <td class="p-3 text-right font-semibold code-font text-slate-700 dark:text-slate-300">
                                ৳{{ number_format($product->cost_price, 2) }}
                            </td>
                            <td class="p-3 text-right font-bold code-font text-emerald-600 dark:text-emerald-400">
                                ৳{{ number_format($product->selling_price, 2) }}
                            </td>
                            <td class="p-3 text-center">
                                <span class="font-black code-font text-sm {{ $product->stock_qty <= 0 ? 'text-rose-500' : ($product->stock_qty <= 10 ? 'text-amber-500' : 'text-slate-900 dark:text-white') }}">
                                    {{ $product->stock_qty }}
                                </span>
                            </td>
                            <td class="p-3 text-right font-bold code-font text-slate-900 dark:text-white">
                                ৳{{ number_format($product->cost_price * $product->stock_qty, 2) }}
                            </td>
                            <td class="p-3 text-right font-bold code-font text-emerald-600 dark:text-emerald-400">
                                ৳{{ number_format($product->selling_price * $product->stock_qty, 2) }}
                            </td>
                            <td class="p-3 text-center">
                                @if($product->stock_qty <= 0)
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase bg-rose-100 dark:bg-rose-950/50 text-rose-600">Out of Stock</span>
                                @elseif($product->stock_qty <= 10)
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase bg-amber-100 dark:bg-amber-950/50 text-amber-600">Low Stock</span>
                                @else
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase bg-emerald-100 dark:bg-emerald-950/50 text-emerald-600">In Stock</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="p-8 text-center text-slate-400">
                                <i data-lucide="boxes" class="w-8 h-8 mx-auto mb-2 opacity-40"></i>
                                <p>No inventory records found.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                    @if(count($data) > 0)
                    <tfoot class="bg-slate-50 dark:bg-slate-800/80 font-bold border-t-2 border-slate-200 dark:border-slate-700">
                        <tr>
                            <td colspan="5" class="p-3 text-slate-900 dark:text-white uppercase tracking-wider text-right">Catalog Valuations:</td>
                            <td class="p-3 text-center font-black code-font text-sm">{{ number_format($summary['total_units'] ?? 0) }} pcs</td>
                            <td class="p-3 text-right font-black code-font text-slate-900 dark:text-white text-sm">৳{{ number_format($summary['cost_value'] ?? 0, 2) }}</td>
                            <td class="p-3 text-right font-black code-font text-emerald-600 dark:text-emerald-400 text-sm">৳{{ number_format($summary['retail_value'] ?? 0, 2) }}</td>
                            <td></td>
                        </tr>
                    </tfoot>
                    @endif
                </table>
            </div>
        </div>

    </div>
    @endif

    <!-- ========================================================================= -->
    <!-- 5. PROFIT & LOSS (P&L) STATEMENT VIEW                                     -->
    <!-- ========================================================================= -->
    @if($reportType === 'profit_loss')
    <div class="space-y-6">

        <!-- Financial P&L Executive Summary Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            
            <!-- Gross Sales Revenue -->
            <div class="bg-white dark:bg-slate-900 rounded-2xl p-5 border border-slate-200 dark:border-slate-800 shadow-sm space-y-1">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Gross Sales Revenue</span>
                <div class="text-2xl font-black text-slate-900 dark:text-white code-font mt-2">৳{{ number_format($summary['sales_total'] ?? $financials['total_revenue'], 2) }}</div>
                <div class="text-xs text-slate-400">Total active orders revenue</div>
            </div>

            <!-- Cost of Goods Sold (COGS) -->
            <div class="bg-white dark:bg-slate-900 rounded-2xl p-5 border border-slate-200 dark:border-slate-800 shadow-sm space-y-1">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Cost of Goods (COGS)</span>
                <div class="text-2xl font-black text-rose-500 code-font mt-2">৳{{ number_format($summary['cogs_total'] ?? $financials['total_cogs'], 2) }}</div>
                <div class="text-xs text-slate-400">Inventory cost of sold products</div>
            </div>

            <!-- Gross Profit -->
            <div class="bg-white dark:bg-slate-900 rounded-2xl p-5 border border-slate-200 dark:border-slate-800 shadow-sm space-y-1">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Gross Profit</span>
                    <span class="text-[11px] font-black px-2 py-0.5 rounded-md bg-sky-50 text-sky-600 dark:bg-sky-950/40 dark:text-sky-400">{{ $summary['gross_margin'] ?? 0 }}% Margin</span>
                </div>
                <div class="text-2xl font-black text-sky-500 code-font mt-2">৳{{ number_format($summary['gross_profit'] ?? $financials['gross_profit'], 2) }}</div>
                <div class="text-xs text-slate-400">Revenue minus COGS</div>
            </div>

            <!-- Net Profit (After Expenses) -->
            <div class="bg-gradient-to-br from-emerald-600 to-teal-700 text-white rounded-2xl p-5 shadow-lg shadow-emerald-600/20 space-y-1">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold uppercase tracking-wider text-emerald-100">Net Business Profit</span>
                    <span class="text-[11px] font-black px-2 py-0.5 rounded-md bg-white/20 text-white">{{ $summary['net_margin'] ?? 0 }}% Net</span>
                </div>
                <div class="text-2xl font-black code-font mt-2">৳{{ number_format($summary['net_profit'] ?? $financials['net_profit'], 2) }}</div>
                <div class="text-xs text-emerald-100/90">After operating expenses (৳{{ number_format($summary['expense_total'] ?? $financials['total_expenses'], 2) }})</div>
            </div>

        </div>

        <!-- Detailed P&L Income Statement Breakdown Table -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
            <div class="p-4 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                        <i data-lucide="file-spreadsheet" class="w-4 h-4 text-emerald-500"></i>
                        <span>Executive Profit & Loss Statement ({{ $startDate }} to {{ $endDate }})</span>
                    </h3>
                    <p class="text-xs text-slate-500">Accounting breakdown of revenues, cost of goods, and operational expenses</p>
                </div>
                <button onclick="window.print()" class="px-3 py-1 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 text-xs font-bold transition print:hidden">
                    Print Statement
                </button>
            </div>

            <table class="w-full text-left text-xs">
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    
                    <!-- 1. Revenue -->
                    <tr class="bg-slate-50 dark:bg-slate-800/40">
                        <td class="p-3 font-bold uppercase tracking-wider text-slate-900 dark:text-white" colspan="2">1. Operating Revenues</td>
                        <td class="p-3 text-right font-bold text-slate-900 dark:text-white">Amount (৳)</td>
                    </tr>
                    <tr>
                        <td class="p-3 pl-6 text-slate-600 dark:text-slate-400">Total Customer Orders & Deliveries</td>
                        <td class="p-3 text-slate-400">Direct sales invoicing</td>
                        <td class="p-3 text-right font-bold code-font text-emerald-600 dark:text-emerald-400">
                            +৳{{ number_format($summary['sales_total'] ?? $financials['total_revenue'], 2) }}
                        </td>
                    </tr>
                    <tr class="bg-emerald-50/40 dark:bg-emerald-950/20 font-bold">
                        <td class="p-3 pl-6 text-slate-900 dark:text-white">Gross Operating Revenue</td>
                        <td></td>
                        <td class="p-3 text-right font-black code-font text-emerald-600 dark:text-emerald-400">
                            ৳{{ number_format($summary['sales_total'] ?? $financials['total_revenue'], 2) }}
                        </td>
                    </tr>

                    <!-- 2. Cost of Sales -->
                    <tr class="bg-slate-50 dark:bg-slate-800/40">
                        <td class="p-3 font-bold uppercase tracking-wider text-slate-900 dark:text-white" colspan="2">2. Cost of Goods Sold (COGS)</td>
                        <td class="p-3 text-right font-bold text-slate-900 dark:text-white">Amount (৳)</td>
                    </tr>
                    <tr>
                        <td class="p-3 pl-6 text-slate-600 dark:text-slate-400">Raw Component & Hardware Purchase Cost of Delivered Items</td>
                        <td class="p-3 text-slate-400">Direct inventory acquisition cost</td>
                        <td class="p-3 text-right font-bold code-font text-rose-500">
                            -৳{{ number_format($summary['cogs_total'] ?? $financials['total_cogs'], 2) }}
                        </td>
                    </tr>
                    <tr class="bg-sky-50/40 dark:bg-sky-950/20 font-bold">
                        <td class="p-3 pl-6 text-slate-900 dark:text-white">Gross Profit (Revenue - COGS)</td>
                        <td class="p-3 text-slate-400">{{ $summary['gross_margin'] ?? 0 }}% Gross Margin</td>
                        <td class="p-3 text-right font-black code-font text-sky-500">
                            ৳{{ number_format($summary['gross_profit'] ?? $financials['gross_profit'], 2) }}
                        </td>
                    </tr>

                    <!-- 3. Operating Expenses -->
                    <tr class="bg-slate-50 dark:bg-slate-800/40">
                        <td class="p-3 font-bold uppercase tracking-wider text-slate-900 dark:text-white" colspan="2">3. Operational & Overhead Expenses</td>
                        <td class="p-3 text-right font-bold text-slate-900 dark:text-white">Amount (৳)</td>
                    </tr>
                    <tr>
                        <td class="p-3 pl-6 text-slate-600 dark:text-slate-400">Facility Rent, Utilities, Courier, Logistics & Sundry</td>
                        <td class="p-3 text-slate-400">Operating overheads recorded in expense ledger</td>
                        <td class="p-3 text-right font-bold code-font text-rose-500">
                            -৳{{ number_format($summary['expense_total'] ?? $financials['total_expenses'], 2) }}
                        </td>
                    </tr>

                    <!-- 4. Net Bottom Line Profit -->
                    <tr class="bg-slate-900 text-white font-bold">
                        <td class="p-4 pl-6 text-sm uppercase tracking-wider">Net Business Operating Profit</td>
                        <td class="p-4 text-emerald-400 text-xs font-normal">{{ $summary['net_margin'] ?? 0 }}% Net Profit Margin</td>
                        <td class="p-4 text-right font-black code-font text-base text-emerald-400">
                            ৳{{ number_format($summary['net_profit'] ?? $financials['net_profit'], 2) }}
                        </td>
                    </tr>

                </tbody>
            </table>
        </div>

        <!-- 12-Month Trend Comparison Chart -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 border border-slate-200 dark:border-slate-800 shadow-sm space-y-4 print:hidden">
            <div class="flex items-center justify-between">
                <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                    <i data-lucide="bar-chart-3" class="w-4 h-4 text-emerald-500"></i>
                    <span>12-Month Sales Revenue vs Procurement Purchases Comparison</span>
                </h3>
            </div>
            <div id="annualComparisonChart" class="h-80"></div>
        </div>

        <!-- Top Selling Products Breakdown -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
            <div class="p-4 border-b border-slate-200 dark:border-slate-800 font-bold text-sm text-slate-900 dark:text-white flex items-center justify-between">
                <span>Top Performing PCB & Electronics Components (Highest Margin)</span>
                <span class="text-xs text-slate-400 font-normal">Top 10 items</span>
            </div>
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 dark:bg-slate-800/50 text-slate-500 font-bold uppercase">
                    <tr>
                        <th class="p-3">Rank & Product Name</th>
                        <th class="p-3">Units Sold</th>
                        <th class="p-3">Total Sales Revenue</th>
                        <th class="p-3">Gross Profit Generated</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($topProducts as $idx => $prod)
                    <tr>
                        <td class="p-3">
                            <div class="flex items-center gap-3">
                                <span class="w-6 h-6 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-bold flex items-center justify-center text-[10px]">#{{ $idx + 1 }}</span>
                                <span class="font-bold text-slate-900 dark:text-white text-xs">{{ $prod->name }}</span>
                            </div>
                        </td>
                        <td class="p-3 font-bold code-font text-slate-800 dark:text-slate-200">{{ $prod->total_qty_sold }} units</td>
                        <td class="p-3 font-bold code-font text-emerald-600 dark:text-emerald-400">৳{{ number_format($prod->total_revenue, 2) }}</td>
                        <td class="p-3 font-bold code-font text-sky-500">৳{{ number_format($prod->total_profit, 2) }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="p-6 text-center text-slate-400">No sales recorded yet.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>
    @endif

</div>
@endsection

@push('scripts')
<script>
    function setDateRange(period) {
        const today = new Date();
        const formatDate = (d) => d.toISOString().split('T')[0];
        
        let start = new Date();
        let end = new Date();

        if (period === 'today') {
            start = today;
            end = today;
        } else if (period === 'week') {
            const day = today.getDay() || 7;
            start.setDate(today.getDate() - day + 1);
            end = today;
        } else if (period === 'month') {
            start = new Date(today.getFullYear(), today.getMonth(), 1);
            end = today;
        } else if (period === 'last_month') {
            start = new Date(today.getFullYear(), today.getMonth() - 1, 1);
            end = new Date(today.getFullYear(), today.getMonth(), 0);
        } else if (period === 'year') {
            start = new Date(today.getFullYear(), 0, 1);
            end = today;
        }

        const startInput = document.getElementById('start_date');
        const endInput = document.getElementById('end_date');
        if (startInput && endInput) {
            startInput.value = formatDate(start);
            endInput.value = formatDate(end);
            document.getElementById('reportFilterForm').submit();
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        const chartEl = document.querySelector("#annualComparisonChart");
        if (chartEl && typeof ApexCharts !== 'undefined') {
            const months = @json($monthlyChart['months'] ?? []);
            const sales = @json($monthlyChart['sales'] ?? []);
            const purchases = @json($monthlyChart['purchases'] ?? []);

            const options = {
                series: [
                    { name: 'Sales Revenue', data: sales },
                    { name: 'Procurement Purchases', data: purchases }
                ],
                chart: {
                    type: 'bar',
                    height: 320,
                    toolbar: { show: false },
                    background: 'transparent'
                },
                colors: ['#10b981', '#f43f5e'],
                plotOptions: {
                    bar: {
                        horizontal: false,
                        columnWidth: '50%',
                        borderRadius: 6
                    }
                },
                dataLabels: { enabled: false },
                xaxis: {
                    categories: months,
                    labels: { style: { colors: '#94a3b8', fontSize: '11px' } }
                },
                yaxis: {
                    labels: {
                        formatter: (val) => '৳' + Number(val).toLocaleString(),
                        style: { colors: '#94a3b8', fontSize: '11px' }
                    }
                },
                grid: { borderColor: 'rgba(148, 163, 184, 0.1)' },
                legend: { position: 'top', labels: { colors: '#94a3b8' } },
                theme: { mode: document.documentElement.classList.contains('dark') ? 'dark' : 'light' }
            };

            const chart = new ApexCharts(chartEl, options);
            chart.render();
        }
    });
</script>
@endpush
