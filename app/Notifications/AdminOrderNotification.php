<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class AdminOrderNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public array $data
    ) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'order_id' => $this->data['order_id'] ?? null,
            'order_no' => $this->data['order_no'] ?? null,
            'type' => $this->data['type'] ?? 'general',
            'title' => $this->data['title'] ?? 'Order Notification',
            'message' => $this->data['message'] ?? '',
            'icon' => $this->data['icon'] ?? 'bell',
            'icon_color' => $this->data['icon_color'] ?? 'emerald',
            'amount' => $this->data['amount'] ?? null,
            'customer_name' => $this->data['customer_name'] ?? null,
            'action_url' => $this->data['action_url'] ?? route('admin.orders.index'),
            'created_at' => now()->toIso8601String(),
        ];
    }
}
