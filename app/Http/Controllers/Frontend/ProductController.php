<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Setting;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display Product Detail Page
     */
    public function show($slug)
    {
        $product = Product::where('slug', $slug)
            ->where('is_active', true)
            ->with(['category', 'subCategory', 'brand', 'variants', 'images'])
            ->firstOrFail();

        // Increment View Count
        $product->increment('views_count');

        // Related Products from same category
        $relatedProducts = Product::where('is_active', true)
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->take(6)
            ->get();

        // Global delivery fee settings
        $settings = Setting::pluck('value', 'key')->toArray();
        $insideDhaka = $settings['inside_dhaka_charge'] ?? 70;
        $outsideDhaka = $settings['outside_dhaka_charge'] ?? 130;

        return view('frontend.product_detail', compact(
            'product',
            'relatedProducts',
            'insideDhaka',
            'outsideDhaka'
        ));
    }
}
