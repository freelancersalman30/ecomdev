<?php

namespace App\Services;

use App\Models\Order;
use App\Models\User;
use App\Notifications\AdminOrderNotification;
use Illuminate\Support\Facades\Notification;
use Throwable;

class AdminNotificationService
{
    /**
     * Send notification to all admin users.
     */
    public function broadcastToAdmins(array $payload): void
    {
        try {
            $admins = User::all();
            if ($admins->isNotEmpty()) {
                Notification::send($admins, new AdminOrderNotification($payload));
            }
        } catch (Throwable $e) {
            report($e);
        }
    }

    /**
     * Notify admins when a new order is received.
     */
    public function notifyNewOrder(Order $order): void
    {
        $this->broadcastToAdmins([
            'order_id' => $order->id,
            'order_no' => $order->order_no,
            'type' => 'new_order',
            'title' => 'New Order: '.$order->order_no,
            'message' => 'New order placed by '.($order->shipping_name ?: 'Customer').' for ৳'.number_format($order->grand_total, 2).' ('.strtoupper($order->order_type ?: 'online').')',
            'icon' => 'shopping-bag',
            'icon_color' => 'emerald',
            'amount' => (float) $order->grand_total,
            'customer_name' => $order->shipping_name,
            'action_url' => route('admin.orders.show', $order->id),
        ]);
    }

    /**
     * Notify admins when an order is booked with a courier.
     */
    public function notifyCourierAssigned(Order $order, string $courierName, string $trackingCode): void
    {
        $this->broadcastToAdmins([
            'order_id' => $order->id,
            'order_no' => $order->order_no,
            'type' => 'courier_assigned',
            'title' => 'Courier Dispatched: '.$order->order_no,
            'message' => "Order handed over to {$courierName}. Tracking Code: {$trackingCode}",
            'icon' => 'truck',
            'icon_color' => 'sky',
            'amount' => (float) $order->grand_total,
            'customer_name' => $order->shipping_name,
            'action_url' => route('admin.orders.show', $order->id),
        ]);
    }

    /**
     * Notify admins when order delivery is completed.
     */
    public function notifyDeliveryDone(Order $order): void
    {
        $this->broadcastToAdmins([
            'order_id' => $order->id,
            'order_no' => $order->order_no,
            'type' => 'delivery_done',
            'title' => 'Delivery Completed: '.$order->order_no,
            'message' => 'Order for '.($order->shipping_name ?: 'Customer').' was marked as delivered & completed.',
            'icon' => 'check-circle',
            'icon_color' => 'teal',
            'amount' => (float) $order->grand_total,
            'customer_name' => $order->shipping_name,
            'action_url' => route('admin.orders.show', $order->id),
        ]);
    }

    /**
     * Notify admins of status updates (cancelled, returned, on_the_way, processing, etc.)
     */
    public function notifyStatusChange(Order $order, string $newStatus, ?string $note = null): void
    {
        if ($newStatus === 'completed') {
            $this->notifyDeliveryDone($order);

            return;
        }

        if ($newStatus === 'in_courier' && $order->courier_name && $order->courier_tracking_id) {
            $this->notifyCourierAssigned($order, $order->courier_name, $order->courier_tracking_id);

            return;
        }

        $title = 'Order Status Updated: '.$order->order_no;
        $icon = 'clock';
        $iconColor = 'blue';
        $type = 'status_updated';

        if ($newStatus === 'cancelled') {
            $title = 'Order Cancelled: '.$order->order_no;
            $icon = 'x-circle';
            $iconColor = 'rose';
            $type = 'order_cancelled';
        } elseif ($newStatus === 'returned') {
            $title = 'Order Returned: '.$order->order_no;
            $icon = 'rotate-ccw';
            $iconColor = 'amber';
            $type = 'order_returned';
        } elseif ($newStatus === 'on_the_way') {
            $title = 'Order On The Way: '.$order->order_no;
            $icon = 'navigation';
            $iconColor = 'indigo';
            $type = 'on_the_way';
        } elseif ($newStatus === 'processing') {
            $title = 'Order Processing: '.$order->order_no;
            $icon = 'package-check';
            $iconColor = 'emerald';
            $type = 'processing';
        }

        $readableStatus = ucfirst(str_replace('_', ' ', $newStatus));
        $message = "Order status changed to {$readableStatus} for ".($order->shipping_name ?: 'Customer');
        if (! empty($note)) {
            $message .= ". Note: {$note}";
        }

        $this->broadcastToAdmins([
            'order_id' => $order->id,
            'order_no' => $order->order_no,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'icon' => $icon,
            'icon_color' => $iconColor,
            'amount' => (float) $order->grand_total,
            'customer_name' => $order->shipping_name,
            'action_url' => route('admin.orders.show', $order->id),
        ]);
    }
}
