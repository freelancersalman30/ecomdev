<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ApiSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ApiHubController extends Controller
{
    public function index()
    {
        $apis = ApiSetting::all()->keyBy('provider');

        // Predefined list of providers if not yet in DB
        $providers = [
            // Couriers
            'steadfast' => ['type' => 'courier', 'title' => 'Steadfast Courier API'],
            'pathao' => ['type' => 'courier', 'title' => 'Pathao Logistics API'],
            'redx' => ['type' => 'courier', 'title' => 'RedX Courier API'],

            // Payment Gateways
            'bkash' => ['type' => 'payment', 'title' => 'bKash Merchant Checkout'],
            'nagad' => ['type' => 'payment', 'title' => 'Nagad PGW'],
            'sslcommerz' => ['type' => 'payment', 'title' => 'SSLCommerz Payment Hub'],
            'amarpay' => ['type' => 'payment', 'title' => 'aamarPay'],
            'stripe' => ['type' => 'payment', 'title' => 'Stripe International Payments'],

            // SMS Gateways
            'bulksms' => ['type' => 'sms', 'title' => 'BulkSMS BD Gateway'],
            'bulksms_bd' => ['type' => 'sms', 'title' => 'BulkSMS BD Gateway'],
            'greenweb' => ['type' => 'sms', 'title' => 'GreenWeb SMS Gateway'],
            'twilio' => ['type' => 'sms', 'title' => 'Twilio SMS API'],

            // Tracking & Pixel
            'fb_capi' => ['type' => 'tracking', 'title' => 'Facebook Conversion API (Multi-Pixel)'],
            'gtm' => ['type' => 'tracking', 'title' => 'Google Tag Manager (GTM)'],
        ];

        // Ensure alias between bulksms and bulksms_bd works both ways
        if (isset($apis['bulksms']) && ! isset($apis['bulksms_bd'])) {
            $apis['bulksms_bd'] = $apis['bulksms'];
        } elseif (isset($apis['bulksms_bd']) && ! isset($apis['bulksms'])) {
            $apis['bulksms'] = $apis['bulksms_bd'];
        }

        return view('admin.settings.api_hub', compact('apis', 'providers'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'provider' => 'required|string',
        ]);

        $provider = $request->input('provider');

        $knownProviders = [
            'steadfast' => ['type' => 'courier',  'title' => 'Steadfast Courier API'],
            'pathao' => ['type' => 'courier',  'title' => 'Pathao Logistics API'],
            'redx' => ['type' => 'courier',  'title' => 'RedX Courier API'],
            'bkash' => ['type' => 'payment',  'title' => 'bKash Payment Gateway'],
            'nagad' => ['type' => 'payment',  'title' => 'Nagad PGW'],
            'sslcommerz' => ['type' => 'payment',  'title' => 'SSLCommerz Payment Hub'],
            'amarpay' => ['type' => 'payment',  'title' => 'aamarPay'],
            'stripe' => ['type' => 'payment',  'title' => 'Stripe International Payments'],
            'bulksms' => ['type' => 'sms',      'title' => 'BulkSMS BD Gateway'],
            'bulksms_bd' => ['type' => 'sms',      'title' => 'BulkSMS BD Gateway'],
            'greenweb' => ['type' => 'sms',      'title' => 'GreenWeb SMS Gateway'],
            'twilio' => ['type' => 'sms',      'title' => 'Twilio SMS API'],
            'fb_capi' => ['type' => 'tracking', 'title' => 'Facebook Conversion API (Multi-Pixel)'],
            'gtm' => ['type' => 'tracking', 'title' => 'Google Tag Manager (GTM)'],
        ];

        $existing = ApiSetting::where('provider', $provider)->first();

        // If provider is bulksms_bd and not found, also check bulksms, and vice-versa
        if (! $existing) {
            if ($provider === 'bulksms_bd') {
                $existing = ApiSetting::where('provider', 'bulksms')->first();
            } elseif ($provider === 'bulksms') {
                $existing = ApiSetting::where('provider', 'bulksms_bd')->first();
            }
        }

        // Determine type and title with graceful fallbacks
        $type = $request->input('type')
            ?: ($existing?->type ?: ($knownProviders[$provider]['type'] ?? 'general'));

        $title = $request->input('title')
            ?: ($existing?->title ?: ($knownProviders[$provider]['title'] ?? Str::headline($provider)));

        // Handle is_sandbox and is_active
        $isSandbox = $request->has('is_sandbox')
            ? $request->boolean('is_sandbox')
            : ($existing->is_sandbox ?? false);

        $isActive = $request->has('is_active')
            ? $request->boolean('is_active')
            : ($existing->is_active ?? true);

        // Handle credentials:
        // Accept either explicit credentials array or any arbitrary input fields
        if ($request->has('credentials') && is_array($request->input('credentials'))) {
            $inputCredentials = $request->input('credentials');
        } else {
            $inputCredentials = $request->except([
                '_token',
                '_method',
                'provider',
                'type',
                'title',
                'is_sandbox',
                'is_active',
            ]);
        }

        $existingCredentials = is_array($existing?->credentials) ? $existing->credentials : [];
        $credentials = array_merge($existingCredentials, $inputCredentials);

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

        // Also sync alias if bulksms_bd or bulksms
        if ($provider === 'bulksms_bd') {
            ApiSetting::updateOrCreate(
                ['provider' => 'bulksms'],
                [
                    'type' => $type,
                    'title' => $title,
                    'credentials' => $credentials,
                    'is_sandbox' => $isSandbox,
                    'is_active' => $isActive,
                ]
            );
        } elseif ($provider === 'bulksms') {
            ApiSetting::updateOrCreate(
                ['provider' => 'bulksms_bd'],
                [
                    'type' => $type,
                    'title' => $title,
                    'credentials' => $credentials,
                    'is_sandbox' => $isSandbox,
                    'is_active' => $isActive,
                ]
            );
        }

        return redirect()->route('admin.settings.api_hub')->with('success', "API integration for {$title} updated successfully!");
    }
}
