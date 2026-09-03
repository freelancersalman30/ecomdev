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

        // Decode popular categories
        $popularCategories = [];
        if (! empty($settings['footer_popular_categories'])) {
            $decoded = json_decode($settings['footer_popular_categories'], true);
            if (is_array($decoded)) {
                $popularCategories = $decoded;
            }
        }

        // Default categories if empty
        if (empty($popularCategories)) {
            $popularCategories = [
                ['title' => 'ESP32 & IoT Microcontrollers', 'url' => '/shop?search=ESP32'],
                ['title' => 'STM32 ARM Cortex Boards', 'url' => '/shop?search=STM32'],
                ['title' => 'Arduino Uno & Mega Kits', 'url' => '/shop?search=Arduino'],
                ['title' => 'Quick 861DW & Soldering Rework', 'url' => '/shop?search=Soldering'],
                ['title' => 'Sensors & Relay Modules', 'url' => '/shop?search=Sensor'],
                ['title' => 'Robotics & DIY Hardware', 'url' => '/shop?search=Robot'],
            ];
        }

        return view('admin.settings.footer', compact('settings', 'popularCategories'));
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

        // Process dynamic popular categories
        if ($request->has('popular_categories')) {
            $categoriesInput = $request->input('popular_categories');
            $cleanCategories = [];

            if (is_array($categoriesInput)) {
                foreach ($categoriesInput as $cat) {
                    $title = trim($cat['title'] ?? '');
                    $url = trim($cat['url'] ?? '');
                    if (! empty($title)) {
                        $cleanCategories[] = [
                            'title' => $title,
                            'url' => $url ?: '/shop',
                        ];
                    }
                }
            }

            Setting::set('footer_popular_categories', json_encode(array_values($cleanCategories)), 'footer');
        }

        return redirect()->route('admin.settings.footer')->with('success', 'Footer information & popular categories updated successfully!');
    }
}
