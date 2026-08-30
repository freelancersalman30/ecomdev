@extends('layouts.customer')

@section('title', 'Product Warranty Verification - DREAMERS PCB')

@section('customer_content')
<div class="space-y-6">

    <!-- Header & Instant Verification Search Box -->
    <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-sm space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-slate-100 pb-4">
            <div>
                <h2 class="text-base font-black text-slate-900 flex items-center gap-2">
                    <i data-lucide="shield-check" class="w-5 h-5 text-daraz-orange"></i>
                    <span>Product Warranty Verification Hub</span>
                </h2>
                <p class="text-xs text-slate-500 mt-0.5">Track remaining warranty days, coverage dates, and verify genuine hardware certificates</p>
            </div>
            <span class="text-xs font-bold text-slate-400 font-mono">100% Genuine Protection</span>
        </div>

        <!-- Verification Search Tool -->
        <form method="GET" action="{{ route('customer.warranties') }}" class="flex flex-col sm:flex-row items-center gap-3 pt-1">
            <div class="relative w-full">
                <i data-lucide="search" class="w-4 h-4 absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400"></i>
                <input 
                    type="text" 
                    name="lookup_code" 
                    value="{{ request('lookup_code') }}" 
                    placeholder="Verify any Warranty Code, Serial #, or Order # (e.g. WAR-202608-XXXXX)" 
                    class="w-full pl-10 pr-4 py-2.5 rounded-2xl border border-slate-200 bg-slate-50 text-xs text-slate-900 outline-none focus:ring-2 focus:ring-daraz-orange/40 focus:border-daraz-orange transition font-mono">
            </div>
            <div class="flex items-center gap-2 w-full sm:w-auto">
                <button type="submit" class="w-full sm:w-auto px-5 py-2.5 rounded-2xl bg-daraz-orange hover:bg-daraz-orange-hover text-white font-bold text-xs flex items-center justify-center gap-2 transition shadow-md shadow-orange-500/20 whitespace-nowrap">
                    <i data-lucide="shield-check" class="w-4 h-4"></i>
                    <span>Verify Code</span>
                </button>
                @if(request()->filled('lookup_code') || request()->filled('search'))
                <a href="{{ route('customer.warranties') }}" class="px-3.5 py-2.5 rounded-2xl bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-bold transition">
                    Reset
                </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Verification Result Certificate Card (If query matched) -->
    @if(request()->filled('lookup_code'))
    <div class="rounded-3xl p-6 border {{ $lookupResult ? 'bg-gradient-to-br from-slate-950 via-slate-900 to-slate-950 border-emerald-500/40 text-white shadow-xl shadow-emerald-950/20' : 'bg-rose-50 border-rose-200 text-rose-900' }}">
        @if($lookupResult)
        <div class="space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-800 pb-4">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-2xl bg-emerald-500/20 text-emerald-400 flex items-center justify-center shadow-inner">
                        <i data-lucide="shield-check" class="w-7 h-7"></i>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-emerald-500 text-slate-950">
                                Verified Genuine Warranty
                            </span>
                            <span class="text-xs text-emerald-400 font-mono font-bold">{{ $lookupResult->warranty_code }}</span>
                        </div>
                        <h3 class="text-base font-black text-white mt-1">{{ $lookupResult->product->name }}</h3>
                    </div>
                </div>

                <!-- Remaining Days Highlight Pill -->
                <div class="flex items-center gap-3 bg-slate-800/80 px-4 py-3 rounded-2xl border border-slate-700/60">
                    <div class="text-right">
                        <div class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Remaining Period</div>
                        <div class="text-xl font-black code-font {{ $lookupResult->remaining_days > 30 ? 'text-emerald-400' : ($lookupResult->remaining_days > 0 ? 'text-amber-400' : 'text-rose-400') }}">
                            {{ $lookupResult->remaining_days }} Days Left
                        </div>
                    </div>
                    <div class="w-10 h-10 rounded-xl {{ $lookupResult->remaining_days > 0 ? 'bg-emerald-500/20 text-emerald-400' : 'bg-rose-500/20 text-rose-400' }} flex items-center justify-center">
                        <i data-lucide="{{ $lookupResult->remaining_days > 0 ? 'check' : 'x' }}" class="w-5 h-5"></i>
                    </div>
                </div>
            </div>

            <!-- Progress Meter -->
            <div class="space-y-1.5 pt-1">
                <div class="flex justify-between text-[11px] text-slate-300 font-medium">
                    <span>Started: {{ $lookupResult->start_date->format('d M Y') }}</span>
                    <span class="font-bold text-emerald-400">{{ $lookupResult->remaining_days }} Days Remaining</span>
                    <span>Expires: {{ $lookupResult->end_date->format('d M Y') }}</span>
                </div>
                <div class="w-full bg-slate-800 rounded-full h-2.5 overflow-hidden">
                    <div class="h-2.5 rounded-full {{ $lookupResult->remaining_days > 30 ? 'bg-emerald-500' : ($lookupResult->remaining_days > 0 ? 'bg-amber-500' : 'bg-rose-500') }}" 
                         style="width: {{ $lookupResult->remaining_percentage }}%"></div>
                </div>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 pt-3 border-t border-slate-800 text-xs">
                <div>
                    <span class="text-[10px] text-slate-400 uppercase font-bold">Serial Number</span>
                    <div class="font-mono font-bold text-white mt-0.5">{{ $lookupResult->serial_number ?: 'Assigned per Order' }}</div>
                </div>
                <div>
                    <span class="text-[10px] text-slate-400 uppercase font-bold">Coverage Period</span>
                    <div class="font-semibold text-white mt-0.5">{{ $lookupResult->warranty_period }}</div>
                </div>
                <div>
                    <span class="text-[10px] text-slate-400 uppercase font-bold">Owner</span>
                    <div class="font-semibold text-white mt-0.5">{{ $lookupResult->customer_name }}</div>
                </div>
                <div>
                    <span class="text-[10px] text-slate-400 uppercase font-bold">Order Reference</span>
                    <div class="mt-0.5">
                        @if($lookupResult->order)
                        <a href="{{ route('customer.orders.show', $lookupResult->order->order_no) }}" class="font-mono text-emerald-400 hover:underline font-bold">
                            {{ $lookupResult->order->order_no }}
                        </a>
                        @else
                        <span class="font-mono text-slate-400">Direct Purchase</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @else
        <div class="flex items-center gap-3 text-rose-700 text-xs">
            <i data-lucide="alert-circle" class="w-5 h-5 flex-shrink-0"></i>
            <div>
                <span class="font-bold">No warranty record found</span> for query: <strong>{{ request('lookup_code') }}</strong>. Please verify the code on your product box or invoice.
            </div>
        </div>
        @endif
    </div>
    @endif

    <!-- 4 Stats Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        
        <div class="bg-white p-5 rounded-3xl border border-slate-200 shadow-sm space-y-1">
            <div class="flex items-center justify-between text-slate-400">
                <span class="text-[10px] font-bold uppercase tracking-wider">Total Products</span>
                <i data-lucide="shield" class="w-4 h-4 text-sky-500"></i>
            </div>
            <div class="text-2xl font-black text-slate-900 code-font">{{ $stats['total'] }}</div>
            <span class="text-[10px] text-slate-500 font-medium">Under warranty registration</span>
        </div>

        <div class="bg-white p-5 rounded-3xl border border-slate-200 shadow-sm space-y-1">
            <div class="flex items-center justify-between text-slate-400">
                <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-600">Active Covered</span>
                <i data-lucide="shield-check" class="w-4 h-4 text-emerald-500"></i>
            </div>
            <div class="text-2xl font-black text-emerald-600 code-font">{{ $stats['active'] }}</div>
            <span class="text-[10px] text-slate-500 font-medium">Currently valid protection</span>
        </div>

        <div class="bg-white p-5 rounded-3xl border border-slate-200 shadow-sm space-y-1">
            <div class="flex items-center justify-between text-slate-400">
                <span class="text-[10px] font-bold uppercase tracking-wider text-amber-600">Expiring Soon</span>
                <i data-lucide="clock" class="w-4 h-4 text-amber-500"></i>
            </div>
            <div class="text-2xl font-black text-amber-600 code-font">{{ $stats['expiring_soon'] }}</div>
            <span class="text-[10px] text-slate-500 font-medium">&le; 30 days left</span>
        </div>

        <div class="bg-white p-5 rounded-3xl border border-slate-200 shadow-sm space-y-1">
            <div class="flex items-center justify-between text-slate-400">
                <span class="text-[10px] font-bold uppercase tracking-wider text-rose-600">Expired</span>
                <i data-lucide="shield-alert" class="w-4 h-4 text-rose-500"></i>
            </div>
            <div class="text-2xl font-black text-rose-600 code-font">{{ $stats['expired'] }}</div>
            <span class="text-[10px] text-slate-500 font-medium">0 days remaining</span>
        </div>

    </div>

    <!-- My Warranties Grid -->
    <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-sm space-y-5">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-100 pb-4">
            <div class="flex items-center gap-2">
                <i data-lucide="package" class="w-4 h-4 text-daraz-orange"></i>
                <h3 class="text-sm font-bold text-slate-900">My Purchased Products & Warranties</h3>
                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-600">{{ $warranties->total() }}</span>
            </div>

            <!-- Filter Search -->
            <form method="GET" action="{{ route('customer.warranties') }}" class="flex items-center gap-2">
                <input 
                    type="text" 
                    name="search" 
                    value="{{ request('search') }}" 
                    placeholder="Search my products..." 
                    class="px-3 py-1.5 rounded-xl border border-slate-200 bg-slate-50 text-xs text-slate-800 outline-none focus:ring-2 focus:ring-daraz-orange/40">
                <button type="submit" class="px-3 py-1.5 rounded-xl bg-slate-900 text-white font-bold text-xs">
                    Search
                </button>
            </form>
        </div>

        @if($warranties->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @foreach($warranties as $w)
            @php
                $badge = $w->status_badge;
                $rem = $w->remaining_days;
            @endphp
            <div class="rounded-2xl border border-slate-200 p-4 space-y-3.5 hover:shadow-md transition bg-gradient-to-b from-white to-slate-50/50 relative overflow-hidden">
                
                <!-- Top Row: Code & Remaining Days Pill -->
                <div class="flex items-start justify-between gap-2">
                    <div class="space-y-0.5">
                        <div class="flex items-center gap-1.5">
                            <span class="font-mono text-xs font-bold text-slate-900">{{ $w->warranty_code }}</span>
                            @if($w->serial_number)
                            <span class="text-[10px] font-mono text-slate-400 font-medium">({{ $w->serial_number }})</span>
                            @endif
                        </div>
                        <div class="text-[11px] text-slate-500">{{ $w->warranty_period }}</div>
                    </div>

                    <!-- Remaining Days Counter Badge -->
                    <div class="text-right">
                        <div class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-black {{ $rem > 30 ? 'bg-emerald-100 text-emerald-800 border border-emerald-200' : ($rem > 0 ? 'bg-amber-100 text-amber-800 border border-amber-200' : 'bg-rose-100 text-rose-800 border border-rose-200') }}">
                            <i data-lucide="{{ $rem > 0 ? 'clock' : 'shield-alert' }}" class="w-3.5 h-3.5"></i>
                            <span>{{ $rem > 0 ? "{$rem} Days Left" : 'Expired' }}</span>
                        </div>
                    </div>
                </div>

                <!-- Product Info -->
                <div class="flex items-center gap-3 pt-1">
                    <img src="{{ $w->product->thumbnail }}" alt="{{ $w->product->name }}" class="w-12 h-12 object-cover rounded-xl border border-slate-200 flex-shrink-0">
                    <div class="min-w-0">
                        <h4 class="font-bold text-xs text-slate-900 truncate hover:text-daraz-orange">
                            <a href="{{ route('product.show', $w->product->slug) }}">{{ $w->product->name }}</a>
                        </h4>
                        <div class="text-[10px] text-slate-400 font-mono mt-0.5">SKU: {{ $w->product->sku }}</div>
                    </div>
                </div>

                <!-- Visual Remaining Days Progress Meter -->
                <div class="space-y-1 bg-white p-2.5 rounded-xl border border-slate-100">
                    <div class="flex justify-between text-[10px] text-slate-500">
                        <span>From: {{ $w->start_date->format('d M Y') }}</span>
                        <span class="font-bold {{ $rem > 30 ? 'text-emerald-600' : ($rem > 0 ? 'text-amber-600' : 'text-rose-600') }}">
                            {{ $w->remaining_percentage }}% Time Left
                        </span>
                        <span>Until: {{ $w->end_date->format('d M Y') }}</span>
                    </div>
                    <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                        <div class="h-2 rounded-full {{ $rem > 30 ? 'bg-emerald-500' : ($rem > 0 ? 'bg-amber-500' : 'bg-rose-500') }}" 
                             style="width: {{ $w->remaining_percentage }}%"></div>
                    </div>
                </div>

                <!-- Footer & Order Link -->
                <div class="flex items-center justify-between pt-1 border-t border-slate-100 text-xs">
                    @if($w->order)
                    <a href="{{ route('customer.orders.show', $w->order->order_no) }}" class="text-[11px] font-bold text-daraz-orange hover:underline flex items-center gap-1 font-mono">
                        <i data-lucide="package" class="w-3.5 h-3.5"></i>
                        <span>Order #{{ $w->order->order_no }}</span>
                    </a>
                    @else
                    <span class="text-[11px] text-slate-400 font-mono">Store Purchase</span>
                    @endif

                    <a href="{{ route('customer.warranties', ['lookup_code' => $w->warranty_code]) }}" class="text-[11px] font-bold text-slate-700 hover:text-slate-900 flex items-center gap-1">
                        <span>View Certificate &rarr;</span>
                    </a>
                </div>

            </div>
            @endforeach
        </div>

        @if($warranties->hasPages())
        <div class="pt-4 border-t border-slate-100">
            {{ $warranties->links() }}
        </div>
        @endif

        @else
        <div class="py-12 text-center text-slate-400 space-y-2">
            <i data-lucide="shield-check" class="w-12 h-12 mx-auto text-slate-300"></i>
            <p class="text-xs font-semibold text-slate-600">No product warranties registered yet</p>
            <p class="text-[11px] text-slate-400">When you purchase hardware from DREAMERS PCB, your warranty coverage will appear here automatically.</p>
            <a href="{{ route('shop.index') }}" class="inline-block mt-2 px-4 py-2 rounded-xl bg-daraz-orange text-white font-bold text-xs shadow-md">
                Browse Hardware Store
            </a>
        </div>
        @endif
    </div>

</div>
@endsection
