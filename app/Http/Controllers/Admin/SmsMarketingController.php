<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ApiSetting;
use App\Models\Customer;
use App\Models\SmsLog;
use App\Services\SmsService;
use Illuminate\Http\Request;

class SmsMarketingController extends Controller
{
    protected SmsService $smsService;

    public function __construct(SmsService $smsService)
    {
        $this->smsService = $smsService;
    }

    public function index()
    {
        $logs = SmsLog::latest()->paginate(25);
        $totalSent = SmsLog::where('status', 'sent')->count();
        $totalFailed = SmsLog::where('status', 'failed')->count();

        // Get active SMS gateway title
        $activeGateway = ApiSetting::where('is_active', true)
            ->whereIn('provider', ['bulksmsdhaka', 'bulksms_bd', 'bulksms'])
            ->first();

        $gatewayName = $activeGateway ? $activeGateway->title : 'Bulk SMS Dhaka Gateway';

        $customersCount = Customer::where('is_active', true)->whereNotNull('phone')->count();

        return view('admin.sms.index', compact('logs', 'totalSent', 'totalFailed', 'gatewayName', 'customersCount', 'activeGateway'));
    }

    public function sendBulk(Request $request)
    {
        $request->validate([
            'recipient_type' => 'required|in:all_customers,custom_numbers',
            'message' => 'required|string|max:1000',
            'custom_numbers' => 'nullable|string',
        ]);

        $template = $request->message;
        $count = 0;
        $failedCount = 0;

        if ($request->recipient_type === 'all_customers') {
            $customers = Customer::where('is_active', true)->whereNotNull('phone')->get();
            foreach ($customers as $cust) {
                $sent = $this->smsService->send(
                    $cust->phone,
                    $template,
                    [
                        'customer_name' => $cust->name,
                        'order_id' => 'PCB-'.rand(1000, 9999),
                        'tracking_link' => 'https://dreamerspcb.com/track',
                    ]
                );
                if ($sent) {
                    $count++;
                } else {
                    $failedCount++;
                }
            }
        } else {
            $numbers = explode(',', str_replace(["\n", "\r", ' '], '', $request->custom_numbers));
            foreach ($numbers as $num) {
                if (! empty($num)) {
                    $sent = $this->smsService->send(
                        $num,
                        $template,
                        [
                            'customer_name' => 'Valued Customer',
                            'order_id' => 'PCB-TEST',
                            'tracking_link' => 'https://dreamerspcb.com/track',
                        ]
                    );
                    if ($sent) {
                        $count++;
                    } else {
                        $failedCount++;
                    }
                }
            }
        }

        $activeGateway = ApiSetting::where('is_active', true)
            ->whereIn('provider', ['bulksmsdhaka', 'bulksms_bd', 'bulksms'])
            ->first();

        $gatewayName = $activeGateway ? $activeGateway->title : 'Bulk SMS Dhaka';

        if ($failedCount > 0) {
            return redirect()->back()->with('warning', "Dispatched {$count} SMS via {$gatewayName}. {$failedCount} failed.");
        }

        return redirect()->back()->with('success', "Dispatched {$count} SMS messages successfully via {$gatewayName}!");
    }
}
