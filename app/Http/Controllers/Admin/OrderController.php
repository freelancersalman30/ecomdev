<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\CustomerOrderPlacedMail;
use App\Models\Order;
use App\Services\AdminNotificationService;
use App\Services\CourierService;
use App\Services\InventoryService;
use App\Services\MailConfigService;
use App\Services\OrderEmailService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class OrderController extends Controller
{
    protected CourierService $courierService;

    protected InventoryService $inventoryService;

    protected AdminNotificationService $adminNotificationService;

    public function __construct(
        CourierService $courierService,
        InventoryService $inventoryService,
        AdminNotificationService $adminNotificationService
    ) {
        $this->courierService = $courierService;
        $this->inventoryService = $inventoryService;
        $this->adminNotificationService = $adminNotificationService;
    }

    public function index(Request $request)
    {
        $status = $request->get('status', 'all');
        $search = $request->get('search');

        $query = Order::with(['customer', 'items'])->latest();

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('order_no', 'like', "%{$search}%")
                    ->orWhere('shipping_name', 'like', "%{$search}%")
                    ->orWhere('shipping_phone', 'like', "%{$search}%")
                    ->orWhere('courier_tracking_id', 'like', "%{$search}%");
            });
        }

        $orders = $query->paginate(20)->withQueryString();

        // Status counts for pipeline tabs
        $counts = [
            'all' => Order::count(),
            'incomplete' => Order::incomplete()->count(),
            'pending' => Order::pending()->count(),
            'processing' => Order::processing()->count(),
            'on_the_way' => Order::onTheWay()->count(),
            'in_courier' => Order::inCourier()->count(),
            'completed' => Order::completed()->count(),
            'cancelled' => Order::cancelled()->count(),
            'returned' => Order::returned()->count(),
        ];

        return view('admin.orders.index', compact('orders', 'counts', 'status', 'search'));
    }

    public function show(Order $order)
    {
        $order->load(['customer', 'items.product', 'items.variant', 'statusLogs.changedBy', 'consignment']);

        return view('admin.orders.show', compact('order'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:incomplete,pending,processing,on_the_way,in_courier,completed,cancelled,returned',
            'note' => 'nullable|string|max:500',
        ]);

        $oldStatus = $order->status;
        $newStatus = $request->status;

        // If transitioning from incomplete to an active status, deduct inventory
        if ($oldStatus === 'incomplete' && ! in_array($newStatus, ['incomplete', 'cancelled', 'returned'])) {
            foreach ($order->items as $item) {
                $this->inventoryService->deductStock($item->product_id, $item->variant_id, $item->quantity);
            }
        }

        // If transitioning to cancelled/returned from an active status, restore inventory
        if (in_array($newStatus, ['cancelled', 'returned']) && ! in_array($oldStatus, ['cancelled', 'returned', 'incomplete'])) {
            foreach ($order->items as $item) {
                $this->inventoryService->restoreStock($item->product_id, $item->variant_id, $item->quantity);
            }
        }

        if ($newStatus === 'completed') {
            $order->delivered_at = now();
            $order->payment_status = 'paid';
            $order->paid_amount = $order->grand_total;
            $order->due_amount = 0;
        }

        $order->logStatusChange($newStatus, $request->note, auth()->id());
        $this->adminNotificationService->notifyStatusChange($order, $newStatus, $request->note);

        // Send automated status update email to customer
        OrderEmailService::sendOrderStatusUpdatedNotification($order, $newStatus, $request->note);

        if ($order->customer) {
            $order->customer->recalculateMetrics();
        }

        return redirect()->back()->with('success', 'Order status updated to '.ucfirst(str_replace('_', ' ', $newStatus)));
    }

    public function bulkUpdateStatus(Request $request)
    {
        $request->validate([
            'order_ids' => 'required|array|min:1',
            'order_ids.*' => 'exists:orders,id',
            'status' => 'required|in:pending,processing,on_the_way,in_courier,completed,cancelled',
        ]);

        $orders = Order::whereIn('id', $request->order_ids)->get();
        foreach ($orders as $order) {
            $oldStatus = $order->status;
            if ($oldStatus === 'incomplete' && ! in_array($request->status, ['incomplete', 'cancelled', 'returned'])) {
                foreach ($order->items as $item) {
                    $this->inventoryService->deductStock($item->product_id, $item->variant_id, $item->quantity);
                }
            }
            $order->logStatusChange($request->status, 'Bulk status update by '.auth()->user()->name, auth()->id());
            $this->adminNotificationService->notifyStatusChange($order, $request->status, 'Bulk status update');
            OrderEmailService::sendOrderStatusUpdatedNotification($order, $request->status, 'Bulk status update');
        }

        return redirect()->back()->with('success', count($orders).' orders updated successfully.');
    }

    public function bulkDestroy(Request $request)
    {
        $request->validate([
            'order_ids' => 'required|array|min:1',
            'order_ids.*' => 'exists:orders,id',
        ]);

        $orders = Order::whereIn('id', $request->order_ids)->get();
        $count = $orders->count();

        foreach ($orders as $order) {
            $customer = $order->customer;
            if (! in_array($order->status, ['cancelled', 'returned', 'incomplete'])) {
                foreach ($order->items as $item) {
                    $this->inventoryService->restoreStock($item->product_id, $item->variant_id, $item->quantity);
                }
            }
            $order->delete();
            if ($customer) {
                $customer->recalculateMetrics();
            }
        }

        return redirect()->back()->with('success', "{$count} selected order(s) deleted successfully.");
    }

    public function destroy(Order $order)
    {
        $orderNo = $order->order_no;
        $customer = $order->customer;

        if (! in_array($order->status, ['cancelled', 'returned', 'incomplete'])) {
            foreach ($order->items as $item) {
                $this->inventoryService->restoreStock($item->product_id, $item->variant_id, $item->quantity);
            }
        }

        $order->delete();

        if ($customer) {
            $customer->recalculateMetrics();
        }

        return redirect()->route('admin.orders.index')->with('success', "Order #{$orderNo} deleted successfully.");
    }

    public function bookCourier(Request $request, Order $order)
    {
        $request->validate([
            'courier_name' => 'required|string|in:Steadfast,Pathao,RedX',
        ]);

        $consignment = $this->courierService->bookConsignment($order, $request->courier_name);

        return redirect()->back()->with('success', "Consignment booked with {$request->courier_name}. Tracking ID: {$consignment->tracking_code}");
    }

    public function invoice(Order $order)
    {
        $order->load(['customer', 'items.product', 'items.variant']);

        return view('admin.orders.invoice', compact('order'));
    }

    public function sendInvoiceEmail(Request $request, Order $order)
    {
        $email = $request->email ?: $order->shipping_email ?: $order->customer?->email;

        if (! $email || ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return redirect()->back()->with('error', 'Please provide a valid customer email address to send the invoice.');
        }

        try {
            if ($request->filled('email') && $request->email !== $order->shipping_email) {
                $order->update(['shipping_email' => $request->email]);
            }

            MailConfigService::applyConfig();
            Mail::to($email)->send(new CustomerOrderPlacedMail($order));

            return redirect()->back()->with('success', "Invoice email successfully sent to {$email}!");
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'Failed to send invoice: '.$e->getMessage());
        }
    }

    public function packingSlip(Order $order)
    {
        $order->load(['customer', 'items.product', 'items.variant']);

        return view('admin.orders.packing_slip', compact('order'));
    }
}
