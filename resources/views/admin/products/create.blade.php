@extends('layouts.admin')

@section('title', 'Add New Electronic Component / PCB')
@section('page-title', 'Create New Product / Component')

@section('content')
<div x-data="productForm()" class="space-y-6 pb-20 lg:pb-0">

    <!-- Top Breadcrumb & Page Header -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl p-4 sm:p-6 border border-slate-200 dark:border-slate-800 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.products.index') }}" class="p-2 rounded-xl text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-800 transition shrink-0" title="Back to Products">
                <i data-lucide="arrow-left" class="w-5 h-5"></i>
            </a>
            <div>
                <h2 class="text-base sm:text-lg font-black text-slate-900 dark:text-white flex items-center gap-2">
                    <i data-lucide="package-plus" class="w-5 h-5 text-emerald-500"></i>
                    <span>Add New Electronic Component / PCB</span>
                </h2>
                <p class="text-xs text-slate-500 mt-0.5">Publish a hardware module, PCB development board, sensor, or electronic component to your storefront.</p>
            </div>
        </div>

        <!-- Desktop Action Buttons -->
        <div class="hidden sm:flex items-center gap-2.5">
            <a href="{{ route('admin.products.index') }}" class="px-4 py-2 rounded-xl border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 text-xs font-bold hover:bg-slate-50 dark:hover:bg-slate-800 transition">
                Cancel
            </a>
            <button type="submit" form="product-create-form" class="px-5 py-2 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white text-xs font-black shadow-lg shadow-emerald-500/20 transition flex items-center gap-2">
                <i data-lucide="save" class="w-4 h-4"></i>
                <span>Publish Component</span>
            </button>
        </div>
    </div>

    <!-- Main Form Form Container -->
    <form id="product-create-form" method="POST" action="{{ route('admin.products.store') }}" enctype="multipart/form-data" class="space-y-6">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">

            <!-- LEFT 2 COLUMNS: Main Product Content -->
            <div class="lg:col-span-2 space-y-6">

                <!-- 1. Title & General Info -->
                <div class="bg-white dark:bg-slate-900 rounded-2xl p-4 sm:p-6 border border-slate-200 dark:border-slate-800 shadow-sm space-y-4">
                    <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
                        <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                            <i data-lucide="info" class="w-4 h-4 text-emerald-500"></i>
                            <span>General Information</span>
                        </h3>
                        <button type="button" @click="window.dispatchEvent(new CustomEvent('trigger-ai-generate'))" class="text-[11px] font-bold text-emerald-600 dark:text-emerald-400 hover:text-emerald-500 flex items-center gap-1.5 transition">
                            <i data-lucide="sparkles" class="w-3.5 h-3.5"></i>
                            <span>Auto-Generate with Gemini</span>
                        </button>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                                Product / Component Name *
                            </label>
                            <input 
                                type="text" 
                                name="name" 
                                x-model="name" 
                                @input="autoGenerateSku()" 
                                required 
                                placeholder="e.g. STM32F103C8T6 ARM Cortex-M3 Minimal Dev Board" 
                                class="w-full px-3.5 py-2.5 sm:py-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs sm:text-sm font-bold text-slate-900 dark:text-white outline-none focus:ring-2 focus:ring-emerald-500 transition">
                        </div>
                    </div>
                </div>

                <!-- 2. Short Marketing Summary -->
                <div class="bg-white dark:bg-slate-900 rounded-2xl p-4 sm:p-6 border border-slate-200 dark:border-slate-800 shadow-sm space-y-3" x-data="{ shortDesc: @js(old('short_description', '')) }">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                                <i data-lucide="align-left" class="w-4 h-4 text-emerald-500"></i>
                                <span>Short Marketing Summary</span>
                            </h3>
                            <p class="text-xs text-slate-500 mt-0.5">Brief overview displayed on product cards, search listings & social cards.</p>
                        </div>
                        <div class="text-[11px] font-mono font-semibold" :class="shortDesc.length > 250 ? 'text-amber-500' : 'text-slate-400'">
                            <span x-text="shortDesc.length"></span> / 250 chars
                        </div>
                    </div>

                    <textarea 
                        name="short_description" 
                        x-model="shortDesc"
                        rows="2" 
                        placeholder="e.g. Ultra-compact STM32 development board with 72MHz ARM Cortex-M3 core, 64KB Flash, and breadboard-friendly 2.54mm pitch headers." 
                        class="w-full px-4 py-2.5 sm:py-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs sm:text-sm outline-none focus:ring-2 focus:ring-emerald-500 transition resize-none"></textarea>
                </div>

                <!-- 3. Detailed Technical Description & Gemini AI Documentation -->
                @include('admin.products.partials.description_editor', ['description' => old('description')])

                <!-- 4. PCB & Hardware Tech Specifications -->
                <div class="bg-white dark:bg-slate-900 rounded-2xl p-4 sm:p-6 border border-slate-200 dark:border-slate-800 shadow-sm space-y-4">
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2 border-b border-slate-100 dark:border-slate-800 pb-3">
                        <i data-lucide="cpu" class="w-4 h-4 text-sky-500"></i>
                        <span>Hardware & PCB Specifications</span>
                    </h3>

                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3.5">
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 mb-1">PCB Model / Version</label>
                            <input type="text" name="pcb_model" placeholder="e.g. V2.1 Blue Pill" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-mono outline-none focus:ring-2 focus:ring-sky-500">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-500 mb-1">Operating Voltage</label>
                            <input type="text" name="voltage" placeholder="e.g. 3.3V / 5V DC" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-mono outline-none focus:ring-2 focus:ring-sky-500">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-500 mb-1">Chipset / Core IC</label>
                            <input type="text" name="chipset" placeholder="e.g. STM32F103 / ESP32-D0WD" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-mono outline-none focus:ring-2 focus:ring-sky-500">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-500 mb-1">Dimensions (L x W x H)</label>
                            <input type="text" name="dimensions" placeholder="e.g. 53mm x 22mm x 12mm" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs outline-none focus:ring-2 focus:ring-sky-500">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-500 mb-1">Weight</label>
                            <input type="text" name="weight" placeholder="e.g. 15g" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs outline-none focus:ring-2 focus:ring-sky-500">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-500 mb-1">Warranty Period</label>
                            <input type="text" name="warranty" placeholder="e.g. 6 Months Replacement" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs outline-none focus:ring-2 focus:ring-sky-500">
                        </div>
                    </div>
                </div>

                <!-- 5. Dynamic Variant Generator -->
                <div class="bg-white dark:bg-slate-900 rounded-2xl p-4 sm:p-6 border border-slate-200 dark:border-slate-800 shadow-sm space-y-4">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-slate-100 dark:border-slate-800 pb-3">
                        <div>
                            <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                                <i data-lucide="layers" class="w-4 h-4 text-purple-500"></i>
                                <span>Dynamic Product Variants</span>
                            </h3>
                            <p class="text-xs text-slate-500">Enable if product has multiple pinouts, PCB colors, or flash sizes</p>
                        </div>
                        <label class="flex items-center gap-2 cursor-pointer self-start sm:self-auto">
                            <input type="checkbox" name="has_variants" x-model="hasVariants" class="rounded text-emerald-600 focus:ring-emerald-500 w-4 h-4">
                            <span class="text-xs font-bold text-slate-800 dark:text-slate-200">Has Variants</span>
                        </label>
                    </div>

                    <div x-show="hasVariants" class="space-y-3 pt-2">
                        <template x-for="(variant, idx) in variants" :key="idx">
                            <div class="p-3.5 rounded-xl bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 space-y-3 sm:space-y-0 sm:grid sm:grid-cols-12 sm:gap-2.5 sm:items-end">
                                <div class="sm:col-span-4">
                                    <label class="block text-[10px] font-bold text-slate-500 mb-1">Variant Name</label>
                                    <input type="text" :name="`variants[${idx}][variant_name]`" x-model="variant.name" placeholder="e.g. 16MB Flash - Black PCB" class="w-full px-3 py-2 rounded-lg border border-slate-200 dark:border-slate-700 text-xs bg-white dark:bg-slate-800 outline-none focus:ring-2 focus:ring-purple-500">
                                </div>
                                <div class="sm:col-span-3">
                                    <label class="block text-[10px] font-bold text-slate-500 mb-1">Variant SKU</label>
                                    <input type="text" :name="`variants[${idx}][sku]`" x-model="variant.sku" placeholder="SKU-VAR" class="w-full px-3 py-2 rounded-lg border border-slate-200 dark:border-slate-700 text-xs font-mono bg-white dark:bg-slate-800 outline-none focus:ring-2 focus:ring-purple-500">
                                </div>
                                <div class="sm:col-span-2">
                                    <label class="block text-[10px] font-bold text-slate-500 mb-1">Selling Price (৳)</label>
                                    <input type="number" step="0.01" :name="`variants[${idx}][selling_price]`" x-model="variant.price" placeholder="0.00" class="w-full px-3 py-2 rounded-lg border border-slate-200 dark:border-slate-700 text-xs font-bold code-font bg-white dark:bg-slate-800 outline-none focus:ring-2 focus:ring-purple-500">
                                </div>
                                <div class="sm:col-span-2">
                                    <label class="block text-[10px] font-bold text-slate-500 mb-1">Stock</label>
                                    <input type="number" :name="`variants[${idx}][stock_quantity]`" x-model="variant.stock" placeholder="0" class="w-full px-3 py-2 rounded-lg border border-slate-200 dark:border-slate-700 text-xs bg-white dark:bg-slate-800 outline-none focus:ring-2 focus:ring-purple-500">
                                </div>
                                <div class="sm:col-span-1 text-right sm:text-center">
                                    <button type="button" @click="removeVariant(idx)" class="w-full sm:w-auto px-3 py-2 rounded-lg bg-rose-500/10 text-rose-500 hover:bg-rose-500 hover:text-white text-xs font-semibold transition" title="Delete Variant">
                                        <i data-lucide="trash-2" class="w-3.5 h-3.5 mx-auto"></i>
                                    </button>
                                </div>
                            </div>
                        </template>

                        <button type="button" @click="addVariant()" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-white text-xs font-bold transition flex items-center gap-1.5">
                            <i data-lucide="plus" class="w-4 h-4"></i>
                            <span>+ Add Another Variant</span>
                        </button>
                    </div>
                </div>

                <!-- 6. Search Engine Optimization (SEO) & Google SERP Preview -->
                @include('admin.products.partials.seo_meta_card')

            </div>

            <!-- RIGHT 1 COLUMN: Sidebar with Publishing, Categories, Pricing, Stock & Media -->
            <div class="lg:col-span-1 space-y-6">

                <!-- 1. Publishing & Placement Flags Card -->
                <div class="bg-white dark:bg-slate-900 rounded-2xl p-4 sm:p-5 border border-slate-200 dark:border-slate-800 shadow-sm space-y-4">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-500 flex items-center gap-1.5 border-b border-slate-100 dark:border-slate-800 pb-2.5">
                        <i data-lucide="rocket" class="w-4 h-4 text-emerald-500"></i>
                        <span>Publishing & Status</span>
                    </h3>

                    <div class="space-y-3">
                        <label class="flex items-center justify-between p-2.5 rounded-xl bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 cursor-pointer">
                            <div class="space-y-0.5">
                                <span class="text-xs font-bold text-slate-800 dark:text-slate-200 block">Featured Product</span>
                                <span class="text-[10px] text-slate-400 block">Showcase on homepage curated grid</span>
                            </div>
                            <input type="checkbox" name="is_featured" value="1" class="rounded text-emerald-600 focus:ring-emerald-500 w-4 h-4">
                        </label>

                        <label class="flex items-center justify-between p-2.5 rounded-xl bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 cursor-pointer">
                            <div class="space-y-0.5">
                                <span class="text-xs font-bold text-slate-800 dark:text-slate-200 block">Flash Sale</span>
                                <span class="text-[10px] text-slate-400 block">Feature in limited-time promotional offers</span>
                            </div>
                            <input type="checkbox" name="is_flash_sale" value="1" class="rounded text-amber-500 focus:ring-amber-500 w-4 h-4">
                        </label>

                        <button type="submit" class="w-full py-3 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white rounded-xl text-xs font-black uppercase tracking-wider shadow-lg shadow-emerald-500/20 transition flex items-center justify-center gap-2">
                            <i data-lucide="save" class="w-4 h-4"></i>
                            <span>Publish Component</span>
                        </button>
                    </div>
                </div>

                <!-- 2. Categorization & Identification -->
                <div class="bg-white dark:bg-slate-900 rounded-2xl p-4 sm:p-5 border border-slate-200 dark:border-slate-800 shadow-sm space-y-4">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-500 flex items-center gap-1.5 border-b border-slate-100 dark:border-slate-800 pb-2.5">
                        <i data-lucide="tag" class="w-4 h-4 text-emerald-500"></i>
                        <span>Organization & Taxonomy</span>
                    </h3>

                    <div class="space-y-3">
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 mb-1">Primary Category *</label>
                            <select name="category_id" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-semibold outline-none focus:ring-2 focus:ring-emerald-500">
                                <option value="">Select Category</option>
                                @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-500 mb-1">Brand / Manufacturer</label>
                            <select name="brand_id" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs outline-none focus:ring-2 focus:ring-emerald-500">
                                <option value="">Select Brand</option>
                                @foreach($brands as $brand)
                                <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-500 mb-1">SKU (Stock Keeping Unit) *</label>
                            <div class="flex items-center gap-1.5">
                                <input type="text" name="sku" x-model="sku" required placeholder="PCB-STM32-001" class="flex-1 px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-mono font-bold uppercase outline-none focus:ring-2 focus:ring-emerald-500">
                                <button type="button" @click="autoGenerateSku()" class="px-2.5 py-2 rounded-xl bg-slate-100 dark:bg-slate-800 text-[11px] font-semibold text-slate-600 dark:text-slate-300 hover:bg-slate-200 shrink-0">Auto</button>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-500 mb-1">Barcode (EAN-13 / UPC)</label>
                            <input type="text" name="barcode" placeholder="894001001009" class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-mono outline-none focus:ring-2 focus:ring-emerald-500">
                        </div>
                    </div>
                </div>

                <!-- 3. Pricing & Financials Card -->
                <div class="bg-white dark:bg-slate-900 rounded-2xl p-4 sm:p-5 border border-slate-200 dark:border-slate-800 shadow-sm space-y-4" x-data="{ cost: 180, price: 320, offer: 280 }">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-500 flex items-center gap-1.5 border-b border-slate-100 dark:border-slate-800 pb-2.5">
                        <i data-lucide="badge-dollar-sign" class="w-4 h-4 text-amber-500"></i>
                        <span>Pricing & Profitability</span>
                    </h3>

                    <div class="space-y-3">
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 mb-1">Purchase / Cost Price (৳) *</label>
                            <input type="number" step="0.01" name="purchase_price" x-model.number="cost" required placeholder="180.00" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-bold code-font outline-none focus:ring-2 focus:ring-emerald-500">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-500 mb-1">Regular Selling Price (৳) *</label>
                            <input type="number" step="0.01" name="selling_price" x-model.number="price" required placeholder="320.00" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-bold code-font outline-none focus:ring-2 focus:ring-emerald-500">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-500 mb-1">Discounted Offer Price (৳)</label>
                            <input type="number" step="0.01" name="discount_price" x-model.number="offer" placeholder="280.00" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-bold code-font text-emerald-600 outline-none focus:ring-2 focus:ring-emerald-500">
                        </div>

                        <!-- Live Profit Margin Calculator -->
                        <div class="p-3 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800/60 text-xs space-y-1">
                            <div class="flex items-center justify-between text-emerald-800 dark:text-emerald-300 font-semibold">
                                <span>Estimated Margin:</span>
                                <span class="font-bold code-font" x-text="cost > 0 && price > cost ? `৳${(price - cost).toFixed(2)} (${(((price - cost) / price) * 100).toFixed(1)}%)` : '—'"></span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 4. Inventory & Stock Controls -->
                <div class="bg-white dark:bg-slate-900 rounded-2xl p-4 sm:p-5 border border-slate-200 dark:border-slate-800 shadow-sm space-y-4">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-500 flex items-center gap-1.5 border-b border-slate-100 dark:border-slate-800 pb-2.5">
                        <i data-lucide="boxes" class="w-4 h-4 text-indigo-500"></i>
                        <span>Inventory & Stock</span>
                    </h3>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 mb-1">Initial Stock</label>
                            <input type="number" name="stock_quantity" value="10" class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-bold outline-none focus:ring-2 focus:ring-emerald-500">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-500 mb-1">Low Stock Alert</label>
                            <input type="number" name="alert_threshold" value="5" class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs outline-none focus:ring-2 focus:ring-emerald-500">
                        </div>
                    </div>
                </div>

                <!-- 5. Media & Photo Uploads Card -->
                <div class="bg-white dark:bg-slate-900 rounded-2xl p-4 sm:p-5 border border-slate-200 dark:border-slate-800 shadow-sm space-y-4">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-500 flex items-center gap-1.5 border-b border-slate-100 dark:border-slate-800 pb-2.5">
                        <i data-lucide="image" class="w-4 h-4 text-sky-500"></i>
                        <span>Product Images</span>
                    </h3>

                    <!-- Primary Thumbnail -->
                    <div class="space-y-2">
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">
                            Primary Product Thumbnail (Main Photo)
                        </label>

                        <div class="relative border-2 border-dashed border-slate-300 dark:border-slate-700 hover:border-emerald-500 rounded-2xl p-4 text-center transition bg-slate-50 dark:bg-slate-800/40 group cursor-pointer">
                            <input 
                                type="file" 
                                name="thumbnail" 
                                accept="image/*" 
                                @change="previewThumbnail($event)" 
                                class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">

                            <div x-show="!thumbnailPreview" class="space-y-1.5 py-2">
                                <div class="w-10 h-10 rounded-xl bg-emerald-50 dark:bg-emerald-950/50 text-emerald-600 flex items-center justify-center mx-auto group-hover:scale-110 transition">
                                    <i data-lucide="upload-cloud" class="w-5 h-5"></i>
                                </div>
                                <div class="text-xs font-semibold text-slate-700 dark:text-slate-300">
                                    Click or drop main thumbnail
                                </div>
                                <span class="text-[10px] text-slate-400">JPG, PNG, WEBP, SVG</span>
                            </div>

                            <!-- Live Preview Box -->
                            <div x-show="thumbnailPreview" x-cloak class="relative rounded-xl overflow-hidden aspect-video bg-slate-900 border border-slate-200 dark:border-slate-700 flex items-center justify-center">
                                <img :src="thumbnailPreview" alt="Thumbnail Preview" class="max-h-36 object-contain">
                                <div class="absolute top-1.5 right-1.5 px-2 py-0.5 rounded bg-slate-900/80 text-white text-[9px] font-bold">
                                    Ready
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Gallery Multi-Images -->
                    <div class="space-y-2 pt-2 border-t border-slate-100 dark:border-slate-800">
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">
                            Gallery Images (Multi-Angles)
                        </label>

                        <div class="relative border-2 border-dashed border-slate-300 dark:border-slate-700 hover:border-sky-500 rounded-2xl p-4 text-center transition bg-slate-50 dark:bg-slate-800/40 group cursor-pointer">
                            <input 
                                type="file" 
                                name="gallery_images[]" 
                                multiple 
                                accept="image/*" 
                                @change="previewGallery($event)" 
                                class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">

                            <div class="space-y-1 py-1">
                                <div class="w-8 h-8 rounded-lg bg-sky-50 dark:bg-sky-950/50 text-sky-600 flex items-center justify-center mx-auto group-hover:scale-110 transition">
                                    <i data-lucide="images" class="w-4 h-4"></i>
                                </div>
                                <div class="text-xs font-semibold text-slate-700 dark:text-slate-300">
                                    + Add Gallery Photos
                                </div>
                            </div>
                        </div>

                        <!-- Multi-Image Preview Grid -->
                        <div x-show="galleryPreviews.length > 0" x-cloak class="pt-1">
                            <div class="flex items-center gap-2 overflow-x-auto pb-2">
                                <template x-for="(imgSrc, i) in galleryPreviews" :key="i">
                                    <div class="w-12 h-12 rounded-xl border-2 border-slate-200 dark:border-slate-700 overflow-hidden flex-shrink-0 bg-slate-100 dark:bg-slate-800">
                                        <img :src="imgSrc" class="w-full h-full object-cover">
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>

                    <!-- Optional External URL -->
                    <div class="pt-2 border-t border-slate-100 dark:border-slate-800">
                        <label class="block text-[11px] font-semibold text-slate-500 mb-1">External Image URL (Optional)</label>
                        <input type="text" name="thumbnail_url" placeholder="https://images.unsplash.com/..." class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs outline-none focus:ring-2 focus:ring-sky-500">
                    </div>
                </div>

            </div>

        </div>

    </form>

    <!-- Mobile Sticky Bottom Action Bar -->
    <div class="fixed bottom-0 inset-x-0 bg-white/95 dark:bg-slate-900/95 backdrop-blur-md border-t border-slate-200 dark:border-slate-800 p-3 z-30 flex items-center justify-between gap-3 lg:hidden shadow-lg">
        <a href="{{ route('admin.products.index') }}" class="px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 text-xs font-bold">
            Cancel
        </a>
        <button type="submit" form="product-create-form" class="flex-1 py-2.5 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 text-white text-xs font-black uppercase tracking-wider shadow-md flex items-center justify-center gap-2">
            <i data-lucide="save" class="w-4 h-4"></i>
            <span>Publish Component</span>
        </button>
    </div>

</div>
@endsection

@push('scripts')
<script>
    function productForm() {
        return {
            name: '',
            sku: '',
            hasVariants: false,
            thumbnailPreview: null,
            galleryPreviews: [],
            variants: [
                { name: 'Standard Edition', sku: '', price: '', stock: '' }
            ],

            autoGenerateSku() {
                if (this.name) {
                    const clean = this.name.replace(/[^a-zA-Z0-9]/g, '').substring(0, 6).toUpperCase();
                    this.sku = `PCB-${clean}-${Math.floor(100 + Math.random() * 900)}`;
                }
            },

            previewThumbnail(event) {
                const file = event.target.files[0];
                if (file) {
                    this.thumbnailPreview = URL.createObjectURL(file);
                }
            },

            previewGallery(event) {
                const files = event.target.files;
                this.galleryPreviews = [];
                if (files && files.length > 0) {
                    for (let i = 0; i < files.length; i++) {
                        this.galleryPreviews.push(URL.createObjectURL(files[i]));
                    }
                }
            },

            addVariant() {
                this.variants.push({ name: '', sku: '', price: '', stock: '' });
            },

            removeVariant(index) {
                this.variants.splice(index, 1);
            }
        };
    }
</script>
@endpush
