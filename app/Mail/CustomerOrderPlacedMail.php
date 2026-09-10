<?php

namespace App\Mail;

use App\Models\Order;
use App\Models\Setting;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CustomerOrderPlacedMail extends Mailable
{
    use Queueable, SerializesModels;

    public Order $order;

    public string $siteName;

    public string $trackingUrl;

    /**
     * Create a new message instance.
     */
    public function __construct(Order $order)
    {
        $this->order = $order->loadMissing(['items.product', 'items.variant']);
        $this->siteName = Setting::get('site_name', 'DREAMERS PCB');
        $this->trackingUrl = url('/order/track?order_no='.$order->order_no.'&phone='.$order->shipping_phone);
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Order Confirmation #{$this->order->order_no} - {$this->siteName}",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.customer_order_placed',
        );
    }
}
