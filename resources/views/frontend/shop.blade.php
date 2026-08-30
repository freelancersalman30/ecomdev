@extends('layouts.app')

@section('title', 'Shop Catalog | DREAMERS PCB Electronics')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-6 space-y-6">

    <!-- Breadcrumb & Results Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-4 rounded-2xl border border-slate-200 shadow-sm">
        <div class="space-y-1">
            <div class="flex items-center gap-2 text-xs text-slate-500">
                <a href="{{ route('home') }}" class="hover:text-daraz-orange">Home</a>
                <span>/</span>
                <span class="text-slate-900 font-bold">Catalog Shop</span>
                @if($selectedCategory)
                <span>/</span>
                <span class="text-daraz-orange font-bold">{{ $selectedCategory->name }}</span>
                @endif
                @if($selectedBrand)
                <span>/</span>
                <span class="text-emerald-600 font-bold">{{ $selectedBrand->name }}</span>
                @endif
            </div>
            
            <h1 class="text-lg font-black text-slate-900">
                @if($search)
                Search Results for "<span class="text-daraz-orange">{{ $search }}</span>"
                @elseif($selectedCategory)
                {{ $selectedCategory->name }} Components
                @elseif($selectedBrand)
                {{ $selectedBrand->name }} Verified Products
                @else
                All Electronic Components & PCB Hardware
                @endif
            </h1>
            <p class="text-xs text-slate-400">Found {{ $products->total() }} items matching your criteria</p>
        </div>

        <!-- Sort Filter -->
        <form method="GET" action="{{ route('shop.index') }}" class="flex items-center gap-2">
            @if(request('search')) <input type="hidden" name="search" value="{{ request('search') }}"> @endif
            @if(request('category_id')) <input type="hidden" name="category_id" value="{{ request('category_id') }}"> @endif
            @if(request('brand_id')) <input type="hidden" name="brand_id" value="{{ request('brand_id') }}"> @endif

            <label class="text-xs font-semibold text-slate-500 whitespace-nowrap">Sort By:</label>
            <select name="sort" onchange="this.form.submit()" class="px-3.5 py-2 rounded-xl border border-slate-200 bg-slate-50 text-xs font-bold text-slate-800 outline-none focus:ring-2 focus:ring-daraz-orange/20">
                <option value="newest" {{ $sort === 'newest' ? 'selected' : '' }}>Newest Arrivals</option>
                <option value="price_low" {{ $sort === 'price_low' ? 'selected' : '' }}>Price: Low to High</option>
                <option value="price_high" {{ $sort === 'price_high' ? 'selected' : '' }}>Price: High to Low</option>
                <option value="popular" {{ $sort === 'popular' ? 'selected' : '' }}>Most Popular</option>
                <option value="featured" {{ $sort === 'featured' ? 'selected' : '' }}>Featured Deals</option>
            </select>
        </form>
    </div>

    <!-- Quick Category Filter Bar -->
    <div class="flex items-center gap-2 overflow-x-auto no-scrollbar pb-1">
        <a href="{{ route('shop.index', request()->except(['category_id', 'page'])) }}" class="px-3.5 py-2 rounded-xl text-xs font-bold transition whitespace-nowrap {{ !request('category_id') ? 'bg-daraz-orange text-white shadow-sm' : 'bg-white text-slate-700 hover:bg-slate-100 border border-slate-200 shadow-xs' }}">
            All Categories
        </a>
        @foreach($categories as $cat)
        <a href="{{ route('shop.index', array_merge(request()->except(['page']), ['category_id' => $cat->id])) }}" class="px-3.5 py-2 rounded-xl text-xs font-bold transition whitespace-nowrap flex items-center gap-1.5 {{ request('category_id') == $cat->id ? 'bg-daraz-orange text-white shadow-sm' : 'bg-white text-slate-700 hover:bg-slate-100 border border-slate-200 shadow-xs' }}">
            <i data-lucide="{{ $cat->icon === 'tool' ? 'wrench' : ($cat->icon ?? 'cpu') }}" class="w-3.5 h-3.5"></i>
            <span>{{ $cat->name }}</span>
            <span class="text-[10px] {{ request('category_id') == $cat->id ? 'text-white/80' : 'text-slate-400' }}">({{ $cat->products_count }})</span>
        </a>
        @endforeach
    </div>

    <!-- 2 Columns: Left Filter Sidebar + Right Products Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        <!-- Left Filter Sidebar (Daraz Style) -->
        <div class="lg:col-span-3 space-y-4">
            
            <form method="GET" action="{{ route('shop.index') }}" class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm space-y-6">
                @if(request('search')) <input type="hidden" name="search" value="{{ request('search') }}"> @endif
                @if(request('sort')) <input type="hidden" name="sort" value="{{ request('sort') }}"> @endif

                <!-- Filter Action Buttons -->
                <div class="flex items-center justify-between border-b pb-3">
                    <h3 class="text-xs font-black uppercase tracking-wider text-slate-900 flex items-center gap-1.5">
                        <i data-lucide="sliders-horizontal" class="w-4 h-4 text-daraz-orange"></i>
                        <span>Filter Options</span>
                    </h3>
                    <a href="{{ route('shop.index') }}" class="text-[11px] text-rose-500 hover:underline font-semibold">Reset All</a>
                </div>

                <!-- 1. Categories Filter -->
                <div class="space-y-2">
                    <h4 class="text-xs font-bold text-slate-900">Categories</h4>
                    <div class="max-h-48 overflow-y-auto space-y-1.5 text-xs pr-1">
                        @foreach($categories as $cat)
                        <label class="flex items-center justify-between cursor-pointer group">
                            <div class="flex items-center gap-2">
                                <input type="radio" name="category_id" value="{{ $cat->id }}" onchange="this.form.submit()" {{ request('category_id') == $cat->id ? 'checked' : '' }} class="text-daraz-orange focus:ring-daraz-orange">
                                <span class="text-slate-700 group-hover:text-daraz-orange transition {{ request('category_id') == $cat->id ? 'font-bold text-daraz-orange' : '' }}">{{ $cat->name }}</span>
                            </div>
                            <span class="text-[10px] text-slate-400 font-mono">({{ $cat->products_count }})</span>
                        </label>
                        @endforeach
                    </div>
                </div>

                <!-- 2. Brands Filter -->
                <div class="space-y-2 pt-3 border-t border-slate-100">
                    <h4 class="text-xs font-bold text-slate-900">Brands / Vendors</h4>
                    <div class="max-h-48 overflow-y-auto space-y-1.5 text-xs pr-1">
                        @foreach($brands as $brand)
                        <label class="flex items-center justify-between cursor-pointer group">
                            <div class="flex items-center gap-2">
                                <input type="radio" name="brand_id" value="{{ $brand->id }}" onchange="this.form.submit()" {{ request('brand_id') == $brand->id ? 'checked' : '' }} class="text-daraz-orange focus:ring-daraz-orange">
                                <span class="text-slate-700 group-hover:text-daraz-orange transition {{ request('brand_id') == $brand->id ? 'font-bold text-daraz-orange' : '' }}">{{ $brand->name }}</span>
                            </div>
                            <span class="text-[10px] text-slate-400 font-mono">({{ $brand->products_count }})</span>
                        </label>
                        @endforeach
                    </div>
                </div>

                <!-- 3. Price Range Filter -->
                <div class="space-y-2 pt-3 border-t border-slate-100">
                    <h4 class="text-xs font-bold text-slate-900">Price Range (৳)</h4>
                    <div class="grid grid-cols-2 gap-2">
                        <input type="number" name="min_price" value="{{ request('min_price') }}" placeholder="Min" class="w-full px-2.5 py-1.5 rounded-lg border text-xs code-font outline-none">
                        <input type="number" name="max_price" value="{{ request('max_price') }}" placeholder="Max" class="w-full px-2.5 py-1.5 rounded-lg border text-xs code-font outline-none">
                    </div>
                </div>

                <!-- 4. Stock Availability -->
                <div class="pt-3 border-t border-slate-100">
                    <label class="flex items-center gap-2 cursor-pointer text-xs">
                        <input type="checkbox" name="in_stock" value="1" onchange="this.form.submit()" {{ request('in_stock') ? 'checked' : '' }} class="rounded text-daraz-orange focus:ring-daraz-orange">
                        <span class="font-bold text-slate-800">In-Stock Only</span>
                    </label>
                </div>

                <button type="submit" class="w-full py-2 bg-daraz-orange hover:bg-daraz-orangeHover text-white rounded-xl text-xs font-bold shadow-md transition">
                    Apply Filter
                </button>
            </form>

        </div>

        <!-- Right Products Grid -->
        <div class="lg:col-span-9 space-y-6">
            
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                @forelse($products as $prod)
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
                        <h3 class="text-xs font-semibold text-slate-900 line-clamp-2 group-hover:text-daraz-orange transition mt-0.5">
                            <a href="{{ route('product.show', $prod->slug) }}">
                                {{ $prod->name }}
                            </a>
                        </h3>

                        @if($prod->voltage || $prod->chipset)
                        <div class="mt-1 text-[9px] text-slate-500 font-mono truncate">
                            {{ $prod->chipset ?? $prod->voltage }}
                        </div>
                        @endif

                        <div class="flex items-center gap-1 mt-1 text-[10px] text-amber-500 font-bold">
                            <span>★★★★★</span>
                            <span class="text-slate-400 text-[9px]">({{ rand(4, 52) }})</span>
                        </div>

                        <div class="mt-2">
                            <div class="text-sm sm:text-base font-black text-daraz-orange code-font">
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
                @empty
                <div class="col-span-full py-16 text-center text-slate-400 bg-white rounded-3xl border border-slate-200 space-y-3">
                    <i data-lucide="search-x" class="w-12 h-12 mx-auto text-slate-300"></i>
                    <p class="text-sm font-semibold">No products found matching your filter</p>
                    <a href="{{ route('shop.index') }}" class="inline-block px-4 py-2 rounded-xl bg-daraz-orange text-white text-xs font-bold">
                        Clear All Filters
                    </a>
                </div>
                @endforelse
            </div>

            <!-- Pagination -->
            <div class="pt-4 flex justify-center">
                {{ $products->links() }}
            </div>

        </div>

    </div>

</div>
@endsection
