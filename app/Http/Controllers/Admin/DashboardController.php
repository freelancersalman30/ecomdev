<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Services\ReportService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    protected ReportService $reportService;

    public function __construct(ReportService $reportService)
    {
        $this->reportService = $reportService;
    }

    public function index()
    {
        $kpis = $this->reportService->getDashboardKpis();
        $chartData = $this->reportService->getMonthlyChartData();

        // Recent orders
        $recentOrders = Order::with('customer')->latest()->take(7)->get();

        // Low stock products alert
        $lowStockProducts = Product::lowStock()->take(5)->get();

        // Order Status Counts for Pipeline Widget
        $statusCounts = [
            'pending' => Order::pending()->count(),
            'processing' => Order::processing()->count(),
            'on_the_way' => Order::onTheWay()->count(),
            'in_courier' => Order::inCourier()->count(),
            'completed' => Order::completed()->count(),
            'cancelled' => Order::cancelled()->count(),
        ];

        return view('admin.dashboard', compact('kpis', 'chartData', 'recentOrders', 'lowStockProducts', 'statusCounts'));
    }
}
