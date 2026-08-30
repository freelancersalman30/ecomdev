<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    /**
     * Catalog Shop Listing with Dynamic Filters
     */
    public function index(Request $request)
    {
        $query = Product::where('is_active', true)->with(['category', 'brand', 'variants']);

        // Search Filter
        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%")
                    ->orWhere('pcb_model', 'like', "%{$search}%")
                    ->orWhere('chipset', 'like', "%{$search}%")
                    ->orWhere('short_description', 'like', "%{$search}%");
            });
        }

        // Category Filter
        if ($categoryId = $request->get('category_id')) {
            $query->where('category_id', $categoryId);
        }

        // Sub Category Filter
        if ($subCategoryId = $request->get('sub_category_id')) {
            $query->where('sub_category_id', $subCategoryId);
        }

        // Brand Filter
        if ($brandId = $request->get('brand_id')) {
            $query->where('brand_id', $brandId);
        }

        // Price Range Filter
        if ($minPrice = $request->get('min_price')) {
            $query->where('selling_price', '>=', (float) $minPrice);
        }
        if ($maxPrice = $request->get('max_price')) {
            $query->where('selling_price', '<=', (float) $maxPrice);
        }

        // In Stock Only
        if ($request->has('in_stock')) {
            $query->where('stock_quantity', '>', 0);
        }

        // Sorting
        $sort = $request->get('sort', 'newest');
        switch ($sort) {
            case 'price_low':
                $query->orderBy('selling_price', 'asc');
                break;
            case 'price_high':
                $query->orderBy('selling_price', 'desc');
                break;
            case 'popular':
                $query->orderBy('views_count', 'desc');
                break;
            case 'featured':
                $query->where('is_featured', true)->latest();
                break;
            case 'newest':
            default:
                $query->latest();
                break;
        }

        $products = $query->paginate(16)->withQueryString();

        $categories = Category::where('is_active', true)->with('subCategories')->withCount('products')->get();
        $brands = Brand::withCount('products')->get();

        $selectedCategory = $categoryId ? Category::find($categoryId) : null;
        $selectedBrand = $brandId ? Brand::find($brandId) : null;

        return view('frontend.shop', compact(
            'products',
            'categories',
            'brands',
            'selectedCategory',
            'selectedBrand',
            'sort',
            'search'
        ));
    }

    /**
     * Category Shortcut Route (/category/{slug})
     */
    public function category($slug)
    {
        $category = Category::where('slug', $slug)->firstOrFail();

        return redirect()->route('shop.index', ['category_id' => $category->id]);
    }

    /**
     * Brand Shortcut Route (/brand/{slug})
     */
    public function brand($slug)
    {
        $brand = Brand::where('slug', $slug)->firstOrFail();

        return redirect()->route('shop.index', ['brand_id' => $brand->id]);
    }
}
