<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\Supplier;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ProductScannerController extends Controller
{
    /**
     * Display the Barcode Scanner & Rapid Auto-Entry Workstation
     */
    public function index(Request $request): View
    {
        $categories = Category::where('is_active', true)->orderBy('name')->get(['id', 'name']);
        $brands = Brand::where('is_active', true)->orderBy('name')->get(['id', 'name']);
        $suppliers = Schema::hasTable('suppliers')
            ? Supplier::where('is_active', true)->orderBy('name')->get(['id', 'name', 'phone'])
            : collect();

        // Recent products added or modified
        $recentScanned = Product::with(['category', 'brand'])
            ->latest('updated_at')
            ->take(8)
            ->get();

        $stats = [
            'total_products' => Product::count(),
            'in_stock' => Product::where('stock_quantity', '>', 0)->count(),
            'low_stock' => Product::where('stock_quantity', '<=', 5)->count(),
            'today_added' => Product::whereDate('created_at', today())->count(),
        ];

        return view('admin.products.scanner', compact('categories', 'brands', 'suppliers', 'recentScanned', 'stats'));
    }

    /**
     * Instant lookup by Barcode, SKU, or Serial Number
     */
    public function lookup(Request $request): JsonResponse
    {
        $query = trim((string) ($request->get('code') ?? $request->get('query')));

        if (empty($query)) {
            return response()->json([
                'success' => false,
                'message' => 'Please provide a barcode, SKU, or product code.',
            ], 422);
        }

        // 1. Direct match on barcode
        $product = Product::with(['category', 'brand'])
            ->where('barcode', $query)
            ->first();

        // 2. Direct match on SKU
        if (! $product) {
            $product = Product::with(['category', 'brand'])
                ->where('sku', $query)
                ->first();
        }

        // 3. Fallback: match by ID or partial match
        if (! $product && is_numeric($query)) {
            $product = Product::with(['category', 'brand'])
                ->where('id', (int) $query)
                ->first();
        }

        if ($product) {
            return response()->json([
                'success' => true,
                'exists' => true,
                'product' => [
                    'id' => $product->id,
                    'name' => $product->name,
                    'sku' => $product->sku,
                    'barcode' => $product->barcode,
                    'category_id' => $product->category_id,
                    'category_name' => $product->category?->name ?? 'Uncategorized',
                    'brand_id' => $product->brand_id,
                    'brand_name' => $product->brand?->name ?? 'Generic',
                    'purchase_price' => (float) $product->purchase_price,
                    'selling_price' => (float) $product->selling_price,
                    'discount_price' => (float) $product->discount_price,
                    'stock_quantity' => (int) $product->stock_quantity,
                    'alert_threshold' => (int) $product->alert_threshold,
                    'warranty' => $product->warranty,
                    'thumbnail' => $product->thumbnail,
                    'edit_url' => route('admin.products.edit', $product->id),
                    'label_url' => route('admin.products.barcode_label', $product->id),
                ],
                'message' => "Found existing product: {$product->name}",
            ]);
        }

        // Generate suggested SKU and Name for new item
        $suggestedSku = $this->generateUniqueSku($query);
        $suggestedName = 'Component '.strtoupper(substr($query, -6));

        return response()->json([
            'success' => true,
            'exists' => false,
            'scanned_code' => $query,
            'suggestion' => [
                'barcode' => $query,
                'sku' => $suggestedSku,
                'name' => $suggestedName,
                'purchase_price' => 0.00,
                'selling_price' => 0.00,
                'stock_quantity' => 1,
            ],
            'message' => "New barcode detected: {$query}",
        ]);
    }

    /**
     * Fast 1-Click Auto-Entry for New Product
     */
    public function quickStore(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'barcode' => 'required|string|max:100',
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'brand_id' => 'nullable|exists:brands,id',
            'sku' => 'nullable|string|max:100',
            'purchase_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'stock_quantity' => 'nullable|integer|min:0',
            'alert_threshold' => 'nullable|integer|min:0',
            'warranty' => 'nullable|string|max:100',
            'supplier_id' => 'nullable|integer',
        ]);

        $barcode = trim($validated['barcode']);
        $sku = ! empty($validated['sku']) ? strtoupper(trim($validated['sku'])) : $this->generateUniqueSku($barcode);

        // Check if SKU already exists
        if (Product::where('sku', $sku)->exists()) {
            $sku = $this->generateUniqueSku($barcode);
        }

        // Check if barcode already exists
        $existing = Product::where('barcode', $barcode)->first();
        if ($existing) {
            $qtyToAdd = (int) ($validated['stock_quantity'] ?? 1);
            $existing->increment('stock_quantity', $qtyToAdd);
            if ($validated['purchase_price'] > 0) {
                $existing->update(['purchase_price' => $validated['purchase_price']]);
            }

            return response()->json([
                'success' => true,
                'action' => 'updated',
                'product' => $existing->fresh(['category', 'brand']),
                'message' => "Stock incremented by +{$qtyToAdd} for {$existing->name} (Total: {$existing->stock_quantity})",
            ]);
        }

        $defaultThumbnail = 'https://images.unsplash.com/photo-1518770660439-4636190af475?w=500&auto=format&fit=crop&q=60';

        $product = Product::create([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']).'-'.rand(1000, 9999),
            'sku' => $sku,
            'barcode' => $barcode,
            'category_id' => $validated['category_id'],
            'brand_id' => $validated['brand_id'] ?? null,
            'purchase_price' => $validated['purchase_price'],
            'selling_price' => $validated['selling_price'],
            'discount_price' => $validated['selling_price'],
            'stock_quantity' => $validated['stock_quantity'] ?? 1,
            'alert_threshold' => $validated['alert_threshold'] ?? 5,
            'warranty' => $validated['warranty'] ?? '1 Year Official Warranty',
            'thumbnail' => $defaultThumbnail,
            'is_active' => true,
        ]);

        return response()->json([
            'success' => true,
            'action' => 'created',
            'product' => [
                'id' => $product->id,
                'name' => $product->name,
                'sku' => $product->sku,
                'barcode' => $product->barcode,
                'category_name' => $product->category?->name ?? 'Uncategorized',
                'brand_name' => $product->brand?->name ?? 'Generic',
                'purchase_price' => (float) $product->purchase_price,
                'selling_price' => (float) $product->selling_price,
                'stock_quantity' => (int) $product->stock_quantity,
                'thumbnail' => $product->thumbnail,
                'edit_url' => route('admin.products.edit', $product->id),
                'label_url' => route('admin.products.barcode_label', $product->id),
            ],
            'message' => "Successfully cataloged product: {$product->name} (SKU: {$product->sku})",
        ]);
    }

    /**
     * Fast Stock-In / Quantity increment for an existing product
     */
    public function stockIn(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'purchase_price' => 'nullable|numeric|min:0',
            'selling_price' => 'nullable|numeric|min:0',
        ]);

        $product = Product::findOrFail($validated['product_id']);
        $product->increment('stock_quantity', $validated['quantity']);

        $updates = [];
        if (! empty($validated['purchase_price']) && $validated['purchase_price'] > 0) {
            $updates['purchase_price'] = $validated['purchase_price'];
        }
        if (! empty($validated['selling_price']) && $validated['selling_price'] > 0) {
            $updates['selling_price'] = $validated['selling_price'];
        }
        if (! empty($updates)) {
            $product->update($updates);
        }

        $fresh = $product->fresh(['category', 'brand']);

        return response()->json([
            'success' => true,
            'product' => [
                'id' => $fresh->id,
                'name' => $fresh->name,
                'sku' => $fresh->sku,
                'barcode' => $fresh->barcode,
                'category_name' => $fresh->category?->name ?? 'Uncategorized',
                'stock_quantity' => (int) $fresh->stock_quantity,
                'purchase_price' => (float) $fresh->purchase_price,
                'selling_price' => (float) $fresh->selling_price,
            ],
            'message' => "Added +{$validated['quantity']} units to {$fresh->name}. New stock: {$fresh->stock_quantity}",
        ]);
    }

    /**
     * Batch commit all scanned items collected in a scan session
     */
    public function batchCommit(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'items' => 'required|array|min:1',
            'items.*.barcode' => 'required|string',
            'items.*.name' => 'required|string',
            'items.*.category_id' => 'required|integer',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.purchase_price' => 'required|numeric|min:0',
            'items.*.selling_price' => 'required|numeric|min:0',
            'items.*.brand_id' => 'nullable|integer',
            'items.*.is_existing' => 'nullable|boolean',
            'items.*.product_id' => 'nullable|integer',
        ]);

        $processedCount = 0;
        $totalUnits = 0;

        DB::transaction(function () use ($validated, &$processedCount, &$totalUnits) {
            foreach ($validated['items'] as $item) {
                $qty = (int) $item['quantity'];
                $totalUnits += $qty;

                if (! empty($item['is_existing']) && ! empty($item['product_id'])) {
                    $prod = Product::find($item['product_id']);
                    if ($prod) {
                        $prod->increment('stock_quantity', $qty);
                        if ($item['purchase_price'] > 0) {
                            $prod->update(['purchase_price' => $item['purchase_price']]);
                        }
                        $processedCount++;

                        continue;
                    }
                }

                // Check by barcode
                $existing = Product::where('barcode', trim($item['barcode']))->first();
                if ($existing) {
                    $existing->increment('stock_quantity', $qty);
                    if ($item['purchase_price'] > 0) {
                        $existing->update(['purchase_price' => $item['purchase_price']]);
                    }
                    $processedCount++;

                    continue;
                }

                // Create new
                $sku = $this->generateUniqueSku($item['barcode']);
                Product::create([
                    'name' => $item['name'],
                    'slug' => Str::slug($item['name']).'-'.rand(1000, 9999),
                    'sku' => $sku,
                    'barcode' => trim($item['barcode']),
                    'category_id' => $item['category_id'],
                    'brand_id' => $item['brand_id'] ?? null,
                    'purchase_price' => $item['purchase_price'],
                    'selling_price' => $item['selling_price'],
                    'discount_price' => $item['selling_price'],
                    'stock_quantity' => $qty,
                    'alert_threshold' => 5,
                    'warranty' => '1 Year Official Warranty',
                    'thumbnail' => 'https://images.unsplash.com/photo-1518770660439-4636190af475?w=500&auto=format&fit=crop&q=60',
                    'is_active' => true,
                ]);

                $processedCount++;
            }
        });

        return response()->json([
            'success' => true,
            'processed_items' => $processedCount,
            'total_units' => $totalUnits,
            'message' => "Successfully committed {$processedCount} products ({$totalUnits} total units) to inventory!",
        ]);
    }

    /**
     * Printable Thermal Barcode & QR Sticker Label
     */
    public function printBarcode(Product $product): View
    {
        $product->loadMissing(['category', 'brand']);

        return view('admin.products.barcode_label', compact('product'));
    }

    /**
     * Helper to generate unique SKU
     */
    protected function generateUniqueSku(string $seed): string
    {
        $clean = preg_replace('/[^A-Za-z0-9]/', '', strtoupper($seed));
        $prefix = strlen($clean) >= 4 ? substr($clean, 0, 4) : 'PCB';

        do {
            $sku = $prefix.'-'.date('ym').'-'.strtoupper(Str::random(4));
        } while (Product::where('sku', $sku)->exists());

        return $sku;
    }
}
