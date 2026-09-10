<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Order Alert #{{ $order->order_no }}</title>
    <style>
        body { margin: 0; padding: 0; background-color: #0f172a; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; color: #1e293b; }
        .wrapper { max-width: 600px; margin: 20px auto; background-color: #ffffff; border-radius: 16px; overflow: hidden; border: 1px solid #334155; }
        .header { background: linear-gradient(135deg, #1e1b4b 0%, #0f172a 100%); padding: 30px 24px; text-align: center; color: #ffffff; }
        .alert-badge { display: inline-block; background-color: #ef4444; color: #ffffff; padding: 4px 12px; border-radius: 9999px; font-size: 11px; font-weight: 900; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 8px; }
        .header-title { font-size: 20px; font-weight: 800; margin: 0; color: #ffffff; }
        .order-no { font-family: monospace; font-size: 14px; color: #38bdf8; margin-top: 6px; font-weight: 700; }
        .content { padding: 24px; }
        .metric-card { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 16px; margin-bottom: 20px; }
        .info-grid { width: 100%; border-collapse: collapse; font-size: 13px; }
        .info-grid td { padding: 5px 0; vertical-align: top; }
        .info-label { color: #64748b; width: 35%; font-weight: 600; }
        .info-value { color: #0f172a; font-weight: 700; }
        .items-table { width: 100%; border-collapse: collapse; font-size: 13px; margin-top: 8px; }
        .items-table th { text-align: left; padding: 8px 6px; background-color: #f1f5f9; color: #475569; font-weight: 700; font-size: 11px; text-transform: uppercase; }
        .items-table td { padding: 8px 6px; border-bottom: 1px solid #f1f5f9; }
        .btn-admin { display: block; text-align: center; background-color: #6366f1; color: #ffffff !important; padding: 14px 24px; border-radius: 12px; font-weight: 800; text-decoration: none; font-size: 14px; margin: 24px 0; box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3); }
        .footer { background-color: #f8fafc; padding: 16px 24px; text-align: center; font-size: 11px; color: #64748b; border-top: 1px solid #e2e8f0; }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="header">
            <span class="alert-badge">🚨 New Online Order</span>
            <h1 class="header-title">Total: ৳{{ number_format($order->grand_total, 2) }}</h1>
            <div class="order-no">Order #{{ $order->order_no }}</div>
        </div>

        <div class="content">
            {{-- Customer Card --}}
            <div class="metric-card">
                <table class="info-grid">
                    <tr>
                        <td class="info-label">Customer Name:</td>
                        <td class="info-value">{{ $order->shipping_name }}</td>
                    </tr>
                    <tr>
                        <td class="info-label">Mobile Phone:</td>
                        <td class="info-value">
                            <a href="tel:{{ $order->shipping_phone }}" style="color: #2563eb; text-decoration: none;">{{ $order->shipping_phone }}</a>
                        </td>
                    </tr>
                    @if($order->shipping_email)
                    <tr>
                        <td class="info-label">Email:</td>
                        <td class="info-value">{{ $order->shipping_email }}</td>
                    </tr>
                    @endif
                    <tr>
                        <td class="info-label">Delivery Address:</td>
                        <td class="info-value">{{ $order->shipping_address }}, {{ $order->shipping_city }}</td>
                    </tr>
                    <tr>
                        <td class="info-label">Payment Method:</td>
                        <td class="info-value">{{ ucfirst(str_replace('_', ' ', $order->payment_method)) }}</td>
                    </tr>
                    <tr>
                        <td class="info-label">Fraud Risk:</td>
                        <td class="info-value">
                            @if($order->is_fraud_suspect)
                                <span style="color: #dc2626; font-weight: 900;">⚠️ High Risk (Score: {{ $order->fraud_score }})</span>
                            @else
                                <span style="color: #16a34a;">✅ Normal (Score: {{ $order->fraud_score ?? 0 }})</span>
                            @endif
                        </td>
                    </tr>
                </table>
            </div>

            {{-- Items --}}
            <div class="metric-card">
                <table class="items-table">
                    <thead>
                        <tr>
                            <th>Item Name</th>
                            <th style="text-align: center;">Qty</th>
                            <th style="text-align: right;">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->items as $item)
                        <tr>
                            <td>
                                <strong>{{ $item->product_name }}</strong>
                                @if($item->variant_name)
                                <div style="font-size: 11px; color: #64748b;">({{ $item->variant_name }})</div>
                                @endif
                            </td>
                            <td style="text-align: center; font-weight: 700;">{{ $item->quantity }}</td>
                            <td style="text-align: right; font-weight: 700;">৳{{ number_format($item->subtotal, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <a href="{{ $adminUrl }}" target="_blank" class="btn-admin">
                ⚡ Open Order in Admin Dashboard
            </a>
        </div>

        <div class="footer">
            Admin Notification Gateway &bull; {{ $siteName }}
        </div>
    </div>
</body>
</html>
