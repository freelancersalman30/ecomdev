@extends('layouts.app')

@section('title', 'Order Placed Successfully! - DREAMERS PCB')

@section('content')
<div class="max-w-3xl mx-auto px-4 py-12 space-y-6">

    <!-- Success Celebration Card -->
    <div class="bg-white rounded-3xl p-8 sm:p-10 border border-slate-200 shadow-xl text-center space-y-4">
        
        <div class="w-20 h-20 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center mx-auto shadow-inner">
            <i data-lucide="check" class="w-10 h-10 stroke-[3]"></i>
        </div>

        <div class="space-y-1">
            <h1 class="text-2xl sm:text-3xl font-black text-slate-900">Thank You for Your Order!</h1>
            <p class="text-xs sm:text-sm text-slate-500">Your hardware order has been received and is now being prepared for dispatch.</p>
        </div>

        <!-- Order Meta Badge -->
        <div class="inline-flex flex-col sm:flex-row items-center gap-2 sm:gap-6 p-4 rounded-2xl bg-slate-50 border border-slate-200 code-font text-xs">
            <div>
                <span class="text-slate-400">Order Invoice ID:</span>
                <span class="font-bold text-slate-900 ml-1">{{ $order->order_no }}</span>
            </div>
            <span class="hidden sm:inline text-slate-300">|</span>
            <div>
                <span class="text-slate-400">Phone:</span>
                <span class="font-bold text-emerald-600 ml-1">{{ $order->shipping_phone }}</span>
            </div>
            <span class="hidden sm:inline text-slate-300">|</span>
            <div>
                <span class="text-slate-400">Total COD:</span>
                <span class="font-black text-daraz-orange ml-1">৳{{ number_format($order->grand_total, 2) }}</span>
            </div>
        </div>

        <!-- 8-Stage Progress Tracker Visual -->
        <div class="pt-6 border-t border-slate-100 space-y-4 text-left">
            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-500">Order Progression Timeline</h3>
            
            <div class="grid grid-cols-4 gap-2 text-center text-[10px] font-bold">
                <div class="space-y-1">
                    <div class="h-2 rounded-full bg-emerald-500"></div>
                    <span class="text-emerald-600">1. Placed</span>
                </div>
                <div class="space-y-1">
                    <div class="h-2 rounded-full {{ in_array($order->order_status, ['confirmed', 'processing', 'shipped', 'in_transit', 'delivered', 'completed']) ? 'bg-emerald-500' : 'bg-slate-200' }}"></div>
                    <span class="{{ in_array($order->order_status, ['confirmed', 'processing', 'shipped', 'in_transit', 'delivered', 'completed']) ? 'text-emerald-600' : 'text-slate-400' }}">2. Confirmed</span>
                </div>
                <div class="space-y-1">
                    <div class="h-2 rounded-full {{ in_array($order->order_status, ['shipped', 'in_transit', 'delivered', 'completed']) ? 'bg-emerald-500' : 'bg-slate-200' }}"></div>
                    <span class="{{ in_array($order->order_status, ['shipped', 'in_transit', 'delivered', 'completed']) ? 'text-emerald-600' : 'text-slate-400' }}">3. Courier Dispatch</span>
                </div>
                <div class="space-y-1">
                    <div class="h-2 rounded-full {{ in_array($order->order_status, ['delivered', 'completed']) ? 'bg-emerald-500' : 'bg-slate-200' }}"></div>
                    <span class="{{ in_array($order->order_status, ['delivered', 'completed']) ? 'text-emerald-600' : 'text-slate-400' }}">4. Delivered</span>
                </div>
            </div>
        </div>

        <!-- Order Items Breakdown -->
        <div class="pt-4 border-t border-slate-100 text-left space-y-2">
            <h4 class="text-xs font-bold text-slate-700">Ordered Components ({{ $order->items->count() }} items):</h4>
            <div class="divide-y divide-slate-100 text-xs">
                @foreach($order->items as $item)
                <div class="py-2 flex items-center justify-between">
                    <div>
                        <span class="font-bold text-slate-900">{{ $item->product_name }}</span>
                        @if($item->variant_name)
                        <span class="text-[10px] text-emerald-600 font-semibold ml-1">({{ $item->variant_name }})</span>
                        @endif
                        <div class="text-[10px] text-slate-400 font-mono">Qty: {{ $item->quantity }} x ৳{{ number_format($item->unit_price, 2) }}</div>
                    </div>
                    <span class="font-bold text-slate-900 code-font">৳{{ number_format($item->subtotal, 2) }}</span>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Action CTAs -->
        <div class="pt-6 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-center gap-3">
            <a href="{{ route('order.track', ['order_no' => $order->order_no]) }}" class="w-full sm:w-auto px-6 py-3 rounded-2xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs flex items-center justify-center gap-2 shadow-md transition">
                <i data-lucide="map-pin" class="w-4 h-4 text-emerald-400"></i>
                <span>Track Live Delivery</span>
            </a>
            
            <a href="{{ route('home') }}" class="w-full sm:w-auto px-6 py-3 rounded-2xl bg-daraz-orange hover:bg-daraz-orangeHover text-white font-extrabold text-xs shadow-md transition text-center">
                Continue Shopping &rarr;
            </a>
        </div>

    </div>

</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // 1. Meta / Facebook Pixel Purchase Event
        if (typeof fbq === 'function') {
            fbq('track', 'Purchase', {
                value: {{ (float) $order->grand_total }},
                currency: '{{ \App\Models\Setting::get("currency_code", "BDT") }}',
                content_type: 'product',
                num_items: {{ $order->items->sum('quantity') }}
            });
        }

        // 2. Google Ads Purchase Conversion Event
        @if(\App\Models\Setting::get('google_ads_enabled', '0') === '1' && \App\Models\Setting::get('google_ads_id') && \App\Models\Setting::get('google_ads_purchase_label'))
        if (typeof gtag === 'function') {
            gtag('event', 'conversion', {
                'send_to': '{{ \App\Models\Setting::get("google_ads_id") }}/{{ \App\Models\Setting::get("google_ads_purchase_label") }}',
                'value': {{ (float) $order->grand_total }},
                'currency': '{{ \App\Models\Setting::get("currency_code", "BDT") }}',
                'transaction_id': '{{ $order->order_no }}'
            });
        }
        @endif

        // 3. TikTok Pixel PlaceAnOrder / CompletePayment Event
        if (typeof ttq === 'object') {
            ttq.track('CompletePayment', {
                value: {{ (float) $order->grand_total }},
                currency: '{{ \App\Models\Setting::get("currency_code", "BDT") }}'
            });
        }
    });
</script>
@endpush
@endsection
