<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class GeneralSettingController extends Controller
{
    /**
     * Display the Enterprise Site Settings Control Hub.
     */
    /**
     * Display the Enterprise Site Settings Control Hub.
     */
    public function index()
    {
        // Pre-fill slider defaults if not yet set
        $sliderDefaults = [
            'slider_1_active' => '1',
            'slider_1_badge' => 'Verified Electronic Component',
            'slider_1_title' => 'STM32 & ESP32-S3 IoT Development Boards',
            'slider_1_subtitle' => 'Official Enterprise Electronics Distribution in Bangladesh',
            'slider_1_image' => 'https://images.unsplash.com/photo-1518770660439-4636190af475?w=1200&auto=format&fit=crop&q=80',
            'slider_1_button_text' => 'Explore Collection',
            'slider_1_link' => '/shop',

            'slider_2_active' => '1',
            'slider_2_badge' => 'Premium Hardware',
            'slider_2_title' => 'Professional Quick 861DW Soldering Rework Stations',
            'slider_2_subtitle' => '1000W High Power Digital SMD Rework Master Kit',
            'slider_2_image' => 'https://images.unsplash.com/photo-1581092160607-ee22621dd758?w=1200&auto=format&fit=crop&q=80',
            'slider_2_button_text' => 'Shop Equipment',
            'slider_2_link' => '/shop',

            'slider_3_active' => '0',
            'slider_3_badge' => 'New Arrival',
            'slider_3_title' => 'Raspberry Pi 4 Model B & High-Speed Sensors',
            'slider_3_subtitle' => 'Industrial Grade Single Board Computers & Robotics Kits',
            'slider_3_image' => 'https://images.unsplash.com/photo-1550751827-4bd374c3f58b?w=1200&auto=format&fit=crop&q=80',
            'slider_3_button_text' => 'View Deals',
            'slider_3_link' => '/shop',

            'slider_autoplay_interval' => '5000',

            'promo_strip_1_tag' => 'IoT Dev Boards',
            'promo_strip_1_title' => 'ESP32-S3 AI Vision Modules',
            'promo_strip_1_offer' => 'From ৳650 Only',
            'promo_strip_1_link' => '/shop?search=ESP32',

            'promo_strip_2_tag' => 'Soldering Equipment',
            'promo_strip_2_title' => 'Quick 861DW 1000W Rework',
            'promo_strip_2_offer' => 'Official 1-Year Warranty',
            'promo_strip_2_link' => '/shop?search=Quick',
        ];

        foreach ($sliderDefaults as $k => $v) {
            if (Setting::get($k) === null) {
                Setting::set($k, $v, 'slider');
            }
        }

        $settings = Setting::all()->pluck('value', 'key');
        return view('admin.settings.general', compact('settings'));
    }

    /**
     * Update Site Settings and process uploaded branding media.
     */
    public function update(Request $request)
    {
        $request->validate([
            'site_name' => 'nullable|string|max:255',
            'site_tagline' => 'nullable|string|max:255',
            'site_email' => 'nullable|email|max:255',
            'site_phone' => 'nullable|string|max:50',
            'site_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:3072',
            'site_logo_dark' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:3072',
            'site_favicon' => 'nullable|mimes:jpeg,png,jpg,gif,svg,ico,webp|max:1024',
            'invoice_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:3072',
            'slider_1_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:5120',
            'slider_2_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:5120',
            'slider_3_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:5120',
        ]);

        $uploadDir = public_path('uploads/settings');
        if (!File::isDirectory($uploadDir)) {
            File::makeDirectory($uploadDir, 0755, true, true);
        }

        // Handle Branding Asset Uploads
        $fileFields = [
            'site_logo', 
            'site_logo_dark', 
            'site_favicon', 
            'invoice_logo',
            'slider_1_image',
            'slider_2_image',
            'slider_3_image',
        ];

        foreach ($fileFields as $field) {
            if ($request->hasFile($field)) {
                $file = $request->file($field);
                $filename = $field . '_' . time() . '_' . Str::random(6) . '.' . $file->getClientOriginalExtension();
                $file->move($uploadDir, $filename);
                Setting::set($field, '/uploads/settings/' . $filename, str_starts_with($field, 'slider_') ? 'slider' : 'branding');
            } elseif ($request->boolean('remove_' . $field)) {
                $current = Setting::get($field);
                if ($current && File::exists(public_path($current))) {
                    File::delete(public_path($current));
                }
                Setting::set($field, '', str_starts_with($field, 'slider_') ? 'slider' : 'branding');
            }
        }

        // Handle direct Slider image URLs if no file was uploaded
        for ($i = 1; $i <= 3; $i++) {
            $imgField = "slider_{$i}_image";
            $urlField = "slider_{$i}_image_url";
            if (!$request->hasFile($imgField) && $request->filled($urlField)) {
                Setting::set($imgField, $request->input($urlField), 'slider');
            }
        }

        // Boolean toggle settings (checkboxes)
        $toggleKeys = [
            'maintenance_mode',
            'announcement_enabled',
            'cod_enabled',
            'guest_checkout_enabled',
            'backorder_enabled',
            'free_shipping_enabled',
            'sticky_header_enabled',
            'google_analytics_enabled',
            'google_tag_manager_enabled',
            'facebook_pixel_enabled',
            'tiktok_pixel_enabled',
            'google_ads_enabled',
            'google_adsense_enabled',
            'bing_ads_enabled',
            'facebook_capi_enabled',
            'slider_1_active',
            'slider_2_active',
            'slider_3_active',
        ];

        foreach ($toggleKeys as $toggleKey) {
            $value = $request->has($toggleKey) ? '1' : '0';
            Setting::set($toggleKey, $value, 'general');
        }

        // All text and configuration fields
        $excludedKeys = array_merge(
            ['_token', '_method'],
            $fileFields,
            array_map(fn($f) => 'remove_' . $f, $fileFields),
            $toggleKeys
        );

        foreach ($request->except($excludedKeys) as $key => $value) {
            if ($value !== null) {
                Setting::set($key, is_array($value) ? json_encode($value) : (string)$value, 'general');
            }
        }

        // Synchronize legacy keys if both were used
        if ($request->filled('site_name')) {
            Setting::set('company_name', (string)$request->site_name, 'general');
        }
        if ($request->filled('site_phone')) {
            Setting::set('phone', (string)$request->site_phone, 'general');
            Setting::set('footer_hotline', (string)$request->site_phone, 'general');
        }
        if ($request->filled('site_email')) {
            Setting::set('email', (string)$request->site_email, 'general');
            Setting::set('footer_email', (string)$request->site_email, 'general');
        }
        if ($request->filled('site_address')) {
            Setting::set('address', (string)$request->site_address, 'general');
            Setting::set('footer_address_office', (string)$request->site_address, 'general');
        }
        if ($request->filled('inside_dhaka_shipping')) {
            Setting::set('inside_dhaka_charge', (string)$request->inside_dhaka_shipping, 'general');
        }
        if ($request->filled('outside_dhaka_shipping')) {
            Setting::set('outside_dhaka_charge', (string)$request->outside_dhaka_shipping, 'general');
        }

        // Synchronize Slider settings to Banner table for seamless compatibility
        for ($i = 1; $i <= 3; $i++) {
            $title = Setting::get("slider_{$i}_title");
            if (!empty($title)) {
                \App\Models\Banner::updateOrCreate(
                    ['placement' => 'hero_slider', 'display_order' => $i],
                    [
                        'title' => $title,
                        'subtitle' => Setting::get("slider_{$i}_subtitle", ''),
                        'image' => Setting::get("slider_{$i}_image") ?: 'https://images.unsplash.com/photo-1518770660439-4636190af475?w=1200&auto=format&fit=crop&q=80',
                        'link' => Setting::get("slider_{$i}_link", '/shop'),
                        'is_active' => Setting::get("slider_{$i}_active", '1') === '1',
                    ]
                );
            }
        }

        return redirect()->back()->with('success', 'Site settings updated successfully! All configurations are now live.');
    }
}
