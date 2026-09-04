<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OrderTrackingController extends Controller
{
    /**
     * Customer Order History API.
     */
    public function orders(Request $request): JsonResponse
    {
        $customer = $request->user();

        $orders = Order::where('customer_id', $customer->id)
            ->with(['items.product'])
            ->orderBy('id', 'desc')
            ->paginate(15);

        $formatted = $orders->getCollection()->map(fn ($order) => $this->formatOrderSummary($order));

        return response()->json([
            'success' => true,
            'data' => $formatted,
            'meta' => [
                'current_page' => $orders->currentPage(),
                'last_page' => $orders->lastPage(),
                'total' => $orders->total(),
            ],
        ]);
    }

    /**
     * Customer Specific Order Details & Invoice.
     */
    public function orderDetail(Request $request, string $orderNo): JsonResponse
    {
        $customer = $request->user();

        $order = Order::where('order_no', $orderNo)
            ->where('customer_id', $customer->id)
            ->with(['items.product', 'statusLogs', 'consignment'])
            ->first();

        if (! $order) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $this->formatOrderFullDetail($order),
        ]);
    }

    /**
     * Live Step-by-Step Order Tracking Timeline (Public & In-App).
     */
    public function trackOrder(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'order_no' => 'required|string',
            'phone' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Order number is required.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $query = Order::where('order_no', trim($request->order_no))
            ->with(['statusLogs', 'consignment', 'items']);

        if ($request->filled('phone')) {
            $query->where('shipping_phone', 'like', '%'.trim($request->phone));
        }

        $order = $query->first();

        if (! $order) {
            return response()->json([
                'success' => false,
                'message' => 'No order found matching the provided order number and phone number.',
            ], 404);
        }

        // Timeline milestones: Pending -> Processing -> In Courier -> On The Way -> Delivered
        $statusOrder = [
            'pending' => 1,
            'processing' => 2,
            'in_courier' => 3,
            'on_the_way' => 4,
            'completed' => 5,
        ];

        $currentStep = $statusOrder[$order->status] ?? 1;
        if ($order->status === 'cancelled' || $order->status === 'returned') {
            $currentStep = -1;
        }

        $milestones = [
            [
                'step' => 1,
                'key' => 'pending',
                'title' => 'Order Placed',
                'description' => 'We have received your order request.',
                'completed' => $currentStep >= 1,
                'is_current' => $order->status === 'pending',
                'timestamp' => $order->created_at?->toIso8601String(),
            ],
            [
                'step' => 2,
                'key' => 'processing',
                'title' => 'Packaging & QA',
                'description' => 'Items are packaged and prepared for dispatch.',
                'completed' => $currentStep >= 2,
                'is_current' => $order->status === 'processing',
                'timestamp' => $this->getStatusTimestamp($order, 'processing'),
            ],
            [
                'step' => 3,
                'key' => 'in_courier',
                'title' => 'Handed to Courier',
                'description' => $order->courier_name ? "Handed over to {$order->courier_name} Courier." : 'Handed over to delivery partner.',
                'completed' => $currentStep >= 3,
                'is_current' => $order->status === 'in_courier',
                'timestamp' => $this->getStatusTimestamp($order, 'in_courier'),
            ],
            [
                'step' => 4,
                'key' => 'on_the_way',
                'title' => 'Out for Delivery',
                'description' => 'Rider is on the way to deliver your parcel.',
                'completed' => $currentStep >= 4,
                'is_current' => $order->status === 'on_the_way',
                'timestamp' => $this->getStatusTimestamp($order, 'on_the_way'),
            ],
            [
                'step' => 5,
                'key' => 'completed',
                'title' => 'Delivered',
                'description' => 'Package successfully delivered to your doorstep.',
                'completed' => $currentStep >= 5,
                'is_current' => $order->status === 'completed',
                'timestamp' => $order->delivered_at?->toIso8601String() ?? $this->getStatusTimestamp($order, 'completed'),
            ],
        ];

        return response()->json([
            'success' => true,
            'data' => [
                'order_no' => $order->order_no,
                'status' => $order->status,
                'is_cancelled' => in_array($order->status, ['cancelled', 'returned']),
                'current_step' => $currentStep,
                'total_steps' => 5,
                'grand_total' => (float) $order->grand_total,
                'item_count' => $order->items->count(),
                'shipping_name' => $order->shipping_name,
                'shipping_address' => $order->shipping_address,
                'shipping_city' => $order->shipping_city,
                'courier_name' => $order->courier_name,
                'courier_tracking_id' => $order->courier_tracking_id,
                'timeline' => $milestones,
                'status_logs' => $order->statusLogs->map(fn ($log) => [
                    'from_status' => $log->from_status,
                    'to_status' => $log->to_status,
                    'note' => $log->note,
                    'created_at' => $log->created_at->toIso8601String(),
                ]),
            ],
        ]);
    }

    private function getStatusTimestamp(Order $order, string $status): ?string
    {
        $log = $order->statusLogs->where('to_status', $status)->first();

        return $log?->created_at?->toIso8601String();
    }

    private function formatOrderSummary(Order $order): array
    {
        return [
            'id' => $order->id,
            'order_no' => $order->order_no,
            'status' => $order->status,
            'grand_total' => (float) $order->grand_total,
            'payment_status' => $order->payment_status,
            'payment_method' => $order->payment_method,
            'item_count' => $order->items->count(),
            'first_item_name' => $order->items->first()?->product_name,
            'first_item_thumbnail' => $order->items->first()?->product?->thumbnail ? asset('storage/'.$order->items->first()->product->thumbnail) : null,
            'created_at' => $order->created_at->toIso8601String(),
        ];
    }

    private function formatOrderFullDetail(Order $order): array
    {
        return [
            'id' => $order->id,
            'order_no' => $order->order_no,
            'status' => $order->status,
            'subtotal' => (float) $order->subtotal,
            'discount' => (float) $order->discount,
            'coupon_code' => $order->coupon_code,
            'shipping_charge' => (float) $order->shipping_charge,
            'grand_total' => (float) $order->grand_total,
            'paid_amount' => (float) $order->paid_amount,
            'due_amount' => (float) $order->due_amount,
            'payment_method' => $order->payment_method,
            'payment_status' => $order->payment_status,
            'shipping_name' => $order->shipping_name,
            'shipping_phone' => $order->shipping_phone,
            'shipping_email' => $order->shipping_email,
            'shipping_address' => $order->shipping_address,
            'shipping_city' => $order->shipping_city,
            'shipping_zone' => $order->shipping_zone,
            'courier_name' => $order->courier_name,
            'courier_tracking_id' => $order->courier_tracking_id,
            'customer_note' => $order->customer_note,
            'created_at' => $order->created_at->toIso8601String(),
            'delivered_at' => $order->delivered_at?->toIso8601String(),
            'items' => $order->items->map(function ($item) {
                return [
                    'id' => $item->id,
                    'product_id' => $item->product_id,
                    'variant_id' => $item->variant_id,
                    'product_name' => $item->product_name,
                    'variant_name' => $item->variant_name,
                    'sku' => $item->sku,
                    'unit_price' => (float) $item->unit_price,
                    'quantity' => (int) $item->quantity,
                    'subtotal' => (float) $item->subtotal,
                    'thumbnail' => $item->product?->thumbnail ? asset('storage/'.$item->product->thumbnail) : null,
                ];
            }),
        ];
    }
}
