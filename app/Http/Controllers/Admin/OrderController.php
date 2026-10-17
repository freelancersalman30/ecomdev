<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\CustomerOrderPlacedMail;
use App\Models\Account;
use App\Models\Order;
use App\Services\AccountTransactionService;
use App\Services\AdminNotificationService;
use App\Services\CourierService;
use App\Services\InventoryService;
use App\Services\MailConfigService;
use App\Services\OrderEmailService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class OrderController extends Controller
{
    protected CourierService $courierService;

    protected InventoryService $inventoryService;

    protected AdminNotificationService $adminNotificationService;

    protected AccountTransactionService $accountTransactionService;

    public function __construct(
        CourierService $courierService,
        InventoryService $inventoryService,
        AdminNotificationService $adminNotificationService,
        AccountTransactionService $accountTransactionService
    ) {
        $this->courierService = $courierService;
        $this->inventoryService = $inventoryService;
        $this->adminNotificationService = $adminNotificationService;
        $this->accountTransactionService = $accountTransactionService;
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
        $order->load(['customer', 'items.product', 'items.variant', 'statusLogs.changedBy', 'consignment', 'account']);
        $accounts = Account::where('is_active', true)->get();

        return view('admin.orders.show', compact('order', 'accounts'));
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

        // If transitioning to cancelled/returned from an active status, restore inventory and reverse credited payment
        if (in_array($newStatus, ['cancelled', 'returned']) && ! in_array($oldStatus, ['cancelled', 'returned', 'incomplete'])) {
            foreach ($order->items as $item) {
                $this->inventoryService->restoreStock($item->product_id, $item->variant_id, $item->quantity);
            }

            try {
                $this->accountTransactionService->reverseOrderPayment(
                    $order,
                    "Reversal/refund: Order #{$order->order_no} status changed to {$newStatus}"
                );
            } catch (\Throwable $e) {
                Log::error("Failed to reverse payment for Order #{$order->order_no}: ".$e->getMessage());
            }

            $order->payment_status = 'refunded';
        }

        if ($newStatus === 'completed') {
            $order->delivered_at = now();
            $order->payment_status = 'paid';

            // Calculate remaining unpaid due to credit to available balance
            $unpaidDue = max(0, round((float) $order->grand_total - (float) $order->paid_amount, 2));
            if ($unpaidDue > 0.0001) {
                try {
                    $this->accountTransactionService->creditOrderPayment(
                        $order,
                        $unpaidDue,
                        $order->account_id,
                        "Order #{$order->order_no} completed & COD/delivery payment collected"
                    );
                } catch (\Throwable $e) {
                    Log::error("Failed to credit payment for Order #{$order->order_no}: ".$e->getMessage());
                }
            }

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
            'status' => 'required|in:pending,processing,on_the_way,in_courier,completed,cancelled,returned',
        ]);

        $orders = Order::whereIn('id', $request->order_ids)->get();
        foreach ($orders as $order) {
            $oldStatus = $order->status;
            if ($oldStatus === 'incomplete' && ! in_array($request->status, ['incomplete', 'cancelled', 'returned'])) {
                foreach ($order->items as $item) {
                    $this->inventoryService->deductStock($item->product_id, $item->variant_id, $item->quantity);
                }
            }

            if (in_array($request->status, ['cancelled', 'returned']) && ! in_array($oldStatus, ['cancelled', 'returned', 'incomplete'])) {
                foreach ($order->items as $item) {
                    $this->inventoryService->restoreStock($item->product_id, $item->variant_id, $item->quantity);
                }

                try {
                    $this->accountTransactionService->reverseOrderPayment(
                        $order,
                        "Bulk reversal: Order #{$order->order_no} changed to {$request->status}"
                    );
                } catch (\Throwable $e) {
                    Log::error("Failed bulk reversal for Order #{$order->order_no}: ".$e->getMessage());
                }

                $order->payment_status = 'refunded';
            }

            if ($request->status === 'completed' && $oldStatus !== 'completed') {
                $unpaidDue = max(0, round((float) $order->grand_total - (float) $order->paid_amount, 2));
                if ($unpaidDue > 0.0001) {
                    try {
                        $this->accountTransactionService->creditOrderPayment(
                            $order,
                            $unpaidDue,
                            $order->account_id,
                            "Bulk completed: Order #{$order->order_no} payment collected"
                        );
                    } catch (\Throwable $e) {
                        Log::error("Failed bulk credit for Order #{$order->order_no}: ".$e->getMessage());
                    }
                }

                $order->delivered_at = now();
                $order->payment_status = 'paid';
                $order->paid_amount = $order->grand_total;
                $order->due_amount = 0;
            }

            $order->logStatusChange($request->status, 'Bulk status update by '.auth()->user()->name, auth()->id());
            $this->adminNotificationService->notifyStatusChange($order, $request->status, 'Bulk status update');
            OrderEmailService::sendOrderStatusUpdatedNotification($order, $request->status, 'Bulk status update');

            if ($order->customer) {
                $order->customer->recalculateMetrics();
            }
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
                try {
                    $this->accountTransactionService->reverseOrderPayment($order, "Order #{$order->order_no} deleted");
                } catch (\Throwable $e) {
                    // pass
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
            try {
                $this->accountTransactionService->reverseOrderPayment($order, "Order #{$order->order_no} deleted");
            } catch (\Throwable $e) {
                // pass
            }
        }

        $order->delete();

        if ($customer) {
            $customer->recalculateMetrics();
        }

        return redirect()->route('admin.orders.index')->with('success', "Order #{$orderNo} deleted successfully.");
    }

    public function recordPayment(Request $request, Order $order)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'account_id' => 'nullable|exists:accounts,id',
            'payment_method' => 'nullable|string|max:50',
            'note' => 'nullable|string|max:255',
        ]);

        $amount = (float) $request->amount;
        $maxPayable = max(0, round((float) $order->grand_total - (float) $order->paid_amount, 2));

        if ($amount > $maxPayable + 0.01) {
            return redirect()->back()->with('error', 'Payment amount (৳'.number_format($amount, 2).') exceeds remaining due of ৳'.number_format($maxPayable, 2).'.');
        }

        try {
            if ($request->filled('payment_method')) {
                $order->payment_method = $request->payment_method;
            }
            if ($request->filled('account_id')) {
                $order->account_id = $request->account_id;
            }

            $tx = $this->accountTransactionService->creditOrderPayment(
                $order,
                $amount,
                $request->account_id ? (int) $request->account_id : null,
                $request->note ?? "Payment received for Order #{$order->order_no}"
            );

            $newPaid = round((float) $order->paid_amount + $amount, 2);
            $newDue = max(0, round((float) $order->grand_total - $newPaid, 2));

            $order->paid_amount = $newPaid;
            $order->due_amount = $newDue;
            $order->payment_status = $newDue <= 0.0001 ? 'paid' : 'partially_paid';
            $order->save();

            $order->statusLogs()->create([
                'from_status' => $order->status,
                'to_status' => $order->status,
                'note' => 'Payment of ৳'.number_format($amount, 2).' recorded'.($tx ? ' to '.$tx->account->name : ''),
                'changed_by' => auth()->id(),
            ]);

            return redirect()->back()->with('success', 'Payment of ৳'.number_format($amount, 2).' recorded successfully and credited to Available Balance!');
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'Failed to record payment: '.$e->getMessage());
        }
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
