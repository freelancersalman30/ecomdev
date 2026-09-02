<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt #{{ $order->order_no }} - DREAMERS PCB</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Courier New', Courier, monospace;
            font-size: 12px;
            color: #000;
        }
        body {
            background-color: #fff;
            padding: 10px;
            display: flex;
            justify-content: center;
        }
        .receipt-container {
            width: {{ $format === '58mm' ? '54mm' : '76mm' }};
            max-width: 100%;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        .text-sm { font-size: 10px; }
        .text-lg { font-size: 15px; }
        .divider {
            border-top: 1px dashed #000;
            margin: 6px 0;
        }
        .double-divider {
            border-top: 2px dashed #000;
            margin: 6px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 6px 0;
        }
        th, td {
            padding: 3px 0;
            vertical-align: top;
        }
        @media print {
            body { padding: 0; }
            @page { margin: 0; }
        }
    </style>
</head>
<body onload="window.print()">

    <div class="receipt-container">
        
        <!-- Header -->
        <div class="text-center">
            @if(\App\Models\Setting::get('invoice_logo') || \App\Models\Setting::get('site_logo'))
                <img src="{{ asset(\App\Models\Setting::get('invoice_logo') ?: \App\Models\Setting::get('site_logo')) }}" alt="Logo" style="max-height: 36px; margin: 0 auto 4px auto;">
            @endif
            <h1 class="text-lg font-bold">{{ \App\Models\Setting::get('site_name', 'DREAMERS PCB') }}</h1>
            <p class="text-sm">{{ \App\Models\Setting::get('site_tagline', 'Enterprise Electronics & PCB') }}</p>
            <p class="text-sm">{{ \App\Models\Setting::get('site_address', 'Elephant Road, Dhaka') }}</p>
            <p class="text-sm">Hotline: {{ \App\Models\Setting::get('site_phone', '+880 1700-112233') }}</p>
        </div>

        <div class="divider"></div>

        <!-- Order Meta -->
        <div>
            <div><strong>Inv No:</strong> {{ $order->order_no }}</div>
            <div><strong>Date:</strong> {{ $order->created_at->format('d/m/Y h:i A') }}</div>
            <div><strong>Customer:</strong> {{ $order->shipping_name }}</div>
            <div><strong>Phone:</strong> {{ $order->shipping_phone }}</div>
            @if($order->shipping_address && $order->shipping_address !== 'Store Counter - DREAMERS PCB')
            <div><strong>Address:</strong> {{ $order->shipping_address }}{{ $order->shipping_city && $order->shipping_city !== 'Store POS' ? ', ' . $order->shipping_city : '' }}</div>
            @endif
            @if($order->customer_note)
            <div><strong>Note:</strong> {{ $order->customer_note }}</div>
            @endif
            <div><strong>Cashier:</strong> {{ auth()->user()->name ?? 'Admin' }} (Terminal 01)</div>
        </div>

        <div class="divider"></div>

        <!-- Items Table -->
        <table>
            <thead>
                <tr>
                    <th style="text-align: left;">Item</th>
                    <th style="text-align: center; width: 25px;">Qty</th>
                    <th style="text-align: right; width: 45px;">Price</th>
                    <th style="text-align: right; width: 55px;">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->items as $item)
                <tr>
                    <td style="text-align: left;">
                        {{ $item->product_name }}
                        @if($item->variant_name)
                        <br><span class="text-sm">({{ $item->variant_name }})</span>
                        @endif
                    </td>
                    <td style="text-align: center;">{{ $item->quantity }}</td>
                    <td style="text-align: right;">{{ number_format($item->unit_price, 2) }}</td>
                    <td style="text-align: right;">{{ number_format($item->subtotal, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="divider"></div>

        <!-- Totals -->
        <table>
            <tr>
                <td>Subtotal:</td>
                <td class="text-right">৳{{ number_format($order->subtotal, 2) }}</td>
            </tr>
            @if($order->discount > 0)
            <tr>
                <td>Discount:</td>
                <td class="text-right">-৳{{ number_format($order->discount, 2) }}</td>
            </tr>
            @endif
            @if($order->shipping_charge > 0)
            <tr>
                <td>Delivery Charge:</td>
                <td class="text-right">+৳{{ number_format($order->shipping_charge, 2) }}</td>
            </tr>
            @endif
            @if($order->tax > 0)
            <tr>
                <td>Tax / VAT:</td>
                <td class="text-right">+৳{{ number_format($order->tax, 2) }}</td>
            </tr>
            @endif
            <tr class="font-bold text-lg">
                <td>Grand Total:</td>
                <td class="text-right">৳{{ number_format($order->grand_total, 2) }}</td>
            </tr>
            <tr>
                <td>{{ $order->due_amount > 0 ? 'Advance Paid' : 'Paid' }} ({{ strtoupper($order->payment_method) }}):</td>
                <td class="text-right">৳{{ number_format($order->paid_amount, 2) }}</td>
            </tr>
            @if($order->due_amount > 0)
            <tr class="font-bold">
                <td>Due Balance:</td>
                <td class="text-right">৳{{ number_format($order->due_amount, 2) }}</td>
            </tr>
            @endif
        </table>

        <div class="double-divider"></div>

        <!-- Footer -->
        <div class="text-center text-sm">
            <p class="font-bold">{{ \App\Models\Setting::get('pos_footer_note', 'Thank you for choosing DREAMERS PCB!') }}</p>
            <p>For warranty support, visit {{ url('/') }}</p>
            <p style="margin-top: 6px;">*** System Generated Receipt ***</p>
        </div>

    </div>

</body>
</html>
