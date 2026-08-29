@extends('layouts.customer')

@section('title', 'My Wishlist & Recommended - DREAMERS PCB')

@section('customer_content')
<div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-sm space-y-6">

    <div class="border-b pb-4">
        <h2 class="text-base font-black text-slate-900 flex items-center gap-2">
            <i data-lucide="heart" class="w-5 h-5 text-rose-500"></i>
            <span>My Wishlist & Recommended Electronic Hardware</span>
        </h2>
        <p class="text-xs text-slate-400">Save items for fast 1-click reordering</p>
    </div>

    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
        @foreach($wishlistProducts as $prod)
        <div class="bg-white rounded-2xl border border-slate-200 daraz-shadow p-3 flex flex-col justify-between group transition hover:-translate-y-0.5">
            <div>
                <div class="relative aspect-square rounded-xl overflow-hidden bg-slate-50 mb-2">
                    <a href="{{ route('product.show', $prod->slug) }}">
                        <img src="{{ $prod->thumbnail }}" alt="{{ $prod->name }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                    </a>
                    
                    @if($prod->discount_percentage > 0)
                    <div class="absolute top-2 left-2 px-1.5 py-0.5 rounded bg-rose-600 text-white text-[9px] font-black">
                        -{{ $prod->discount_percentage }}%
                    </div>
                    @endif
                </div>

                <div class="text-[10px] text-slate-400 font-semibold uppercase">{{ $prod->category->name ?? 'Hardware' }}</div>
                <h4 class="text-xs font-semibold text-slate-900 line-clamp-2 group-hover:text-daraz-orange transition mt-0.5">
                    <a href="{{ route('product.show', $prod->slug) }}">
                        {{ $prod->name }}
                    </a>
                </h4>

                <div class="mt-2">
                    <div class="text-sm font-black text-daraz-orange code-font">
                        ৳{{ number_format($prod->effective_price, 2) }}
                    </div>
                    @if($prod->discount_price)
                    <div class="text-[10px] text-slate-400 line-through code-font">
                        ৳{{ number_format($prod->selling_price, 2) }}
                    </div>
                    @endif
                </div>
            </div>

            <div class="pt-2">
                <button 
                    @click="addToCart({{ $prod->id }})" 
                    class="w-full py-1.5 rounded-xl bg-slate-900 hover:bg-daraz-orange text-white font-bold text-xs flex items-center justify-center gap-1 transition shadow-sm">
                    <i data-lucide="shopping-cart" class="w-3.5 h-3.5"></i>
                    <span>Add to Cart</span>
                </button>
            </div>
        </div>
        @endforeach
    </div>

</div>
@endsection
