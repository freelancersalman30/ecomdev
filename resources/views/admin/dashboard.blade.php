@extends('layouts.admin')

@section('title', 'Dashboard Overview')
@section('page-title', 'Dashboard Overview')

@section('content')
<div class="space-y-6">

    <!-- KPI Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
        
        <!-- 1. Today's Sales -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl p-5 border border-slate-200 dark:border-slate-800 shadow-sm relative overflow-hidden group hover:border-emerald-500/50 transition">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Today's Sales</span>
                <div class="w-9 h-9 rounded-xl bg-emerald-500/10 text-emerald-500 flex items-center justify-center">
                    <i data-lucide="trending-up" class="w-5 h-5"></i>
                </div>
            </div>
            <div class="mt-3">
                <div class="text-2xl font-black text-slate-900 dark:text-white code-font">৳{{ number_format($kpis['today_sales'], 2) }}</div>
                <div class="text-xs text-slate-500 mt-1">{{ $kpis['today_orders_count'] }} orders today</div>
            </div>
            <div class="absolute bottom-0 inset-x-0 h-1 bg-emerald-500 opacity-60"></div>
        </div>

        <!-- 2. Available Fund Balance -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl p-5 border border-slate-200 dark:border-slate-800 shadow-sm relative overflow-hidden group hover:border-teal-500/50 transition">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Available Funds</span>
                <div class="w-9 h-9 rounded-xl bg-teal-500/10 text-teal-500 flex items-center justify-center">
                    <i data-lucide="wallet" class="w-5 h-5"></i>
                </div>
            </div>
            <div class="mt-3">
                <div class="text-2xl font-black text-teal-600 dark:text-teal-400 code-font">৳{{ number_format($kpis['available_funds'], 2) }}</div>
                <div class="text-xs text-slate-500 mt-1">Cash & Bank Accounts</div>
            </div>
            <div class="absolute bottom-0 inset-x-0 h-1 bg-teal-500 opacity-60"></div>
        </div>

        <!-- 3. Total Stock Value -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl p-5 border border-slate-200 dark:border-slate-800 shadow-sm relative overflow-hidden group hover:border-sky-500/50 transition">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Stock Valuation</span>
                <div class="w-9 h-9 rounded-xl bg-sky-500/10 text-sky-500 flex items-center justify-center">
                    <i data-lucide="package" class="w-5 h-5"></i>
                </div>
            </div>
            <div class="mt-3">
                <div class="text-2xl font-black text-slate-900 dark:text-white code-font">৳{{ number_format($kpis['stock_cost_value'], 2) }}</div>
                <div class="text-xs text-slate-500 mt-1">{{ number_format($kpis['total_items_in_stock']) }} items in inventory</div>
            </div>
            <div class="absolute bottom-0 inset-x-0 h-1 bg-sky-500 opacity-60"></div>
        </div>

        <!-- 4. Net Profit / Performance -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl p-5 border border-slate-200 dark:border-slate-800 shadow-sm relative overflow-hidden group hover:border-purple-500/50 transition">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Net Profit Ledger</span>
                <div class="w-9 h-9 rounded-xl bg-purple-500/10 text-purple-500 flex items-center justify-center">
                    <i data-lucide="dollar-sign" class="w-5 h-5"></i>
                </div>
            </div>
            <div class="mt-3">
                <div class="text-2xl font-black text-purple-600 dark:text-purple-400 code-font">৳{{ number_format($kpis['net_profit'], 2) }}</div>
                <div class="text-xs text-slate-500 mt-1">Delivered Sales - (COGS + Expenses)</div>
            </div>
            <div class="absolute bottom-0 inset-x-0 h-1 bg-purple-500 opacity-60"></div>
        </div>

        <!-- 5. Low Stock Alerts -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl p-5 border border-slate-200 dark:border-slate-800 shadow-sm relative overflow-hidden group hover:border-rose-500/50 transition">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Low Stock Alerts</span>
                <div class="w-9 h-9 rounded-xl bg-rose-500/10 text-rose-500 flex items-center justify-center">
                    <i data-lucide="alert-octagon" class="w-5 h-5"></i>
                </div>
            </div>
            <div class="mt-3">
                <div class="text-2xl font-black text-rose-600 dark:text-rose-400 code-font">{{ $kpis['low_stock_count'] }} SKUs</div>
                <div class="text-xs text-slate-500 mt-1">Below safety threshold</div>
            </div>
            <div class="absolute bottom-0 inset-x-0 h-1 bg-rose-500 opacity-60"></div>
        </div>

    </div>

    <!-- Quick Access Business Reports Bar -->
    <div class="bg-gradient-to-r from-slate-900 via-slate-800 to-slate-900 rounded-2xl p-4 sm:p-5 border border-slate-800 text-white shadow-md">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-3 pb-3 border-b border-slate-700/60">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-xl bg-emerald-500/20 text-emerald-400 flex items-center justify-center">
                    <i data-lucide="bar-chart-3" class="w-4 h-4"></i>
                </div>
                <div>
                    <h4 class="text-xs sm:text-sm font-bold">Reports & Financial Intelligence</h4>
                    <p class="text-[11px] text-slate-400">Detailed ledgers, procurement audits, inventory valuation, and P&L statements</p>
                </div>
            </div>
            <a href="{{ route('admin.reports.index') }}" class="text-xs font-bold text-emerald-400 hover:text-emerald-300 flex items-center gap-1">
                <span>Open Full Reports Hub</span>
                <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
            </a>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-2.5">
            <a href="{{ route('admin.reports.orders') }}" class="p-3 rounded-xl bg-slate-800/80 hover:bg-slate-700/80 border border-slate-700/50 hover:border-emerald-500/50 transition flex items-center gap-3 group">
                <div class="w-8 h-8 rounded-lg bg-emerald-500/20 text-emerald-400 flex items-center justify-center group-hover:scale-110 transition flex-shrink-0">
                    <i data-lucide="shopping-bag" class="w-4 h-4"></i>
                </div>
                <div>
                    <div class="text-xs font-bold group-hover:text-emerald-400 transition">Order Report</div>
                    <div class="text-[10px] text-slate-400">Sales & Invoicing</div>
                </div>
            </a>

            <a href="{{ route('admin.reports.purchases') }}" class="p-3 rounded-xl bg-slate-800/80 hover:bg-slate-700/80 border border-slate-700/50 hover:border-sky-500/50 transition flex items-center gap-3 group">
                <div class="w-8 h-8 rounded-lg bg-sky-500/20 text-sky-400 flex items-center justify-center group-hover:scale-110 transition flex-shrink-0">
                    <i data-lucide="truck" class="w-4 h-4"></i>
                </div>
                <div>
                    <div class="text-xs font-bold group-hover:text-sky-400 transition">Purchase Report</div>
                    <div class="text-[10px] text-slate-400">Procurement & Dues</div>
                </div>
            </a>

            <a href="{{ route('admin.reports.expenses') }}" class="p-3 rounded-xl bg-slate-800/80 hover:bg-slate-700/80 border border-slate-700/50 hover:border-rose-500/50 transition flex items-center gap-3 group">
                <div class="w-8 h-8 rounded-lg bg-rose-500/20 text-rose-400 flex items-center justify-center group-hover:scale-110 transition flex-shrink-0">
                    <i data-lucide="receipt" class="w-4 h-4"></i>
                </div>
                <div>
                    <div class="text-xs font-bold group-hover:text-rose-400 transition">Expense Report</div>
                    <div class="text-[10px] text-slate-400">Overhead Ledgers</div>
                </div>
            </a>

            <a href="{{ route('admin.reports.stock') }}" class="p-3 rounded-xl bg-slate-800/80 hover:bg-slate-700/80 border border-slate-700/50 hover:border-amber-500/50 transition flex items-center gap-3 group">
                <div class="w-8 h-8 rounded-lg bg-amber-500/20 text-amber-400 flex items-center justify-center group-hover:scale-110 transition flex-shrink-0">
                    <i data-lucide="boxes" class="w-4 h-4"></i>
                </div>
                <div>
                    <div class="text-xs font-bold group-hover:text-amber-400 transition">Stock Report</div>
                    <div class="text-[10px] text-slate-400">Valuation & Health</div>
                </div>
            </a>

            <a href="{{ route('admin.reports.profit_loss') }}" class="p-3 rounded-xl bg-slate-800/80 hover:bg-slate-700/80 border border-slate-700/50 hover:border-teal-400 transition flex items-center gap-3 group col-span-2 sm:col-span-1">
                <div class="w-8 h-8 rounded-lg bg-teal-400/20 text-teal-300 flex items-center justify-center group-hover:scale-110 transition flex-shrink-0">
                    <i data-lucide="trending-up" class="w-4 h-4"></i>
                </div>
                <div>
                    <div class="text-xs font-bold group-hover:text-teal-300 transition">Profit & Loss</div>
                    <div class="text-[10px] text-slate-400">P&L Statement</div>
                </div>
            </a>
        </div>
    </div>

    <!-- Charts Section: Revenue vs Purchases & Status Pipeline -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Monthly Revenue vs Purchases Chart -->
        <div class="lg:col-span-2 bg-white dark:bg-slate-900 rounded-2xl p-6 border border-slate-200 dark:border-slate-800 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h2 class="text-base font-bold text-slate-900 dark:text-white">Revenue vs. Purchases</h2>
                    <p class="text-xs text-slate-500">12-Month Financial Performance Comparison</p>
                </div>
                <div class="flex items-center gap-2 text-xs">
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 font-medium">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span> Sales
                    </span>
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-sky-500/10 text-sky-600 dark:text-sky-400 font-medium">
                        <span class="w-2 h-2 rounded-full bg-sky-500"></span> Purchases
                    </span>
                </div>
            </div>
            <div id="revenueChart" class="h-72"></div>
        </div>

        <!-- Order Pipeline Status Donut -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 border border-slate-200 dark:border-slate-800 shadow-sm flex flex-col justify-between">
            <div>
                <h2 class="text-base font-bold text-slate-900 dark:text-white mb-1">Order Pipeline Status</h2>
                <p class="text-xs text-slate-500 mb-4">Current distribution across delivery stages</p>
                <div id="statusDonutChart" class="h-56"></div>
            </div>
            
            <div class="grid grid-cols-3 gap-2 pt-4 border-t border-slate-100 dark:border-slate-800 text-center">
                <div class="p-2 rounded-xl bg-slate-50 dark:bg-slate-800/50">
                    <div class="text-xs text-slate-500">Pending</div>
                    <div class="text-base font-bold text-amber-500">{{ $statusCounts['pending'] }}</div>
                </div>
                <div class="p-2 rounded-xl bg-slate-50 dark:bg-slate-800/50">
                    <div class="text-xs text-slate-500">In Courier</div>
                    <div class="text-base font-bold text-sky-500">{{ $statusCounts['in_courier'] }}</div>
                </div>
                <div class="p-2 rounded-xl bg-slate-50 dark:bg-slate-800/50">
                    <div class="text-xs text-slate-500">Completed</div>
                    <div class="text-base font-bold text-emerald-500">{{ $statusCounts['completed'] }}</div>
                </div>
            </div>
        </div>

    </div>

    <!-- Recent Orders & Low Stock Table -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Recent Orders (2 Columns) -->
        <div class="lg:col-span-2 bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
            <div class="p-5 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between">
                <div>
                    <h2 class="text-base font-bold text-slate-900 dark:text-white">Recent Orders</h2>
                    <p class="text-xs text-slate-500">Latest customer & POS transactions</p>
                </div>
                <a href="{{ route('admin.orders.index') }}" class="text-xs font-semibold text-emerald-600 dark:text-emerald-400 hover:underline">
                    View All Orders &rarr;
                </a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-slate-50 dark:bg-slate-800/50 text-slate-500 text-xs uppercase font-semibold">
                        <tr>
                            <th class="px-5 py-3">Order No</th>
                            <th class="px-5 py-3">Customer</th>
                            <th class="px-5 py-3">Amount</th>
                            <th class="px-5 py-3">Status</th>
                            <th class="px-5 py-3 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @forelse($recentOrders as $order)
                        <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition">
                            <td class="px-5 py-3.5 font-mono text-xs font-bold text-slate-900 dark:text-white">
                                <a href="{{ route('admin.orders.show', $order->id) }}" class="hover:text-emerald-500">
                                    {{ $order->order_no }}
                                </a>
                            </td>
                            <td class="px-5 py-3.5">
                                <div class="font-medium text-slate-900 dark:text-white text-xs">{{ $order->shipping_name }}</div>
                                <div class="text-[11px] text-slate-500 font-mono">{{ $order->shipping_phone }}</div>
                            </td>
                            <td class="px-5 py-3.5 font-bold code-font text-slate-900 dark:text-white">
                                ৳{{ number_format($order->grand_total, 2) }}
                            </td>
                            <td class="px-5 py-3.5">
                                @php
                                    $statusClasses = [
                                        'pending' => 'bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-300',
                                        'processing' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300',
                                        'on_the_way' => 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900/30 dark:text-indigo-300',
                                        'in_courier' => 'bg-cyan-100 text-cyan-800 dark:bg-cyan-900/30 dark:text-cyan-300',
                                        'completed' => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-300',
                                        'cancelled' => 'bg-rose-100 text-rose-800 dark:bg-rose-900/30 dark:text-rose-300',
                                        'returned' => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300',
                                    ];
                                @endphp
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider {{ $statusClasses[$order->status] ?? 'bg-slate-100 text-slate-800' }}">
                                    {{ str_replace('_', ' ', $order->status) }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5 text-right">
                                <a href="{{ route('admin.orders.show', $order->id) }}" class="p-1.5 rounded-lg text-slate-500 hover:text-emerald-500 hover:bg-slate-100 dark:hover:bg-slate-800 inline-flex transition">
                                    <i data-lucide="eye" class="w-4 h-4"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-5 py-8 text-center text-slate-400 text-xs">No orders recorded yet.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Low Stock Items (1 Column) -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden flex flex-col justify-between">
            <div class="p-5 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between">
                <div>
                    <h2 class="text-base font-bold text-slate-900 dark:text-white">Critical Stock Alert</h2>
                    <p class="text-xs text-rose-500 font-medium">Re-order required</p>
                </div>
                <a href="{{ route('admin.purchases.create') }}" class="px-2.5 py-1 rounded-lg bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-semibold">
                    + Create PO
                </a>
            </div>
            <div class="divide-y divide-slate-100 dark:divide-slate-800 flex-1">
                @forelse($lowStockProducts as $prod)
                <div class="p-4 flex items-center justify-between hover:bg-slate-50 dark:hover:bg-slate-800/30 transition">
                    <div class="flex items-center gap-3">
                        <img src="{{ $prod->thumbnail }}" alt="{{ $prod->name }}" class="w-10 h-10 rounded-lg object-cover border border-slate-200 dark:border-slate-700">
                        <div>
                            <div class="text-xs font-bold text-slate-900 dark:text-white line-clamp-1">{{ $prod->name }}</div>
                            <div class="text-[10px] text-slate-500 font-mono">{{ $prod->sku }}</div>
                        </div>
                    </div>
                    <div class="text-right">
                        <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-rose-500/10 text-rose-500 code-font">
                            {{ $prod->stock_quantity }} Left
                        </span>
                    </div>
                </div>
                @empty
                <div class="p-8 text-center text-slate-400 text-xs">All inventory levels are healthy!</div>
                @endforelse
            </div>
            <div class="p-4 bg-slate-50 dark:bg-slate-800/40 text-center border-t border-slate-200 dark:border-slate-800">
                <a href="{{ route('admin.products.index', ['stock_filter' => 'low_stock']) }}" class="text-xs font-semibold text-slate-600 dark:text-slate-300 hover:text-emerald-500">
                    View Complete Low Stock List &rarr;
                </a>
            </div>
        </div>

    </div>

    <!-- Live Operational Updates & Notifications Feed -->
    @php
        $latestNotifications = collect();
        try {
            if (\Illuminate\Support\Facades\Schema::hasTable('notifications')) {
                $latestNotifications = Auth::guard('web')->user()?->notifications()->take(6)->get() ?? collect();
            }
        } catch (\Throwable $e) {}
    @endphp
    @if($latestNotifications->isNotEmpty())
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
        <div class="p-5 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-xl bg-amber-500/15 text-amber-500 flex items-center justify-center">
                    <i data-lucide="bell-ring" class="w-4 h-4"></i>
                </div>
                <div>
                    <h2 class="text-base font-bold text-slate-900 dark:text-white">Live Activity & Operational Updates</h2>
                    <p class="text-xs text-slate-500">Real-time alerts for incoming orders, courier dispatches, and delivery completions</p>
                </div>
            </div>
            <a href="{{ route('admin.notifications.index') }}" class="text-xs font-semibold text-emerald-600 dark:text-emerald-400 hover:underline flex items-center gap-1">
                <span>Notification Hub</span>
                <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
            </a>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 divide-y md:divide-y-0 md:divide-x divide-slate-100 dark:divide-slate-800">
            @foreach($latestNotifications->take(3) as $notif)
            @php
                $nData = $notif->data;
                $nType = $nData['type'] ?? 'general';
                $nIcon = $nData['icon'] ?? 'bell';
                $badgeColors = [
                    'new_order' => 'bg-emerald-500/10 text-emerald-500',
                    'courier_assigned' => 'bg-sky-500/10 text-sky-500',
                    'in_courier' => 'bg-sky-500/10 text-sky-500',
                    'delivery_done' => 'bg-teal-500/10 text-teal-400',
                    'order_cancelled' => 'bg-rose-500/10 text-rose-500',
                    'order_returned' => 'bg-amber-500/10 text-amber-500',
                ];
                $bColor = $badgeColors[$nType] ?? 'bg-slate-500/10 text-slate-500';
            @endphp
            <div class="p-4 flex items-start gap-3 hover:bg-slate-50/60 dark:hover:bg-slate-800/30 transition">
                <div class="w-9 h-9 rounded-xl {{ $bColor }} flex items-center justify-center flex-shrink-0 mt-0.5">
                    <i data-lucide="{{ $nIcon }}" class="w-4 h-4"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center justify-between gap-1 mb-0.5">
                        <h4 class="text-xs font-bold text-slate-900 dark:text-white truncate">{{ $nData['title'] ?? 'Update' }}</h4>
                        <span class="text-[10px] text-slate-400">{{ $notif->created_at->diffForHumans() }}</span>
                    </div>
                    <p class="text-[11px] text-slate-600 dark:text-slate-300 line-clamp-2 leading-relaxed">{{ $nData['message'] ?? '' }}</p>
                    @if(!empty($nData['action_url']))
                    <a href="{{ $nData['action_url'] }}" class="inline-flex items-center gap-1 text-[11px] font-semibold text-emerald-600 dark:text-emerald-400 hover:underline mt-1.5">
                        <span>View Order</span>
                        <i data-lucide="chevron-right" class="w-3 h-3"></i>
                    </a>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        // 1. Monthly Revenue Chart
        const chartData = @json($chartData);
        const optionsRevenue = {
            series: [{
                name: 'Sales Revenue (৳)',
                data: chartData.sales
            }, {
                name: 'Purchases (৳)',
                data: chartData.purchases
            }],
            chart: {
                type: 'area',
                height: 280,
                toolbar: { show: false },
                fontFamily: 'Inter, sans-serif'
            },
            colors: ['#10b981', '#0ea5e9'],
            dataLabels: { enabled: false },
            stroke: { curve: 'smooth', width: 2.5 },
            fill: {
                type: 'gradient',
                gradient: {
                    shadeIntensity: 1,
                    opacityFrom: 0.35,
                    opacityTo: 0.05,
                    stops: [0, 95, 100]
                }
            },
            xaxis: {
                categories: chartData.categories,
                labels: { style: { colors: '#94a3b8', fontSize: '11px' } }
            },
            yaxis: {
                labels: {
                    style: { colors: '#94a3b8', fontSize: '11px' },
                    formatter: (val) => '৳' + Number(val).toLocaleString()
                }
            },
            grid: {
                borderColor: '#33415520',
                strokeDashArray: 4
            },
            tooltip: {
                theme: 'dark'
            }
        };

        const chart = new ApexCharts(document.querySelector("#revenueChart"), optionsRevenue);
        chart.render();

        // 2. Status Pipeline Donut
        const statusCounts = @json($statusCounts);
        const optionsStatus = {
            series: [
                statusCounts.pending,
                statusCounts.processing,
                statusCounts.on_the_way,
                statusCounts.in_courier,
                statusCounts.completed,
                statusCounts.cancelled
            ],
            labels: ['Pending', 'Processing', 'On The Way', 'In Courier', 'Completed', 'Cancelled'],
            chart: {
                type: 'donut',
                height: 220,
                fontFamily: 'Inter, sans-serif'
            },
            colors: ['#f59e0b', '#3b82f6', '#6366f1', '#06b6d4', '#10b981', '#f43f5e'],
            legend: { show: false },
            dataLabels: { enabled: false },
            stroke: { width: 0 }
        };

        const donutChart = new ApexCharts(document.querySelector("#statusDonutChart"), optionsStatus);
        donutChart.render();
    });
</script>
@endpush
