@extends('layouts.app')

@section('title', 'DREAMERS PCB | Bangladesh\'s #1 Electronics & PCB Marketplace')

@section('content')
<div class="space-y-8 pb-12">

    <!-- 1. HERO SECTION: Mega Category Left Sidebar + Main Slider -->
    <div class="max-w-7xl mx-auto px-4 pt-4">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-4 items-stretch">
            
            <!-- Left 3-Tier Mega Category Menu (Daraz Style) -->
            <div class="hidden lg:block lg:col-span-3 bg-white rounded-2xl border border-slate-200 shadow-sm p-3 relative group/mega">
                <div class="font-black text-xs uppercase tracking-wider text-slate-900 px-3 py-2 border-b border-slate-100 flex items-center gap-2">
                    <i data-lucide="menu" class="w-4 h-4 text-daraz-orange"></i>
                    <span>Categories Directory</span>
                </div>

                <div class="divide-y divide-slate-50 py-1 text-xs">
                    @foreach($categories->take(9) as $cat)
                    <div class="group/item relative">
                        <a href="{{ route('shop.index', ['category_id' => $cat->id]) }}" class="px-3 py-2.5 rounded-xl flex items-center justify-between text-slate-700 hover:text-daraz-orange hover:bg-slate-50 transition">
                            <span class="font-medium truncate">{{ $cat->name }}</span>
                            <i data-lucide="chevron-right" class="w-3.5 h-3.5 text-slate-400 group-hover/item:text-daraz-orange group-hover/item:translate-x-0.5 transition"></i>
                        </a>

                        <!-- Flyout Subcategory Popup -->
                        @if($cat->subCategories->count() > 0)
                        <div class="absolute left-full top-0 ml-2 w-72 bg-white rounded-2xl border border-slate-200 mega-menu-shadow p-4 hidden group-hover/item:block z-50">
                            <h4 class="font-bold text-xs text-slate-900 border-b pb-2 mb-2 text-daraz-orange">{{ $cat->name }} Sub-Categories</h4>
                            <div class="space-y-1.5">
                                @foreach($cat->subCategories as $sub)
                                <div>
                                    <a href="{{ route('shop.index', ['sub_category_id' => $sub->id]) }}" class="font-semibold text-xs text-slate-800 hover:text-daraz-orange flex items-center gap-1.5">
                                        <span class="w-1.5 h-1.5 rounded-full bg-daraz-orange"></span>
                                        <span>{{ $sub->name }}</span>
                                    </a>
                                    @if($sub->childCategories->count() > 0)
                                    <div class="pl-3.5 py-1 flex flex-wrap gap-1">
                                        @foreach($sub->childCategories as $child)
                                        <a href="{{ route('shop.index', ['search' => $child->name]) }}" class="text-[10px] text-slate-500 hover:text-slate-900 bg-slate-100 hover:bg-slate-200 px-1.5 py-0.5 rounded">
                                            {{ $child->name }}
                                        </a>
                                        @endforeach
                                    </div>
                                    @endif
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Right Hero Main Slider -->
            <div class="lg:col-span-9 space-y-4">
                <div x-data="{ activeSlide: 0, slidesCount: {{ count($heroBanners) }} }" x-init="setInterval(() => { activeSlide = (activeSlide + 1) % slidesCount }, 5000)" class="relative aspect-[21/9] rounded-2xl overflow-hidden shadow-sm border border-slate-200 bg-slate-900 group">
                    
                    @foreach($heroBanners as $idx => $banner)
                    <div 
                        x-show="activeSlide === {{ $idx }}" 
                        x-transition:enter="transition ease-out duration-700"
                        x-transition:enter-start="opacity-0 scale-95"
                        x-transition:enter-end="opacity-100 scale-100"
                        x-transition:leave="transition ease-in duration-500"
                        x-transition:leave-start="opacity-100 scale-100"
                        x-transition:leave-end="opacity-0 scale-105"
                        class="absolute inset-0">
                        <img src="{{ $banner->image }}" alt="{{ $banner->title }}" class="w-full h-full object-cover">
                        <div class="absolute inset-0 bg-gradient-to-r from-slate-950/90 via-slate-950/40 to-transparent"></div>
                        
                        <div class="absolute inset-y-0 left-0 p-6 sm:p-10 flex flex-col justify-center max-w-lg text-white space-y-3">
                            <span class="inline-block px-3 py-1 rounded-full bg-daraz-orange text-white text-[10px] font-black uppercase tracking-wider w-max shadow-sm">
                                Verified Electronic Component
                            </span>
                            <h2 class="text-xl sm:text-3xl font-black leading-tight tracking-tight drop-shadow-sm">
                                {{ $banner->title }}
                            </h2>
                            <p class="text-xs text-slate-300 hidden sm:block">
                                {{ $banner->subtitle ?? 'Genuine original microcontrollers, high-power rework stations, and sensor modules at wholesale factory rates.' }}
                            </p>
                            <div class="pt-2">
                                <a href="{{ $banner->link_url ?? route('shop.index') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-gradient-to-r from-daraz-orange to-amber-500 hover:from-daraz-orangeHover hover:to-amber-600 text-white font-extrabold text-xs uppercase tracking-wider shadow-lg shadow-daraz-orange/30 transition transform active:scale-95">
                                    <span>Explore Collection</span>
                                    <i data-lucide="arrow-right" class="w-4 h-4"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                    @endforeach

                    <!-- Slider Nav Dots -->
                    <div class="absolute bottom-3 right-4 flex items-center gap-1.5 z-10">
                        @foreach($heroBanners as $idx => $banner)
                        <button @click="activeSlide = {{ $idx }}" class="h-2 rounded-full transition-all duration-300" :class="activeSlide === {{ $idx }} ? 'w-6 bg-daraz-orange' : 'w-2 bg-white/60'"></button>
                        @endforeach
                    </div>
                </div>

                <!-- Promo Mini Strip -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <a href="{{ route('shop.index', ['search' => 'ESP32']) }}" class="p-3.5 rounded-2xl bg-gradient-to-r from-sky-900 to-slate-900 text-white border border-sky-800/40 flex items-center justify-between hover:scale-[1.01] transition shadow-sm">
                        <div class="space-y-0.5">
                            <span class="text-[10px] uppercase font-bold text-sky-300">IoT Dev Boards</span>
                            <h4 class="font-bold text-xs">ESP32-S3 AI Vision Modules</h4>
                            <span class="text-[11px] text-emerald-400 font-bold">From ৳650 Only</span>
                        </div>
                        <i data-lucide="cpu" class="w-8 h-8 text-sky-400 opacity-80"></i>
                    </a>

                    <a href="{{ route('shop.index', ['search' => 'Quick']) }}" class="p-3.5 rounded-2xl bg-gradient-to-r from-rose-950 to-slate-900 text-white border border-rose-900/40 flex items-center justify-between hover:scale-[1.01] transition shadow-sm">
                        <div class="space-y-0.5">
                            <span class="text-[10px] uppercase font-bold text-rose-300">Soldering Equipment</span>
                            <h4 class="font-bold text-xs">Quick 861DW 1000W Rework</h4>
                            <span class="text-[11px] text-amber-400 font-bold">Official 1-Year Warranty</span>
                        </div>
                        <i data-lucide="flame" class="w-8 h-8 text-rose-400 opacity-80"></i>
                    </a>
                </div>
            </div>

        </div>
    </div>

    <!-- 2. DARAZ FLASH SALE SECTION (Live Countdown Timer + Discount Badges) -->
    <section class="max-w-7xl mx-auto px-4">
        <div class="bg-white rounded-3xl p-5 sm:p-6 border border-slate-200 shadow-sm space-y-4">
            
            <!-- Flash Sale Header Bar -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-100 pb-4">
                <div class="flex items-center gap-4 flex-wrap">
                    <div class="flex items-center gap-2">
                        <span class="w-7 h-7 rounded-lg bg-daraz-orange text-white flex items-center justify-center font-bold">
                            <i data-lucide="zap" class="w-4 h-4 fill-current"></i>
                        </span>
                        <h3 class="text-base sm:text-lg font-black text-slate-900 uppercase tracking-tight">Flash Sale</h3>
                    </div>

                    <!-- Live Dynamic Countdown Timer -->
                    <div x-data="{ hours: 4, minutes: 28, seconds: 45 }" x-init="setInterval(() => { if (seconds > 0) seconds--; else { seconds = 59; if (minutes > 0) minutes--; else { minutes = 59; if (hours > 0) hours--; } } }, 1000)" class="flex items-center gap-1 text-xs font-mono font-bold text-slate-800">
                        <span class="text-slate-400 text-[11px] font-sans mr-1">Ending in:</span>
                        <span class="px-2 py-1 rounded-md bg-slate-900 text-white code-font text-xs" x-text="String(hours).padStart(2, '0')">04</span>
                        <span>:</span>
                        <span class="px-2 py-1 rounded-md bg-slate-900 text-white code-font text-xs" x-text="String(minutes).padStart(2, '0')">28</span>
                        <span>:</span>
                        <span class="px-2 py-1 rounded-md bg-daraz-orange text-white code-font text-xs" x-text="String(seconds).padStart(2, '0')">45</span>
                    </div>
                </div>

                <a href="{{ route('shop.index', ['sort' => 'featured']) }}" class="text-xs font-bold text-daraz-orange hover:text-daraz-orangeHover flex items-center gap-1">
                    <span>SHOP ALL FLASH DEALS</span>
                    <i data-lucide="chevron-right" class="w-4 h-4"></i>
                </a>
            </div>

            <!-- Flash Sale Products Grid -->
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-3 sm:gap-4">
                @foreach($flashSaleProducts as $prod)
                <div class="bg-white rounded-2xl border border-slate-100 hover:border-daraz-orange/40 daraz-shadow p-2.5 flex flex-col justify-between group transition">
                    <div>
                        <!-- Thumbnail + Discount Badge -->
                        <div class="relative aspect-square rounded-xl overflow-hidden bg-slate-50 mb-2">
                            <a href="{{ route('product.show', $prod->slug) }}">
                                <img src="{{ $prod->thumbnail }}" alt="{{ $prod->name }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                            </a>
                            
                            @if($prod->discount_percentage > 0)
                            <div class="absolute top-2 left-2 px-1.5 py-0.5 rounded-md bg-daraz-orange text-white text-[9px] font-black uppercase shadow-sm">
                                -{{ $prod->discount_percentage }}%
                            </div>
                            @endif

                            @if($prod->pcb_model)
                            <div class="absolute bottom-1.5 left-1.5 right-1.5 px-1.5 py-0.5 rounded bg-slate-950/70 backdrop-blur-sm text-[9px] font-mono text-emerald-300 truncate">
                                {{ $prod->pcb_model }}
                            </div>
                            @endif
                        </div>

                        <!-- Product Title -->
                        <h4 class="text-xs font-semibold text-slate-800 line-clamp-2 group-hover:text-daraz-orange transition">
                            <a href="{{ route('product.show', $prod->slug) }}">
                                {{ $prod->name }}
                            </a>
                        </h4>

                        <!-- Price Tag -->
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

                    <!-- Instant Quick Add Button -->
                    <div class="pt-2">
                        <button 
                            @click="addToCart({{ $prod->id }})" 
                            class="w-full py-1.5 rounded-xl bg-slate-900 hover:bg-daraz-orange text-white font-bold text-[11px] flex items-center justify-center gap-1 transition shadow-sm">
                            <i data-lucide="shopping-cart" class="w-3.5 h-3.5"></i>
                            <span>Add</span>
                        </button>
                    </div>
                </div>
                @endforeach
            </div>

        </div>
    </section>

    <!-- 3. DARAZMALL / OFFICIAL PCB BRANDS SECTION -->
    <section class="max-w-7xl mx-auto px-4">
        <div class="bg-white rounded-3xl p-5 sm:p-6 border border-slate-200 shadow-sm space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <div class="flex items-center gap-2">
                    <span class="w-7 h-7 rounded-lg bg-emerald-600 text-white flex items-center justify-center font-bold">
                        <i data-lucide="badge-check" class="w-4 h-4"></i>
                    </span>
                    <h3 class="text-base font-black text-slate-900 uppercase tracking-tight">Official PCB Brands & IC Vendors</h3>
                </div>
                <span class="text-xs text-slate-400 hidden sm:inline">100% Authentic Factory Sealed</span>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-8 gap-3">
                @foreach($brands as $brand)
                <a href="{{ route('shop.index', ['brand_id' => $brand->id]) }}" class="p-3 rounded-2xl bg-slate-50 hover:bg-emerald-50/50 border border-slate-200/80 text-center flex flex-col items-center justify-center space-y-1.5 group transition">
                    <div class="w-12 h-12 rounded-xl bg-white shadow-sm border flex items-center justify-center font-black text-slate-800 text-sm group-hover:scale-105 transition">
                        {{ substr($brand->name, 0, 2) }}
                    </div>
                    <span class="text-xs font-bold text-slate-800 group-hover:text-emerald-700 transition truncate max-w-full">{{ $brand->name }}</span>
                    <span class="text-[10px] text-slate-400 font-mono">{{ $brand->products_count }} Prods</span>
                </a>
                @endforeach
            </div>
        </div>
    </section>

    <!-- 4. TOP CATEGORIES BROWSE GRID -->
    <section class="max-w-7xl mx-auto px-4">
        <div class="bg-white rounded-3xl p-5 sm:p-6 border border-slate-200 shadow-sm space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h3 class="text-base font-black text-slate-900 uppercase tracking-tight">Browse Electronic Categories</h3>
                <a href="{{ route('shop.index') }}" class="text-xs font-bold text-daraz-orange hover:underline">All Categories &rarr;</a>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-8 gap-3">
                @foreach($featuredCategories as $cat)
                <a href="{{ route('shop.index', ['category_id' => $cat->id]) }}" class="p-3 rounded-2xl bg-slate-50 hover:bg-slate-100 border border-slate-200/60 text-center flex flex-col items-center justify-center space-y-2 group transition">
                    <div class="w-12 h-12 rounded-full bg-slate-200 text-slate-700 flex items-center justify-center group-hover:bg-daraz-orange group-hover:text-white transition">
                        <i data-lucide="{{ $cat->icon ?? 'cpu' }}" class="w-5 h-5"></i>
                    </div>
                    <span class="text-xs font-bold text-slate-800 group-hover:text-daraz-orange transition line-clamp-1">{{ $cat->name }}</span>
                </a>
                @endforeach
            </div>
        </div>
    </section>

    <!-- 5. "JUST FOR YOU" RECOMMENDED PCB COMPONENTS (Infinite/Responsive Grid) -->
    <section class="max-w-7xl mx-auto px-4">
        <div class="space-y-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-6 bg-daraz-orange rounded-full"></span>
                    <h3 class="text-base sm:text-lg font-black text-slate-900 uppercase tracking-tight">Just For You</h3>
                </div>
                <span class="text-xs text-slate-500 font-medium">Curated Electronics & Hardware Essentials</span>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-3 sm:gap-4">
                @foreach($justForYouProducts as $prod)
                <div class="bg-white rounded-2xl border border-slate-200 daraz-shadow p-3 flex flex-col justify-between group transition hover:-translate-y-0.5">
                    <div>
                        <!-- Thumbnail -->
                        <div class="relative aspect-square rounded-xl overflow-hidden bg-slate-50 mb-2">
                            <a href="{{ route('product.show', $prod->slug) }}">
                                <img src="{{ $prod->thumbnail }}" alt="{{ $prod->name }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                            </a>
                            
                            @if($prod->discount_percentage > 0)
                            <div class="absolute top-2 left-2 px-1.5 py-0.5 rounded bg-rose-600 text-white text-[9px] font-black">
                                -{{ $prod->discount_percentage }}%
                            </div>
                            @endif

                            @if($prod->is_featured)
                            <div class="absolute top-2 right-2 px-1.5 py-0.5 rounded bg-amber-500 text-slate-950 text-[9px] font-black">
                                ★ Featured
                            </div>
                            @endif
                        </div>

                        <!-- Category & Title -->
                        <div class="text-[10px] text-slate-400 font-semibold uppercase">{{ $prod->category->name ?? 'Hardware' }}</div>
                        <h4 class="text-xs font-semibold text-slate-900 line-clamp-2 group-hover:text-daraz-orange transition mt-0.5">
                            <a href="{{ route('product.show', $prod->slug) }}">
                                {{ $prod->name }}
                            </a>
                        </h4>

                        <!-- PCB Spec Specs Pill -->
                        @if($prod->voltage || $prod->chipset)
                        <div class="mt-1 flex items-center gap-1 text-[9px] text-slate-500 font-mono">
                            <span class="truncate">{{ $prod->chipset ?? $prod->voltage }}</span>
                        </div>
                        @endif

                        <!-- Ratings Stars (Social Proof) -->
                        <div class="flex items-center gap-1 mt-1.5 text-[10px] text-amber-500 font-bold">
                            <span>★★★★★</span>
                            <span class="text-slate-400 text-[9px]">({{ rand(5, 84) }})</span>
                        </div>

                        <!-- Price Tag -->
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

                    <!-- Direct Fast Action Button -->
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

            <!-- Pagination Links -->
            <div class="pt-4 flex justify-center">
                {{ $justForYouProducts->links() }}
            </div>
        </div>
    </section>

</div>
@endsection
