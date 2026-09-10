<?php

namespace App\Mail;

use App\Models\Order;
use App\Models\Setting;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AdminNewOrderAlertMail extends Mailable
{
    use Queueable, SerializesModels;

    public Order $order;

    public string $siteName;

    public string $adminUrl;

    /**
     * Create a new message instance.
     */
    public function __construct(Order $order)
    {
        $this->order = $order->loadMissing(['items.product', 'items.variant']);
        $this->siteName = Setting::get('site_name', 'DREAMERS PCB');
        $this->adminUrl = route('admin.orders.show', $order->id);
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "🚨 New Order Received #{$this->order->order_no} [৳".number_format($this->order->grand_total, 2).'] - '.$this->siteName,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.admin_new_order_alert',
        );
    }
}
