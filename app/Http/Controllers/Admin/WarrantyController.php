<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Warranty;
use App\Services\WarrantyService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class WarrantyController extends Controller
{
    public function __construct(protected WarrantyService $warrantyService) {}

    public function index(Request $request)
    {
        $query = Warranty::with(['product', 'order', 'customer'])->latest();

        // Search
        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('warranty_code', 'LIKE', "%{$search}%")
                    ->orWhere('serial_number', 'LIKE', "%{$search}%")
                    ->orWhere('customer_name', 'LIKE', "%{$search}%")
                    ->orWhere('customer_phone', 'LIKE', "%{$search}%")
                    ->orWhereHas('order', function ($oq) use ($search) {
                        $oq->where('order_no', 'LIKE', "%{$search}%");
                    })
                    ->orWhereHas('product', function ($pq) use ($search) {
                        $pq->where('name', 'LIKE', "%{$search}%");
                    });
            });
        }

        // Status filter
        $today = Carbon::now()->toDateString();
        $in30Days = Carbon::now()->addDays(30)->toDateString();

        if ($request->filled('status')) {
            $status = $request->status;
            if ($status === 'active') {
                $query->where('status', 'active')->where('end_date', '>=', $today);
            } elseif ($status === 'expiring_soon') {
                $query->where('status', 'active')
                    ->where('end_date', '>=', $today)
                    ->where('end_date', '<=', $in30Days);
            } elseif ($status === 'expired') {
                $query->where(function ($q) use ($today) {
                    $q->where('status', 'expired')
                        ->orWhere(function ($q2) use ($today) {
                            $q2->where('status', 'active')->where('end_date', '<', $today);
                        });
                });
            } elseif (in_array($status, ['voided', 'claimed'])) {
                $query->where('status', $status);
            }
        }

        $warranties = $query->paginate(20)->withQueryString();

        // Metrics & KPI calculations
        $kpis = [
            'total' => Warranty::count(),
            'active' => Warranty::where('status', 'active')->where('end_date', '>=', $today)->count(),
            'expiring_soon' => Warranty::where('status', 'active')->where('end_date', '>=', $today)->where('end_date', '<=', $in30Days)->count(),
            'expired' => Warranty::where('status', 'expired')->orWhere(fn ($q) => $q->where('status', 'active')->where('end_date', '<', $today))->count(),
            'claimed' => Warranty::where('status', 'claimed')->count(),
        ];

        $products = Product::where('is_active', true)->select('id', 'name', 'sku', 'warranty')->orderBy('name')->get();

        // Verification query if initiated from search tool
        $verifiedWarranty = null;
        if ($request->filled('verify_code')) {
            $verifiedWarranty = $this->warrantyService->verifyWarranty($request->verify_code);
        }

        return view('admin.warranties.index', compact('warranties', 'kpis', 'products', 'verifiedWarranty'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|max:50',
            'customer_email' => 'nullable|email|max:255',
            'serial_number' => 'nullable|string|max:100',
            'order_id' => 'nullable|exists:orders,id',
            'warranty_period' => 'nullable|string|max:100',
            'warranty_days' => 'nullable|integer|min:1',
            'start_date' => 'required|date',
            'status' => 'required|in:active,claimed,voided',
            'admin_notes' => 'nullable|string',
        ]);

        $warranty = $this->warrantyService->createManualWarranty($validated);

        return redirect()->route('admin.warranties.index')
            ->with('success', "Warranty {$warranty->warranty_code} registered successfully with {$warranty->remaining_days} days coverage!");
    }

    public function update(Request $request, Warranty $warranty)
    {
        $validated = $request->validate([
            'serial_number' => 'nullable|string|max:100',
            'status' => 'required|in:active,expired,claimed,voided',
            'end_date' => 'required|date',
            'claim_notes' => 'nullable|string',
            'admin_notes' => 'nullable|string',
        ]);

        $warranty->update($validated);

        return redirect()->back()->with('success', "Warranty {$warranty->warranty_code} updated successfully!");
    }

    public function verify(Request $request)
    {
        $code = $request->get('code') ?? $request->get('query');
        $warranty = $this->warrantyService->verifyWarranty((string) $code);

        if ($request->wantsJson()) {
            if (! $warranty) {
                return response()->json(['found' => false, 'message' => 'No warranty record found matching query.'], 404);
            }

            return response()->json([
                'found' => true,
                'warranty_code' => $warranty->warranty_code,
                'serial_number' => $warranty->serial_number,
                'product_name' => $warranty->product->name,
                'customer_name' => $warranty->customer_name,
                'customer_phone' => $warranty->customer_phone,
                'warranty_period' => $warranty->warranty_period,
                'start_date' => $warranty->start_date->format('Y-m-d'),
                'end_date' => $warranty->end_date->format('Y-m-d'),
                'remaining_days' => $warranty->remaining_days,
                'status' => $warranty->status,
                'is_valid' => $warranty->is_valid,
                'badge' => $warranty->status_badge,
            ]);
        }

        return redirect()->route('admin.warranties.index', ['verify_code' => $code]);
    }

    public function destroy(Warranty $warranty)
    {
        $warranty->delete();

        return redirect()->route('admin.warranties.index')->with('success', 'Warranty record deleted successfully.');
    }
}
