<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SeoSetting;
use Illuminate\Http\Request;

class SeoController extends Controller
{
    public function index()
    {
        $seo = SeoSetting::first() ?? new SeoSetting([
            'meta_title' => 'DREAMERS PCB - Enterprise Gadgets & PCB Components in Bangladesh',
            'meta_description' => 'Best online shop for Arduino, STM32, ESP32, Robotics, Soldering Stations, DIY Kits, and Electronic Components.',
            'meta_keywords' => 'PCB, Arduino, Electronics, Raspberry Pi, Sensors, Robotics, Bangladesh',
            'robots_txt' => "User-agent: *\nAllow: /\nDisallow: /admin/\nSitemap: " . url('/sitemap.xml'),
        ]);

        return view('admin.settings.seo', compact('seo'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'meta_title' => 'required|string|max:255',
        ]);

        SeoSetting::updateOrCreate(
            ['id' => 1],
            $request->only('meta_title', 'meta_description', 'meta_keywords', 'og_image', 'canonical_url', 'robots_txt', 'sitemap_auto_ping')
        );

        return redirect()->back()->with('success', 'SEO settings saved successfully!');
    }
}
