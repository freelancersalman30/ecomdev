<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Purchase;
use App\Services\InventoryService;
use App\Services\ReportService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    protected ReportService $reportService;
    protected InventoryService $inventoryService;

    public function __construct(ReportService $reportService, InventoryService $inventoryService)
    {
        $this->reportService = $reportService;
        $this->inventoryService = $inventoryService;
    }

    public function index(Request $request)
    {
        $reportType = $request->get('type', 'sales'); // sales, purchases, expenses, stock, profit_loss
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->get('end_date', Carbon::now()->toDateString());

        $data = [];

        if ($reportType === 'sales') {
            $data = Order::with(['customer', 'items'])
                ->whereDate('created_at', '>=', $startDate)
                ->whereDate('created_at', '<=', $endDate)
                ->where('status', '!=', 'cancelled')
                ->latest()
                ->get();
        } elseif ($reportType === 'purchases') {
            $data = Purchase::with(['supplier', 'items'])
                ->whereDate('purchase_date', '>=', $startDate)
                ->whereDate('purchase_date', '<=', $endDate)
                ->latest('purchase_date')
                ->get();
        } elseif ($reportType === 'expenses') {
            $data = Expense::with(['category', 'account'])
                ->whereDate('expense_date', '>=', $startDate)
                ->whereDate('expense_date', '<=', $endDate)
                ->latest('expense_date')
                ->get();
        } elseif ($reportType === 'stock') {
            $data = Product::with(['category', 'variants'])->get();
        } elseif ($reportType === 'profit_loss') {
            $salesTotal = Order::whereDate('created_at', '>=', $startDate)
                ->whereDate('created_at', '<=', $endDate)
                ->where('status', 'completed')
                ->sum('grand_total');

            $cogsTotal = OrderItem::whereHas('order', function ($q) use ($startDate, $endDate) {
                $q->whereDate('created_at', '>=', $startDate)
                  ->whereDate('created_at', '<=', $endDate)
                  ->where('status', 'completed');
            })->sum(DB::raw('unit_cost * quantity'));

            $expenseTotal = Expense::whereDate('expense_date', '>=', $startDate)
                ->whereDate('expense_date', '<=', $endDate)
                ->sum('amount');

            $grossProfit = $salesTotal - $cogsTotal;
            $netProfit = $grossProfit - $expenseTotal;

            $data = [
                'sales_total' => $salesTotal,
                'cogs_total' => $cogsTotal,
                'gross_profit' => $grossProfit,
                'expense_total' => $expenseTotal,
                'net_profit' => $netProfit,
            ];
        }

        $kpis = $this->reportService->getDashboardKpis();
        $financials = $this->reportService->getFinancialSummary();
        $stockValuation = $this->reportService->getStockValuation();
        $monthlyChart = $this->reportService->get12MonthSalesVsPurchases();
        $topProducts = $this->reportService->getTopSellingProducts(5);

        return view('admin.reports.index', compact(
            'reportType', 
            'startDate', 
            'endDate', 
            'data', 
            'kpis',
            'financials',
            'stockValuation',
            'monthlyChart',
            'topProducts'
        ));
    }
}
