<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use App\Services\PosService;
use Illuminate\Http\Request;

class PosController extends Controller
{
    protected PosService $posService;

    public function __construct(PosService $posService)
    {
        $this->posService = $posService;
    }

    public function index()
    {
        $categories = Category::where('is_active', true)->get();
        $customers = Customer::where('is_active', true)->orderBy('name')->take(50)->get();
        $accounts = Account::where('is_active', true)->get();
        $products = Product::with(['variants.color', 'variants.size', 'category'])
            ->where('is_active', true)
            ->take(40)
            ->get();

        return view('admin.pos.index', compact('categories', 'customers', 'accounts', 'products'));
    }

    public function search(Request $request)
    {
        $query = $request->get('q', '');
        $categoryId = $request->get('category_id');

        $products = Product::with(['variants.color', 'variants.size', 'category'])
            ->where('is_active', true)
            ->when($categoryId, fn ($q) => $q->where('category_id', $categoryId))
            ->when($query, function ($q) use ($query) {
                $q->where(function ($sub) use ($query) {
                    $sub->where('name', 'like', "%{$query}%")
                        ->orWhere('sku', 'like', "%{$query}%")
                        ->orWhere('barcode', 'like', "%{$query}%")
                        ->orWhereHas('variants', function ($v) use ($query) {
                            $v->where('sku', 'like', "%{$query}%")
                                ->orWhere('barcode', 'like', "%{$query}%")
                                ->orWhere('variant_name', 'like', "%{$query}%");
                        });
                });
            })
            ->take(30)
            ->get();

        return response()->json($products);
    }

    public function checkout(Request $request)
    {
        $request->validate([
            'cart' => 'required|array|min:1',
            'cart.*.product_id' => 'required|exists:products,id',
            'cart.*.quantity' => 'required|integer|min:1',
            'paid_amount' => 'required|numeric|min:0',
        ]);

        try {
            $order = $this->posService->processPosSale($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Sale completed successfully!',
                'order_id' => $order->id,
                'order_no' => $order->order_no,
                'receipt_url' => route('admin.pos.receipt', $order->id),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function receipt(Order $order, Request $request)
    {
        $order->load(['items.product', 'items.variant', 'customer']);
        $format = $request->get('format', '80mm'); // 80mm or 58mm

        return view('admin.pos.receipt', compact('order', 'format'));
    }
}
