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
    public function index()
    {
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
        ]);

        $uploadDir = public_path('uploads/settings');
        if (!File::isDirectory($uploadDir)) {
            File::makeDirectory($uploadDir, 0755, true, true);
        }

        // Handle Branding Asset Uploads
        $fileFields = ['site_logo', 'site_logo_dark', 'site_favicon', 'invoice_logo'];
        foreach ($fileFields as $field) {
            if ($request->hasFile($field)) {
                $file = $request->file($field);
                $filename = $field . '_' . time() . '_' . Str::random(6) . '.' . $file->getClientOriginalExtension();
                $file->move($uploadDir, $filename);
                Setting::set($field, '/uploads/settings/' . $filename, 'branding');
            } elseif ($request->boolean('remove_' . $field)) {
                $current = Setting::get($field);
                if ($current && File::exists(public_path($current))) {
                    File::delete(public_path($current));
                }
                Setting::set($field, '', 'branding');
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

        return redirect()->back()->with('success', 'Site settings updated successfully! All configurations are now live.');
    }
}
