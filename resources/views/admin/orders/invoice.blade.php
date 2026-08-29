<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice #{{ $order->order_no }} - DREAMERS PCB</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .code-font { font-family: 'JetBrains Mono', monospace; }
        @media print {
            body { background: white !important; }
            .no-print { display: none !important; }
        }
    </style>
</head>
<body class="bg-slate-100 p-4 sm:p-8 text-slate-800 antialiased">

    <!-- Print Button Floating Bar -->
    <div class="max-w-4xl mx-auto mb-6 flex items-center justify-between no-print">
        <button onclick="window.close()" class="px-4 py-2 rounded-xl bg-slate-200 hover:bg-slate-300 text-xs font-semibold text-slate-700 transition">
            &larr; Close Window
        </button>
        <button onclick="window.print()" class="px-5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold shadow-md transition flex items-center gap-2">
            <span>Print Invoice</span>
        </button>
    </div>

    <!-- Invoice Sheet -->
    <div class="max-w-4xl mx-auto bg-white p-8 sm:p-12 rounded-2xl border border-slate-200 shadow-xl space-y-8">
        
        <!-- Header -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 pb-8 border-b border-slate-200">
            <div class="space-y-1">
                @if(\App\Models\Setting::get('invoice_logo') || \App\Models\Setting::get('site_logo'))
                    <img src="{{ asset(\App\Models\Setting::get('invoice_logo') ?: \App\Models\Setting::get('site_logo')) }}" alt="Logo" class="h-10 object-contain mb-2">
                @endif
                <h1 class="text-2xl font-black tracking-wider text-slate-900">{{ \App\Models\Setting::get('site_name', 'DREAMERS PCB') }}</h1>
                <p class="text-xs text-slate-500 font-medium">{{ \App\Models\Setting::get('site_tagline', 'Enterprise Electronics & Gadget Solutions') }}</p>
                <p class="text-xs text-slate-500">{{ \App\Models\Setting::get('site_address', 'Multiplan Center, Elephant Road, Dhaka, Bangladesh') }}</p>
                <p class="text-xs text-slate-500">Phone: {{ \App\Models\Setting::get('site_phone', '+880 1700-112233') }} | {{ \App\Models\Setting::get('site_email', 'support@dreamerspcb.com') }}</p>
                @if(\App\Models\Setting::get('invoice_trade_license') || \App\Models\Setting::get('footer_trade_license'))
                <p class="text-[11px] text-slate-400 font-mono">Trade Lic / BIN: {{ \App\Models\Setting::get('invoice_trade_license') ?: \App\Models\Setting::get('footer_trade_license') }}</p>
                @endif
            </div>
            <div class="sm:text-right">
                <span class="inline-block px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-emerald-100 text-emerald-800">
                    {{ \App\Models\Setting::get('invoice_title', 'Official Tax Invoice') }}
                </span>
                <div class="text-lg font-black text-slate-900 code-font mt-2">{{ $order->order_no }}</div>
                <div class="text-xs text-slate-500">Date: {{ $order->created_at->format('d F Y') }}</div>
            </div>
        </div>

        <!-- Bill To / Ship To -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 text-xs">
            <div class="p-4 rounded-xl bg-slate-50 border border-slate-100 space-y-1">
                <div class="font-bold text-slate-500 uppercase tracking-wider text-[10px]">Billed & Shipped To:</div>
                <div class="text-sm font-bold text-slate-900">{{ $order->shipping_name }}</div>
                <div class="font-mono text-emerald-600 font-semibold">{{ $order->shipping_phone }}</div>
                <div class="text-slate-600 mt-1">{{ $order->shipping_address }}</div>
                <div class="font-medium text-slate-700">City: {{ $order->shipping_city }}</div>
            </div>

            <div class="p-4 rounded-xl bg-slate-50 border border-slate-100 space-y-1">
                <div class="font-bold text-slate-500 uppercase tracking-wider text-[10px]">Payment & Shipment Details:</div>
                <div><strong>Payment Method:</strong> {{ strtoupper($order->payment_method) }}</div>
                <div><strong>Payment Status:</strong> {{ strtoupper($order->payment_status) }}</div>
                <div><strong>Courier:</strong> {{ $order->courier_name ?? 'Standard Courier' }}</div>
                <div><strong>Tracking No:</strong> {{ $order->courier_tracking_id ?? 'N/A' }}</div>
            </div>
        </div>

        <!-- Items Table -->
        <table class="w-full text-left text-xs">
            <thead>
                <tr class="border-y border-slate-200 text-slate-500 font-bold uppercase tracking-wider text-[10px] bg-slate-50">
                    <th class="py-3 px-3">#</th>
                    <th class="py-3 px-3">Item Description</th>
                    <th class="py-3 px-3">SKU</th>
                    <th class="py-3 px-3 text-right">Unit Price</th>
                    <th class="py-3 px-3 text-center">Qty</th>
                    <th class="py-3 px-3 text-right">Subtotal</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach($order->items as $idx => $item)
                <tr>
                    <td class="py-3 px-3 text-slate-400 font-mono">{{ $idx + 1 }}</td>
                    <td class="py-3 px-3">
                        <div class="font-bold text-slate-900">{{ $item->product_name }}</div>
                        @if($item->variant_name)
                        <div class="text-[11px] text-emerald-600">{{ $item->variant_name }}</div>
                        @endif
                        @if($item->product && $item->product->pcb_model)
                        <div class="text-[10px] text-slate-400">Spec: {{ $item->product->pcb_model }} | {{ $item->product->voltage }}</div>
                        @endif
                    </td>
                    <td class="py-3 px-3 font-mono text-slate-500">{{ $item->sku }}</td>
                    <td class="py-3 px-3 text-right code-font">৳{{ number_format($item->unit_price, 2) }}</td>
                    <td class="py-3 px-3 text-center font-bold">{{ $item->quantity }}</td>
                    <td class="py-3 px-3 text-right font-bold text-slate-900 code-font">৳{{ number_format($item->subtotal, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Totals Calculation -->
        <div class="flex justify-end pt-4">
            <div class="w-72 space-y-2 text-xs">
                <div class="flex justify-between text-slate-500">
                    <span>Subtotal:</span>
                    <span class="font-semibold text-slate-900 code-font">৳{{ number_format($order->subtotal, 2) }}</span>
                </div>
                @if($order->discount > 0)
                <div class="flex justify-between text-emerald-600">
                    <span>Discount:</span>
                    <span class="font-semibold code-font">-৳{{ number_format($order->discount, 2) }}</span>
                </div>
                @endif
                <div class="flex justify-between text-slate-500">
                    <span>Shipping Fee:</span>
                    <span class="font-semibold text-slate-900 code-font">+৳{{ number_format($order->shipping_charge, 2) }}</span>
                </div>
                @if($order->tax > 0)
                <div class="flex justify-between text-slate-500">
                    <span>Tax / VAT:</span>
                    <span class="font-semibold text-slate-900 code-font">+৳{{ number_format($order->tax, 2) }}</span>
                </div>
                @endif
                <div class="flex justify-between text-base font-black pt-2 border-t-2 border-slate-900 text-slate-900">
                    <span>Total Payable:</span>
                    <span class="text-emerald-600 code-font">৳{{ number_format($order->grand_total, 2) }}</span>
                </div>
                <div class="flex justify-between text-xs text-slate-600 pt-1">
                    <span>Amount Paid:</span>
                    <span class="font-bold code-font">৳{{ number_format($order->paid_amount, 2) }}</span>
                </div>
                @if($order->due_amount > 0)
                <div class="flex justify-between text-xs font-bold text-rose-600">
                    <span>Amount Due (COD):</span>
                    <span class="code-font">৳{{ number_format($order->due_amount, 2) }}</span>
                </div>
                @endif
            </div>
        </div>

        <!-- Footer / Signature -->
        <div class="pt-12 border-t border-slate-200 flex justify-between items-end text-xs text-slate-500">
            <div class="max-w-md">
                <p class="font-bold text-slate-900 mb-0.5">Terms & Conditions:</p>
                <p class="text-[11px] leading-relaxed text-slate-600 whitespace-pre-line">{{ \App\Models\Setting::get('invoice_terms', "1. Warranty replacement valid with this original invoice.\n2. Physical burn or water damage voids PCB warranty.") }}</p>
            </div>
            <div class="text-center">
                <div class="w-40 border-b border-slate-400 mb-2"></div>
                <p class="font-semibold text-slate-700">{{ \App\Models\Setting::get('invoice_signatory', 'Authorized Signature') }}</p>
            </div>
        </div>

    </div>

</body>
</html>
