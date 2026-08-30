<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Color;
use App\Models\Product;
use App\Models\Size;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $categoryId = $request->get('category_id');
        $stockFilter = $request->get('stock_filter');

        $query = Product::with(['category', 'brand', 'variants'])->latest();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%")
                    ->orWhere('pcb_model', 'like', "%{$search}%");
            });
        }

        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        if ($stockFilter === 'low_stock') {
            $query->lowStock();
        }

        $products = $query->paginate(20)->withQueryString();
        $categories = Category::all();

        return view('admin.products.index', compact('products', 'categories', 'search', 'categoryId', 'stockFilter'));
    }

    public function create()
    {
        $categories = Category::with('subCategories.childCategories')->where('is_active', true)->get();
        $brands = Brand::where('is_active', true)->get();
        $colors = Color::all();
        $sizes = Size::all();

        return view('admin.products.create', compact('categories', 'brands', 'colors', 'sizes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'sku' => 'required|string|unique:products,sku',
            'purchase_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'stock_quantity' => 'nullable|integer|min:0',
        ]);

        $thumbnailUrl = 'https://images.unsplash.com/photo-1518770660439-4636190af475?w=500&auto=format&fit=crop&q=60';

        // 1. Handle Primary Thumbnail Upload
        if ($request->hasFile('thumbnail')) {
            $file = $request->file('thumbnail');
            $fileName = 'thumb_'.time().'_'.Str::random(8).'.'.$file->getClientOriginalExtension();
            $file->move(public_path('uploads/products'), $fileName);
            $thumbnailUrl = '/uploads/products/'.$fileName;
        } elseif ($request->filled('thumbnail_url')) {
            $thumbnailUrl = $request->thumbnail_url;
        }

        $product = Product::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name).'-'.rand(100, 999),
            'sku' => strtoupper($request->sku),
            'barcode' => $request->barcode ?? strtoupper($request->sku),
            'category_id' => $request->category_id,
            'sub_category_id' => $request->sub_category_id,
            'child_category_id' => $request->child_category_id,
            'brand_id' => $request->brand_id,
            'pcb_model' => $request->pcb_model,
            'voltage' => $request->voltage,
            'warranty' => $request->warranty,
            'dimensions' => $request->dimensions,
            'weight' => $request->weight,
            'chipset' => $request->chipset,
            'purchase_price' => $request->purchase_price,
            'selling_price' => $request->selling_price,
            'discount_price' => $request->discount_price,
            'stock_quantity' => $request->stock_quantity ?? 0,
            'alert_threshold' => $request->alert_threshold ?? 5,
            'has_variants' => $request->has('has_variants'),
            'short_description' => $request->short_description,
            'description' => $request->description,
            'thumbnail' => $thumbnailUrl,
            'is_featured' => $request->has('is_featured'),
            'is_flash_sale' => $request->has('is_flash_sale'),
            'is_active' => true,
        ]);

        // 2. Handle Multiple Gallery Images Upload
        if ($request->hasFile('gallery_images')) {
            foreach ($request->file('gallery_images') as $idx => $gFile) {
                if ($gFile->isValid()) {
                    $gFileName = 'gal_'.time().'_'.Str::random(8).'.'.$gFile->getClientOriginalExtension();
                    $gFile->move(public_path('uploads/products/gallery'), $gFileName);
                    $product->images()->create([
                        'image_path' => '/uploads/products/gallery/'.$gFileName,
                        'display_order' => $idx + 1,
                    ]);
                }
            }
        }

        // Handle Variants if supplied
        if ($request->has('variants') && is_array($request->variants)) {
            foreach ($request->variants as $variantData) {
                if (! empty($variantData['variant_name'])) {
                    $product->variants()->create([
                        'variant_name' => $variantData['variant_name'],
                        'sku' => $variantData['sku'] ?? ($product->sku.'-'.Str::random(4)),
                        'barcode' => $variantData['barcode'] ?? null,
                        'color_id' => $variantData['color_id'] ?? null,
                        'size_id' => $variantData['size_id'] ?? null,
                        'purchase_price' => $variantData['purchase_price'] ?? $product->purchase_price,
                        'selling_price' => $variantData['selling_price'] ?? $product->selling_price,
                        'discount_price' => $variantData['discount_price'] ?? null,
                        'stock_quantity' => $variantData['stock_quantity'] ?? 0,
                        'alert_threshold' => $variantData['alert_threshold'] ?? 3,
                    ]);
                }
            }
        }

        return redirect()->route('admin.products.index')->with('success', 'Product created successfully with uploaded media!');
    }

    public function edit(Product $product)
    {
        $product->load(['variants', 'images']);
        $categories = Category::with('subCategories.childCategories')->where('is_active', true)->get();
        $brands = Brand::where('is_active', true)->get();
        $colors = Color::all();
        $sizes = Size::all();

        return view('admin.products.edit', compact('product', 'categories', 'brands', 'colors', 'sizes'));
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'sku' => 'required|string|unique:products,sku,'.$product->id,
            'purchase_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
        ]);

        $updateData = [
            'name' => $request->name,
            'sku' => strtoupper($request->sku),
            'barcode' => $request->barcode,
            'category_id' => $request->category_id,
            'sub_category_id' => $request->sub_category_id,
            'child_category_id' => $request->child_category_id,
            'brand_id' => $request->brand_id,
            'pcb_model' => $request->pcb_model,
            'voltage' => $request->voltage,
            'warranty' => $request->warranty,
            'dimensions' => $request->dimensions,
            'weight' => $request->weight,
            'chipset' => $request->chipset,
            'purchase_price' => $request->purchase_price,
            'selling_price' => $request->selling_price,
            'discount_price' => $request->discount_price,
            'stock_quantity' => $request->stock_quantity ?? $product->stock_quantity,
            'alert_threshold' => $request->alert_threshold ?? $product->alert_threshold,
            'short_description' => $request->short_description,
            'description' => $request->description,
            'is_featured' => $request->has('is_featured'),
            'is_flash_sale' => $request->has('is_flash_sale'),
            'is_active' => $request->has('is_active'),
        ];

        // 1. Handle Thumbnail Replacement
        if ($request->hasFile('thumbnail')) {
            $file = $request->file('thumbnail');
            $fileName = 'thumb_'.time().'_'.Str::random(8).'.'.$file->getClientOriginalExtension();
            $file->move(public_path('uploads/products'), $fileName);
            $updateData['thumbnail'] = '/uploads/products/'.$fileName;
        } elseif ($request->filled('thumbnail_url')) {
            $updateData['thumbnail'] = $request->thumbnail_url;
        }

        $product->update($updateData);

        // 2. Handle Additional Gallery Images Upload
        if ($request->hasFile('gallery_images')) {
            foreach ($request->file('gallery_images') as $idx => $gFile) {
                if ($gFile->isValid()) {
                    $gFileName = 'gal_'.time().'_'.Str::random(8).'.'.$gFile->getClientOriginalExtension();
                    $gFile->move(public_path('uploads/products/gallery'), $gFileName);
                    $product->images()->create([
                        'image_path' => '/uploads/products/gallery/'.$gFileName,
                        'display_order' => $product->images()->count() + $idx + 1,
                    ]);
                }
            }
        }

        return redirect()->route('admin.products.index')->with('success', 'Product updated successfully!');
    }

    public function destroy(Product $product)
    {
        $product->delete();

        return redirect()->route('admin.products.index')->with('success', 'Product deleted successfully!');
    }
}
