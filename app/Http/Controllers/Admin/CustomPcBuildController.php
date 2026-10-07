<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\Category;
use App\Models\Customer;
use App\Models\CustomPcBuild;
use App\Models\Order;
use App\Models\Product;
use App\Services\OrderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CustomPcBuildController extends Controller
{
    public function __construct(protected OrderService $orderService) {}

    /**
     * Display a listing of custom PC builds
     */
    public function index(Request $request): View
    {
        $search = $request->get('search');
        $category = $request->get('category');
        $performanceLevel = $request->get('performance_level');
        $stockStatus = $request->get('stock_status');
        $status = $request->get('status');

        $query = CustomPcBuild::with('creator')->latest();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('build_code', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($category) {
            $query->where('category', $category);
        }

        if ($performanceLevel) {
            $query->where('performance_level', $performanceLevel);
        }

        if ($stockStatus) {
            $query->where('stock_status', $stockStatus);
        }

        if ($status !== null && $status !== '') {
            $query->where('is_published', (bool) $status);
        }

        $builds = $query->paginate(15)->withQueryString();

        // Summary metrics
        $metrics = [
            'total_builds' => CustomPcBuild::count(),
            'published_builds' => CustomPcBuild::where('is_published', true)->count(),
            'avg_price' => (float) CustomPcBuild::avg('final_price') ?? 0,
            'featured_count' => CustomPcBuild::where('is_featured', true)->count(),
        ];

        return view('admin.custom_pc_builds.index', compact(
            'builds',
            'metrics',
            'search',
            'category',
            'performanceLevel',
            'stockStatus',
            'status'
        ));
    }

    /**
     * Show the form for creating a new Custom PC build
     */
    public function create(): View
    {
        $defaultSlots = CustomPcBuild::getDefaultSlots();
        $categories = Category::where('is_active', true)->orderBy('name')->get();

        return view('admin.custom_pc_builds.create', compact('defaultSlots', 'categories'));
    }

    /**
     * Store a newly created Custom PC build in storage
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'nullable|string|max:100',
            'performance_level' => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,webp,svg|max:4096',
            'thumbnail_url' => 'nullable|string|max:500',
            'estimated_wattage' => 'nullable|integer|min:0',
            'regular_price' => 'nullable|numeric|min:0',
            'discount_type' => 'nullable|string|in:none,fixed,percentage',
            'discount_amount' => 'nullable|numeric|min:0',
            'final_price' => 'required|numeric|min:0',
            'stock_status' => 'nullable|string|in:in_stock,pre_order,out_of_stock',
            'is_published' => 'nullable|boolean',
            'is_featured' => 'nullable|boolean',
            'notes' => 'nullable|string',
            'components_json' => 'nullable|string',
        ]);

        $thumbnailPath = null;
        if ($request->hasFile('thumbnail')) {
            $file = $request->file('thumbnail');
            $dir = public_path('uploads/pc_builds');
            if (! file_exists($dir)) {
                mkdir($dir, 0755, true);
            }
            $fileName = 'pc_'.time().'_'.Str::random(8).'.'.$file->getClientOriginalExtension();
            $file->move($dir, $fileName);
            $thumbnailPath = '/uploads/pc_builds/'.$fileName;
        } elseif ($request->filled('thumbnail_url')) {
            $thumbnailPath = $request->thumbnail_url;
        }

        $components = [];
        if (! empty($validated['components_json'])) {
            $decoded = json_decode($validated['components_json'], true);
            if (is_array($decoded)) {
                $components = $decoded;
            }
        }

        // Recalculate or sanitize regular price from components if available
        $regularPrice = (float) ($validated['regular_price'] ?? 0);
        if ($regularPrice <= 0 && ! empty($components)) {
            $regularPrice = array_reduce($components, function ($carry, $item) {
                return $carry + ((float) ($item['subtotal'] ?? 0));
            }, 0.0);
        }

        $build = CustomPcBuild::create([
            'title' => $validated['title'],
            'category' => $validated['category'] ?? 'Gaming',
            'performance_level' => $validated['performance_level'] ?? 'Mid-Range',
            'description' => $validated['description'] ?? null,
            'thumbnail' => $thumbnailPath,
            'components' => $components,
            'estimated_wattage' => (int) ($validated['estimated_wattage'] ?? 450),
            'regular_price' => $regularPrice,
            'discount_type' => $validated['discount_type'] ?? 'none',
            'discount_amount' => (float) ($validated['discount_amount'] ?? 0),
            'final_price' => (float) $validated['final_price'],
            'stock_status' => $validated['stock_status'] ?? 'in_stock',
            'is_published' => $request->boolean('is_published', true),
            'is_featured' => $request->boolean('is_featured', false),
            'notes' => $validated['notes'] ?? null,
            'created_by' => auth()->id(),
        ]);

        return redirect()->route('admin.custom-pc-builds.show', $build->id)
            ->with('success', "Custom PC Build '{$build->title}' created successfully!");
    }

    /**
     * Display the specified Custom PC build details
     */
    public function show(CustomPcBuild $customPcBuild): View
    {
        $customPcBuild->load('creator');
        $customers = Customer::where('is_active', true)->orderBy('name')->take(50)->get();
        $accounts = Account::where('is_active', true)->get();

        return view('admin.custom_pc_builds.show', compact('customPcBuild', 'customers', 'accounts'));
    }

    /**
     * Show the form for editing the specified Custom PC build
     */
    public function edit(CustomPcBuild $customPcBuild): View
    {
        $defaultSlots = CustomPcBuild::getDefaultSlots();
        $categories = Category::where('is_active', true)->orderBy('name')->get();

        return view('admin.custom_pc_builds.edit', compact('customPcBuild', 'defaultSlots', 'categories'));
    }

    /**
     * Update the specified Custom PC build in storage
     */
    public function update(Request $request, CustomPcBuild $customPcBuild): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'nullable|string|max:100',
            'performance_level' => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,webp,svg|max:4096',
            'thumbnail_url' => 'nullable|string|max:500',
            'estimated_wattage' => 'nullable|integer|min:0',
            'regular_price' => 'nullable|numeric|min:0',
            'discount_type' => 'nullable|string|in:none,fixed,percentage',
            'discount_amount' => 'nullable|numeric|min:0',
            'final_price' => 'required|numeric|min:0',
            'stock_status' => 'nullable|string|in:in_stock,pre_order,out_of_stock',
            'is_published' => 'nullable|boolean',
            'is_featured' => 'nullable|boolean',
            'notes' => 'nullable|string',
            'components_json' => 'nullable|string',
        ]);

        $thumbnailPath = $customPcBuild->thumbnail;
        if ($request->hasFile('thumbnail')) {
            $file = $request->file('thumbnail');
            $dir = public_path('uploads/pc_builds');
            if (! file_exists($dir)) {
                mkdir($dir, 0755, true);
            }
            $fileName = 'pc_'.time().'_'.Str::random(8).'.'.$file->getClientOriginalExtension();
            $file->move($dir, $fileName);
            $thumbnailPath = '/uploads/pc_builds/'.$fileName;
        } elseif ($request->filled('thumbnail_url')) {
            $thumbnailPath = $request->thumbnail_url;
        }

        $components = $customPcBuild->components ?: [];
        if (! empty($validated['components_json'])) {
            $decoded = json_decode($validated['components_json'], true);
            if (is_array($decoded)) {
                $components = $decoded;
            }
        }

        $regularPrice = (float) ($validated['regular_price'] ?? 0);
        if ($regularPrice <= 0 && ! empty($components)) {
            $regularPrice = array_reduce($components, function ($carry, $item) {
                return $carry + ((float) ($item['subtotal'] ?? 0));
            }, 0.0);
        }

        $customPcBuild->update([
            'title' => $validated['title'],
            'category' => $validated['category'] ?? 'Gaming',
            'performance_level' => $validated['performance_level'] ?? 'Mid-Range',
            'description' => $validated['description'] ?? null,
            'thumbnail' => $thumbnailPath,
            'components' => $components,
            'estimated_wattage' => (int) ($validated['estimated_wattage'] ?? 450),
            'regular_price' => $regularPrice,
            'discount_type' => $validated['discount_type'] ?? 'none',
            'discount_amount' => (float) ($validated['discount_amount'] ?? 0),
            'final_price' => (float) $validated['final_price'],
            'stock_status' => $validated['stock_status'] ?? 'in_stock',
            'is_published' => $request->boolean('is_published', true),
            'is_featured' => $request->boolean('is_featured', false),
            'notes' => $validated['notes'] ?? null,
        ]);

        return redirect()->route('admin.custom-pc-builds.show', $customPcBuild->id)
            ->with('success', 'Custom PC Build updated successfully!');
    }

    /**
     * Remove the specified Custom PC build from storage
     */
    public function destroy(CustomPcBuild $customPcBuild): RedirectResponse
    {
        $title = $customPcBuild->title;
        $customPcBuild->delete();

        return redirect()->route('admin.custom-pc-builds.index')
            ->with('success', "Custom PC Build '{$title}' deleted successfully.");
    }

    /**
     * Duplicate / Clone an existing PC build
     */
    public function duplicate(CustomPcBuild $customPcBuild): RedirectResponse
    {
        $newBuild = $customPcBuild->replicate([
            'slug',
            'build_code',
        ]);

        $newBuild->title = $customPcBuild->title.' (Copy)';
        $newBuild->is_published = false;
        $newBuild->created_by = auth()->id();
        $newBuild->save();

        return redirect()->route('admin.custom-pc-builds.edit', $newBuild->id)
            ->with('success', 'Custom PC Build cloned. You can now tweak components and publish!');
    }

    /**
     * Generate a printable PC quotation & specification sheet
     */
    public function quotation(CustomPcBuild $customPcBuild): View
    {
        return view('admin.custom_pc_builds.quotation', compact('customPcBuild'));
    }

    /**
     * Live search products for component slots (AJAX API)
     */
    public function searchProducts(Request $request): JsonResponse
    {
        $q = trim($request->get('q', ''));
        $categoryId = $request->get('category_id');

        $products = Product::with(['category', 'brand'])
            ->where('is_active', true)
            ->when($categoryId, fn ($query) => $query->where('category_id', $categoryId))
            ->when($q, function ($query) use ($q) {
                $query->where(function ($sub) use ($q) {
                    $sub->where('name', 'like', "%{$q}%")
                        ->orWhere('sku', 'like', "%{$q}%")
                        ->orWhere('chipset', 'like', "%{$q}%")
                        ->orWhere('short_description', 'like', "%{$q}%");
                });
            })
            ->select([
                'id',
                'name',
                'sku',
                'selling_price',
                'discount_price',
                'stock_quantity',
                'thumbnail',
                'warranty',
                'category_id',
                'brand_id',
            ])
            ->take(25)
            ->get()
            ->map(function ($p) {
                $effectivePrice = ($p->discount_price && $p->discount_price > 0 && $p->discount_price < $p->selling_price)
                    ? (float) $p->discount_price
                    : (float) $p->selling_price;

                return [
                    'id' => $p->id,
                    'name' => $p->name,
                    'sku' => $p->sku,
                    'price' => $effectivePrice,
                    'regular_price' => (float) $p->selling_price,
                    'stock' => (int) $p->stock_quantity,
                    'thumbnail' => $p->thumbnail,
                    'warranty' => $p->warranty ?: 'Standard Warranty',
                    'category_name' => $p->category?->name ?? 'Hardware',
                ];
            });

        return response()->json($products);
    }

    /**
     * Convert the PC Build into a customer Order / POS Invoice
     */
    public function convertToOrder(Request $request, CustomPcBuild $customPcBuild): RedirectResponse
    {
        $validated = $request->validate([
            'customer_type' => 'required|string|in:existing,new,walkin',
            'customer_id' => 'nullable|exists:customers,id',
            'customer_name' => 'nullable|string|max:255',
            'customer_phone' => 'nullable|string|max:50',
            'customer_address' => 'nullable|string|max:500',
            'payment_method' => 'required|string',
            'account_id' => 'nullable|exists:accounts,id',
            'paid_amount' => 'nullable|numeric|min:0',
            'order_type' => 'required|string|in:pos,online',
        ]);

        $components = $customPcBuild->components ?: [];
        if (empty($components)) {
            return redirect()->back()->with('error', 'Cannot convert an empty PC build to an order.');
        }

        // Resolve Customer
        $customerId = null;
        $customerName = 'Walk-in Customer';
        $customerPhone = '01700000000';
        $customerAddress = 'Store Counter - DREAMERS PCB';
        $customerCity = 'Dhaka';

        if ($validated['customer_type'] === 'existing' && ! empty($validated['customer_id'])) {
            $cust = Customer::find($validated['customer_id']);
            if ($cust) {
                $customerId = $cust->id;
                $customerName = $cust->name;
                $customerPhone = $cust->phone;
                $customerAddress = $cust->address ?: 'Store Counter';
                $customerCity = $cust->city ?: 'Dhaka';
            }
        } elseif ($validated['customer_type'] === 'new' && ! empty($validated['customer_phone'])) {
            $cust = Customer::firstOrCreate(
                ['phone' => $validated['customer_phone']],
                [
                    'name' => $validated['customer_name'] ?: 'Customer',
                    'address' => $validated['customer_address'] ?: null,
                    'city' => 'Dhaka',
                    'is_active' => true,
                ]
            );
            $customerId = $cust->id;
            $customerName = $cust->name;
            $customerPhone = $cust->phone;
            $customerAddress = $cust->address ?: 'Store Counter';
        }

        // Map items
        $items = [];
        foreach ($components as $comp) {
            $productId = ! empty($comp['product_id']) ? (int) $comp['product_id'] : null;
            if ($productId) {
                $items[] = [
                    'product_id' => $productId,
                    'variant_id' => null,
                    'quantity' => (int) ($comp['quantity'] ?? 1),
                ];
            }
        }

        if (empty($items)) {
            return redirect()->back()->with('error', 'This PC build has no inventoried products linked to create order line items.');
        }

        // Calculate package discount difference if any
        $regularTotal = (float) $customPcBuild->regular_price;
        $finalPrice = (float) $customPcBuild->final_price;
        $packageDiscount = max(0, $regularTotal - $finalPrice);

        $orderData = [
            'customer_id' => $customerId,
            'order_type' => $validated['order_type'] ?? 'pos',
            'status' => 'completed',
            'shipping_name' => $customerName,
            'shipping_phone' => $customerPhone,
            'shipping_address' => $customerAddress,
            'shipping_city' => $customerCity,
            'discount' => $packageDiscount,
            'paid_amount' => (float) ($validated['paid_amount'] ?? $finalPrice),
            'payment_method' => $validated['payment_method'],
            'account_id' => $validated['account_id'] ?? 1,
            'admin_note' => "Assembled from Custom PC Build: {$customPcBuild->title} ({$customPcBuild->build_code}). Sold by: ".(auth()->user()->name ?? 'Admin'),
        ];

        session()->forget('incomplete_order_id');

        try {
            $order = $this->orderService->createOrder($orderData, $items);

            return redirect()->route('admin.orders.show', $order->id)
                ->with('success', "Order #{$order->order_no} created successfully from PC Build '{$customPcBuild->title}'!");
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'Failed to generate order: '.$e->getMessage());
        }
    }
}
