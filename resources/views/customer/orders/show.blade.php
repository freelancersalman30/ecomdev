@extends('layouts.customer')

@section('title', 'Order ' . $order->order_no . ' - DREAMERS PCB')

@section('customer_content')
<div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-sm space-y-6">

    <!-- Header & Action Toolbar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b pb-4">
        <div>
            <div class="flex items-center gap-2">
                <a href="{{ route('customer.orders') }}" class="text-xs font-bold text-daraz-orange hover:underline">&larr; All Orders</a>
                <span class="text-slate-300">/</span>
                <h2 class="text-base font-black text-slate-900 code-font">{{ $order->order_no }}</h2>
            </div>
            <p class="text-xs text-slate-400 mt-0.5">Placed on {{ $order->created_at->format('d M Y, h:i A') }}</p>
        </div>

        <div class="flex items-center gap-2">
            <span class="px-3 py-1 rounded-full text-xs font-black uppercase tracking-wider {{ in_array($order->status, ['delivered', 'completed']) ? 'bg-emerald-100 text-emerald-800' : 'bg-daraz-light text-daraz-orange border border-daraz-orange/30' }}">
                {{ str_replace('_', ' ', $order->status) }}
            </span>

            <button onclick="window.print()" class="px-3.5 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs flex items-center gap-1.5 transition">
                <i data-lucide="printer" class="w-3.5 h-3.5"></i>
                <span>Print</span>
            </button>
        </div>
    </div>

    <!-- 8-Stage Progress Tracker Visual -->
    <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200 space-y-4">
        <h3 class="text-xs font-bold uppercase tracking-wider text-slate-500">Live Delivery Timeline</h3>

        <div class="grid grid-cols-4 gap-2 text-center text-[10px] font-bold">
            <div class="space-y-1">
                <div class="h-2 rounded-full bg-emerald-500"></div>
                <span class="text-emerald-600">1. Placed</span>
            </div>
            <div class="space-y-1">
                <div class="h-2 rounded-full {{ in_array($order->status, ['confirmed', 'processing', 'shipped', 'in_transit', 'delivered', 'completed']) ? 'bg-emerald-500' : 'bg-slate-200' }}"></div>
                <span class="{{ in_array($order->status, ['confirmed', 'processing', 'shipped', 'in_transit', 'delivered', 'completed']) ? 'text-emerald-600' : 'text-slate-400' }}">2. Confirmed</span>
            </div>
            <div class="space-y-1">
                <div class="h-2 rounded-full {{ in_array($order->status, ['shipped', 'in_transit', 'delivered', 'completed']) ? 'bg-emerald-500' : 'bg-slate-200' }}"></div>
                <span class="{{ in_array($order->status, ['shipped', 'in_transit', 'delivered', 'completed']) ? 'text-emerald-600' : 'text-slate-400' }}">3. Dispatched</span>
            </div>
            <div class="space-y-1">
                <div class="h-2 rounded-full {{ in_array($order->status, ['delivered', 'completed']) ? 'bg-emerald-500' : 'bg-slate-200' }}"></div>
                <span class="{{ in_array($order->status, ['delivered', 'completed']) ? 'text-emerald-600' : 'text-slate-400' }}">4. Delivered</span>
            </div>
        </div>

        @if($order->courier_tracking_id)
        <div class="text-[11px] text-emerald-700 bg-emerald-50 p-2.5 rounded-xl border border-emerald-200 flex items-center justify-between">
            <span>Courier Partner: <strong>{{ $order->courier_name ?? 'Steadfast Courier' }}</strong></span>
            <span class="font-mono font-bold">Ref: {{ $order->courier_tracking_id }}</span>
        </div>
        @endif
    </div>

    <!-- Items Breakdown -->
    <div class="space-y-3">
        <h3 class="text-xs font-bold uppercase tracking-wider text-slate-500">Ordered Hardware Items</h3>
        <div class="border border-slate-200 rounded-2xl overflow-hidden divide-y divide-slate-100 text-xs">
            @foreach($order->items as $item)
            <div class="p-3.5 flex items-center justify-between gap-4">
                <div class="min-w-0">
                    <h4 class="font-bold text-slate-900 truncate">{{ $item->product_name }}</h4>
                    @if($item->variant_name)
                    <div class="text-[11px] text-emerald-600 font-semibold">{{ $item->variant_name }}</div>
                    @endif
                    <div class="text-[10px] text-slate-400 font-mono">Unit: ৳{{ number_format($item->unit_price, 2) }} x {{ $item->quantity }}</div>
                </div>
                <div class="font-black text-slate-900 code-font whitespace-nowrap">
                    ৳{{ number_format($item->subtotal, 2) }}
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <!-- 2 Columns: Shipping Address & Financial Summary -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-2">
        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 text-xs space-y-1.5">
            <span class="text-[10px] uppercase font-bold text-slate-400">Delivery Address:</span>
            <div class="font-bold text-slate-900">{{ $order->shipping_name }}</div>
            <div class="text-emerald-600 font-mono font-bold">{{ $order->shipping_phone }}</div>
            <div class="text-slate-600">{{ $order->shipping_address }}, {{ $order->shipping_city }}</div>
            @if($order->customer_note)
            <div class="text-[11px] text-slate-400 pt-1">Note: {{ $order->customer_note }}</div>
            @endif
        </div>

        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 text-xs space-y-2">
            <span class="text-[10px] uppercase font-bold text-slate-400">Payment Breakdown:</span>
            <div class="flex justify-between text-slate-600">
                <span>Items Subtotal:</span>
                <span class="font-bold text-slate-900 code-font">৳{{ number_format($order->subtotal, 2) }}</span>
            </div>
            @if($order->discount > 0)
            <div class="flex justify-between text-emerald-600 font-bold">
                <span>Discount:</span>
                <span class="code-font">-৳{{ number_format($order->discount, 2) }}</span>
            </div>
            @endif
            <div class="flex justify-between text-slate-600">
                <span>Delivery Charge:</span>
                <span class="font-bold text-slate-900 code-font">৳{{ number_format($order->shipping_charge, 2) }}</span>
            </div>
            <div class="flex justify-between text-sm font-black text-slate-900 pt-2 border-t border-slate-200">
                <span>Total Amount:</span>
                <span class="text-daraz-orange code-font text-base">৳{{ number_format($order->grand_total, 2) }}</span>
            </div>
        </div>
    </div>

</div>
@endsection
