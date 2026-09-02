@extends('layouts.app')

@section('title', $product->meta_title ?: $product->name . ' - ' . \App\Models\Setting::get('site_name', 'DREAMERS PCB'))
@section('meta_description', $product->meta_description ?: ($product->short_description ?: \Illuminate\Support\Str::limit(strip_tags($product->description), 155)))
@section('meta_keywords', $product->meta_keywords ?: $product->name)

@section('content')
<div x-data="productDetailApp()" class="max-w-7xl mx-auto px-4 py-6 space-y-8">

    <!-- Breadcrumb -->
    <div class="flex items-center gap-2 text-xs text-slate-500">
        <a href="{{ route('home') }}" class="hover:text-daraz-orange">Home</a>
        <span>/</span>
        <a href="{{ route('shop.index') }}" class="hover:text-daraz-orange">Shop</a>
        <span>/</span>
        <a href="{{ route('shop.index', ['category_id' => $product->category_id]) }}" class="hover:text-daraz-orange">{{ $product->category->name ?? 'Category' }}</a>
        <span>/</span>
        <span class="text-slate-900 font-bold truncate max-w-xs">{{ $product->name }}</span>
    </div>

    <!-- Product Main Card (Gallery + Info + Delivery Box) -->
    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-sm grid grid-cols-1 lg:grid-cols-12 gap-8">
        
        <!-- Left: Image Gallery (4 Cols) -->
        <div class="lg:col-span-5 space-y-4">
            <div class="relative aspect-square rounded-2xl overflow-hidden bg-slate-50 border border-slate-200">
                <img :src="activeImage" alt="{{ $product->name }}" class="w-full h-full object-cover">
                
                @if($product->discount_percentage > 0)
                <div class="absolute top-3 left-3 px-2 py-1 rounded-lg bg-daraz-orange text-white text-xs font-black uppercase shadow-md">
                    -{{ $product->discount_percentage }}% OFF
                </div>
                @endif
            </div>

            <!-- Thumbnail Carousel -->
            <div class="flex items-center gap-2 overflow-x-auto pb-1">
                <button @click="activeImage = '{{ $product->thumbnail }}'" class="w-16 h-16 rounded-xl overflow-hidden border-2 flex-shrink-0 transition" :class="activeImage === '{{ $product->thumbnail }}' ? 'border-daraz-orange' : 'border-slate-200 opacity-70'">
                    <img src="{{ $product->thumbnail }}" alt="Main" class="w-full h-full object-cover">
                </button>
                @foreach($product->images as $img)
                <button @click="activeImage = '{{ $img->image_path }}'" class="w-16 h-16 rounded-xl overflow-hidden border-2 flex-shrink-0 transition" :class="activeImage === '{{ $img->image_path }}' ? 'border-daraz-orange' : 'border-slate-200 opacity-70'">
                    <img src="{{ $img->image_path }}" alt="Gallery" class="w-full h-full object-cover">
                </button>
                @endforeach
            </div>
        </div>

        <!-- Middle: Product Information & Purchase Options (4 Cols) -->
        <div class="lg:col-span-4 space-y-4">
            
            <div class="space-y-1">
                <div class="flex items-center gap-2">
                    <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-emerald-100 text-emerald-800">
                        {{ $product->brand->name ?? 'Verified Genuine' }}
                    </span>
                    <span class="text-xs text-slate-400 font-mono">SKU: <span x-text="currentSku"></span></span>
                </div>

                <h1 class="text-lg sm:text-xl font-extrabold text-slate-900 leading-snug">
                    {{ $product->name }}
                </h1>

                <!-- Ratings & Social Proof -->
                <div class="flex items-center gap-2 text-xs pt-1">
                    <div class="flex text-amber-500 font-bold">★★★★★</div>
                    <span class="font-bold text-slate-700">4.9</span>
                    <span class="text-slate-400">({{ rand(12, 98) }} Customer Ratings &bull; {{ rand(40, 250) }} Sold)</span>
                </div>
            </div>

            <!-- Price Box -->
            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-1">
                <div class="flex items-baseline gap-3">
                    <div class="text-2xl sm:text-3xl font-black text-daraz-orange code-font">
                        ৳<span x-text="currentPrice.toFixed(2)"></span>
                    </div>
                    @if($product->discount_price)
                    <div class="text-sm text-slate-400 line-through code-font">
                        ৳{{ number_format($product->selling_price, 2) }}
                    </div>
                    @endif
                </div>
                <div class="text-[11px] text-emerald-600 font-semibold flex items-center gap-1">
                    <i data-lucide="check" class="w-3.5 h-3.5"></i>
                    <span>Cash on Delivery Available Nationwide</span>
                </div>
            </div>

            <!-- Variants Selection (If available) -->
            @if($product->has_variants && $product->variants->count() > 0)
            <div class="space-y-2">
                <label class="block text-xs font-bold text-slate-800">Select Model / Variant:</label>
                <div class="grid grid-cols-2 gap-2">
                    @foreach($product->variants as $variant)
                    <button 
                        type="button"
                        @click="selectVariant({{ $variant->id }}, {{ $variant->selling_price }}, '{{ $variant->sku }}', {{ $variant->stock_quantity }})"
                        class="p-2.5 rounded-xl border text-left text-xs transition"
                        :class="selectedVariantId === {{ $variant->id }} ? 'border-daraz-orange bg-daraz-light font-bold text-daraz-orange' : 'border-slate-200 text-slate-700 hover:border-slate-300'">
                        <div class="truncate">{{ $variant->variant_name }}</div>
                        <div class="text-[11px] font-mono text-slate-500">৳{{ number_format($variant->selling_price, 2) }}</div>
                    </button>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Quantity Selector & Action Buttons -->
            <div class="space-y-3 pt-2">
                <div class="flex items-center gap-3">
                    <span class="text-xs font-bold text-slate-700">Quantity:</span>
                    <div class="flex items-center border border-slate-200 rounded-xl p-1 bg-slate-50">
                        <button type="button" @click="quantity = Math.max(1, quantity - 1)" class="w-7 h-7 rounded-lg flex items-center justify-center font-bold text-sm text-slate-700 hover:bg-slate-200">-</button>
                        <span class="w-10 text-center font-bold text-sm code-font" x-text="quantity"></span>
                        <button type="button" @click="quantity = quantity + 1" class="w-7 h-7 rounded-lg flex items-center justify-center font-bold text-sm text-slate-700 hover:bg-slate-200">+</button>
                    </div>
                    <span class="text-xs text-slate-400 font-medium">(In Stock: <span x-text="currentStock"></span>)</span>
                </div>

                <div class="grid grid-cols-2 gap-3 pt-2">
                    <button 
                        @click="addToCart({{ $product->id }}, selectedVariantId, quantity)" 
                        class="w-full py-3 rounded-2xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs flex items-center justify-center gap-2 shadow-md transition">
                        <i data-lucide="shopping-cart" class="w-4 h-4 text-emerald-400"></i>
                        <span>Add to Cart</span>
                    </button>

                    <button 
                        @click="buyNow({{ $product->id }}, selectedVariantId, quantity)" 
                        class="w-full py-3 rounded-2xl bg-gradient-to-r from-daraz-orange to-amber-500 hover:from-daraz-orangeHover hover:to-amber-600 text-white font-extrabold text-xs flex items-center justify-center gap-2 shadow-lg shadow-daraz-orange/30 transition transform active:scale-95">
                        <i data-lucide="zap" class="w-4 h-4 fill-current"></i>
                        <span>Buy Now</span>
                    </button>
                </div>
            </div>

        </div>

        <!-- Right: Delivery Options & Warranty Box (3 Cols) -->
        <div class="lg:col-span-3 space-y-4">
            <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200 space-y-4 text-xs">
                
                <div class="border-b pb-3 space-y-1">
                    <span class="text-[10px] uppercase font-bold text-slate-400">Delivery Information</span>
                    <div class="flex items-start gap-2 pt-1">
                        <i data-lucide="map-pin" class="w-4 h-4 text-daraz-orange flex-shrink-0 mt-0.5"></i>
                        <div>
                            <p class="font-bold text-slate-900">Dhaka & Nationwide</p>
                            <p class="text-slate-500 text-[11px]">Inside Dhaka: <strong>৳{{ $insideDhaka }}</strong> (1-2 Days)</p>
                            <p class="text-slate-500 text-[11px]">Outside Dhaka: <strong>৳{{ $outsideDhaka }}</strong> (2-4 Days)</p>
                        </div>
                    </div>
                </div>

                <div class="border-b pb-3 space-y-2">
                    <span class="text-[10px] uppercase font-bold text-slate-400">Service & Warranties</span>
                    <div class="flex items-center gap-2">
                        <i data-lucide="shield-check" class="w-4 h-4 text-emerald-600"></i>
                        <span class="font-semibold text-slate-800">{{ $product->warranty ?? '100% Genuine Replacement Warranty' }}</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <i data-lucide="refresh-cw" class="w-4 h-4 text-sky-600"></i>
                        <span class="font-semibold text-slate-800">7 Days Easy Return Policy</span>
                    </div>
                </div>

                <div class="space-y-1.5 pt-1">
                    <span class="text-[10px] uppercase font-bold text-slate-400">Seller Information</span>
                    <p class="font-bold text-slate-900">DREAMERS PCB Official Store</p>
                    <p class="text-slate-500 text-[11px]">Positive Seller Rating: <strong>99.2%</strong></p>
                </div>

            </div>
        </div>

    </div>

    <!-- Technical Specifications & Pinout Documentation Tabs -->
    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-sm space-y-6">
        <h2 class="text-lg font-black text-slate-900 border-b pb-4 flex items-center gap-2">
            <i data-lucide="cpu" class="w-5 h-5 text-daraz-orange"></i>
            <span>Hardware Specifications & Technical Documentation</span>
        </h2>

        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4 text-xs">
            <div class="p-3.5 rounded-xl bg-slate-50 border">
                <span class="text-slate-400">PCB Model / Version:</span>
                <div class="font-bold text-slate-900 font-mono mt-0.5">{{ $product->pcb_model ?? 'Standard Industrial Edition' }}</div>
            </div>
            <div class="p-3.5 rounded-xl bg-slate-50 border">
                <span class="text-slate-400">Operating Voltage:</span>
                <div class="font-bold text-emerald-600 font-mono mt-0.5">{{ $product->voltage ?? '3.3V / 5V DC Tolerant' }}</div>
            </div>
            <div class="p-3.5 rounded-xl bg-slate-50 border">
                <span class="text-slate-400">Main Chipset / IC:</span>
                <div class="font-bold text-slate-900 font-mono mt-0.5">{{ $product->chipset ?? 'Original High-Speed Microchip' }}</div>
            </div>
            <div class="p-3.5 rounded-xl bg-slate-50 border">
                <span class="text-slate-400">Dimensions:</span>
                <div class="font-bold text-slate-900 mt-0.5">{{ $product->dimensions ?? 'Standard PCB Dimensions' }}</div>
            </div>
            <div class="p-3.5 rounded-xl bg-slate-50 border">
                <span class="text-slate-400">Weight:</span>
                <div class="font-bold text-slate-900 mt-0.5">{{ $product->weight ?? '25g' }}</div>
            </div>
            <div class="p-3.5 rounded-xl bg-slate-50 border">
                <span class="text-slate-400">Warranty Coverage:</span>
                <div class="font-bold text-slate-900 mt-0.5">{{ $product->warranty ?? 'Factory Tested' }}</div>
            </div>
        </div>

        @if($product->description)
        <div class="pt-6 border-t border-slate-100 space-y-4">
            <div class="flex items-center justify-between border-b pb-3">
                <h3 class="font-black text-slate-900 text-sm sm:text-base flex items-center gap-2">
                    <i data-lucide="file-text" class="w-5 h-5 text-emerald-500"></i>
                    <span>Detailed Technical Overview & Specifications</span>
                </h3>
                <span class="text-[11px] font-bold text-slate-400 font-mono">Hardware Guide</span>
            </div>

            <div class="gemini-content text-xs sm:text-sm text-slate-700 leading-relaxed">
                {!! \Illuminate\Support\Str::markdown($product->description, [
                    'html_input' => 'allow',
                    'allow_unsafe_links' => false,
                ]) !!}
            </div>
        </div>
        @endif
    </div>

    @push('styles')
    <style>
    .gemini-content h1, .gemini-content h2 {
        font-size: 1.15rem;
        font-weight: 800;
        margin-top: 1.5rem;
        margin-bottom: 0.5rem;
        padding-bottom: 0.4rem;
        border-bottom: 1px solid #e2e8f0;
        color: #0f172a;
    }
    .gemini-content h3 {
        font-size: 0.95rem;
        font-weight: 700;
        margin-top: 1.25rem;
        margin-bottom: 0.35rem;
        color: #047857;
    }
    .gemini-content p {
        margin-bottom: 0.75rem;
        line-height: 1.6;
    }
    .gemini-content ul {
        list-style-type: disc;
        padding-left: 1.25rem;
        margin-bottom: 0.75rem;
    }
    .gemini-content ol {
        list-style-type: decimal;
        padding-left: 1.25rem;
        margin-bottom: 0.75rem;
    }
    .gemini-content li {
        margin-bottom: 0.25rem;
    }
    .gemini-content table {
        width: 100%;
        border-collapse: collapse;
        margin: 1.25rem 0;
        border-radius: 0.75rem;
        overflow: hidden;
        border: 1px solid #e2e8f0;
        font-size: 0.78rem;
    }
    .gemini-content th {
        background: #f8fafc;
        padding: 0.6rem 0.85rem;
        font-weight: 700;
        text-align: left;
        border-bottom: 1px solid #cbd5e1;
        color: #0f172a;
    }
    .gemini-content td {
        padding: 0.6rem 0.85rem;
        border-bottom: 1px solid #f1f5f9;
    }
    .gemini-content tr:last-child td {
        border-bottom: none;
    }
    .gemini-content tr:hover {
        background: #f8fafc;
    }
    .gemini-content blockquote {
        border-left: 4px solid #10b981;
        background: #f0fdf4;
        padding: 0.75rem 1rem;
        border-radius: 0 0.75rem 0.75rem 0;
        margin: 1rem 0;
        font-style: italic;
        font-size: 0.8rem;
        color: #065f46;
    }
    .gemini-content code {
        background: #f1f5f9;
        color: #059669;
        padding: 0.15rem 0.35rem;
        border-radius: 0.25rem;
        font-family: 'JetBrains Mono', monospace;
        font-size: 0.75rem;
    }
    .gemini-content pre {
        background: #0f172a;
        color: #f8fafc;
        padding: 1rem;
        border-radius: 0.75rem;
        overflow-x: auto;
        margin: 1rem 0;
        font-family: 'JetBrains Mono', monospace;
        font-size: 0.75rem;
    }
    .gemini-content strong {
        font-weight: 700;
        color: #0f172a;
    }
    </style>
    @endpush

    <!-- Related Components Auto-Sliding Carousel -->
    @if($relatedProducts->count() > 0)
    <div class="space-y-4">
        <div class="flex items-center justify-between">
            <h3 class="text-base font-black text-slate-900 uppercase tracking-tight flex items-center gap-2">
                <i data-lucide="sparkles" class="w-4 h-4 text-daraz-orange"></i>
                <span>People Also Bought</span>
            </h3>
            <span class="text-xs text-slate-400 font-medium">Auto-sliding recommendations</span>
        </div>

        <div x-data="productCarousel({ interval: 3400 })" 
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
                @foreach($relatedProducts as $rel)
                <div class="w-[165px] sm:w-[190px] md:w-[205px] flex-shrink-0 bg-white rounded-2xl border border-slate-200 daraz-shadow p-2.5 flex flex-col justify-between group transition hover:-translate-y-0.5">
                    <div>
                        <div class="aspect-square rounded-xl overflow-hidden bg-slate-50 mb-2">
                            <a href="{{ route('product.show', $rel->slug) }}">
                                <img src="{{ $rel->thumbnail }}" alt="{{ $rel->name }}" loading="lazy" onerror="this.onerror=null;this.src='https://images.unsplash.com/photo-1518770660439-4636190af475?w=600&auto=format&fit=crop&q=80';" class="w-full h-full object-cover group-hover:scale-105 transition">
                            </a>
                        </div>
                        <h4 class="text-xs font-semibold text-slate-800 line-clamp-2 group-hover:text-daraz-orange transition min-h-[2rem]">
                            <a href="{{ route('product.show', $rel->slug) }}">{{ $rel->name }}</a>
                        </h4>
                        <div class="text-sm font-black text-daraz-orange code-font mt-1.5">৳{{ number_format($rel->effective_price, 2) }}</div>
                    </div>

                    <!-- Instant Quick Add Button -->
                    <div class="pt-2">
                        <button 
                            @click="addToCart({{ $rel->id }})" 
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
    @endif

</div>
@endsection

@push('scripts')
<script>
    function productDetailApp() {
        return {
            activeImage: '{{ $product->thumbnail }}',
            currentPrice: {{ (float) $product->effective_price }},
            currentSku: '{{ $product->sku }}',
            currentStock: {{ (int) $product->stock_quantity }},
            selectedVariantId: null,
            quantity: 1,

            selectVariant(id, price, sku, stock) {
                this.selectedVariantId = id;
                this.currentPrice = price;
                this.currentSku = sku;
                this.currentStock = stock;
            },

            async buyNow(productId, variantId, qty) {
                await this.addToCart(productId, variantId, qty);
                window.location.href = `{{ route('checkout.index') }}`;
            }
        };
    }
</script>
@endpush
