@extends('layouts.admin')

@section('title', 'Analytics & Financial Reports')
@section('page-title', 'Business Reports & P&L Statement')

@section('content')
<div class="space-y-6">

    <!-- Financial P&L Executive Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        
        <!-- Gross Sales Revenue -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl p-5 border border-slate-200 dark:border-slate-800 shadow-sm space-y-1">
            <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Gross Sales Revenue</span>
            <div class="text-2xl font-black text-slate-900 dark:text-white code-font mt-2">৳{{ number_format($financials['total_revenue'], 2) }}</div>
            <div class="text-xs text-slate-400">Total completed orders value</div>
        </div>

        <!-- Cost of Goods Sold (COGS) -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl p-5 border border-slate-200 dark:border-slate-800 shadow-sm space-y-1">
            <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Cost of Goods (COGS)</span>
            <div class="text-2xl font-black text-rose-500 code-font mt-2">৳{{ number_format($financials['total_cogs'], 2) }}</div>
            <div class="text-xs text-slate-400">Inventory cost of sold products</div>
        </div>

        <!-- Gross Profit -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl p-5 border border-slate-200 dark:border-slate-800 shadow-sm space-y-1">
            <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Gross Profit Margin</span>
            <div class="text-2xl font-black text-sky-500 code-font mt-2">৳{{ number_format($financials['gross_profit'], 2) }}</div>
            <div class="text-xs text-slate-400">Revenue minus COGS</div>
        </div>

        <!-- Net Profit (After Expenses) -->
        <div class="bg-gradient-to-br from-emerald-600 to-teal-700 text-white rounded-2xl p-5 shadow-lg shadow-emerald-600/20 space-y-1">
            <span class="text-xs font-bold uppercase tracking-wider text-emerald-100">Net Business Profit</span>
            <div class="text-2xl font-black code-font mt-2">৳{{ number_format($financials['net_profit'], 2) }}</div>
            <div class="text-xs text-emerald-100/90">After operating expenses (৳{{ number_format($financials['total_expenses'], 2) }})</div>
        </div>

    </div>

    <!-- Inventory Valuation Metrics -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div class="bg-white dark:bg-slate-900 rounded-2xl p-5 border border-slate-200 dark:border-slate-800 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Live Inventory Valuation (Cost Price)</span>
                <div class="text-xl font-black text-slate-900 dark:text-white code-font mt-1">৳{{ number_format($stockValuation['cost_value'], 2) }}</div>
            </div>
            <i data-lucide="boxes" class="w-8 h-8 text-slate-400"></i>
        </div>

        <div class="bg-white dark:bg-slate-900 rounded-2xl p-5 border border-slate-200 dark:border-slate-800 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Potential Inventory Valuation (Retail Price)</span>
                <div class="text-xl font-black text-emerald-600 dark:text-emerald-400 code-font mt-1">৳{{ number_format($stockValuation['retail_value'], 2) }}</div>
            </div>
            <i data-lucide="trending-up" class="w-8 h-8 text-emerald-500"></i>
        </div>
    </div>

    <!-- 12-Month Trend Comparison Chart -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 border border-slate-200 dark:border-slate-800 shadow-sm space-y-4">
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
        <div class="p-4 border-b border-slate-200 dark:border-slate-800 font-bold text-sm text-slate-900 dark:text-white">
            Top Performing PCB & Electronics Components
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
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const months = @json($monthlyChart['months']);
        const sales = @json($monthlyChart['sales']);
        const purchases = @json($monthlyChart['purchases']);

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

        const chart = new ApexCharts(document.querySelector("#annualComparisonChart"), options);
        chart.render();
    });
</script>
@endpush
