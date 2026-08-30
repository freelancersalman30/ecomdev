<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Supplier;
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
        $summary = [];

        if ($reportType === 'sales') {
            $query = Order::with(['customer', 'items.product'])
                ->whereDate('created_at', '>=', $startDate)
                ->whereDate('created_at', '<=', $endDate);

            if ($request->filled('status')) {
                $query->where('status', $request->status);
            } else {
                $query->where('status', '!=', 'cancelled');
            }

            if ($request->filled('payment_method')) {
                $query->where('payment_method', $request->payment_method);
            }

            $data = $query->latest()->get();

            $summary = [
                'total_orders' => $data->count(),
                'total_revenue' => $data->sum('grand_total'),
                'total_paid' => $data->sum('paid_amount'),
                'total_discount' => $data->sum('coupon_discount'),
                'avg_order_value' => $data->count() > 0 ? $data->sum('grand_total') / $data->count() : 0,
            ];
        } elseif ($reportType === 'purchases') {
            $query = Purchase::with(['supplier', 'items.product'])
                ->whereDate('purchase_date', '>=', $startDate)
                ->whereDate('purchase_date', '<=', $endDate);

            if ($request->filled('supplier_id')) {
                $query->where('supplier_id', $request->supplier_id);
            }
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            $data = $query->latest('purchase_date')->get();

            $summary = [
                'total_purchases' => $data->count(),
                'total_cost' => $data->sum('grand_total'),
                'total_paid' => $data->sum('paid_amount'),
                'total_due' => $data->sum('due_amount'),
            ];
        } elseif ($reportType === 'expenses') {
            $query = Expense::with(['category', 'account'])
                ->whereDate('expense_date', '>=', $startDate)
                ->whereDate('expense_date', '<=', $endDate);

            if ($request->filled('category_id')) {
                $query->where('expense_category_id', $request->category_id);
            }
            if ($request->filled('account_id')) {
                $query->where('account_id', $request->account_id);
            }

            $data = $query->latest('expense_date')->get();

            $summary = [
                'total_expenses' => $data->sum('amount'),
                'total_count' => $data->count(),
                'avg_expense' => $data->count() > 0 ? $data->sum('amount') / $data->count() : 0,
            ];
        } elseif ($reportType === 'stock') {
            $query = Product::with(['category', 'brand']);

            if ($request->filled('category_id')) {
                $query->where('category_id', $request->category_id);
            }

            if ($request->filled('stock_status')) {
                if ($request->stock_status === 'out_of_stock') {
                    $query->where('stock_qty', '<=', 0);
                } elseif ($request->stock_status === 'low_stock') {
                    $query->where('stock_qty', '>', 0)->where('stock_qty', '<=', 10);
                } elseif ($request->stock_status === 'in_stock') {
                    $query->where('stock_qty', '>', 10);
                }
            }

            $data = $query->orderBy('stock_qty', 'asc')->get();

            $summary = [
                'total_products' => $data->count(),
                'total_units' => $data->sum('stock_qty'),
                'cost_value' => $data->sum(fn ($p) => $p->cost_price * $p->stock_qty),
                'retail_value' => $data->sum(fn ($p) => $p->selling_price * $p->stock_qty),
                'low_stock_count' => $data->where('stock_qty', '>', 0)->where('stock_qty', '<=', 10)->count(),
                'out_of_stock_count' => $data->where('stock_qty', '<=', 0)->count(),
            ];
        } elseif ($reportType === 'profit_loss') {
            $salesTotal = Order::whereDate('created_at', '>=', $startDate)
                ->whereDate('created_at', '<=', $endDate)
                ->where('status', '!=', 'cancelled')
                ->sum('grand_total');

            $cogsTotal = OrderItem::whereHas('order', function ($q) use ($startDate, $endDate) {
                $q->whereDate('created_at', '>=', $startDate)
                    ->whereDate('created_at', '<=', $endDate)
                    ->where('status', '!=', 'cancelled');
            })->sum(DB::raw('unit_cost * quantity'));

            $expenseTotal = Expense::whereDate('expense_date', '>=', $startDate)
                ->whereDate('expense_date', '<=', $endDate)
                ->sum('amount');

            $grossProfit = $salesTotal - $cogsTotal;
            $netProfit = $grossProfit - $expenseTotal;

            $grossMargin = $salesTotal > 0 ? round(($grossProfit / $salesTotal) * 100, 1) : 0;
            $netMargin = $salesTotal > 0 ? round(($netProfit / $salesTotal) * 100, 1) : 0;

            $summary = [
                'sales_total' => $salesTotal,
                'cogs_total' => $cogsTotal,
                'gross_profit' => $grossProfit,
                'gross_margin' => $grossMargin,
                'expense_total' => $expenseTotal,
                'net_profit' => $netProfit,
                'net_margin' => $netMargin,
            ];
        }

        $kpis = $this->reportService->getDashboardKpis();
        $financials = $this->reportService->getFinancialSummary();
        $stockValuation = $this->reportService->getStockValuation();
        $monthlyChart = $this->reportService->get12MonthSalesVsPurchases();
        $topProducts = $this->reportService->getTopSellingProducts(10);

        // Filter options for dropdowns
        $categories = Category::where('is_active', true)->get();
        $suppliers = Supplier::where('is_active', true)->get();
        $expenseCategories = ExpenseCategory::all();

        return view('admin.reports.index', compact(
            'reportType',
            'startDate',
            'endDate',
            'data',
            'summary',
            'kpis',
            'financials',
            'stockValuation',
            'monthlyChart',
            'topProducts',
            'categories',
            'suppliers',
            'expenseCategories'
        ));
    }

    public function orders(Request $request)
    {
        $request->merge(['type' => 'sales']);

        return $this->index($request);
    }

    public function purchases(Request $request)
    {
        $request->merge(['type' => 'purchases']);

        return $this->index($request);
    }

    public function expenses(Request $request)
    {
        $request->merge(['type' => 'expenses']);

        return $this->index($request);
    }

    public function stock(Request $request)
    {
        $request->merge(['type' => 'stock']);

        return $this->index($request);
    }

    public function profitLoss(Request $request)
    {
        $request->merge(['type' => 'profit_loss']);

        return $this->index($request);
    }
}
