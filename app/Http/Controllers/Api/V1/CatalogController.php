<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Models\Brand;
use App\Models\Campaign;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CatalogController extends Controller
{
    /**
     * Home Dashboard Feed for Mobile App.
     */
    public function home(): JsonResponse
    {
        // 1. Sliders & Banners
        $banners = Banner::where('is_active', true)
            ->orderBy('sort_order')
            ->get()
            ->map(function ($banner) {
                return [
                    'id' => $banner->id,
                    'title' => $banner->title,
                    'subtitle' => $banner->subtitle ?? null,
                    'image' => $banner->image ? asset('storage/'.$banner->image) : null,
                    'link_url' => $banner->link_url ?? null,
                    'action_type' => $banner->action_type ?? 'url',
                ];
            });

        // 2. Featured Categories
        $categories = Category::where('is_active', true)
            ->where('is_featured', true)
            ->orderBy('sort_order')
            ->take(12)
            ->get()
            ->map(function ($cat) {
                return [
                    'id' => $cat->id,
                    'name' => $cat->name,
                    'slug' => $cat->slug,
                    'image' => $cat->image ? asset('storage/'.$cat->image) : null,
                    'icon' => $cat->icon ?? null,
                ];
            });

        // 3. Active Flash Campaigns
        $flashCampaigns = Campaign::where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('start_date')->orWhere('start_date', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('end_date')->orWhere('end_date', '>=', now());
            })
            ->orderBy('id', 'desc')
            ->take(3)
            ->get()
            ->map(function ($c) {
                return [
                    'id' => $c->id,
                    'name' => $c->name,
                    'banner' => $c->banner ? asset('storage/'.$c->banner) : null,
                    'discount_percentage' => $c->discount_percentage ?? null,
                    'start_date' => $c->start_date?->toIso8601String(),
                    'end_date' => $c->end_date?->toIso8601String(),
                ];
            });

        // 4. Popular Brands
        $brands = Brand::where('is_active', true)
            ->where('is_featured', true)
            ->orderBy('sort_order')
            ->take(10)
            ->get()
            ->map(function ($brand) {
                return [
                    'id' => $brand->id,
                    'name' => $brand->name,
                    'slug' => $brand->slug,
                    'logo' => $brand->logo ? asset('storage/'.$brand->logo) : null,
                ];
            });

        // 5. Flash Deals / Flash Sale Products
        $flashDeals = Product::active()
            ->flashSale()
            ->with(['category', 'brand'])
            ->orderBy('sales_count', 'desc')
            ->take(8)
            ->get()
            ->map(fn ($p) => $this->formatProductCard($p));

        // 6. Featured Products
        $featuredProducts = Product::active()
            ->featured()
            ->with(['category', 'brand'])
            ->orderBy('id', 'desc')
            ->take(10)
            ->get()
            ->map(fn ($p) => $this->formatProductCard($p));

        // 7. Best Selling Products
        $bestSellers = Product::active()
            ->with(['category', 'brand'])
            ->orderBy('sales_count', 'desc')
            ->take(10)
            ->get()
            ->map(fn ($p) => $this->formatProductCard($p));

        // 8. New Arrivals
        $newArrivals = Product::active()
            ->with(['category', 'brand'])
            ->orderBy('id', 'desc')
            ->take(10)
            ->get()
            ->map(fn ($p) => $this->formatProductCard($p));

        return response()->json([
            'success' => true,
            'data' => [
                'banners' => $banners,
                'categories' => $categories,
                'flash_campaigns' => $flashCampaigns,
                'brands' => $brands,
                'flash_deals' => $flashDeals,
                'featured_products' => $featuredProducts,
                'best_sellers' => $bestSellers,
                'new_arrivals' => $newArrivals,
            ],
        ]);
    }

    /**
     * Hierarchical Categories Tree.
     */
    public function categories(): JsonResponse
    {
        $categories = Category::where('is_active', true)
            ->with([
                'subCategories' => function ($q) {
                    $q->where('is_active', true)->orderBy('sort_order')->with([
                        'childCategories' => function ($cq) {
                            $cq->where('is_active', true)->orderBy('sort_order');
                        },
                    ]);
                },
            ])
            ->orderBy('sort_order')
            ->get()
            ->map(function ($cat) {
                return [
                    'id' => $cat->id,
                    'name' => $cat->name,
                    'slug' => $cat->slug,
                    'image' => $cat->image ? asset('storage/'.$cat->image) : null,
                    'icon' => $cat->icon ?? null,
                    'sub_categories' => $cat->subCategories->map(function ($sub) {
                        return [
                            'id' => $sub->id,
                            'name' => $sub->name,
                            'slug' => $sub->slug,
                            'image' => $sub->image ? asset('storage/'.$sub->image) : null,
                            'child_categories' => $sub->childCategories->map(function ($child) {
                                return [
                                    'id' => $child->id,
                                    'name' => $child->name,
                                    'slug' => $child->slug,
                                    'image' => $child->image ? asset('storage/'.$child->image) : null,
                                ];
                            }),
                        ];
                    }),
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $categories,
        ]);
    }

    /**
     * Filterable & Paginated Product Catalog Feed.
     */
    public function products(Request $request): JsonResponse
    {
        $query = Product::active()->with(['category', 'brand']);

        // Search keyword
        if ($request->filled('q')) {
            $keyword = $request->q;
            $query->where(function ($q) use ($keyword) {
                $q->where('name', 'like', "%{$keyword}%")
                    ->orWhere('sku', 'like', "%{$keyword}%")
                    ->orWhere('short_description', 'like', "%{$keyword}%")
                    ->orWhere('description', 'like', "%{$keyword}%");
            });
        }

        // Category filter
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('category_slug')) {
            $query->whereHas('category', fn ($q) => $q->where('slug', $request->category_slug));
        }

        if ($request->filled('sub_category_id')) {
            $query->where('sub_category_id', $request->sub_category_id);
        }

        if ($request->filled('child_category_id')) {
            $query->where('child_category_id', $request->child_category_id);
        }

        // Brand filter
        if ($request->filled('brand_id')) {
            $query->where('brand_id', $request->brand_id);
        }

        // Price range
        if ($request->filled('min_price')) {
            $query->where('selling_price', '>=', $request->min_price);
        }
        if ($request->filled('max_price')) {
            $query->where('selling_price', '<=', $request->max_price);
        }

        // In Stock filter
        if ($request->boolean('in_stock_only')) {
            $query->where('stock_quantity', '>', 0);
        }

        // Sorting
        $sortBy = $request->get('sort', 'newest');
        switch ($sortBy) {
            case 'price_low_high':
                $query->orderBy('selling_price', 'asc');
                break;
            case 'price_high_low':
                $query->orderBy('selling_price', 'desc');
                break;
            case 'popular':
            case 'bestselling':
                $query->orderBy('sales_count', 'desc');
                break;
            case 'views':
                $query->orderBy('views_count', 'desc');
                break;
            case 'newest':
            default:
                $query->orderBy('id', 'desc');
                break;
        }

        $perPage = min((int) $request->get('per_page', 20), 50);
        $paginator = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $paginator->getCollection()->map(fn ($p) => $this->formatProductCard($p)),
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
            ],
        ]);
    }

    /**
     * Full Product Details Screen API.
     */
    public function productDetail(string $slugOrId): JsonResponse
    {
        $product = Product::active()
            ->where(function ($q) use ($slugOrId) {
                $q->where('slug', $slugOrId)->orWhere('id', $slugOrId);
            })
            ->with([
                'category',
                'subCategory',
                'childCategory',
                'brand',
                'images',
                'variants' => fn ($q) => $q->where('is_active', true)->with(['color', 'size']),
            ])
            ->first();

        if (! $product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found or currently unavailable.',
            ], 404);
        }

        // Increment view count
        $product->increment('views_count');

        // Gallery Images
        $gallery = collect([
            ['id' => 0, 'image' => $product->thumbnail ? asset('storage/'.$product->thumbnail) : null, 'is_primary' => true],
        ]);

        foreach ($product->images as $img) {
            $gallery->push([
                'id' => $img->id,
                'image' => asset('storage/'.$img->image_path),
                'is_primary' => false,
            ]);
        }

        // Color & Size Variant matrices
        $variants = $product->variants->map(function ($v) {
            return [
                'id' => $v->id,
                'name' => $v->variant_name,
                'sku' => $v->sku,
                'color' => $v->color ? [
                    'id' => $v->color->id,
                    'name' => $v->color->name,
                    'code' => $v->color->code ?? '#000000',
                ] : null,
                'size' => $v->size ? [
                    'id' => $v->size->id,
                    'name' => $v->size->name,
                ] : null,
                'selling_price' => (float) $v->selling_price,
                'discount_price' => $v->discount_price ? (float) $v->discount_price : null,
                'effective_price' => (float) $v->effective_price,
                'stock_quantity' => (int) $v->stock_quantity,
                'image' => $v->image ? asset('storage/'.$v->image) : null,
                'in_stock' => $v->stock_quantity > 0,
            ];
        });

        // Related Products
        $relatedProducts = Product::active()
            ->where('id', '!=', $product->id)
            ->where('category_id', $product->category_id)
            ->take(6)
            ->get()
            ->map(fn ($p) => $this->formatProductCard($p));

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $product->id,
                'name' => $product->name,
                'slug' => $product->slug,
                'sku' => $product->sku,
                'barcode' => $product->barcode,
                'category' => $product->category ? [
                    'id' => $product->category->id,
                    'name' => $product->category->name,
                    'slug' => $product->category->slug,
                ] : null,
                'brand' => $product->brand ? [
                    'id' => $product->brand->id,
                    'name' => $product->brand->name,
                    'logo' => $product->brand->logo ? asset('storage/'.$product->brand->logo) : null,
                ] : null,
                'selling_price' => (float) $product->selling_price,
                'discount_price' => $product->discount_price ? (float) $product->discount_price : null,
                'effective_price' => (float) $product->effective_price,
                'discount_percentage' => $product->discount_percentage,
                'stock_quantity' => (int) $product->stock_quantity,
                'in_stock' => $product->stock_quantity > 0,
                'has_variants' => (bool) $product->has_variants,
                'warranty' => $product->warranty,
                'short_description' => $product->short_description,
                'description' => $product->description,
                'specifications' => $product->specifications,
                'datasheet_pdf' => $product->datasheet_pdf ? asset('storage/'.$product->datasheet_pdf) : null,
                'images' => $gallery->filter(fn ($g) => ! empty($g['image']))->values(),
                'variants' => $variants,
                'related_products' => $relatedProducts,
            ],
        ]);
    }

    /**
     * Format concise product card for lists & grids.
     */
    private function formatProductCard(Product $product): array
    {
        return [
            'id' => $product->id,
            'name' => $product->name,
            'slug' => $product->slug,
            'sku' => $product->sku,
            'thumbnail' => $product->thumbnail ? asset('storage/'.$product->thumbnail) : null,
            'category_name' => $product->category?->name,
            'brand_name' => $product->brand?->name,
            'selling_price' => (float) $product->selling_price,
            'discount_price' => $product->discount_price ? (float) $product->discount_price : null,
            'effective_price' => (float) $product->effective_price,
            'discount_percentage' => $product->discount_percentage,
            'stock_quantity' => (int) $product->stock_quantity,
            'in_stock' => $product->stock_quantity > 0,
            'has_variants' => (bool) $product->has_variants,
            'warranty' => $product->warranty,
            'is_flash_sale' => (bool) $product->is_flash_sale,
            'is_featured' => (bool) $product->is_featured,
        ];
    }
}
