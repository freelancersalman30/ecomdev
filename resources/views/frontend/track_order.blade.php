@extends('layouts.app')

@section('title', 'Track Your Order - DREAMERS PCB')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-8 space-y-8">

    <!-- Track Order Search Box -->
    <div class="bg-white rounded-3xl p-6 sm:p-10 border border-slate-200 shadow-sm space-y-6 text-center">
        <div class="space-y-1">
            <h1 class="text-xl sm:text-2xl font-black text-slate-900 flex items-center justify-center gap-2">
                <i data-lucide="map-pin" class="w-6 h-6 text-daraz-orange"></i>
                <span>Live Order & Courier Tracking</span>
            </h1>
            <p class="text-xs text-slate-500 max-w-md mx-auto">Enter your Order Invoice ID (e.g. ORD-20260827-XXXX) or registered phone number to track live status.</p>
        </div>

        <form method="GET" action="{{ route('order.track') }}" class="max-w-xl mx-auto flex flex-col sm:flex-row gap-2">
            <input 
                type="text" 
                name="order_no" 
                value="{{ $orderNo }}" 
                placeholder="Enter Order ID (e.g. ORD-20260827-001)" 
                class="flex-1 px-4 py-3 rounded-2xl border border-slate-200 bg-slate-50 text-xs font-mono font-bold outline-none focus:ring-2 focus:ring-daraz-orange/20">
            
            <input 
                type="tel" 
                name="phone" 
                value="{{ $phone }}" 
                placeholder="Or Phone Number" 
                class="w-full sm:w-44 px-4 py-3 rounded-2xl border border-slate-200 bg-slate-50 text-xs font-mono outline-none focus:ring-2 focus:ring-daraz-orange/20">

            <button type="submit" class="px-6 py-3 rounded-2xl bg-daraz-orange hover:bg-daraz-orangeHover text-white font-extrabold text-xs flex items-center justify-center gap-1.5 shadow-lg shadow-daraz-orange/20 transition">
                <i data-lucide="search" class="w-4 h-4"></i>
                <span>Track Now</span>
            </button>
        </form>
    </div>

    <!-- Tracking Results Display -->
    @if($order)
    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-sm space-y-6">
        
        <!-- Header Info -->
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 border-b border-slate-100 pb-4">
            <div>
                <span class="text-[10px] uppercase font-bold text-slate-400">Order Invoice ID:</span>
                <h2 class="text-base font-black text-slate-900 code-font">{{ $order->order_no }}</h2>
                <div class="text-xs text-slate-500">Placed on {{ $order->created_at->format('d M Y, h:i A') }}</div>
            </div>

            <div class="sm:text-right space-y-1">
                <span class="inline-block px-3 py-1 rounded-full text-xs font-black uppercase tracking-wider {{ in_array($order->order_status, ['delivered', 'completed']) ? 'bg-emerald-100 text-emerald-800' : 'bg-daraz-light text-daraz-orange border border-daraz-orange/30' }}">
                    Status: {{ str_replace('_', ' ', $order->order_status) }}
                </span>
                @if($order->courier_name)
                <div class="text-xs text-slate-500">Courier: <strong>{{ $order->courier_name }}</strong></div>
                @endif
                @if($order->courier_tracking_id)
                <div class="text-[11px] font-mono text-emerald-600 font-bold">Tracking Ref: {{ $order->courier_tracking_id }}</div>
                @endif
            </div>
        </div>

        <!-- 8-Stage Progress Tracker Visual -->
        <div class="space-y-4">
            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-500">Live Delivery Stages</h3>

            <div class="grid grid-cols-4 gap-2 text-center text-[10px] font-bold">
                <div class="space-y-1">
                    <div class="h-2 rounded-full bg-emerald-500"></div>
                    <span class="text-emerald-600">1. Order Placed</span>
                </div>
                <div class="space-y-1">
                    <div class="h-2 rounded-full {{ in_array($order->order_status, ['confirmed', 'processing', 'shipped', 'in_transit', 'delivered', 'completed']) ? 'bg-emerald-500' : 'bg-slate-200' }}"></div>
                    <span class="{{ in_array($order->order_status, ['confirmed', 'processing', 'shipped', 'in_transit', 'delivered', 'completed']) ? 'text-emerald-600' : 'text-slate-400' }}">2. Confirmed</span>
                </div>
                <div class="space-y-1">
                    <div class="h-2 rounded-full {{ in_array($order->order_status, ['shipped', 'in_transit', 'delivered', 'completed']) ? 'bg-emerald-500' : 'bg-slate-200' }}"></div>
                    <span class="{{ in_array($order->order_status, ['shipped', 'in_transit', 'delivered', 'completed']) ? 'text-emerald-600' : 'text-slate-400' }}">3. Dispatched</span>
                </div>
                <div class="space-y-1">
                    <div class="h-2 rounded-full {{ in_array($order->order_status, ['delivered', 'completed']) ? 'bg-emerald-500' : 'bg-slate-200' }}"></div>
                    <span class="{{ in_array($order->order_status, ['delivered', 'completed']) ? 'text-emerald-600' : 'text-slate-400' }}">4. Delivered</span>
                </div>
            </div>
        </div>

        <!-- Ordered Items Summary -->
        <div class="pt-4 border-t border-slate-100 space-y-3">
            <h4 class="text-xs font-bold text-slate-800">Purchased Hardware Components:</h4>
            <div class="divide-y divide-slate-100 text-xs">
                @foreach($order->items as $item)
                <div class="py-2.5 flex items-center justify-between">
                    <div>
                        <span class="font-bold text-slate-900">{{ $item->product_name }}</span>
                        @if($item->variant_name)
                        <span class="text-[11px] text-emerald-600 ml-1">({{ $item->variant_name }})</span>
                        @endif
                        <div class="text-[10px] text-slate-400 font-mono">Qty: {{ $item->quantity }}</div>
                    </div>
                    <span class="font-bold text-slate-900 code-font">৳{{ number_format($item->subtotal, 2) }}</span>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Shipping Destination Info -->
        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 text-xs space-y-1">
            <span class="text-[10px] uppercase font-bold text-slate-400">Recipient Details:</span>
            <div class="font-bold text-slate-900">{{ $order->shipping_name }} &bull; <span class="font-mono text-emerald-600">{{ $order->shipping_phone }}</span></div>
            <div class="text-slate-600">{{ $order->shipping_address }}, {{ $order->shipping_city }}</div>
        </div>

    </div>
    @elseif($orderNo || $phone)
    <div class="bg-white rounded-3xl p-12 text-center border border-slate-200 shadow-sm space-y-3">
        <i data-lucide="package-search" class="w-12 h-12 mx-auto text-slate-300"></i>
        <h3 class="text-base font-bold text-slate-900">No order found matching your query</h3>
        <p class="text-xs text-slate-400">Please double check your Order ID or phone number and try again.</p>
    </div>
    @endif

</div>
@endsection
