<?php

namespace App\Services;

use App\Models\Account;
use App\Models\Expense;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Purchase;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReportService
{
    protected InventoryService $inventoryService;

    public function __construct(InventoryService $inventoryService)
    {
        $this->inventoryService = $inventoryService;
    }

    /**
     * Get real-time dashboard KPIs
     */
    public function getDashboardKpis(): array
    {
        $today = Carbon::today();
        $thisMonth = Carbon::now()->month;
        $thisYear = Carbon::now()->year;

        // Sales
        $todaySales = Order::whereDate('created_at', $today)->where('status', '!=', 'cancelled')->sum('grand_total');
        $todayOrdersCount = Order::whereDate('created_at', $today)->count();

        $monthSales = Order::whereMonth('created_at', $thisMonth)->whereYear('created_at', $thisYear)->where('status', '!=', 'cancelled')->sum('grand_total');
        $yearSales = Order::whereYear('created_at', $thisYear)->where('status', '!=', 'cancelled')->sum('grand_total');

        // Purchases
        $todayPurchases = Purchase::whereDate('purchase_date', $today)->sum('grand_total');
        $monthPurchases = Purchase::whereMonth('purchase_date', $thisMonth)->whereYear('purchase_date', $thisYear)->sum('grand_total');
        $yearPurchases = Purchase::whereYear('purchase_date', $thisYear)->sum('grand_total');

        // Expenses
        $todayExpenses = Expense::whereDate('expense_date', $today)->sum('amount');
        $monthExpenses = Expense::whereMonth('expense_date', $thisMonth)->whereYear('expense_date', $thisYear)->sum('amount');
        $yearExpenses = Expense::whereYear('expense_date', $thisYear)->sum('amount');

        // Available Fund Balance across all active accounts
        $availableFunds = Account::where('is_active', true)->sum('current_balance');

        // Gross & Net Profit calculation
        $totalCostOfDeliveredGoods = OrderItem::whereHas('order', function ($q) {
            $q->where('status', 'completed');
        })->sum(DB::raw('unit_cost * quantity'));

        $totalRevenueDelivered = Order::where('status', 'completed')->sum('grand_total');
        $grossProfit = $totalRevenueDelivered - $totalCostOfDeliveredGoods;
        $totalAllExpenses = Expense::sum('amount');
        $netProfit = $grossProfit - $totalAllExpenses;

        // Stock Valuation
        $stockMetrics = $this->inventoryService->getStockValuation();

        // Low stock products count
        $lowStockCount = \App\Models\Product::lowStock()->count();

        return [
            'today_sales' => round($todaySales, 2),
            'today_orders_count' => $todayOrdersCount,
            'month_sales' => round($monthSales, 2),
            'year_sales' => round($yearSales, 2),
            'today_purchases' => round($todayPurchases, 2),
            'month_purchases' => round($monthPurchases, 2),
            'year_purchases' => round($yearPurchases, 2),
            'today_expenses' => round($todayExpenses, 2),
            'month_expenses' => round($monthExpenses, 2),
            'year_expenses' => round($yearExpenses, 2),
            'available_funds' => round($availableFunds, 2),
            'gross_profit' => round($grossProfit, 2),
            'net_profit' => round($netProfit, 2),
            'stock_cost_value' => $stockMetrics['total_cost_value'],
            'stock_retail_value' => $stockMetrics['total_retail_value'],
            'total_items_in_stock' => $stockMetrics['total_items_in_stock'],
            'low_stock_count' => $lowStockCount,
            'current_month_name' => Carbon::now()->format('F'),
            'current_year' => $thisYear,
        ];
    }

    /**
     * Get 12-Month Sales vs Purchases Trend for ApexCharts
     */
    public function getMonthlyChartData(): array
    {
        $months = [];
        $sales = [];
        $purchases = [];

        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $monthName = $date->format('M Y');
            $m = $date->month;
            $y = $date->year;

            $monthSale = Order::whereMonth('created_at', $m)
                ->whereYear('created_at', $y)
                ->where('status', '!=', 'cancelled')
                ->sum('grand_total');

            $monthPurchase = Purchase::whereMonth('purchase_date', $m)
                ->whereYear('purchase_date', $y)
                ->sum('grand_total');

            $months[] = $monthName;
            $sales[] = round($monthSale, 2);
            $purchases[] = round($monthPurchase, 2);
        }

        return [
            'months' => $months,
            'categories' => $months,
            'sales' => $sales,
            'purchases' => $purchases,
        ];
    }

    public function get12MonthSalesVsPurchases(): array
    {
        return $this->getMonthlyChartData();
    }

    public function getFinancialSummary(): array
    {
        $totalRevenue = Order::where('status', 'completed')->sum('grand_total');
        $totalCogs = OrderItem::whereHas('order', function ($q) {
            $q->where('status', 'completed');
        })->sum(DB::raw('unit_cost * quantity'));
        $totalExpenses = Expense::sum('amount');
        $grossProfit = $totalRevenue - $totalCogs;
        $netProfit = $grossProfit - $totalExpenses;

        return [
            'total_revenue' => round($totalRevenue, 2),
            'total_cogs' => round($totalCogs, 2),
            'total_expenses' => round($totalExpenses, 2),
            'gross_profit' => round($grossProfit, 2),
            'net_profit' => round($netProfit, 2),
        ];
    }

    public function getStockValuation(): array
    {
        $metrics = $this->inventoryService->getStockValuation();
        return [
            'cost_value' => $metrics['total_cost_value'],
            'retail_value' => $metrics['total_retail_value'],
            'items_count' => $metrics['total_items_in_stock'],
        ];
    }

    public function getTopSellingProducts(int $limit = 5)
    {
        return OrderItem::select('product_id', DB::raw('SUM(quantity) as total_qty_sold'), DB::raw('SUM(subtotal) as total_revenue'), DB::raw('SUM(subtotal - (unit_cost * quantity)) as total_profit'))
            ->with('product')
            ->groupBy('product_id')
            ->orderByDesc('total_qty_sold')
            ->take($limit)
            ->get()
            ->map(function ($item) {
                return (object) [
                    'name' => $item->product->name ?? 'Product #' . $item->product_id,
                    'total_qty_sold' => $item->total_qty_sold,
                    'total_revenue' => $item->total_revenue,
                    'total_profit' => $item->total_profit,
                ];
            });
    }
}
