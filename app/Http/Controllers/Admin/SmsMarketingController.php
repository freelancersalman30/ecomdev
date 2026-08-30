<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
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
        $estimatedBalance = 485.50; // Mock balance BDT

        $customersCount = Customer::where('is_active', true)->count();

        return view('admin.sms.index', compact('logs', 'totalSent', 'totalFailed', 'estimatedBalance', 'customersCount'));
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

        if ($request->recipient_type === 'all_customers') {
            $customers = Customer::where('is_active', true)->whereNotNull('phone')->get();
            foreach ($customers as $cust) {
                $this->smsService->send(
                    $cust->phone,
                    $template,
                    [
                        'customer_name' => $cust->name,
                        'order_id' => 'PCB-'.rand(1000, 9999),
                        'tracking_link' => 'https://dreamerspcb.com/track',
                    ]
                );
                $count++;
            }
        } else {
            $numbers = explode(',', str_replace(["\n", "\r", ' '], '', $request->custom_numbers));
            foreach ($numbers as $num) {
                if (! empty($num)) {
                    $this->smsService->send(
                        $num,
                        $template,
                        [
                            'customer_name' => 'Valued Customer',
                            'order_id' => 'PCB-TEST',
                            'tracking_link' => 'https://dreamerspcb.com/track',
                        ]
                    );
                    $count++;
                }
            }
        }

        return redirect()->back()->with('success', "Dispatched {$count} SMS messages successfully!");
    }
}
