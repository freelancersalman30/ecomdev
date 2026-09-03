<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ApiSetting;
use App\Services\BkashService;
use App\Services\CourierService;
use App\Services\SmsService;
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
            'custom_sms' => ['type' => 'sms', 'title' => 'Custom Bulk SMS Gateway (Any Provider)'],
            'bulksmsdhaka' => ['type' => 'sms', 'title' => 'Bulk SMS Dhaka Gateway (bulksmsdhaka.com)'],
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
            'custom_sms' => ['type' => 'sms',  'title' => 'Custom Bulk SMS Gateway (Any Provider)'],
            'bulksmsdhaka' => ['type' => 'sms',  'title' => 'Bulk SMS Dhaka Gateway (bulksmsdhaka.com)'],
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

        // Handle is_sandbox and is_active accurately:
        $isSandbox = $request->boolean('is_sandbox');
        $isActive = $request->boolean('is_active');

        // Handle credentials
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

        $statusText = $isActive ? 'Activated & Updated' : 'Deactivated & Saved';

        return redirect()->route('admin.settings.api_hub')->with('success', "API integration for {$title} {$statusText} successfully!");
    }

    /**
     * Test live API connection for a provider
     */
    public function testConnection(Request $request, SmsService $smsService, CourierService $courierService, BkashService $bkashService)
    {
        $provider = $request->input('provider');

        switch ($provider) {
            case 'custom_sms':
                $result = $smsService->testCustomSms($request->all());

                return response()->json($result);

            case 'bulksmsdhaka':
                $result = $smsService->checkBulkSmsDhaka(
                    $request->input('api_key') ?: $request->input('apikey'),
                    $request->input('caller_id') ?: ($request->input('callerID') ?: $request->input('sender_id'))
                );

                return response()->json($result);

            case 'bulksms':
            case 'bulksms_bd':
                $result = $smsService->checkBalance($request->input('api_key'));

                return response()->json($result);

            case 'steadfast':
                $result = $courierService->testSteadfastConnection($request->input('api_key'), $request->input('secret_key'));

                return response()->json($result);

            case 'bkash':
                $result = $bkashService->testConnection(
                    $request->input('app_key'),
                    $request->input('app_secret'),
                    $request->input('username'),
                    $request->input('password'),
                    $request->boolean('is_sandbox')
                );

                return response()->json($result);

            default:
                return response()->json([
                    'success' => false,
                    'message' => 'Direct ping test is not implemented for this provider.',
                ]);
        }
    }
}
