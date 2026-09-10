<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation #{{ $order->order_no }}</title>
    <style>
        body { margin: 0; padding: 0; background-color: #f8fafc; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; color: #1e293b; }
        .wrapper { max-width: 600px; margin: 20px auto; background-color: #ffffff; border-radius: 16px; overflow: hidden; border: 1px solid #e2e8f0; }
        .header { background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); padding: 32px 24px; text-align: center; color: #ffffff; }
        .brand-name { font-size: 20px; font-weight: 800; letter-spacing: 0.5px; margin: 0; color: #10b981; }
        .header-title { font-size: 18px; font-weight: 700; margin: 12px 0 4px 0; color: #ffffff; }
        .order-badge { display: inline-block; background-color: rgba(16, 185, 129, 0.2); border: 1px solid rgba(16, 185, 129, 0.4); color: #34d399; padding: 4px 12px; border-radius: 9999px; font-size: 12px; font-weight: 700; font-family: monospace; }
        .content { padding: 24px; }
        .greeting { font-size: 14px; line-height: 1.6; color: #334155; margin-bottom: 20px; }
        .section-card { background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 16px; margin-bottom: 20px; }
        .section-title { font-size: 12px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px; color: #64748b; margin: 0 0 12px 0; }
        .info-grid { width: 100%; border-collapse: collapse; font-size: 13px; }
        .info-grid td { padding: 4px 0; vertical-align: top; }
        .info-label { color: #64748b; width: 35%; font-weight: 600; }
        .info-value { color: #0f172a; font-weight: 700; }
        .items-table { width: 100%; border-collapse: collapse; font-size: 13px; margin-top: 8px; }
        .items-table th { text-align: left; padding: 8px 6px; background-color: #f1f5f9; color: #475569; font-weight: 700; font-size: 11px; text-transform: uppercase; border-radius: 4px; }
        .items-table td { padding: 10px 6px; border-bottom: 1px solid #f1f5f9; }
        .total-row td { padding: 6px 6px; font-size: 13px; }
        .grand-total-row td { padding: 12px 6px; font-size: 16px; font-weight: 900; color: #0f172a; border-top: 2px solid #cbd5e1; }
        .btn-track { display: block; text-align: center; background-color: #10b981; color: #ffffff !important; padding: 14px 24px; border-radius: 12px; font-weight: 800; text-decoration: none; font-size: 14px; margin: 24px 0; box-shadow: 0 4px 12px rgba(16, 185, 129, 0.25); }
        .footer { background-color: #f1f5f9; padding: 20px 24px; text-align: center; font-size: 11px; color: #64748b; border-top: 1px solid #e2e8f0; }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="header">
            <h1 class="brand-name">{{ $siteName }}</h1>
            <div class="header-title">Thank You for Your Order!</div>
            <div class="order-badge">#{{ $order->order_no }}</div>
        </div>

        <div class="content">
            <p class="greeting">
                Hello <strong>{{ $order->shipping_name }}</strong>,<br>
                We have received your order and our fulfillment team is preparing it for shipment. Below is your detailed receipt and order summary:
            </p>

            {{-- Shipping & Order Details --}}
            <div class="section-card">
                <div class="section-title">Delivery & Payment Information</div>
                <table class="info-grid">
                    <tr>
                        <td class="info-label">Customer:</td>
                        <td class="info-value">{{ $order->shipping_name }}</td>
                    </tr>
                    <tr>
                        <td class="info-label">Phone:</td>
                        <td class="info-value">{{ $order->shipping_phone }}</td>
                    </tr>
                    <tr>
                        <td class="info-label">Delivery Address:</td>
                        <td class="info-value">{{ $order->shipping_address }}, {{ $order->shipping_city }}</td>
                    </tr>
                    <tr>
                        <td class="info-label">Payment Method:</td>
                        <td class="info-value">{{ ucfirst(str_replace('_', ' ', $order->payment_method)) }}</td>
                    </tr>
                    <tr>
                        <td class="info-label">Order Date:</td>
                        <td class="info-value">{{ $order->created_at->format('d M Y, h:i A') }}</td>
                    </tr>
                </table>
            </div>

            {{-- Items Table --}}
            <div class="section-card">
                <div class="section-title">Order Items Summary</div>
                <table class="items-table">
                    <thead>
                        <tr>
                            <th>Item</th>
                            <th style="text-align: center;">Qty</th>
                            <th style="text-align: right;">Price</th>
                            <th style="text-align: right;">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->items as $item)
                        <tr>
                            <td>
                                <strong>{{ $item->product_name }}</strong>
                                @if($item->variant_name)
                                <div style="font-size: 11px; color: #64748b;">Variant: {{ $item->variant_name }}</div>
                                @endif
                            </td>
                            <td style="text-align: center; font-weight: 700;">{{ $item->quantity }}</td>
                            <td style="text-align: right; color: #475569;">৳{{ number_format($item->unit_price, 2) }}</td>
                            <td style="text-align: right; font-weight: 700;">৳{{ number_format($item->subtotal, 2) }}</td>
                        </tr>
                        @endforeach

                        <tr class="total-row">
                            <td colspan="3" style="text-align: right; color: #64748b;">Subtotal:</td>
                            <td style="text-align: right; font-weight: 700;">৳{{ number_format($order->subtotal, 2) }}</td>
                        </tr>

                        @if($order->discount > 0)
                        <tr class="total-row">
                            <td colspan="3" style="text-align: right; color: #10b981;">Discount:</td>
                            <td style="text-align: right; font-weight: 700; color: #10b981;">-৳{{ number_format($order->discount, 2) }}</td>
                        </tr>
                        @endif

                        <tr class="total-row">
                            <td colspan="3" style="text-align: right; color: #64748b;">Shipping Fee:</td>
                            <td style="text-align: right; font-weight: 700;">
                                @if($order->shipping_charge == 0)
                                    <span style="color: #10b981;">FREE</span>
                                @else
                                    ৳{{ number_format($order->shipping_charge, 2) }}
                                @endif
                            </td>
                        </tr>

                        <tr class="grand-total-row">
                            <td colspan="3" style="text-align: right;">Grand Total:</td>
                            <td style="text-align: right; color: #059669;">৳{{ number_format($order->grand_total, 2) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            {{-- Live Tracking Button --}}
            <a href="{{ $trackingUrl }}" target="_blank" class="btn-track">
                🚚 Track Your Order Live
            </a>

            <p style="font-size: 12px; color: #64748b; line-height: 1.5; text-align: center;">
                If you have any questions or need to make adjustments to your order, feel free to contact our customer support team directly.
            </p>
        </div>

        <div class="footer">
            &copy; {{ date('Y') }} {{ $siteName }}. All rights reserved.<br>
            Official Enterprise Distribution & Electronic Hardware in Bangladesh.
        </div>
    </div>
</body>
</html>
