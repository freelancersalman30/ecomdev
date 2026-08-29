<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Packing Slip #{{ $order->order_no }} - DREAMERS PCB</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: sans-serif; }
        @media print {
            .no-print { display: none !important; }
        }
    </style>
</head>
<body class="bg-white p-6 text-slate-800 text-xs">

    <div class="max-w-3xl mx-auto border-2 border-dashed border-slate-300 p-6 rounded-xl space-y-6">
        
        <!-- Header -->
        <div class="flex justify-between items-center border-b pb-4">
            <div>
                <h1 class="text-xl font-bold">DREAMERS PCB - WAREHOUSE PACKING SLIP</h1>
                <p class="text-slate-500">Order Ref: <span class="font-mono font-bold text-slate-900">{{ $order->order_no }}</span></p>
                <p class="text-slate-500">Courier: <span class="font-bold text-slate-900">{{ $order->courier_name ?? 'Standard Dispatch' }}</span></p>
            </div>
            <div class="text-right">
                <div class="text-xs font-mono font-bold bg-slate-100 p-2 rounded border">
                    Tracking: {{ $order->courier_tracking_id ?? 'PENDING' }}
                </div>
            </div>
        </div>

        <!-- Shipping Destination -->
        <div class="bg-slate-50 p-4 rounded-lg border">
            <p class="font-bold text-slate-900 text-sm">{{ $order->shipping_name }}</p>
            <p class="text-emerald-700 font-mono font-bold">{{ $order->shipping_phone }}</p>
            <p class="mt-1">{{ $order->shipping_address }}, {{ $order->shipping_city }}</p>
        </div>

        <!-- Packing Items List -->
        <div>
            <h3 class="font-bold uppercase tracking-wider text-[11px] mb-2 text-slate-600">Items to Pack:</h3>
            <table class="w-full text-left border">
                <thead class="bg-slate-100 border-b">
                    <tr>
                        <th class="p-2 w-8 text-center">Check</th>
                        <th class="p-2">Product Name</th>
                        <th class="p-2">SKU / Model</th>
                        <th class="p-2 text-center">Qty to Pack</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @foreach($order->items as $item)
                    <tr>
                        <td class="p-2 text-center">
                            <div class="w-4 h-4 border-2 border-slate-400 rounded inline-block"></div>
                        </td>
                        <td class="p-2">
                            <span class="font-bold">{{ $item->product_name }}</span>
                            @if($item->variant_name)
                            <div class="text-[10px] text-slate-500 font-semibold">{{ $item->variant_name }}</div>
                            @endif
                        </td>
                        <td class="p-2 font-mono text-[11px]">{{ $item->sku }}</td>
                        <td class="p-2 text-center font-bold text-sm">{{ $item->quantity }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Verification Signature -->
        <div class="flex justify-between items-center pt-8 border-t text-[11px] text-slate-500">
            <div>Packed By: ____________________</div>
            <div>Quality Inspected: [ OK ]</div>
            <div>Dispatch Date: {{ now()->format('d/m/Y') }}</div>
        </div>

    </div>

</body>
</html>
