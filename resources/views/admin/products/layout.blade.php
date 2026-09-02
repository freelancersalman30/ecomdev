@extends('layouts.admin')

@section('title', 'Product Layout & Display Settings')

@section('content')
<div class="space-y-6 pb-12" x-data="productLayoutApp()">
    
    <!-- Top Header & Breadcrumb -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white dark:bg-slate-900 p-6 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-sm">
        <div>
            <div class="flex items-center gap-2 text-xs font-semibold text-slate-500 mb-1">
                <a href="{{ route('admin.dashboard') }}" class="hover:text-emerald-500">Dashboard</a>
                <span>/</span>
                <a href="{{ route('admin.products.index') }}" class="hover:text-emerald-500">Products</a>
                <span>/</span>
                <span class="text-slate-800 dark:text-slate-200">Layout & Display Options</span>
            </div>
            <h1 class="text-2xl font-black text-slate-900 dark:text-white flex items-center gap-2">
                <i data-lucide="layout-grid" class="w-7 h-7 text-emerald-500"></i>
                Product Layout & Display Customizer
            </h1>
            <p class="text-xs text-slate-500 mt-1">Configure card presentation themes, carousel sliding behaviors, column density, and on-card element toggles across your store.</p>
        </div>

        <div class="flex items-center gap-3">
            <form action="{{ route('admin.products.layout.reset') }}" method="POST" onsubmit="return confirm('Are you sure you want to reset all product layout settings to factory defaults?')">
                @csrf
                <button type="submit" class="px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-300 text-xs font-bold transition flex items-center gap-1.5">
                    <i data-lucide="rotate-ccw" class="w-4 h-4"></i>
                    Reset Defaults
                </button>
            </form>

            <button type="button" @click="$refs.layoutForm.submit()" class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold transition shadow-lg shadow-emerald-600/20 flex items-center gap-2">
                <i data-lucide="save" class="w-4 h-4"></i>
                Save Layout Settings
            </button>
        </div>
    </div>

    <!-- Feedback Alerts -->
    @if(session('success'))
    <div class="p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800/60 text-emerald-800 dark:text-emerald-300 text-xs flex items-center gap-3 shadow-xs">
        <i data-lucide="check-circle" class="w-5 h-5 flex-shrink-0 text-emerald-600"></i>
        <span>{{ session('success') }}</span>
    </div>
    @endif

    @if($errors->any())
    <div class="p-4 rounded-2xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800/60 text-rose-800 dark:text-rose-300 text-xs space-y-1">
        <div class="font-bold flex items-center gap-2">
            <i data-lucide="alert-triangle" class="w-4 h-4"></i>
            Please correct the errors below:
        </div>
        <ul class="list-disc pl-5 space-y-0.5">
            @foreach($errors->all() as $err)
            <li>{{ $err }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form x-ref="layoutForm" method="POST" action="{{ route('admin.products.layout.update') }}">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            
            <!-- Left 7 Cols: Controls and Options -->
            <div class="lg:col-span-7 space-y-6">

                <!-- 1. Card Presentation Theme -->
                <div class="bg-white dark:bg-slate-900 p-6 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-sm space-y-4">
                    <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
                        <div>
                            <h2 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                                <i data-lucide="palette" class="w-4 h-4 text-emerald-500"></i>
                                Product Card Presentation Theme
                            </h2>
                            <p class="text-xs text-slate-500">Select the visual skin and styling paradigm used for product cards.</p>
                        </div>
                        <span class="px-2 py-0.5 rounded-md bg-emerald-50 dark:bg-emerald-950 text-emerald-600 dark:text-emerald-400 font-extrabold text-[10px]">Active Theme</span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <!-- Option 1: Modern Daraz -->
                        <label class="cursor-pointer relative p-3.5 rounded-2xl border-2 transition flex flex-col justify-between text-left"
                               :class="form.product_card_style === 'modern_daraz' ? 'border-emerald-500 bg-emerald-50/20 dark:bg-emerald-950/20' : 'border-slate-200 dark:border-slate-800 hover:border-slate-300'">
                            <input type="radio" name="product_card_style" value="modern_daraz" x-model="form.product_card_style" class="sr-only">
                            <div>
                                <div class="w-full h-16 rounded-xl bg-slate-100 dark:bg-slate-800 mb-2 border border-slate-200/60 dark:border-slate-700/60 flex items-center justify-center p-2">
                                    <div class="w-full h-full bg-white dark:bg-slate-900 rounded-lg shadow-sm border border-slate-200 dark:border-slate-700 flex items-center justify-around px-2">
                                        <div class="w-6 h-6 rounded bg-orange-500/20 border border-orange-500/40"></div>
                                        <div class="space-y-1 w-12">
                                            <div class="h-1.5 bg-slate-300 dark:bg-slate-600 rounded"></div>
                                            <div class="h-1.5 w-8 bg-orange-500 rounded"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="font-bold text-xs text-slate-900 dark:text-white">Modern Daraz</div>
                                <div class="text-[10px] text-slate-500 mt-0.5">High-conversion layout, orange accents, rounded borders.</div>
                            </div>
                            <div class="mt-2 text-right" x-show="form.product_card_style === 'modern_daraz'">
                                <i data-lucide="check-circle-2" class="w-4 h-4 text-emerald-500 inline"></i>
                            </div>
                        </label>

                        <!-- Option 2: Compact Tech -->
                        <label class="cursor-pointer relative p-3.5 rounded-2xl border-2 transition flex flex-col justify-between text-left"
                               :class="form.product_card_style === 'compact_tech' ? 'border-emerald-500 bg-emerald-50/20 dark:bg-emerald-950/20' : 'border-slate-200 dark:border-slate-800 hover:border-slate-300'">
                            <input type="radio" name="product_card_style" value="compact_tech" x-model="form.product_card_style" class="sr-only">
                            <div>
                                <div class="w-full h-16 rounded-xl bg-slate-950 mb-2 border border-slate-800 flex items-center justify-center p-2">
                                    <div class="w-full h-full bg-slate-900 rounded-lg border border-emerald-500/30 flex items-center justify-around px-2">
                                        <div class="w-6 h-6 rounded bg-emerald-950 border border-emerald-500/50 flex items-center justify-center text-[8px] text-emerald-400 font-mono">PCB</div>
                                        <div class="space-y-1 w-12">
                                            <div class="h-1.5 bg-slate-700 rounded font-mono"></div>
                                            <div class="h-1.5 w-8 bg-emerald-400 rounded"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="font-bold text-xs text-slate-900 dark:text-white">Compact Tech</div>
                                <div class="text-[10px] text-slate-500 mt-0.5">Engineered for PCB specs, pinouts, chipset pills, dark accents.</div>
                            </div>
                            <div class="mt-2 text-right" x-show="form.product_card_style === 'compact_tech'">
                                <i data-lucide="check-circle-2" class="w-4 h-4 text-emerald-500 inline"></i>
                            </div>
                        </label>

                        <!-- Option 3: Minimalist Bordered -->
                        <label class="cursor-pointer relative p-3.5 rounded-2xl border-2 transition flex flex-col justify-between text-left"
                               :class="form.product_card_style === 'minimalist_bordered' ? 'border-emerald-500 bg-emerald-50/20 dark:bg-emerald-950/20' : 'border-slate-200 dark:border-slate-800 hover:border-slate-300'">
                            <input type="radio" name="product_card_style" value="minimalist_bordered" x-model="form.product_card_style" class="sr-only">
                            <div>
                                <div class="w-full h-16 rounded-xl bg-white dark:bg-slate-950 mb-2 border-2 border-slate-900 dark:border-slate-200 flex items-center justify-center p-2">
                                    <div class="w-full h-full bg-slate-50 dark:bg-slate-900 rounded border border-slate-300 dark:border-slate-600 flex items-center justify-around px-2">
                                        <div class="w-6 h-6 rounded-none bg-slate-200 dark:bg-slate-700"></div>
                                        <div class="space-y-1 w-12">
                                            <div class="h-1.5 bg-slate-900 dark:bg-white rounded-none"></div>
                                            <div class="h-1.5 w-8 bg-slate-500 rounded-none"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="font-bold text-xs text-slate-900 dark:text-white">Minimalist Bordered</div>
                                <div class="text-[10px] text-slate-500 mt-0.5">Ultra-clean, high-contrast borders, Apple-style monochrome polish.</div>
                            </div>
                            <div class="mt-2 text-right" x-show="form.product_card_style === 'minimalist_bordered'">
                                <i data-lucide="check-circle-2" class="w-4 h-4 text-emerald-500 inline"></i>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- 2. Section Layout Mode: Carousel vs Grid -->
                <div class="bg-white dark:bg-slate-900 p-6 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-sm space-y-4">
                    <div class="border-b border-slate-100 dark:border-slate-800 pb-3">
                        <h2 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                            <i data-lucide="sliders" class="w-4 h-4 text-emerald-500"></i>
                            Storefront Sections Layout Modes
                        </h2>
                        <p class="text-xs text-slate-500">Choose between Auto-sliding Carousel or Multi-Column Grid for each section.</p>
                    </div>

                    <div class="space-y-4">
                        <!-- Homepage Flash Sale Layout -->
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-800/50 border border-slate-200/80 dark:border-slate-700/60">
                            <div>
                                <div class="text-xs font-bold text-slate-900 dark:text-white">Homepage Flash Sale Section</div>
                                <div class="text-[11px] text-slate-500">Show high-urgency deals as a continuous horizontal carousel or standard grid.</div>
                            </div>
                            <div class="flex items-center gap-2">
                                <label class="cursor-pointer px-3 py-1.5 rounded-xl text-xs font-bold transition flex items-center gap-1.5 border"
                                       :class="form.home_flash_sale_layout === 'carousel' ? 'bg-emerald-600 text-white border-emerald-600 shadow-xs' : 'bg-white dark:bg-slate-900 text-slate-700 dark:text-slate-300 border-slate-300 dark:border-slate-700'">
                                    <input type="radio" name="home_flash_sale_layout" value="carousel" x-model="form.home_flash_sale_layout" class="sr-only">
                                    <i data-lucide="play" class="w-3.5 h-3.5"></i>
                                    Carousel
                                </label>
                                <label class="cursor-pointer px-3 py-1.5 rounded-xl text-xs font-bold transition flex items-center gap-1.5 border"
                                       :class="form.home_flash_sale_layout === 'grid' ? 'bg-emerald-600 text-white border-emerald-600 shadow-xs' : 'bg-white dark:bg-slate-900 text-slate-700 dark:text-slate-300 border-slate-300 dark:border-slate-700'">
                                    <input type="radio" name="home_flash_sale_layout" value="grid" x-model="form.home_flash_sale_layout" class="sr-only">
                                    <i data-lucide="grid" class="w-3.5 h-3.5"></i>
                                    Grid
                                </label>
                            </div>
                        </div>

                        <!-- Homepage Category Blocks Layout -->
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-800/50 border border-slate-200/80 dark:border-slate-700/60">
                            <div>
                                <div class="text-xs font-bold text-slate-900 dark:text-white">Homepage Category-Wise Blocks</div>
                                <div class="text-[11px] text-slate-500">Category blocks (Microcontrollers, Sensors, PCB Tools).</div>
                            </div>
                            <div class="flex items-center gap-2">
                                <label class="cursor-pointer px-3 py-1.5 rounded-xl text-xs font-bold transition flex items-center gap-1.5 border"
                                       :class="form.home_category_layout === 'carousel' ? 'bg-emerald-600 text-white border-emerald-600 shadow-xs' : 'bg-white dark:bg-slate-900 text-slate-700 dark:text-slate-300 border-slate-300 dark:border-slate-700'">
                                    <input type="radio" name="home_category_layout" value="carousel" x-model="form.home_category_layout" class="sr-only">
                                    <i data-lucide="play" class="w-3.5 h-3.5"></i>
                                    Carousel
                                </label>
                                <label class="cursor-pointer px-3 py-1.5 rounded-xl text-xs font-bold transition flex items-center gap-1.5 border"
                                       :class="form.home_category_layout === 'grid' ? 'bg-emerald-600 text-white border-emerald-600 shadow-xs' : 'bg-white dark:bg-slate-900 text-slate-700 dark:text-slate-300 border-slate-300 dark:border-slate-700'">
                                    <input type="radio" name="home_category_layout" value="grid" x-model="form.home_category_layout" class="sr-only">
                                    <i data-lucide="grid" class="w-3.5 h-3.5"></i>
                                    Grid
                                </label>
                            </div>
                        </div>

                        <!-- Related Products ("People Also Bought") -->
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-800/50 border border-slate-200/80 dark:border-slate-700/60">
                            <div>
                                <div class="text-xs font-bold text-slate-900 dark:text-white">Related Products Carousel (Product Detail)</div>
                                <div class="text-[11px] text-slate-500">"People Also Bought" section at the bottom of the product detail page.</div>
                            </div>
                            <div class="flex items-center gap-2">
                                <label class="cursor-pointer px-3 py-1.5 rounded-xl text-xs font-bold transition flex items-center gap-1.5 border"
                                       :class="form.product_related_layout === 'carousel' ? 'bg-emerald-600 text-white border-emerald-600 shadow-xs' : 'bg-white dark:bg-slate-900 text-slate-700 dark:text-slate-300 border-slate-300 dark:border-slate-700'">
                                    <input type="radio" name="product_related_layout" value="carousel" x-model="form.product_related_layout" class="sr-only">
                                    <i data-lucide="play" class="w-3.5 h-3.5"></i>
                                    Carousel
                                </label>
                                <label class="cursor-pointer px-3 py-1.5 rounded-xl text-xs font-bold transition flex items-center gap-1.5 border"
                                       :class="form.product_related_layout === 'grid' ? 'bg-emerald-600 text-white border-emerald-600 shadow-xs' : 'bg-white dark:bg-slate-900 text-slate-700 dark:text-slate-300 border-slate-300 dark:border-slate-700'">
                                    <input type="radio" name="product_related_layout" value="grid" x-model="form.product_related_layout" class="sr-only">
                                    <i data-lucide="grid" class="w-3.5 h-3.5"></i>
                                    Grid
                                </label>
                            </div>
                        </div>

                        <!-- Shop Catalog Density (Columns) -->
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-800/50 border border-slate-200/80 dark:border-slate-700/60">
                            <div>
                                <div class="text-xs font-bold text-slate-900 dark:text-white">Shop Catalog Grid Columns (Desktop)</div>
                                <div class="text-[11px] text-slate-500">Determines card density on `/shop` catalog and category listing pages.</div>
                            </div>
                            <select name="shop_grid_columns" x-model="form.shop_grid_columns" class="px-3 py-1.5 rounded-xl text-xs font-bold bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-slate-800 dark:text-slate-200 focus:ring-2 focus:ring-emerald-500 focus:outline-hidden">
                                <option value="3_cols">3 Columns (Spacious Cards)</option>
                                <option value="4_cols">4 Columns (Standard Recommended)</option>
                                <option value="5_cols">5 Columns (Compact Tech Grid)</option>
                                <option value="6_cols">6 Columns (Ultra Dense Inventory)</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- 3. Carousel Engine Configuration -->
                <div class="bg-white dark:bg-slate-900 p-6 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-sm space-y-4">
                    <div class="border-b border-slate-100 dark:border-slate-800 pb-3">
                        <h2 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                            <i data-lucide="repeat" class="w-4 h-4 text-emerald-500"></i>
                            Carousel Motion & Behavior Engine
                        </h2>
                        <p class="text-xs text-slate-500">Fine-tune automated transitions, swipe timing, and hover pause behavior.</p>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <!-- Auto-Slide Enabled -->
                        <div class="p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-800/50 border border-slate-200/80 dark:border-slate-700/60 flex flex-col justify-between">
                            <div class="space-y-1">
                                <div class="text-xs font-bold text-slate-900 dark:text-white">Auto-Sliding Active</div>
                                <div class="text-[10px] text-slate-500">Automatically progress items one-by-one.</div>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer mt-3">
                                <input type="checkbox" name="carousel_autoplay" value="1" x-model="form.carousel_autoplay" class="sr-only peer">
                                <div class="w-10 h-5 bg-slate-300 peer-focus:outline-hidden rounded-full peer dark:bg-slate-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all dark:border-slate-600 peer-checked:bg-emerald-600"></div>
                                <span class="ml-2 text-xs font-bold text-slate-700 dark:text-slate-300" x-text="form.carousel_autoplay ? 'Enabled' : 'Disabled'"></span>
                            </label>
                        </div>

                        <!-- Slide Interval -->
                        <div class="p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-800/50 border border-slate-200/80 dark:border-slate-700/60 flex flex-col justify-between">
                            <div class="space-y-1">
                                <div class="text-xs font-bold text-slate-900 dark:text-white">Slide Transition Speed</div>
                                <div class="text-[10px] text-slate-500">Delay before next item slides into view.</div>
                            </div>
                            <div class="mt-2 flex items-center gap-2">
                                <input type="number" name="carousel_interval" step="100" min="1500" max="10000" x-model="form.carousel_interval" class="w-full px-2.5 py-1.5 rounded-lg text-xs font-mono font-bold bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-slate-800 dark:text-slate-200">
                                <span class="text-[11px] font-bold text-slate-400">ms</span>
                            </div>
                        </div>

                        <!-- Pause on Hover -->
                        <div class="p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-800/50 border border-slate-200/80 dark:border-slate-700/60 flex flex-col justify-between">
                            <div class="space-y-1">
                                <div class="text-xs font-bold text-slate-900 dark:text-white">Pause on Mouse Hover</div>
                                <div class="text-[10px] text-slate-500">Stops sliding when user hovers to click.</div>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer mt-3">
                                <input type="checkbox" name="carousel_pause_hover" value="1" x-model="form.carousel_pause_hover" class="sr-only peer">
                                <div class="w-10 h-5 bg-slate-300 peer-focus:outline-hidden rounded-full peer dark:bg-slate-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all dark:border-slate-600 peer-checked:bg-emerald-600"></div>
                                <span class="ml-2 text-xs font-bold text-slate-700 dark:text-slate-300" x-text="form.carousel_pause_hover ? 'Yes' : 'No'"></span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- 4. Element On/Off Toggles -->
                <div class="bg-white dark:bg-slate-900 p-6 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-sm space-y-4">
                    <div class="border-b border-slate-100 dark:border-slate-800 pb-3">
                        <h2 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                            <i data-lucide="eye" class="w-4 h-4 text-emerald-500"></i>
                            Product Card Element Visibility Toggles
                        </h2>
                        <p class="text-xs text-slate-500">Toggle individual informational elements and badges on or off.</p>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                        <!-- Discount Badge on Thumbnail -->
                        <label class="p-3 rounded-2xl border border-slate-200 dark:border-slate-800 flex items-center justify-between cursor-pointer hover:bg-slate-50 dark:hover:bg-slate-800/40 transition">
                            <div class="space-y-0.5">
                                <div class="text-xs font-bold text-slate-900 dark:text-white">Discount Badge on Thumbnail</div>
                                <div class="text-[10px] text-slate-500">Top-left percentage badge (e.g. -16%).</div>
                            </div>
                            <input type="checkbox" name="show_discount_badge" value="1" x-model="form.show_discount_badge" class="w-4 h-4 text-emerald-600 rounded border-slate-300 focus:ring-emerald-500">
                        </label>

                        <!-- Strikethrough Old Regular Price -->
                        <label class="p-3 rounded-2xl border border-slate-200 dark:border-slate-800 flex items-center justify-between cursor-pointer hover:bg-slate-50 dark:hover:bg-slate-800/40 transition">
                            <div class="space-y-0.5">
                                <div class="text-xs font-bold text-slate-900 dark:text-white">Strikethrough Old Price</div>
                                <div class="text-[10px] text-slate-500">Show original regular price with strikethrough.</div>
                            </div>
                            <input type="checkbox" name="show_old_price" value="1" x-model="form.show_old_price" class="w-4 h-4 text-emerald-600 rounded border-slate-300 focus:ring-emerald-500">
                        </label>

                        <!-- Instant Quick Add Button -->
                        <label class="p-3 rounded-2xl border border-slate-200 dark:border-slate-800 flex items-center justify-between cursor-pointer hover:bg-slate-50 dark:hover:bg-slate-800/40 transition">
                            <div class="space-y-0.5">
                                <div class="text-xs font-bold text-slate-900 dark:text-white">Quick Add to Cart Button</div>
                                <div class="text-[10px] text-slate-500">Action button at bottom of card.</div>
                            </div>
                            <input type="checkbox" name="show_quick_add" value="1" x-model="form.show_quick_add" class="w-4 h-4 text-emerald-600 rounded border-slate-300 focus:ring-emerald-500">
                        </label>

                        <!-- PCB Chipset / Tech Specs Pill -->
                        <label class="p-3 rounded-2xl border border-slate-200 dark:border-slate-800 flex items-center justify-between cursor-pointer hover:bg-slate-50 dark:hover:bg-slate-800/40 transition">
                            <div class="space-y-0.5">
                                <div class="text-xs font-bold text-slate-900 dark:text-white">Chipset & Voltage Specs Pill</div>
                                <div class="text-[10px] text-slate-500">Technical spec label under the product title.</div>
                            </div>
                            <input type="checkbox" name="show_tech_specs" value="1" x-model="form.show_tech_specs" class="w-4 h-4 text-emerald-600 rounded border-slate-300 focus:ring-emerald-500">
                        </label>

                        <!-- Star Ratings & Review Count -->
                        <label class="p-3 rounded-2xl border border-slate-200 dark:border-slate-800 flex items-center justify-between cursor-pointer hover:bg-slate-50 dark:hover:bg-slate-800/40 transition">
                            <div class="space-y-0.5">
                                <div class="text-xs font-bold text-slate-900 dark:text-white">Rating Stars & Review Count</div>
                                <div class="text-[10px] text-slate-500">Displays 5-star customer rating indicator.</div>
                            </div>
                            <input type="checkbox" name="show_ratings" value="1" x-model="form.show_ratings" class="w-4 h-4 text-emerald-600 rounded border-slate-300 focus:ring-emerald-500">
                        </label>
                    </div>
                </div>
            </div>

            <!-- Right 5 Cols: Live Interactive Storefront Preview -->
            <div class="lg:col-span-5 sticky top-24 space-y-4">
                <div class="bg-white dark:bg-slate-900 p-6 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-sm space-y-4">
                    <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
                        <div class="flex items-center gap-2">
                            <span class="relative flex h-2.5 w-2.5">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-emerald-500"></span>
                            </span>
                            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-800 dark:text-white">Live Storefront Card Preview</h3>
                        </div>
                        <span class="text-[10px] font-mono px-2 py-0.5 rounded bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400" x-text="form.product_card_style"></span>
                    </div>

                    <p class="text-[11px] text-slate-500">This simulates exactly how product cards will appear to shoppers on your homepage, catalog, and related widgets.</p>

                    <!-- Interactive Live Card Render Box -->
                    <div class="p-6 rounded-2xl bg-slate-100/70 dark:bg-slate-950 flex items-center justify-center">
                        <div class="w-full max-w-[240px] transition duration-300"
                             :class="{
                                 'bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 daraz-shadow p-3': form.product_card_style === 'modern_daraz',
                                 'bg-slate-950 rounded-2xl border border-emerald-500/40 p-3 text-white shadow-lg shadow-emerald-950/30': form.product_card_style === 'compact_tech',
                                 'bg-white dark:bg-slate-900 rounded-none border-2 border-slate-900 dark:border-slate-100 p-3': form.product_card_style === 'minimalist_bordered'
                             }">
                            
                            <!-- Thumbnail Box with Badge -->
                            <div class="relative aspect-square overflow-hidden mb-2.5"
                                 :class="{
                                     'rounded-xl bg-slate-50 dark:bg-slate-800': form.product_card_style === 'modern_daraz',
                                     'rounded-xl bg-slate-900 border border-slate-800': form.product_card_style === 'compact_tech',
                                     'rounded-none bg-slate-100 dark:bg-slate-800 border border-slate-300': form.product_card_style === 'minimalist_bordered'
                                 }">
                                <img src="{{ $sampleProduct->thumbnail ?? 'https://images.unsplash.com/photo-1518770660439-4636190af475?w=600&auto=format&fit=crop&q=80' }}" alt="Preview" class="w-full h-full object-cover">
                                
                                <!-- Discount Badge on Thumbnail -->
                                <template x-if="form.show_discount_badge">
                                    <div class="absolute top-2 left-2 px-1.5 py-0.5 rounded text-[9px] font-black uppercase shadow-sm"
                                         :class="{
                                             'bg-rose-600 text-white': form.product_card_style === 'modern_daraz',
                                             'bg-emerald-500 text-slate-950 font-mono': form.product_card_style === 'compact_tech',
                                             'bg-black text-white dark:bg-white dark:text-black rounded-none': form.product_card_style === 'minimalist_bordered'
                                         }">
                                        -{{ $sampleProduct->discount_percentage > 0 ? $sampleProduct->discount_percentage : 16 }}%
                                    </div>
                                </template>

                                <!-- PCB Model Watermark -->
                                <div class="absolute bottom-1.5 left-1.5 right-1.5 px-1.5 py-0.5 rounded bg-slate-950/70 backdrop-blur-sm text-[9px] font-mono text-emerald-300 truncate">
                                    {{ $sampleProduct->pcb_model ?? 'PCB-REV-2.4' }}
                                </div>
                            </div>

                            <!-- Category & Title -->
                            <div class="text-[10px] font-semibold uppercase text-slate-400">
                                {{ $sampleProduct->category->name ?? 'Microcontrollers' }}
                            </div>
                            <h4 class="text-xs font-semibold text-slate-900 dark:text-white line-clamp-2 mt-0.5 min-h-[2rem]">
                                {{ $sampleProduct->name ?? 'ESP32-S3 Dual-Core AI Dev Board' }}
                            </h4>

                            <!-- Chipset Pill -->
                            <template x-if="form.show_tech_specs">
                                <div class="mt-1 flex items-center gap-1 text-[9px] font-mono"
                                     :class="form.product_card_style === 'compact_tech' ? 'text-emerald-400 bg-emerald-950/50 px-1.5 py-0.5 rounded w-fit border border-emerald-500/20' : 'text-slate-500'">
                                    <i data-lucide="cpu" class="w-3 h-3 inline"></i>
                                    <span>{{ $sampleProduct->chipset ?? 'ESP32-S3-WROOM-1' }}</span>
                                </div>
                            </template>

                            <!-- Ratings Stars -->
                            <template x-if="form.show_ratings">
                                <div class="flex items-center gap-1 mt-1 text-[10px] text-amber-500 font-bold">
                                    <span>★★★★★</span>
                                    <span class="text-slate-400 text-[9px]">(48)</span>
                                </div>
                            </template>

                            <!-- Price Section -->
                            <div class="mt-2 space-y-0.5">
                                <div class="flex items-baseline gap-1.5 flex-wrap">
                                    <span class="text-sm sm:text-base font-black code-font"
                                          :class="{
                                              'text-daraz-orange': form.product_card_style === 'modern_daraz',
                                              'text-emerald-400': form.product_card_style === 'compact_tech',
                                              'text-slate-900 dark:text-white': form.product_card_style === 'minimalist_bordered'
                                          }">
                                        ৳{{ number_format($sampleProduct->effective_price ?? 2950, 2) }}
                                    </span>
                                    
                                    <template x-if="form.show_old_price">
                                        <span class="text-[11px] text-slate-400 line-through code-font">
                                            ৳{{ number_format($sampleProduct->selling_price ?? 3500, 2) }}
                                        </span>
                                    </template>
                                </div>
                            </div>

                            <!-- Quick Add Button -->
                            <template x-if="form.show_quick_add">
                                <div class="pt-2.5">
                                    <button type="button" class="w-full py-1.5 rounded-xl font-bold text-xs flex items-center justify-center gap-1.5 transition shadow-sm active:scale-95"
                                            :class="{
                                                'bg-slate-900 hover:bg-daraz-orange text-white': form.product_card_style === 'modern_daraz',
                                                'bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-mono': form.product_card_style === 'compact_tech',
                                                'bg-black dark:bg-white text-white dark:text-black rounded-none border border-slate-900': form.product_card_style === 'minimalist_bordered'
                                            }">
                                        <i data-lucide="shopping-cart" class="w-3.5 h-3.5"></i>
                                        <span>Quick Add</span>
                                    </button>
                                </div>
                            </template>
                        </div>
                    </div>

                    <!-- Summary Specs Box -->
                    <div class="p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-800/40 border border-slate-200 dark:border-slate-800 text-[11px] space-y-1.5 text-slate-600 dark:text-slate-400">
                        <div class="flex justify-between">
                            <span>Flash Sale:</span>
                            <span class="font-bold text-slate-900 dark:text-white uppercase font-mono" x-text="form.home_flash_sale_layout"></span>
                        </div>
                        <div class="flex justify-between">
                            <span>Category Showcases:</span>
                            <span class="font-bold text-slate-900 dark:text-white uppercase font-mono" x-text="form.home_category_layout"></span>
                        </div>
                        <div class="flex justify-between">
                            <span>Related Products:</span>
                            <span class="font-bold text-slate-900 dark:text-white uppercase font-mono" x-text="form.product_related_layout"></span>
                        </div>
                        <div class="flex justify-between">
                            <span>Catalog Grid Columns:</span>
                            <span class="font-bold text-slate-900 dark:text-white uppercase font-mono" x-text="form.shop_grid_columns.replace('_', ' ')"></span>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </form>
</div>

<script>
function productLayoutApp() {
    return {
        form: {
            product_card_style: '{{ $config["product_card_style"] }}',
            home_flash_sale_layout: '{{ $config["home_flash_sale_layout"] }}',
            home_category_layout: '{{ $config["home_category_layout"] }}',
            product_related_layout: '{{ $config["product_related_layout"] }}',
            shop_grid_columns: '{{ $config["shop_grid_columns"] }}',
            carousel_autoplay: {{ $config["carousel_autoplay"] === '1' ? 'true' : 'false' }},
            carousel_interval: {{ (int) $config["carousel_interval"] }},
            carousel_pause_hover: {{ $config["carousel_pause_hover"] === '1' ? 'true' : 'false' }},
            show_discount_badge: {{ $config["show_discount_badge"] === '1' ? 'true' : 'false' }},
            show_old_price: {{ $config["show_old_price"] === '1' ? 'true' : 'false' }},
            show_quick_add: {{ $config["show_quick_add"] === '1' ? 'true' : 'false' }},
            show_tech_specs: {{ $config["show_tech_specs"] === '1' ? 'true' : 'false' }},
            show_ratings: {{ $config["show_ratings"] === '1' ? 'true' : 'false' }},
        },

        init() {
            this.$watch('form', () => {
                this.$nextTick(() => {
                    if (window.lucide) {
                        window.lucide.createIcons();
                    }
                });
            });
        }
    };
}
</script>
@endsection
