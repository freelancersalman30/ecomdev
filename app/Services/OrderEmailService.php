<?php

namespace App\Services;

use App\Mail\AdminNewOrderAlertMail;
use App\Mail\CustomerOrderPlacedMail;
use App\Mail\CustomerOrderStatusUpdatedMail;
use App\Models\Order;
use App\Models\Setting;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

class OrderEmailService
{
    /**
     * Send email notifications when an order is placed.
     */
    public static function sendOrderPlacedNotifications(Order $order): void
    {
        try {
            MailConfigService::applyConfig();

            // 1. Send Order Confirmation Email to Customer
            $customerEmail = $order->shipping_email ?: $order->customer?->email;
            if (! empty($customerEmail) && filter_var($customerEmail, FILTER_VALIDATE_EMAIL)) {
                Mail::to($customerEmail)->send(new CustomerOrderPlacedMail($order));
            }

            // 2. Send New Order Alert to Admin / Store Owner
            $adminEmail = Setting::get('site_email') ?: Setting::get('mail_from_address') ?: config('mail.from.address');
            if (! empty($adminEmail) && filter_var($adminEmail, FILTER_VALIDATE_EMAIL)) {
                Mail::to($adminEmail)->send(new AdminNewOrderAlertMail($order));
            }
        } catch (Throwable $e) {
            Log::error('OrderPlaced Email Failed for #'.$order->order_no.': '.$e->getMessage());
        }
    }

    /**
     * Send email notification when an order status is updated.
     */
    public static function sendOrderStatusUpdatedNotification(Order $order, string $newStatus, ?string $note = null): void
    {
        try {
            MailConfigService::applyConfig();

            $customerEmail = $order->shipping_email ?: $order->customer?->email;
            if (! empty($customerEmail) && filter_var($customerEmail, FILTER_VALIDATE_EMAIL)) {
                Mail::to($customerEmail)->send(new CustomerOrderStatusUpdatedMail($order, $newStatus, $note));
            }
        } catch (Throwable $e) {
            Log::error('OrderStatusUpdated Email Failed for #'.$order->order_no.': '.$e->getMessage());
        }
    }
}
