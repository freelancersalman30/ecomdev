<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;

class FrontendController extends Controller
{
    /**
     * Modern Daraz-style Homepage
     */
    public function index()
    {
        // 3-Tier Mega Menu Categories
        $categories = Category::where('is_active', true)
            ->with(['subCategories.childCategories'])
            ->withCount('products')
            ->orderBy('id', 'asc')
            ->get();

        // Hero Sliders: Load from customizable settings or Banner table
        $heroBanners = collect();
        for ($i = 1; $i <= 3; $i++) {
            $isActive = \App\Models\Setting::get("slider_{$i}_active", $i <= 2 ? '1' : '0') === '1';
            $title = \App\Models\Setting::get("slider_{$i}_title");
            if ($isActive && !empty($title)) {
                $heroBanners->push((object)[
                    'badge' => \App\Models\Setting::get("slider_{$i}_badge", 'Verified Electronic Component'),
                    'title' => $title,
                    'subtitle' => \App\Models\Setting::get("slider_{$i}_subtitle", ''),
                    'image' => \App\Models\Setting::get("slider_{$i}_image") ?: 'https://images.unsplash.com/photo-1518770660439-4636190af475?w=1200&auto=format&fit=crop&q=80',
                    'link_url' => \App\Models\Setting::get("slider_{$i}_link", '/shop'),
                    'button_text' => \App\Models\Setting::get("slider_{$i}_button_text", 'Explore Collection'),
                ]);
            }
        }

        // Fallback to Banner table if no settings configured yet
        if ($heroBanners->isEmpty()) {
            $dbBanners = Banner::whereIn('placement', ['hero_slider', 'main_slider'])
                ->where('is_active', true)
                ->orderBy('display_order')
                ->get();
            if ($dbBanners->isNotEmpty()) {
                $heroBanners = $dbBanners->map(fn($b) => (object)[
                    'badge' => 'Verified Electronic Component',
                    'title' => $b->title,
                    'subtitle' => $b->subtitle,
                    'image' => $b->image,
                    'link_url' => $b->link ?? '/shop',
                    'button_text' => 'Explore Collection',
                ]);
            }
        }

        // Ultimate fallback if still empty
        if ($heroBanners->isEmpty()) {
            $heroBanners = collect([
                (object) [
                    'badge' => 'Verified Electronic Component',
                    'title' => 'STM32 & ESP32-S3 IoT Development Boards',
                    'image' => 'https://images.unsplash.com/photo-1518770660439-4636190af475?w=1200&auto=format&fit=crop&q=80',
                    'link_url' => '/shop',
                    'subtitle' => 'Official Enterprise Electronics Distribution in Bangladesh',
                    'button_text' => 'Explore Collection',
                ],
                (object) [
                    'badge' => 'Premium Hardware',
                    'title' => 'Professional Quick 861DW Soldering Rework Stations',
                    'image' => 'https://images.unsplash.com/photo-1581092160607-ee22621dd758?w=1200&auto=format&fit=crop&q=80',
                    'link_url' => '/shop',
                    'subtitle' => '1000W High Power Digital SMD Rework Master Kit',
                    'button_text' => 'Shop Equipment',
                ]
            ]);
        }

        $promoBanners = Banner::where('placement', 'promo_strip')->where('is_active', true)->latest()->take(2)->get();

        // Flash Sale Items (items with discount or marked flash sale)
        $flashSaleProducts = Product::where('is_active', true)
            ->where(function ($q) {
                $q->where('is_flash_sale', true)->orWhereNotNull('discount_price');
            })
            ->with(['category', 'brand', 'variants'])
            ->latest()
            ->take(6)
            ->get();

        // If less than 4, grab any active products to showcase flash sale
        if ($flashSaleProducts->count() < 4) {
            $flashSaleProducts = Product::where('is_active', true)->with(['category', 'brand'])->latest()->take(6)->get();
        }

        // Official PCB Brands (DarazMall Style)
        $brands = Brand::withCount('products')->take(8)->get();

        // Featured & Top Categories
        $featuredCategories = Category::where('is_active', true)->withCount('products')->take(8)->get();

        // Category-wise products grouping
        $categoriesWithProducts = Category::where('is_active', true)
            ->whereHas('products', function ($q) {
                $q->where('is_active', true);
            })
            ->with(['products' => function ($q) {
                $q->where('is_active', true)
                    ->with(['category', 'brand', 'variants'])
                    ->latest();
            }, 'subCategories'])
            ->withCount('products')
            ->orderBy('display_order', 'asc')
            ->get();

        // "Just For You" Recommendation Grid
        $justForYouProducts = Product::where('is_active', true)
            ->with(['category', 'brand', 'variants'])
            ->latest()
            ->paginate(12);

        return view('frontend.home', compact(
            'categories',
            'heroBanners',
            'promoBanners',
            'flashSaleProducts',
            'brands',
            'featuredCategories',
            'categoriesWithProducts',
            'justForYouProducts'
        ));
    }

    /**
     * Order Tracking Page
     */
    public function trackOrder(Request $request)
    {
        $order = null;
        $orderNo = $request->get('order_no');
        $phone = $request->get('phone');

        if ($orderNo || $phone) {
            $query = Order::with(['items.product', 'statusLogs', 'courierConsignment']);
            if ($orderNo) {
                $query->where('order_no', trim($orderNo));
            }
            if ($phone) {
                $query->where('shipping_phone', trim($phone));
            }
            $order = $query->latest()->first();
        }

        return view('frontend.track_order', compact('order', 'orderNo', 'phone'));
    }
}
