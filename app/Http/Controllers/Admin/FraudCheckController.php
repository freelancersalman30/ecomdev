<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ApiSetting;
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

        // Load Fraud API Settings
        $apis = ApiSetting::where('type', 'fraud')->get()->keyBy('provider');
        $activeApi = $this->fraudCheckService->getActiveApiSetting();

        return view('admin.fraud.index', compact(
            'fraudRecords',
            'flaggedCustomers',
            'suspiciousOrders',
            'apis',
            'activeApi'
        ));
    }

    public function checkNumber(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
        ]);

        $phone = $this->fraudCheckService->normalizePhone(trim($request->phone));
        $evaluation = $this->fraudCheckService->evaluateOrder($phone);

        $customer = Customer::where('phone', $phone)->first();
        $pastOrders = Order::where('shipping_phone', $phone)->latest()->get();
        $fraudRecord = FraudCheck::where('phone', $phone)->first();

        return view('admin.fraud.check_result', compact('phone', 'evaluation', 'customer', 'pastOrders', 'fraudRecord'));
    }

    public function blacklistNumber(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
            'notes' => 'nullable|string',
        ]);

        $phone = $this->fraudCheckService->normalizePhone(trim($request->phone));

        FraudCheck::updateOrCreate(
            ['phone' => $phone],
            [
                'risk_level' => 'critical',
                'is_blacklisted' => true,
                'notes' => $request->notes ?? 'Manually blacklisted by admin',
                'courier_success_rate' => 0.00,
            ]
        );

        return redirect()->back()->with('success', "Phone {$phone} has been added to blacklist.");
    }

    public function removeBlacklist(Request $request, $id)
    {
        $record = FraudCheck::findOrFail($id);
        $record->update([
            'is_blacklisted' => false,
            'risk_level' => 'low',
        ]);

        return redirect()->back()->with('success', "Phone {$record->phone} removed from blacklist.");
    }

    public function updateApiSettings(Request $request)
    {
        $request->validate([
            'provider' => 'required|string|in:zachaikori,universal_fraud',
        ]);

        $provider = $request->input('provider');
        $isActive = $request->boolean('is_active');

        // If activating this provider, deactivate any other fraud API provider so only one is primary active
        if ($isActive) {
            ApiSetting::where('type', 'fraud')
                ->where('provider', '!=', $provider)
                ->update(['is_active' => false]);
        }

        $inputCredentials = $request->except([
            '_token',
            '_method',
            'provider',
            'type',
            'title',
            'is_active',
        ]);

        $title = $provider === 'zachaikori' ? 'Zachaikori Fraud & Courier Risk API' : ($request->input('provider_name') ?: 'Universal Fraud Checker API');

        ApiSetting::updateOrCreate(
            ['provider' => $provider],
            [
                'type' => 'fraud',
                'title' => $title,
                'credentials' => $inputCredentials,
                'is_active' => $isActive,
            ]
        );

        $statusText = $isActive ? 'activated as Primary Fraud API' : 'saved successfully';

        return redirect()->back()->with('success', "{$title} settings {$statusText}!");
    }

    public function testApiConnection(Request $request)
    {
        $request->validate([
            'phone' => 'nullable|string',
            'provider' => 'required|string',
        ]);

        $phone = $request->input('phone') ?: '01711223344';
        $provider = $request->input('provider');

        // Create a temporary ApiSetting instance for on-the-fly testing
        $tempSetting = new ApiSetting([
            'provider' => $provider,
            'type' => 'fraud',
            'credentials' => $request->all(),
            'is_active' => true,
        ]);

        $startTime = microtime(true);
        $result = $this->fraudCheckService->checkExternalApi($phone, $tempSetting);
        $latency = round((microtime(true) - $startTime) * 1000, 2);

        $result['latency_ms'] = $latency;

        return response()->json($result);
    }
}
