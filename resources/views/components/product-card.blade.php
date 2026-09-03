@props([
    'product',
    'layout' => null,
    'class' => ''
])

@php
    $layout = $layout ?? \App\Services\ProductLayoutService::getConfig();
    $style = $layout['product_card_style'] ?? 'modern_daraz';
    $showDiscountBadge = ($layout['show_discount_badge'] ?? '1') === '1';
    $showOldPrice = ($layout['show_old_price'] ?? '1') === '1';
    $showQuickAdd = ($layout['show_quick_add'] ?? '1') === '1';
    $showTechSpecs = ($layout['show_tech_specs'] ?? '1') === '1';
    $showRatings = ($layout['show_ratings'] ?? '1') === '1';
@endphp

@if($style === 'compact_tech')
    <!-- Style 2: Compact Tech Hardware Card -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 hover:border-emerald-500/50 shadow-xs hover:shadow-md p-2.5 flex flex-col justify-between group transition duration-200 {{ $class }}">
        <div>
            <!-- Thumbnail + Badges -->
            <div class="relative aspect-square rounded-xl overflow-hidden bg-slate-950/5 dark:bg-slate-950 mb-2">
                <a href="{{ route('product.show', $product->slug) }}">
                    <img src="{{ $product->thumbnail }}" alt="{{ $product->name }}" loading="lazy" onerror="this.onerror=null;this.src='https://images.unsplash.com/photo-1518770660439-4636190af475?w=600&auto=format&fit=crop&q=80';" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                </a>
                
                @if($showDiscountBadge && $product->discount_percentage > 0)
                <div class="absolute top-1.5 left-1.5 px-1.5 py-0.5 rounded bg-emerald-600 text-white text-[9px] font-black uppercase font-mono shadow-xs">
                    -{{ $product->discount_percentage }}%
                </div>
                @endif

                @if($showTechSpecs && ($product->pcb_model || $product->chipset))
                <div class="absolute bottom-1 left-1 right-1 px-1.5 py-0.5 rounded bg-slate-950/80 backdrop-blur-xs text-[9px] font-mono text-emerald-400 truncate">
                    {{ $product->pcb_model ?: $product->chipset }}
                </div>
                @endif
            </div>

            <!-- Category & Title -->
            <div class="text-[9px] text-emerald-600 dark:text-emerald-400 font-mono uppercase font-bold truncate">{{ $product->category->name ?? 'Hardware' }}</div>
            <h4 class="text-xs font-bold text-slate-900 dark:text-white line-clamp-2 group-hover:text-emerald-600 transition min-h-[2rem]">
                <a href="{{ route('product.show', $product->slug) }}">
                    {{ $product->name }}
                </a>
            </h4>

            @if($showRatings)
            <div class="flex items-center gap-1 mt-1 text-[10px] text-amber-500 font-bold">
                <span>★★★★★</span>
                <span class="text-slate-400 text-[9px]">({{ rand(5, 45) }})</span>
            </div>
            @endif

            <!-- Price -->
            <div class="mt-2 flex items-baseline gap-1.5 flex-wrap">
                <span class="text-sm font-black text-emerald-600 dark:text-emerald-400 code-font">
                    ৳{{ number_format($product->effective_price, 2) }}
                </span>
                @if($showOldPrice && ($product->discount_percentage > 0 || ($product->discount_price && $product->discount_price < $product->selling_price)))
                <span class="text-[10px] text-slate-400 line-through code-font">
                    ৳{{ number_format($product->selling_price, 2) }}
                </span>
                @endif
            </div>
        </div>

        @if($showQuickAdd)
        <div class="pt-2">
            <button 
                @click="addToCart({{ $product->id }})" 
                class="w-full py-1.5 rounded-xl bg-slate-900 hover:bg-emerald-600 text-white font-bold text-[11px] flex items-center justify-center gap-1 transition shadow-xs active:scale-95">
                <i data-lucide="shopping-cart" class="w-3.5 h-3.5"></i>
                <span>Add</span>
            </button>
        </div>
        @endif
    </div>

@elseif($style === 'minimalist_bordered')
    <!-- Style 3: Minimalist Bordered Clean Card -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 hover:border-slate-400 dark:hover:border-slate-600 shadow-xs hover:shadow-md p-3 flex flex-col justify-between group transition duration-200 {{ $class }}">
        <div>
            <!-- Thumbnail -->
            <div class="relative aspect-square rounded-xl overflow-hidden bg-slate-50 dark:bg-slate-800/50 mb-2.5">
                <a href="{{ route('product.show', $product->slug) }}">
                    <img src="{{ $product->thumbnail }}" alt="{{ $product->name }}" loading="lazy" onerror="this.onerror=null;this.src='https://images.unsplash.com/photo-1518770660439-4636190af475?w=600&auto=format&fit=crop&q=80';" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                </a>
                
                @if($showDiscountBadge && $product->discount_percentage > 0)
                <div class="absolute top-2 left-2 px-2 py-0.5 rounded-md bg-slate-900 text-white text-[9px] font-bold">
                    {{ $product->discount_percentage }}% OFF
                </div>
                @endif
            </div>

            <!-- Title -->
            <h4 class="text-xs font-semibold text-slate-900 dark:text-white line-clamp-2 group-hover:text-sky-600 transition min-h-[2rem]">
                <a href="{{ route('product.show', $product->slug) }}">
                    {{ $product->name }}
                </a>
            </h4>

            @if($showTechSpecs && ($product->pcb_model || $product->chipset))
            <div class="mt-1 text-[10px] text-slate-500 font-mono truncate">
                {{ $product->pcb_model ?: $product->chipset }}
            </div>
            @endif

            @if($showRatings)
            <div class="flex items-center gap-1 mt-1 text-[10px] text-amber-500">
                <span>★★★★★</span>
                <span class="text-slate-400 text-[9px]">({{ rand(4, 30) }})</span>
            </div>
            @endif

            <!-- Price -->
            <div class="mt-2.5 flex items-baseline gap-1.5 flex-wrap">
                <span class="text-sm font-extrabold text-slate-900 dark:text-white code-font">
                    ৳{{ number_format($product->effective_price, 2) }}
                </span>
                @if($showOldPrice && ($product->discount_percentage > 0 || ($product->discount_price && $product->discount_price < $product->selling_price)))
                <span class="text-[11px] text-slate-400 line-through code-font">
                    ৳{{ number_format($product->selling_price, 2) }}
                </span>
                @endif
            </div>
        </div>

        @if($showQuickAdd)
        <div class="pt-2.5">
            <button 
                @click="addToCart({{ $product->id }})" 
                class="w-full py-1.5 rounded-xl border border-slate-300 dark:border-slate-700 hover:bg-slate-900 hover:text-white dark:hover:bg-white dark:hover:text-slate-950 text-slate-800 dark:text-slate-200 font-bold text-xs flex items-center justify-center gap-1.5 transition">
                <i data-lucide="plus" class="w-3.5 h-3.5"></i>
                <span>Add to Cart</span>
            </button>
        </div>
        @endif
    </div>

@else
    <!-- Style 1: Modern Daraz Mega Store Card (Default) -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-100 dark:border-slate-800 hover:border-daraz-orange/50 daraz-shadow p-2.5 sm:p-3 flex flex-col justify-between group transition duration-200 {{ $class }}">
        <div>
            <!-- Thumbnail + Discount Badge -->
            <div class="relative aspect-square rounded-xl overflow-hidden bg-slate-50 dark:bg-slate-800/50 mb-2">
                <a href="{{ route('product.show', $product->slug) }}">
                    <img src="{{ $product->thumbnail }}" alt="{{ $product->name }}" loading="lazy" onerror="this.onerror=null;this.src='https://images.unsplash.com/photo-1518770660439-4636190af475?w=600&auto=format&fit=crop&q=80';" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                </a>
                
                @if($showDiscountBadge && $product->discount_percentage > 0)
                <div class="absolute top-2 left-2 px-1.5 py-0.5 rounded-md bg-rose-600 text-white text-[9px] font-black uppercase shadow-xs">
                    -{{ $product->discount_percentage }}%
                </div>
                @endif

                @if($showTechSpecs && $product->pcb_model)
                <div class="absolute bottom-1.5 left-1.5 right-1.5 px-1.5 py-0.5 rounded bg-slate-950/70 backdrop-blur-xs text-[9px] font-mono text-emerald-300 truncate">
                    {{ $product->pcb_model }}
                </div>
                @endif
            </div>

            <!-- Category & Title -->
            <div class="text-[10px] text-slate-400 font-semibold uppercase truncate">{{ $product->category->name ?? 'Hardware' }}</div>
            <h4 class="text-xs font-semibold text-slate-800 dark:text-slate-100 line-clamp-2 group-hover:text-daraz-orange transition mt-0.5 min-h-[2rem]">
                <a href="{{ route('product.show', $product->slug) }}">
                    {{ $product->name }}
                </a>
            </h4>

            @if($showRatings)
            <div class="flex items-center gap-1 mt-1 text-[10px] text-amber-500 font-bold">
                <span>★★★★★</span>
                <span class="text-slate-400 text-[9px]">({{ rand(5, 60) }})</span>
            </div>
            @endif

            <!-- Price Tag -->
            <div class="mt-2 space-y-0.5">
                <div class="flex items-baseline gap-1.5 flex-wrap">
                    <span class="text-sm sm:text-base font-black text-daraz-orange code-font">
                        ৳{{ number_format($product->effective_price, 2) }}
                    </span>
                    @if($showOldPrice && ($product->discount_percentage > 0 || ($product->discount_price && $product->discount_price < $product->selling_price)))
                    <span class="text-[11px] text-slate-400 line-through code-font">
                        ৳{{ number_format($product->selling_price, 2) }}
                    </span>
                    @endif
                </div>
            </div>
        </div>

        @if($showQuickAdd)
        <div class="pt-2">
            <button 
                @click="addToCart({{ $product->id }})" 
                class="w-full py-1.5 rounded-xl bg-slate-900 hover:bg-daraz-orange text-white font-bold text-[11px] sm:text-xs flex items-center justify-center gap-1 transition shadow-xs active:scale-95">
                <i data-lucide="shopping-cart" class="w-3.5 h-3.5"></i>
                <span>Add to Cart</span>
            </button>
        </div>
        @endif
    </div>
@endif
