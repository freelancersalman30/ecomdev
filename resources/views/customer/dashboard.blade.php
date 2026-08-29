@extends('layouts.customer')

@section('title', 'Customer Dashboard - DREAMERS PCB')

@section('customer_content')
<div class="space-y-6">

    <!-- 4 Overview Metric Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        
        <div class="bg-white p-5 rounded-3xl border border-slate-200 shadow-sm space-y-1">
            <div class="flex items-center justify-between text-slate-400">
                <span class="text-[10px] font-bold uppercase tracking-wider">Total Orders</span>
                <i data-lucide="package" class="w-4 h-4 text-sky-500"></i>
            </div>
            <div class="text-2xl font-black text-slate-900 code-font">{{ $totalOrders }}</div>
            <span class="text-[10px] text-slate-500 font-medium">Lifetime placements</span>
        </div>

        <div class="bg-white p-5 rounded-3xl border border-slate-200 shadow-sm space-y-1">
            <div class="flex items-center justify-between text-slate-400">
                <span class="text-[10px] font-bold uppercase tracking-wider">In Transit</span>
                <i data-lucide="truck" class="w-4 h-4 text-amber-500"></i>
            </div>
            <div class="text-2xl font-black text-amber-600 code-font">{{ $inTransitOrders }}</div>
            <span class="text-[10px] text-slate-500 font-medium">Courier on the way</span>
        </div>

        <div class="bg-white p-5 rounded-3xl border border-slate-200 shadow-sm space-y-1">
            <div class="flex items-center justify-between text-slate-400">
                <span class="text-[10px] font-bold uppercase tracking-wider">Delivered</span>
                <i data-lucide="check-circle" class="w-4 h-4 text-emerald-500"></i>
            </div>
            <div class="text-2xl font-black text-emerald-600 code-font">{{ $deliveredOrders }}</div>
            <span class="text-[10px] text-slate-500 font-medium">Successfully received</span>
        </div>

        <div class="bg-white p-5 rounded-3xl border border-slate-200 shadow-sm space-y-1">
            <div class="flex items-center justify-between text-slate-400">
                <span class="text-[10px] font-bold uppercase tracking-wider">Total Spent</span>
                <i data-lucide="banknote" class="w-4 h-4 text-daraz-orange"></i>
            </div>
            <div class="text-2xl font-black text-daraz-orange code-font">৳{{ number_format($totalSpent, 0) }}</div>
            <span class="text-[10px] text-slate-500 font-medium">Paid orders</span>
        </div>

    </div>

    <!-- Recent Orders Section -->
    <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-sm space-y-4">
        <div class="flex items-center justify-between border-b pb-3">
            <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                <i data-lucide="clock" class="w-4 h-4 text-daraz-orange"></i>
                <span>Recent Orders</span>
            </h3>
            <a href="{{ route('customer.orders') }}" class="text-xs font-bold text-daraz-orange hover:underline">
                View All Orders &rarr;
            </a>
        </div>

        @if($recentOrders->count() > 0)
        <div class="divide-y divide-slate-100 text-xs">
            @foreach($recentOrders as $order)
            <div class="py-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div class="space-y-1">
                    <div class="flex items-center gap-2">
                        <span class="font-bold text-slate-900 code-font">{{ $order->order_no }}</span>
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase {{ in_array($order->status, ['delivered', 'completed']) ? 'bg-emerald-100 text-emerald-800' : 'bg-daraz-light text-daraz-orange' }}">
                            {{ str_replace('_', ' ', $order->status) }}
                        </span>
                    </div>
                    <p class="text-slate-400 text-[11px]">{{ $order->created_at->format('d M Y, h:i A') }} &bull; {{ $order->items->count() }} items</p>
                </div>

                <div class="flex items-center gap-4">
                    <div class="font-black text-slate-900 code-font text-sm">
                        ৳{{ number_format($order->grand_total, 2) }}
                    </div>
                    
                    <a href="{{ route('customer.orders.show', $order->order_no) }}" class="px-3 py-1.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs transition">
                        View Details
                    </a>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="py-12 text-center text-slate-400 space-y-2">
            <i data-lucide="package" class="w-10 h-10 mx-auto text-slate-300"></i>
            <p class="text-xs font-semibold">You haven't placed any hardware orders yet</p>
            <a href="{{ route('shop.index') }}" class="inline-block px-4 py-2 rounded-xl bg-daraz-orange text-white font-bold text-xs">
                Explore Products Now
            </a>
        </div>
        @endif
    </div>

    <!-- Maker Perks & Loyalty Banner -->
    <div class="p-6 rounded-3xl bg-gradient-to-r from-emerald-950 to-slate-900 text-white border border-emerald-800/40 flex flex-col sm:flex-row items-center justify-between gap-4">
        <div class="space-y-1 text-center sm:text-left">
            <h4 class="font-black text-base text-emerald-300">🪙 Maker Rewards Club</h4>
            <p class="text-xs text-slate-300">Earn 5% points on every STM32, ESP32, and PCB tool purchase. Redeem points for instant cart discounts.</p>
        </div>
        <a href="{{ route('shop.index') }}" class="px-5 py-2.5 rounded-xl bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-black text-xs uppercase tracking-wider whitespace-nowrap shadow-lg">
            Shop & Earn Points
        </a>
    </div>

</div>
@endsection
