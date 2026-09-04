<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Warranty;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class WarrantyController extends Controller
{
    /**
     * Customer Registered Warranties.
     */
    public function customerWarranties(Request $request): JsonResponse
    {
        $customer = $request->user();

        $warranties = Warranty::where('customer_id', $customer->id)
            ->with(['product', 'order'])
            ->orderBy('id', 'desc')
            ->get()
            ->map(function ($w) {
                return [
                    'id' => $w->id,
                    'warranty_code' => $w->warranty_code,
                    'serial_number' => $w->serial_number,
                    'product_name' => $w->product?->name ?? 'Electronics Item',
                    'product_thumbnail' => $w->product?->thumbnail ? asset('storage/'.$w->product->thumbnail) : null,
                    'order_no' => $w->order?->order_no,
                    'warranty_period' => $w->warranty_period,
                    'warranty_days' => (int) $w->warranty_days,
                    'start_date' => $w->start_date?->format('Y-m-d'),
                    'end_date' => $w->end_date?->format('Y-m-d'),
                    'status' => $w->status,
                    'is_valid' => $w->status === 'active' && ($w->end_date ? now()->lte($w->end_date) : true),
                    'days_remaining' => $w->end_date ? max(0, now()->diffInDays($w->end_date, false)) : null,
                    'claim_notes' => $w->claim_notes,
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $warranties,
        ]);
    }

    /**
     * Instant Serial Number Verification (for Barcode / QR Scanner).
     */
    public function verifyWarranty(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'serial_no' => 'required|string|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Serial number is required.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $serial = trim($request->serial_no);

        $warranty = Warranty::where('serial_number', $serial)
            ->orWhere('warranty_code', $serial)
            ->with(['product', 'order'])
            ->first();

        if (! $warranty) {
            return response()->json([
                'success' => false,
                'message' => "No valid warranty record found for serial/code '{$serial}'.",
            ], 404);
        }

        $isValid = $warranty->status === 'active' && ($warranty->end_date ? now()->lte($warranty->end_date) : true);
        $daysRemaining = $warranty->end_date ? max(0, now()->diffInDays($warranty->end_date, false)) : null;

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $warranty->id,
                'warranty_code' => $warranty->warranty_code,
                'serial_number' => $warranty->serial_number,
                'product_name' => $warranty->product?->name ?? 'PCB / Hardware Product',
                'product_thumbnail' => $warranty->product?->thumbnail ? asset('storage/'.$warranty->product->thumbnail) : null,
                'customer_name' => $warranty->customer_name,
                'start_date' => $warranty->start_date?->format('Y-m-d'),
                'end_date' => $warranty->end_date?->format('Y-m-d'),
                'status' => $warranty->status,
                'is_valid' => $isValid,
                'days_remaining' => $daysRemaining,
                'warranty_period' => $warranty->warranty_period,
            ],
        ]);
    }

    /**
     * Submit Warranty Claim.
     */
    public function claimWarranty(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'warranty_id' => 'required|exists:product_warranties,id',
            'issue_description' => 'required|string|max:1000',
            'photos' => 'nullable|array|max:5',
            'photos.*' => 'nullable|image|max:4096',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $customer = $request->user();
        $warranty = Warranty::where('id', $request->warranty_id)
            ->where('customer_id', $customer->id)
            ->first();

        if (! $warranty) {
            return response()->json([
                'success' => false,
                'message' => 'Warranty record not found or does not belong to your account.',
            ], 404);
        }

        if ($warranty->status === 'claimed') {
            return response()->json([
                'success' => false,
                'message' => 'A warranty claim has already been submitted for this item.',
            ], 422);
        }

        // Store photos if uploaded
        $photoPaths = [];
        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $photo) {
                $path = $photo->store('warranties/claims', 'public');
                $photoPaths[] = $path;
            }
        }

        $warranty->update([
            'status' => 'claimed',
            'claim_notes' => json_encode([
                'description' => $request->issue_description,
                'photos' => $photoPaths,
                'claimed_at' => now()->toIso8601String(),
            ]),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Warranty claim submitted successfully. Our support engineer will inspect and contact you.',
            'data' => [
                'warranty_id' => $warranty->id,
                'status' => 'claimed',
                'claimed_at' => now()->toIso8601String(),
            ],
        ]);
    }
}
