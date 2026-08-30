<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Supplier;
use App\Services\InventoryService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PurchaseController extends Controller
{
    protected InventoryService $inventoryService;

    public function __construct(InventoryService $inventoryService)
    {
        $this->inventoryService = $inventoryService;
    }

    public function index(Request $request)
    {
        $timeFilter = $request->get('filter', 'all'); // today, this_month, this_year, due
        $supplierId = $request->get('supplier_id');

        $query = Purchase::with(['supplier', 'items.product'])->latest('purchase_date');

        if ($timeFilter === 'today') {
            $query->whereDate('purchase_date', Carbon::today());
        } elseif ($timeFilter === 'this_month') {
            $query->whereMonth('purchase_date', Carbon::now()->month)->whereYear('purchase_date', Carbon::now()->year);
        } elseif ($timeFilter === 'this_year') {
            $query->whereYear('purchase_date', Carbon::now()->year);
        } elseif ($timeFilter === 'due') {
            $query->where('due_amount', '>', 0);
        }

        if ($supplierId) {
            $query->where('supplier_id', $supplierId);
        }

        $purchases = $query->paginate(20)->withQueryString();
        $suppliers = Supplier::all();

        // Metric summaries for headers
        $todayTotal = Purchase::whereDate('purchase_date', Carbon::today())->sum('grand_total');
        $monthTotal = Purchase::whereMonth('purchase_date', Carbon::now()->month)->whereYear('purchase_date', Carbon::now()->year)->sum('grand_total');
        $yearTotal = Purchase::whereYear('purchase_date', Carbon::now()->year)->sum('grand_total');
        $totalDue = Supplier::sum('current_due');

        return view('admin.purchases.index', compact(
            'purchases', 'suppliers', 'timeFilter', 'supplierId',
            'todayTotal', 'monthTotal', 'yearTotal', 'totalDue'
        ));
    }

    public function create()
    {
        $suppliers = Supplier::where('is_active', true)->get();
        $products = Product::with('variants')->where('is_active', true)->get();

        return view('admin.purchases.create', compact('suppliers', 'products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'purchase_date' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.unit_cost' => 'required|numeric|min:0',
            'items.*.quantity' => 'required|integer|min:1',
            'paid_amount' => 'nullable|numeric|min:0',
        ]);

        DB::transaction(function () use ($request) {
            $subtotal = 0;
            $itemsData = [];

            foreach ($request->items as $item) {
                $itemSubtotal = $item['unit_cost'] * $item['quantity'];
                $subtotal += $itemSubtotal;

                $itemsData[] = [
                    'product_id' => $item['product_id'],
                    'variant_id' => $item['variant_id'] ?? null,
                    'unit_cost' => $item['unit_cost'],
                    'quantity' => $item['quantity'],
                    'subtotal' => $itemSubtotal,
                    'batch_no' => $item['batch_no'] ?? null,
                    'serial_numbers' => ! empty($item['serial_numbers']) ? explode(',', $item['serial_numbers']) : null,
                ];

                // Increase stock in inventory
                $this->inventoryService->addStockFromPurchase(
                    $item['product_id'],
                    $item['variant_id'] ?? null,
                    $item['quantity'],
                    $item['unit_cost']
                );
            }

            $discount = (float) ($request->discount ?? 0);
            $tax = (float) ($request->tax ?? 0);
            $shipping = (float) ($request->shipping_cost ?? 0);
            $grandTotal = ($subtotal - $discount) + $tax + $shipping;
            $paidAmount = (float) ($request->paid_amount ?? 0);
            $dueAmount = max(0, $grandTotal - $paidAmount);

            $purchaseNo = 'PO-'.date('Ymd').'-'.rand(1000, 9999);

            $purchase = Purchase::create([
                'supplier_id' => $request->supplier_id,
                'purchase_no' => $purchaseNo,
                'supplier_invoice_no' => $request->supplier_invoice_no,
                'purchase_date' => $request->purchase_date,
                'subtotal' => $subtotal,
                'discount' => $discount,
                'tax' => $tax,
                'shipping_cost' => $shipping,
                'grand_total' => $grandTotal,
                'paid_amount' => $paidAmount,
                'due_amount' => $dueAmount,
                'payment_status' => $dueAmount == 0 ? 'paid' : ($paidAmount > 0 ? 'partial' : 'due'),
                'status' => 'received',
                'notes' => $request->notes,
                'created_by' => auth()->id(),
            ]);

            foreach ($itemsData as $iData) {
                $purchase->items()->create($iData);
            }

            // If paid anything, record supplier payment
            if ($paidAmount > 0) {
                $purchase->supplier->payments()->create([
                    'purchase_id' => $purchase->id,
                    'payment_date' => $request->purchase_date,
                    'amount' => $paidAmount,
                    'payment_method' => $request->payment_method ?? 'cash',
                    'notes' => 'Initial payment for '.$purchaseNo,
                    'created_by' => auth()->id(),
                ]);
            }

            $purchase->supplier->recalculateDue();
        });

        return redirect()->route('admin.purchases.index')->with('success', 'Purchase recorded and inventory updated successfully!');
    }

    public function show(Purchase $purchase)
    {
        $purchase->load(['supplier', 'items.product', 'items.variant', 'payments', 'createdBy']);

        return view('admin.purchases.show', compact('purchase'));
    }
}
