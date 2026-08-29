<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class FooterSettingController extends Controller
{
    public function index()
    {
        $settings = Setting::where('group', 'footer')
            ->orWhere('group', 'general')
            ->pluck('value', 'key')
            ->toArray();

        return view('admin.settings.footer', compact('settings'));
    }

    public function update(Request $request)
    {
        $fields = [
            'footer_about',
            'footer_hotline',
            'footer_phone_secondary',
            'footer_whatsapp',
            'footer_email',
            'footer_address_office',
            'footer_address_showroom',
            'footer_working_hours',
            'footer_copyright',
            'footer_trade_license',
            'footer_facebook_url',
            'footer_youtube_url',
            'footer_linkedin_url',
            'footer_github_url',
            'footer_instagram_url',
            'footer_discord_url',
            'footer_payment_methods',
            'footer_courier_partners',
            'footer_custom_link1_title',
            'footer_custom_link1_url',
            'footer_custom_link2_title',
            'footer_custom_link2_url',
            'footer_custom_link3_title',
            'footer_custom_link3_url',
            'footer_custom_link4_title',
            'footer_custom_link4_url',
        ];

        foreach ($fields as $field) {
            Setting::set($field, $request->input($field, ''), 'footer');
        }

        return redirect()->route('admin.settings.footer')->with('success', 'Footer information updated successfully!');
    }
}
