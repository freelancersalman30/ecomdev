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
                <div x-data="{ activeSlide: 0, slidesCount: {{ count($heroBanners) }} }" x-init="setInterval(() => { if (slidesCount > 1) { activeSlide = (activeSlide + 1) % slidesCount } }, {{ (int)\App\Models\Setting::get('slider_autoplay_interval', '5000') }})" class="relative aspect-[21/9] rounded-2xl overflow-hidden shadow-sm border border-slate-200 bg-slate-900 group">
                    
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
                                {{ $banner->badge ?? 'Verified Electronic Component' }}
                            </span>
                            <h2 class="text-xl sm:text-3xl font-black leading-tight tracking-tight drop-shadow-sm text-white !text-white">
                                {{ $banner->title }}
                            </h2>
                            <p class="text-xs text-slate-200 hidden sm:block">
                                {{ $banner->subtitle ?? 'Genuine original microcontrollers, high-power rework stations, and sensor modules at wholesale factory rates.' }}
                            </p>
                            <div class="pt-2">
                                <a href="{{ $banner->link_url ?? route('shop.index') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-gradient-to-r from-daraz-orange to-amber-500 hover:from-daraz-orangeHover hover:to-amber-600 text-white font-extrabold text-xs uppercase tracking-wider shadow-lg shadow-daraz-orange/30 transition transform active:scale-95">
                                    <span>{{ $banner->button_text ?? 'Explore Collection' }}</span>
                                    <i data-lucide="arrow-right" class="w-4 h-4"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                    @endforeach

                    <!-- Slider Nav Dots -->
                    @if(count($heroBanners) > 1)
                    <div class="absolute bottom-3 right-4 flex items-center gap-1.5 z-10">
                        @foreach($heroBanners as $idx => $banner)
                        <button @click="activeSlide = {{ $idx }}" class="h-2 rounded-full transition-all duration-300" :class="activeSlide === {{ $idx }} ? 'w-6 bg-daraz-orange' : 'w-2 bg-white/60'"></button>
                        @endforeach
                    </div>
                    @endif
                </div>

                <!-- Promo Mini Strip -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <a href="{{ \App\Models\Setting::get('promo_strip_1_link', route('shop.index', ['search' => 'ESP32'])) }}" class="p-3.5 rounded-2xl bg-gradient-to-r from-sky-900 to-slate-900 text-white border border-sky-800/40 flex items-center justify-between hover:scale-[1.01] transition shadow-sm">
                        <div class="space-y-0.5">
                            <span class="text-[10px] uppercase font-bold text-sky-300">{{ \App\Models\Setting::get('promo_strip_1_tag', 'IoT Dev Boards') }}</span>
                            <h4 class="font-bold text-xs text-white">{{ \App\Models\Setting::get('promo_strip_1_title', 'ESP32-S3 AI Vision Modules') }}</h4>
                            <span class="text-[11px] text-emerald-400 font-bold">{{ \App\Models\Setting::get('promo_strip_1_offer', 'From ৳650 Only') }}</span>
                        </div>
                        <i data-lucide="cpu" class="w-8 h-8 text-sky-400 opacity-80"></i>
                    </a>

                    <a href="{{ \App\Models\Setting::get('promo_strip_2_link', route('shop.index', ['search' => 'Quick'])) }}" class="p-3.5 rounded-2xl bg-gradient-to-r from-rose-950 to-slate-900 text-white border border-rose-900/40 flex items-center justify-between hover:scale-[1.01] transition shadow-sm">
                        <div class="space-y-0.5">
                            <span class="text-[10px] uppercase font-bold text-rose-300">{{ \App\Models\Setting::get('promo_strip_2_tag', 'Soldering Equipment') }}</span>
                            <h4 class="font-bold text-xs text-white">{{ \App\Models\Setting::get('promo_strip_2_title', 'Quick 861DW 1000W Rework') }}</h4>
                            <span class="text-[11px] text-amber-400 font-bold">{{ \App\Models\Setting::get('promo_strip_2_offer', 'Official 1-Year Warranty') }}</span>
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

            <!-- Flash Sale Auto-Sliding Product Carousel -->
            <div x-data="productCarousel({ interval: 3200 })" 
                 @mouseenter="pause()" 
                 @mouseleave="resume()" 
                 class="relative group/carousel">

                <!-- Left Nav Arrow Button -->
                <button 
                    type="button" 
                    @click="prev()" 
                    class="absolute -left-3 sm:-left-4 top-1/2 -translate-y-1/2 z-20 w-9 h-9 sm:w-10 sm:h-10 rounded-full bg-white/95 dark:bg-slate-900/95 border border-slate-200 dark:border-slate-700 shadow-xl flex items-center justify-center text-slate-800 dark:text-white hover:bg-daraz-orange hover:text-white transition opacity-0 group-hover/carousel:opacity-100 focus:opacity-100"
                    aria-label="Previous Products">
                    <i data-lucide="chevron-left" class="w-5 h-5"></i>
                </button>

                <!-- Carousel Track (Auto-Slides One-by-One) -->
                <div x-ref="track" class="flex items-stretch gap-3 sm:gap-4 overflow-x-auto no-scrollbar scroll-smooth py-2 px-1">
                    @foreach($flashSaleProducts as $prod)
                    <div class="w-[165px] sm:w-[190px] md:w-[205px] flex-shrink-0 bg-white rounded-2xl border border-slate-100 hover:border-daraz-orange/40 daraz-shadow p-2.5 flex flex-col justify-between group transition duration-300">
                        <div>
                            <!-- Thumbnail + Discount Badge -->
                            <div class="relative aspect-square rounded-xl overflow-hidden bg-slate-50 mb-2">
                                <a href="{{ route('product.show', $prod->slug) }}">
                                    <img src="{{ $prod->thumbnail }}" alt="{{ $prod->name }}" loading="lazy" onerror="this.onerror=null;this.src='https://images.unsplash.com/photo-1518770660439-4636190af475?w=600&auto=format&fit=crop&q=80';" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                                </a>
                                
                                @if($prod->discount_percentage > 0)
                                <div class="absolute top-2 left-2 px-1.5 py-0.5 rounded-md bg-rose-600 text-white text-[9px] font-black uppercase shadow-sm">
                                    -{{ $prod->discount_percentage }}% OFF
                                </div>
                                @endif

                                @if($prod->pcb_model)
                                <div class="absolute bottom-1.5 left-1.5 right-1.5 px-1.5 py-0.5 rounded bg-slate-950/70 backdrop-blur-sm text-[9px] font-mono text-emerald-300 truncate">
                                    {{ $prod->pcb_model }}
                                </div>
                                @endif
                            </div>

                            <!-- Product Title -->
                            <h4 class="text-xs font-semibold text-slate-800 line-clamp-2 group-hover:text-daraz-orange transition min-h-[2rem]">
                                <a href="{{ route('product.show', $prod->slug) }}">
                                    {{ $prod->name }}
                                </a>
                            </h4>

                            <!-- Price Tag -->
                            <div class="mt-2 space-y-0.5">
                                <div class="flex items-baseline gap-1.5 flex-wrap">
                                    <span class="text-sm font-black text-daraz-orange code-font">
                                        ৳{{ number_format($prod->effective_price, 2) }}
                                    </span>
                                    @if($prod->discount_percentage > 0)
                                    <span class="text-[11px] text-slate-400 line-through code-font">
                                        ৳{{ number_format($prod->selling_price, 2) }}
                                    </span>
                                    <span class="text-[9px] font-extrabold text-rose-600 bg-rose-50 border border-rose-200/60 px-1 py-0.5 rounded font-sans leading-none">
                                        -{{ $prod->discount_percentage }}%
                                    </span>
                                    @elseif($prod->discount_price && $prod->discount_price < $prod->selling_price)
                                    <span class="text-[11px] text-slate-400 line-through code-font">
                                        ৳{{ number_format($prod->selling_price, 2) }}
                                    </span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Instant Quick Add Button -->
                        <div class="pt-2">
                            <button 
                                @click="addToCart({{ $prod->id }})" 
                                class="w-full py-1.5 rounded-xl bg-slate-900 hover:bg-daraz-orange text-white font-bold text-[11px] flex items-center justify-center gap-1 transition shadow-sm active:scale-95">
                                <i data-lucide="shopping-cart" class="w-3.5 h-3.5"></i>
                                <span>Add</span>
                            </button>
                        </div>
                    </div>
                    @endforeach
                </div>

                <!-- Right Nav Arrow Button -->
                <button 
                    type="button" 
                    @click="next()" 
                    class="absolute -right-3 sm:-right-4 top-1/2 -translate-y-1/2 z-20 w-9 h-9 sm:w-10 sm:h-10 rounded-full bg-white/95 dark:bg-slate-900/95 border border-slate-200 dark:border-slate-700 shadow-xl flex items-center justify-center text-slate-800 dark:text-white hover:bg-daraz-orange hover:text-white transition opacity-0 group-hover/carousel:opacity-100 focus:opacity-100"
                    aria-label="Next Products">
                    <i data-lucide="chevron-right" class="w-5 h-5"></i>
                </button>
            </div>

        </div>
    </section>



    <!-- 5. CATEGORY-WISE PRODUCTS SHOWCASE SECTION -->
    <div class="space-y-10">

        <!-- Each Category's Dedicated Product Showcase Block -->
        @foreach($categoriesWithProducts as $catGroup)
        <section id="cat-section-{{ $catGroup->id }}" class="max-w-7xl mx-auto px-4">
            
            <div class="bg-white rounded-3xl p-5 sm:p-6 border border-slate-200 daraz-shadow space-y-5">
                
                <!-- Category Section Header -->
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-100 pb-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-gradient-to-br from-daraz-orange to-amber-500 text-white flex items-center justify-center shadow-md shadow-daraz-orange/20 shrink-0">
                            <i data-lucide="{{ $catGroup->icon === 'tool' ? 'wrench' : ($catGroup->icon ?? 'cpu') }}" class="w-5 h-5"></i>
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <h3 class="text-base sm:text-lg font-black text-slate-900 tracking-tight">
                                    {{ $catGroup->name }}
                                </h3>
                                <span class="px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-700 text-[10px] font-black uppercase">
                                    {{ $catGroup->products_count }} Products
                                </span>
                            </div>
                            <p class="text-xs text-slate-400 line-clamp-1">
                                {{ $catGroup->description ?? 'Original electronic components, dev boards, sensors and repair tools.' }}
                            </p>
                        </div>
                    </div>

                    <!-- Subcategories pills & View All link -->
                    <div class="flex items-center gap-2 flex-wrap sm:flex-nowrap">
                        @if($catGroup->subCategories && $catGroup->subCategories->isNotEmpty())
                        <div class="hidden md:flex items-center gap-1.5 text-xs text-slate-500">
                            @foreach($catGroup->subCategories->take(3) as $sub)
                            <a href="{{ route('shop.index', ['category_id' => $catGroup->id, 'sub_category_id' => $sub->id]) }}" class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-[11px] font-medium transition">
                                {{ $sub->name }}
                            </a>
                            @endforeach
                        </div>
                        @endif
                        
                        <a href="{{ route('shop.index', ['category_id' => $catGroup->id]) }}" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl bg-slate-100 hover:bg-daraz-orange hover:text-white text-slate-700 text-xs font-bold transition group shrink-0">
                            <span>Explore {{ $catGroup->name }}</span>
                            <i data-lucide="arrow-right" class="w-3.5 h-3.5 group-hover:translate-x-0.5 transition"></i>
                        </a>
                    </div>
                </div>

                <!-- Category Products Auto-Sliding Carousel -->
                <div x-data="productCarousel({ interval: 3600 })" 
                     @mouseenter="pause()" 
                     @mouseleave="resume()" 
                     class="relative group/carousel">

                    <!-- Left Nav Arrow Button -->
                    <button 
                        type="button" 
                        @click="prev()" 
                        class="absolute -left-3 sm:-left-4 top-1/2 -translate-y-1/2 z-20 w-9 h-9 sm:w-10 sm:h-10 rounded-full bg-white/95 dark:bg-slate-900/95 border border-slate-200 dark:border-slate-700 shadow-xl flex items-center justify-center text-slate-800 dark:text-white hover:bg-daraz-orange hover:text-white transition opacity-0 group-hover/carousel:opacity-100 focus:opacity-100"
                        aria-label="Previous Products">
                        <i data-lucide="chevron-left" class="w-5 h-5"></i>
                    </button>

                    <!-- Carousel Track -->
                    <div x-ref="track" class="flex items-stretch gap-3 sm:gap-4 overflow-x-auto no-scrollbar scroll-smooth py-2 px-1">
                        @foreach($catGroup->products as $prod)
                        <div class="w-[170px] sm:w-[195px] md:w-[215px] flex-shrink-0 bg-slate-50/60 hover:bg-white rounded-2xl border border-slate-200/80 hover:border-daraz-orange/40 hover:shadow-lg p-3 flex flex-col justify-between group transition duration-300">
                            <div>
                                <!-- Thumbnail -->
                                <div class="relative aspect-square rounded-xl overflow-hidden bg-white mb-2 shadow-inner">
                                    <a href="{{ route('product.show', $prod->slug) }}">
                                        <img src="{{ $prod->thumbnail }}" alt="{{ $prod->name }}" loading="lazy" onerror="this.onerror=null;this.src='https://images.unsplash.com/photo-1518770660439-4636190af475?w=600&auto=format&fit=crop&q=80';" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                                    </a>
                                    
                                    @if($prod->discount_percentage > 0)
                                    <div class="absolute top-2 left-2 px-1.5 py-0.5 rounded bg-rose-600 text-white text-[9px] font-black uppercase shadow-sm">
                                        -{{ $prod->discount_percentage }}% OFF
                                    </div>
                                    @endif

                                    @if($prod->pcb_model)
                                    <div class="absolute bottom-1.5 left-1.5 right-1.5 px-1.5 py-0.5 rounded bg-slate-950/70 backdrop-blur-sm text-[9px] font-mono text-emerald-300 truncate">
                                        {{ $prod->pcb_model }}
                                    </div>
                                    @endif
                                </div>

                                <!-- Category & Title -->
                                <div class="text-[10px] text-slate-400 font-semibold uppercase">{{ $catGroup->name }}</div>
                                <h4 class="text-xs font-semibold text-slate-900 line-clamp-2 group-hover:text-daraz-orange transition mt-0.5 min-h-[2rem]">
                                    <a href="{{ route('product.show', $prod->slug) }}">
                                        {{ $prod->name }}
                                    </a>
                                </h4>

                                <!-- PCB Spec Chipset Pill -->
                                @if($prod->chipset || $prod->voltage)
                                <div class="mt-1 flex items-center gap-1 text-[9px] text-slate-500 font-mono">
                                    <span class="truncate">{{ $prod->chipset ?? $prod->voltage }}</span>
                                </div>
                                @endif

                                <!-- Ratings Stars -->
                                <div class="flex items-center gap-1 mt-1 text-[10px] text-amber-500 font-bold">
                                    <span>★★★★★</span>
                                    <span class="text-slate-400 text-[9px]">({{ rand(8, 75) }})</span>
                                </div>

                                <!-- Price Tag -->
                                <div class="mt-2 space-y-0.5">
                                    <div class="flex items-baseline gap-1.5 flex-wrap">
                                        <span class="text-sm sm:text-base font-black text-daraz-orange code-font">
                                            ৳{{ number_format($prod->effective_price, 2) }}
                                        </span>
                                        @if($prod->discount_percentage > 0)
                                        <span class="text-[11px] text-slate-400 line-through code-font">
                                            ৳{{ number_format($prod->selling_price, 2) }}
                                        </span>
                                        <span class="text-[9px] font-extrabold text-rose-600 bg-rose-50 border border-rose-200/60 px-1 py-0.5 rounded font-sans leading-none">
                                            -{{ $prod->discount_percentage }}%
                                        </span>
                                        @elseif($prod->discount_price && $prod->discount_price < $prod->selling_price)
                                        <span class="text-[11px] text-slate-400 line-through code-font">
                                            ৳{{ number_format($prod->selling_price, 2) }}
                                        </span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Direct Fast Action Button -->
                            <div class="pt-3">
                                <button 
                                    @click="addToCart({{ $prod->id }})" 
                                    class="w-full py-1.5 rounded-xl bg-slate-900 hover:bg-daraz-orange text-white font-bold text-xs flex items-center justify-center gap-1 transition shadow-sm active:scale-95">
                                    <i data-lucide="shopping-cart" class="w-3.5 h-3.5"></i>
                                    <span>Add to Cart</span>
                                </button>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <!-- Right Nav Arrow Button -->
                    <button 
                        type="button" 
                        @click="next()" 
                        class="absolute -right-3 sm:-right-4 top-1/2 -translate-y-1/2 z-20 w-9 h-9 sm:w-10 sm:h-10 rounded-full bg-white/95 dark:bg-slate-900/95 border border-slate-200 dark:border-slate-700 shadow-xl flex items-center justify-center text-slate-800 dark:text-white hover:bg-daraz-orange hover:text-white transition opacity-0 group-hover/carousel:opacity-100 focus:opacity-100"
                        aria-label="Next Products">
                        <i data-lucide="chevron-right" class="w-5 h-5"></i>
                    </button>
                </div>

            </div>
        </section>
        @endforeach

    </div>

</div>
@endsection
