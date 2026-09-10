<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CustomerOrderStatusUpdatedMail extends Mailable
{
    use Queueable, SerializesModels;

    public Order $order;

    public string $status;

    public ?string $note;

    public string $siteName;

    public string $trackingUrl;

    public string $statusTitle;

    /**
     * Create a new message instance.
     */
    public function __construct(Order $order, string $status, ?string $note = null)
    {
        $this->order = $order->loadMissing(['items.product', 'items.variant']);
        $this->status = $status;
        $this->note = $note;
        $this->trackingUrl = route('order.track', [
            'order_no' => $order->order_no,
            'phone' => $order->shipping_phone,
        ]);

        $this->statusTitle = match ($status) {
            'processing' => 'Order In Processing & Packaging',
            'on_the_way', 'in_courier' => 'Order Dispatched with Courier',
            'completed' => 'Order Successfully Delivered',
            'cancelled' => 'Order Cancelled',
            'returned' => 'Order Returned',
            default => 'Order Status Updated to '.ucfirst(str_replace('_', ' ', $status)),
        };
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "{$this->statusTitle} - Order #{$this->order->order_no} ({$this->siteName})",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.customer_order_status_updated',
        );
    }
}
