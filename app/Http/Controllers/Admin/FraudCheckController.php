<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\FraudCheck;
use App\Models\Order;
use App\Services\FraudCheckService;
use Illuminate\Http\Request;

class FraudCheckController extends Controller
{
    protected FraudCheckService $fraudCheckService;

    public function __construct(FraudCheckService $fraudCheckService)
    {
        $this->fraudCheckService = $fraudCheckService;
    }

    public function index()
    {
        $fraudRecords = FraudCheck::latest()->paginate(25);
        $flaggedCustomers = Customer::where('is_flagged_fraud', true)->get();
        $suspiciousOrders = Order::where('is_fraud_suspect', true)->latest()->take(15)->get();

        return view('admin.fraud.index', compact('fraudRecords', 'flaggedCustomers', 'suspiciousOrders'));
    }

    public function checkNumber(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
        ]);

        $phone = trim($request->phone);
        $evaluation = $this->fraudCheckService->evaluateOrder($phone);

        $customer = Customer::where('phone', $phone)->first();
        $pastOrders = Order::where('shipping_phone', $phone)->latest()->get();

        return view('admin.fraud.check_result', compact('phone', 'evaluation', 'customer', 'pastOrders'));
    }

    public function blacklistNumber(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
            'notes' => 'nullable|string',
        ]);

        FraudCheck::updateOrCreate(
            ['phone' => trim($request->phone)],
            [
                'risk_level' => 'critical',
                'is_blacklisted' => true,
                'notes' => $request->notes ?? 'Manually blacklisted by admin',
                'courier_success_rate' => 0.00,
            ]
        );

        return redirect()->back()->with('success', "Phone {$request->phone} has been added to blacklist.");
    }
}
