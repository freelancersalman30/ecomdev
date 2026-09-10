<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $statusTitle }} #{{ $order->order_no }}</title>
    <style>
        body { margin: 0; padding: 0; background-color: #f8fafc; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; color: #1e293b; }
        .wrapper { max-width: 600px; margin: 20px auto; background-color: #ffffff; border-radius: 16px; overflow: hidden; border: 1px solid #e2e8f0; }
        .header { background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); padding: 32px 24px; text-align: center; color: #ffffff; }
        .brand-name { font-size: 18px; font-weight: 800; color: #10b981; margin: 0; }
        .status-pill { display: inline-block; padding: 6px 16px; border-radius: 9999px; font-size: 13px; font-weight: 800; text-transform: uppercase; margin: 12px 0 6px 0; }
        .status-processing { background-color: #fef3c7; color: #b45309; }
        .status-courier { background-color: #e0f2fe; color: #0369a1; }
        .status-completed { background-color: #d1fae5; color: #047857; }
        .status-cancelled { background-color: #ffe4e6; color: #be123c; }
        .order-no { font-family: monospace; font-size: 13px; color: #94a3b8; }
        .content { padding: 24px; }
        .msg-box { background-color: #f8fafc; border-left: 4px solid #10b981; padding: 14px 16px; border-radius: 0 12px 12px 0; margin-bottom: 20px; font-size: 14px; line-height: 1.6; }
        .section-card { background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 16px; margin-bottom: 20px; }
        .info-grid { width: 100%; border-collapse: collapse; font-size: 13px; }
        .info-grid td { padding: 5px 0; vertical-align: top; }
        .info-label { color: #64748b; width: 35%; font-weight: 600; }
        .info-value { color: #0f172a; font-weight: 700; }
        .btn-track { display: block; text-align: center; background-color: #0284c7; color: #ffffff !important; padding: 14px 24px; border-radius: 12px; font-weight: 800; text-decoration: none; font-size: 14px; margin: 24px 0; box-shadow: 0 4px 12px rgba(2, 132, 199, 0.25); }
        .footer { background-color: #f1f5f9; padding: 16px 24px; text-align: center; font-size: 11px; color: #64748b; border-top: 1px solid #e2e8f0; }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="header">
            <h1 class="brand-name">{{ $siteName }}</h1>
            
            @php
                $pillClass = match($status) {
                    'processing' => 'status-processing',
                    'on_the_way', 'in_courier' => 'status-courier',
                    'completed' => 'status-completed',
                    'cancelled' => 'status-cancelled',
                    default => 'status-courier',
                };
            @endphp
            <div class="status-pill {{ $pillClass }}">{{ ucfirst(str_replace('_', ' ', $status)) }}</div>
            <div class="order-no">Order #{{ $order->order_no }}</div>
        </div>

        <div class="content">
            <div class="msg-box">
                Hello <strong>{{ $order->shipping_name }}</strong>,<br>
                Your order status has been updated to <strong>{{ ucfirst(str_replace('_', ' ', $status)) }}</strong>.
                @if(!empty($note))
                <div style="margin-top: 6px; font-size: 13px; color: #475569;"><em>Note: {{ $note }}</em></div>
                @endif
            </div>

            {{-- Courier Info if dispatched --}}
            @if($order->courier_name && $order->courier_tracking_id)
            <div class="section-card" style="background-color: #f0f9ff; border-color: #bae6fd;">
                <div style="font-size: 11px; font-weight: 800; text-transform: uppercase; color: #0284c7; margin-bottom: 8px;">
                    🚚 Courier Consignment Details
                </div>
                <table class="info-grid">
                    <tr>
                        <td class="info-label">Courier Partner:</td>
                        <td class="info-value">{{ $order->courier_name }}</td>
                    </tr>
                    <tr>
                        <td class="info-label">Tracking ID:</td>
                        <td class="info-value" style="font-family: monospace; color: #0369a1;">{{ $order->courier_tracking_id }}</td>
                    </tr>
                </table>
            </div>
            @endif

            {{-- Summary details --}}
            <div class="section-card">
                <table class="info-grid">
                    <tr>
                        <td class="info-label">Order Total:</td>
                        <td class="info-value" style="color: #059669;">৳{{ number_format($order->grand_total, 2) }}</td>
                    </tr>
                    <tr>
                        <td class="info-label">Payment Mode:</td>
                        <td class="info-value">{{ ucfirst(str_replace('_', ' ', $order->payment_method)) }}</td>
                    </tr>
                    <tr>
                        <td class="info-label">Delivery Destination:</td>
                        <td class="info-value">{{ $order->shipping_address }}, {{ $order->shipping_city }}</td>
                    </tr>
                </table>
            </div>

            <a href="{{ $trackingUrl }}" target="_blank" class="btn-track">
                🔍 Track Live Delivery Progress
            </a>
        </div>

        <div class="footer">
            &copy; {{ date('Y') }} {{ $siteName }} &bull; Thank you for shopping with us!
        </div>
    </div>
</body>
</html>
