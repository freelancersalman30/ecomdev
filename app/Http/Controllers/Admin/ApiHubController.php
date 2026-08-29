<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ApiSetting;
use Illuminate\Http\Request;

class ApiHubController extends Controller
{
    public function index()
    {
        $apis = ApiSetting::all()->keyBy('provider');

        // Predefined list of providers if not yet in DB
        $providers = [
            // Couriers
            'steadfast' => ['type' => 'courier', 'title' => 'Steadfast Courier API'],
            'pathao' => ['type' => 'courier', 'title' => 'Pathao Courier API'],
            'redx' => ['type' => 'courier', 'title' => 'RedX Courier API'],

            // Payment Gateways
            'bkash' => ['type' => 'payment', 'title' => 'bKash Merchant Checkout'],
            'nagad' => ['type' => 'payment', 'title' => 'Nagad PGW'],
            'sslcommerz' => ['type' => 'payment', 'title' => 'SSLCommerz Payment Hub'],
            'amarpay' => ['type' => 'payment', 'title' => 'aamarPay'],
            'stripe' => ['type' => 'payment', 'title' => 'Stripe International Payments'],

            // SMS Gateways
            'bulksms' => ['type' => 'sms', 'title' => 'BulkSMS BD Gateway'],
            'greenweb' => ['type' => 'sms', 'title' => 'GreenWeb SMS Gateway'],
            'twilio' => ['type' => 'sms', 'title' => 'Twilio SMS API'],

            // Tracking & Pixel
            'fb_capi' => ['type' => 'tracking', 'title' => 'Facebook Conversion API (Multi-Pixel)'],
            'gtm' => ['type' => 'tracking', 'title' => 'Google Tag Manager (GTM)'],
        ];

        return view('admin.settings.api_hub', compact('apis', 'providers'));
    }

    public function update(Request $request)
    {
        $provider = $request->provider;
        $type = $request->type;
        $title = $request->title;
        $isSandbox = $request->has('is_sandbox');
        $isActive = $request->has('is_active');
        $credentials = $request->credentials ?? [];

        ApiSetting::updateOrCreate(
            ['provider' => $provider],
            [
                'type' => $type,
                'title' => $title,
                'credentials' => $credentials,
                'is_sandbox' => $isSandbox,
                'is_active' => $isActive,
            ]
        );

        return redirect()->back()->with('success', "API integration for {$title} updated successfully!");
    }
}
