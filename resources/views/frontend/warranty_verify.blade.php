@extends('layouts.app')

@section('title', 'Official Product Warranty Verification - DREAMERS PCB')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-8 space-y-8">

    <!-- Warranty Search Box -->
    <div class="bg-white rounded-3xl p-6 sm:p-10 border border-slate-200 shadow-sm space-y-6 text-center">
        <div class="space-y-1.5">
            <div class="w-12 h-12 rounded-2xl bg-emerald-500/10 text-emerald-600 flex items-center justify-center mx-auto mb-2">
                <i data-lucide="shield-check" class="w-7 h-7"></i>
            </div>
            <h1 class="text-xl sm:text-2xl font-black text-slate-900">
                Official Product Warranty Verification
            </h1>
            <p class="text-xs text-slate-500 max-w-md mx-auto">
                Check warranty validity, authentic coverage period, and remaining days for any hardware purchased from DREAMERS PCB.
            </p>
        </div>

        <form method="GET" action="{{ route('warranty.verify') }}" class="max-w-xl mx-auto flex flex-col sm:flex-row gap-2.5">
            <input 
                type="text" 
                name="code" 
                value="{{ $code }}" 
                required
                placeholder="Enter Warranty Code, Serial #, or Order #" 
                class="flex-1 px-4 py-3 rounded-2xl border border-slate-200 bg-slate-50 text-xs font-mono font-bold outline-none focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500">

            <button type="submit" class="px-6 py-3 rounded-2xl bg-emerald-600 hover:bg-emerald-500 text-white font-extrabold text-xs flex items-center justify-center gap-2 shadow-lg shadow-emerald-600/20 transition whitespace-nowrap">
                <i data-lucide="shield-check" class="w-4 h-4"></i>
                <span>Verify Product</span>
            </button>
        </form>

        <div class="flex items-center justify-center gap-6 text-[11px] text-slate-400">
            <span class="flex items-center gap-1"><i data-lucide="check" class="w-3.5 h-3.5 text-emerald-500"></i> 100% Genuine Hardware</span>
            <span class="flex items-center gap-1"><i data-lucide="check" class="w-3.5 h-3.5 text-emerald-500"></i> Instant Days Countdown</span>
            <span class="flex items-center gap-1"><i data-lucide="check" class="w-3.5 h-3.5 text-emerald-500"></i> Official Invoice Matched</span>
        </div>
    </div>

    <!-- Verification Results Display -->
    @if($searched)
        @if($warranty)
        @php
            $rem = $warranty->remaining_days;
            $badge = $warranty->status_badge;
        @endphp
        <div class="bg-white rounded-3xl border border-slate-200 shadow-xl overflow-hidden">
            
            <!-- Top Verified Banner -->
            <div class="p-6 sm:p-8 bg-gradient-to-r from-slate-950 via-slate-900 to-slate-950 text-white flex flex-col sm:flex-row sm:items-center justify-between gap-6 border-b border-slate-800">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 rounded-2xl bg-emerald-500/20 text-emerald-400 flex items-center justify-center flex-shrink-0 shadow-inner">
                        <i data-lucide="shield-check" class="w-8 h-8"></i>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-emerald-500 text-slate-950">
                                Verified Genuine Hardware
                            </span>
                            <span class="font-mono text-xs text-emerald-400 font-bold">{{ $warranty->warranty_code }}</span>
                        </div>
                        <h2 class="text-lg sm:text-xl font-black text-white mt-1">{{ $warranty->product->name }}</h2>
                        <p class="text-xs text-slate-400 font-mono">Serial / IMEI: {{ $warranty->serial_number ?: 'Registered via Order' }}</p>
                    </div>
                </div>

                <!-- Remaining Days Counter Badge -->
                <div class="bg-slate-800/90 border border-slate-700/80 rounded-2xl p-4 text-center sm:text-right flex-shrink-0">
                    <div class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Remaining Warranty</div>
                    <div class="text-2xl font-black code-font {{ $rem > 30 ? 'text-emerald-400' : ($rem > 0 ? 'text-amber-400' : 'text-rose-400') }}">
                        {{ $rem > 0 ? "{$rem} Days Left" : 'Expired' }}
                    </div>
                    <div class="text-[10px] text-slate-400 mt-0.5">Expires on {{ $warranty->end_date->format('d M Y') }}</div>
                </div>
            </div>

            <!-- Details & Progress Bar -->
            <div class="p-6 sm:p-8 space-y-6">
                
                <!-- Progress Meter -->
                <div class="space-y-2 bg-slate-50 p-4 rounded-2xl border border-slate-100">
                    <div class="flex justify-between text-xs font-semibold text-slate-600">
                        <span>Started: {{ $warranty->start_date->format('d M Y') }}</span>
                        <span class="font-black {{ $rem > 30 ? 'text-emerald-600' : ($rem > 0 ? 'text-amber-600' : 'text-rose-600') }}">
                            {{ $rem }} Days Remaining ({{ $warranty->remaining_percentage }}%)
                        </span>
                        <span>Expires: {{ $warranty->end_date->format('d M Y') }}</span>
                    </div>
                    <div class="w-full bg-slate-200 rounded-full h-3 overflow-hidden">
                        <div class="h-3 rounded-full transition-all duration-500 {{ $rem > 30 ? 'bg-emerald-500' : ($rem > 0 ? 'bg-amber-500' : 'bg-rose-500') }}" 
                             style="width: {{ $warranty->remaining_percentage }}%"></div>
                    </div>
                </div>

                <!-- 4 Specifications Grid -->
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-xs">
                    <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-100">
                        <div class="text-[10px] font-bold text-slate-400 uppercase">Coverage Plan</div>
                        <div class="font-bold text-slate-900 mt-1">{{ $warranty->warranty_period }}</div>
                        <div class="text-[10px] text-slate-400 mt-0.5">{{ $warranty->warranty_days }} days total</div>
                    </div>

                    <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-100">
                        <div class="text-[10px] font-bold text-slate-400 uppercase">Current Status</div>
                        <div class="mt-1">
                            <span class="inline-block px-2 py-0.5 rounded text-[10px] font-black uppercase {{ $badge['badge_class'] }}">
                                {{ $badge['label'] }}
                            </span>
                        </div>
                        <div class="text-[10px] text-slate-400 mt-0.5">{{ $badge['message'] }}</div>
                    </div>

                    <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-100">
                        <div class="text-[10px] font-bold text-slate-400 uppercase">Registered Owner</div>
                        <div class="font-bold text-slate-900 mt-1">{{ $warranty->customer_name }}</div>
                        <div class="text-[10px] text-slate-400 font-mono mt-0.5">{{ substr($warranty->customer_phone, 0, 5) }}****{{ substr($warranty->customer_phone, -2) }}</div>
                    </div>

                    <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-100">
                        <div class="text-[10px] font-bold text-slate-400 uppercase">Product SKU</div>
                        <div class="font-bold text-slate-900 font-mono mt-1">{{ $warranty->product->sku }}</div>
                        <div class="text-[10px] text-emerald-600 mt-0.5 font-bold">DREAMERS PCB Genuine</div>
                    </div>
                </div>

                <!-- Hardware Product Card -->
                <div class="flex items-center gap-4 p-4 rounded-2xl border border-slate-200">
                    <img src="{{ $warranty->product->thumbnail }}" alt="{{ $warranty->product->name }}" class="w-16 h-16 object-cover rounded-xl border border-slate-200 flex-shrink-0">
                    <div class="flex-1 min-w-0">
                        <h4 class="font-bold text-sm text-slate-900 truncate">{{ $warranty->product->name }}</h4>
                        <p class="text-xs text-slate-500 line-clamp-1 mt-0.5">{{ $warranty->product->short_description ?: 'Official Hardware Component with manufacturer warranty guarantee.' }}</p>
                    </div>
                    <a href="{{ route('product.show', $warranty->product->slug) }}" class="px-4 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs whitespace-nowrap transition">
                        View Product
                    </a>
                </div>

            </div>

        </div>
        @else
        <!-- No Match Found -->
        <div class="bg-white rounded-3xl p-10 border border-slate-200 text-center space-y-4 shadow-sm">
            <div class="w-14 h-14 rounded-2xl bg-rose-50 text-rose-500 flex items-center justify-center mx-auto">
                <i data-lucide="shield-alert" class="w-7 h-7"></i>
            </div>
            <div class="space-y-1">
                <h3 class="text-base font-black text-slate-900">Warranty Record Not Found</h3>
                <p class="text-xs text-slate-500 max-w-sm mx-auto">
                    We could not locate any warranty certificate matching <strong>{{ $code }}</strong>. Please check your product packaging, serial number, or invoice receipt.
                </p>
            </div>
            <div class="pt-2">
                <a href="{{ route('warranty.verify') }}" class="px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs">
                    Try Another Code
                </a>
            </div>
        </div>
        @endif
    @endif

</div>
@endsection
