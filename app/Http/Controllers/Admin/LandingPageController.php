<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LandingPage;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class LandingPageController extends Controller
{
    public function index()
    {
        $landingPages = LandingPage::with('product')->latest()->get();

        return view('admin.landing_pages.index', compact('landingPages'));
    }

    public function create()
    {
        $products = Product::where('is_active', true)->get();

        return view('admin.landing_pages.create', compact('products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'product_id' => 'required|exists:products,id',
            'headline' => 'nullable|string|max:255',
        ]);

        $slug = Str::slug($request->title).'-'.rand(100, 999);

        LandingPage::create([
            'title' => $request->title,
            'slug' => $slug,
            'product_id' => $request->product_id,
            'headline' => $request->headline,
            'sub_headline' => $request->sub_headline,
            'video_url' => $request->video_url,
            'features_list' => ! empty($request->features) ? explode("\n", str_replace("\r", '', $request->features)) : [],
            'theme_color' => $request->theme_color ?? '#0ea5e9',
            'fb_pixel_id' => $request->fb_pixel_id,
            'is_active' => true,
        ]);

        return redirect()->route('admin.landing-pages.index')->with('success', 'Landing page created successfully!');
    }

    public function edit(LandingPage $landingPage)
    {
        $products = Product::where('is_active', true)->get();

        return view('admin.landing_pages.edit', compact('landingPage', 'products'));
    }

    public function update(Request $request, LandingPage $landingPage)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'product_id' => 'required|exists:products,id',
        ]);

        $landingPage->update([
            'title' => $request->title,
            'product_id' => $request->product_id,
            'headline' => $request->headline,
            'sub_headline' => $request->sub_headline,
            'video_url' => $request->video_url,
            'features_list' => ! empty($request->features) ? explode("\n", str_replace("\r", '', $request->features)) : [],
            'theme_color' => $request->theme_color,
            'fb_pixel_id' => $request->fb_pixel_id,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('admin.landing-pages.index')->with('success', 'Landing page updated!');
    }

    public function preview(LandingPage $landingPage)
    {
        $landingPage->load('product.variants');

        return view('admin.landing_pages.preview', compact('landingPage'));
    }

    public function destroy(LandingPage $landingPage)
    {
        $landingPage->delete();

        return redirect()->route('admin.landing-pages.index')->with('success', 'Landing page deleted!');
    }
}
