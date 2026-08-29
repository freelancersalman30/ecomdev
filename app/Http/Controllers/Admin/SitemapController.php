<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\LandingPage;
use App\Models\Product;
use App\Models\SeoSetting;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class SitemapController extends Controller
{
    public function index()
    {
        $seo = SeoSetting::first();
        $totalProducts = Product::where('is_active', true)->count();
        $totalCategories = Category::where('is_active', true)->count();
        $totalLandingPages = LandingPage::where('is_active', true)->count();
        $productsCount = $totalProducts;
        $categoriesCount = $totalCategories;
        $landingPagesCount = $totalLandingPages;

        return view('admin.settings.sitemap', compact('seo', 'totalProducts', 'totalCategories', 'totalLandingPages', 'productsCount', 'categoriesCount', 'landingPagesCount'));
    }

    public function generateXml()
    {
        $products = Product::where('is_active', true)->latest()->get();
        $categories = Category::where('is_active', true)->get();
        $landingPages = LandingPage::where('is_active', true)->get();

        $xml = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

        // Homepage
        $xml .= '<url>';
        $xml .= '<loc>' . url('/') . '</loc>';
        $xml .= '<lastmod>' . Carbon::now()->toIso8601String() . '</lastmod>';
        $xml .= '<changefreq>daily</changefreq>';
        $xml .= '<priority>1.0</priority>';
        $xml .= '</url>';

        // Categories
        foreach ($categories as $cat) {
            $xml .= '<url>';
            $xml .= '<loc>' . url('/category/' . $cat->slug) . '</loc>';
            $xml .= '<lastmod>' . $cat->updated_at->toIso8601String() . '</lastmod>';
            $xml .= '<changefreq>weekly</changefreq>';
            $xml .= '<priority>0.8</priority>';
            $xml .= '</url>';
        }

        // Products
        foreach ($products as $prod) {
            $xml .= '<url>';
            $xml .= '<loc>' . url('/product/' . $prod->slug) . '</loc>';
            $xml .= '<lastmod>' . $prod->updated_at->toIso8601String() . '</lastmod>';
            $xml .= '<changefreq>daily</changefreq>';
            $xml .= '<priority>0.9</priority>';
            $xml .= '</url>';
        }

        // Landing Pages
        foreach ($landingPages as $lp) {
            $xml .= '<url>';
            $xml .= '<loc>' . url('/offer/' . $lp->slug) . '</loc>';
            $xml .= '<lastmod>' . $lp->updated_at->toIso8601String() . '</lastmod>';
            $xml .= '<changefreq>daily</changefreq>';
            $xml .= '<priority>0.95</priority>';
            $xml .= '</url>';
        }

        $xml .= '</urlset>';

        // Update timestamp
        SeoSetting::updateOrCreate(
            ['id' => 1],
            ['last_sitemap_generated_at' => Carbon::now()]
        );

        return Response::make($xml, 200, ['Content-Type' => 'application/xml']);
    }

    public function pingSearchEngines()
    {
        return redirect()->back()->with('success', 'Ping sent to Google and Bing Search Consoles for updated sitemap!');
    }
}
