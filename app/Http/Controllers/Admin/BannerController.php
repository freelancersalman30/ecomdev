<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\Request;

class BannerController extends Controller
{
    public function index()
    {
        $banners = Banner::orderBy('placement')->orderBy('display_order')->get();

        return view('admin.banners.index', compact('banners'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'image' => 'required|string',
            'placement' => 'required|in:hero_slider,category_header,promo_popup,sidebar_ad,footer_banner',
        ]);

        Banner::create([
            'title' => $request->title,
            'subtitle' => $request->subtitle,
            'image' => $request->image,
            'link' => $request->link,
            'placement' => $request->placement,
            'display_order' => $request->display_order ?? 0,
            'is_active' => true,
        ]);

        return redirect()->back()->with('success', 'Banner created successfully!');
    }

    public function destroy(Banner $banner)
    {
        $banner->delete();

        return redirect()->back()->with('success', 'Banner deleted!');
    }
}
