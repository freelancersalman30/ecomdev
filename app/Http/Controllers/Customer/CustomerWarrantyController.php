<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Warranty;
use App\Services\WarrantyService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomerWarrantyController extends Controller
{
    public function __construct(protected WarrantyService $warrantyService) {}

    /**
     * Authenticated customer's registered warranties
     */
    public function index(Request $request)
    {
        $customer = Auth::guard('customer')->user();

        $query = Warranty::with(['product', 'order'])
            ->where(function ($q) use ($customer) {
                $q->where('customer_id', $customer->id);
                if ($customer->phone) {
                    $q->orWhere('customer_phone', $customer->phone);
                }
            })
            ->latest();

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('warranty_code', 'LIKE', "%{$search}%")
                    ->orWhere('serial_number', 'LIKE', "%{$search}%")
                    ->orWhereHas('product', fn ($pq) => $pq->where('name', 'LIKE', "%{$search}%"))
                    ->orWhereHas('order', fn ($oq) => $oq->where('order_no', 'LIKE', "%{$search}%"));
            });
        }

        $warranties = $query->paginate(12)->withQueryString();

        // Customer's warranty summary stats
        $allWarranties = Warranty::where(function ($q) use ($customer) {
            $q->where('customer_id', $customer->id);
            if ($customer->phone) {
                $q->orWhere('customer_phone', $customer->phone);
            }
        })->get();

        $stats = [
            'total' => $allWarranties->count(),
            'active' => $allWarranties->filter(fn ($w) => $w->is_valid)->count(),
            'expiring_soon' => $allWarranties->filter(fn ($w) => $w->is_valid && $w->remaining_days <= 30)->count(),
            'expired' => $allWarranties->filter(fn ($w) => $w->is_expired)->count(),
        ];

        // Specific lookup result if user used verification input
        $lookupResult = null;
        if ($request->filled('lookup_code')) {
            $lookupResult = $this->warrantyService->verifyWarranty($request->lookup_code);
        }

        return view('customer.warranties.index', compact('warranties', 'stats', 'lookupResult'));
    }

    /**
     * Public warranty verification page (accessible by anyone)
     */
    public function publicVerify(Request $request)
    {
        $code = $request->get('code');
        $warranty = null;
        $searched = false;

        if ($request->filled('code')) {
            $searched = true;
            $warranty = $this->warrantyService->verifyWarranty($code);
        }

        return view('frontend.warranty_verify', compact('warranty', 'searched', 'code'));
    }
}
