@extends('layouts.admin')

@section('title', 'Add New Electronic Component / PCB')
@section('page-title', 'Create New Product / Component')

@section('content')
<div x-data="productForm()" class="max-w-5xl mx-auto space-y-6">

    <form method="POST" action="{{ route('admin.products.store') }}" enctype="multipart/form-data" class="space-y-6">
        @csrf

        <!-- 1. General & Classification -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 border border-slate-200 dark:border-slate-800 shadow-sm space-y-4">
            <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                <i data-lucide="info" class="w-4 h-4 text-emerald-500"></i>
                <span>General Information & Categorization</span>
            </h3>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Product / Component Name *</label>
                    <input type="text" name="name" x-model="name" @input="autoGenerateSku()" required placeholder="e.g. STM32F103C8T6 ARM Cortex-M3 Minimal Dev Board" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-semibold outline-none focus:ring-2 focus:ring-emerald-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">SKU (Stock Keeping Unit) *</label>
                    <div class="flex items-center gap-2">
                        <input type="text" name="sku" x-model="sku" required placeholder="PCB-STM32-001" class="flex-1 px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-mono font-bold uppercase outline-none focus:ring-2 focus:ring-emerald-500">
                        <button type="button" @click="autoGenerateSku()" class="px-3 py-2.5 rounded-xl bg-slate-100 dark:bg-slate-800 text-xs font-semibold text-slate-600 dark:text-slate-300 hover:bg-slate-200">Generate</button>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Barcode (EAN-13 / UPC)</label>
                    <input type="text" name="barcode" placeholder="894001001009" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-mono outline-none">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Category (Primary) *</label>
                    <select name="category_id" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-semibold outline-none">
                        <option value="">Select Category</option>
                        @foreach($categories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Brand / Manufacturer</label>
                    <select name="brand_id" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs outline-none">
                        <option value="">Select Brand</option>
                        @foreach($brands as $brand)
                        <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <!-- 2. PCB & Hardware Tech Specifications -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 border border-slate-200 dark:border-slate-800 shadow-sm space-y-4">
            <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                <i data-lucide="cpu" class="w-4 h-4 text-sky-500"></i>
                <span>Hardware & PCB Specifications</span>
            </h3>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">PCB Model / Version</label>
                    <input type="text" name="pcb_model" placeholder="e.g. V2.1 Blue Pill" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs outline-none font-mono">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Operating Voltage</label>
                    <input type="text" name="voltage" placeholder="e.g. 3.3V / 5V DC" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs outline-none font-mono">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Chipset / Core IC</label>
                    <input type="text" name="chipset" placeholder="e.g. STM32F103 / ESP32-D0WD" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs outline-none font-mono">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Dimensions (L x W x H)</label>
                    <input type="text" name="dimensions" placeholder="e.g. 53mm x 22mm x 12mm" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs outline-none">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Weight</label>
                    <input type="text" name="weight" placeholder="e.g. 15g" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs outline-none">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Warranty Period</label>
                    <input type="text" name="warranty" placeholder="e.g. 6 Months Replacement" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs outline-none">
                </div>
            </div>
        </div>

        <!-- 3. Pricing, Inventory & Media Upload Options -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 border border-slate-200 dark:border-slate-800 shadow-sm space-y-6">
            <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2 border-b dark:border-slate-800 pb-3">
                <i data-lucide="badge-dollar-sign" class="w-4 h-4 text-amber-500"></i>
                <span>Pricing, Stock & Product Media Upload</span>
            </h3>

            <!-- Price & Stock Numbers -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Purchase / Cost Price (৳) *</label>
                    <input type="number" step="0.01" name="purchase_price" required placeholder="180.00" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-bold code-font outline-none focus:ring-2 focus:ring-emerald-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Regular Selling Price (৳) *</label>
                    <input type="number" step="0.01" name="selling_price" required placeholder="320.00" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-bold code-font outline-none focus:ring-2 focus:ring-emerald-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Discounted Offer Price (৳)</label>
                    <input type="number" step="0.01" name="discount_price" placeholder="280.00" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-bold code-font text-emerald-600 outline-none">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Initial Stock Quantity</label>
                    <input type="number" name="stock_quantity" value="10" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-bold outline-none">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Low Stock Alert Threshold</label>
                    <input type="number" name="alert_threshold" value="5" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs outline-none">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Optional External Image URL</label>
                    <input type="text" name="thumbnail_url" placeholder="https://images.unsplash.com/..." class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs outline-none">
                </div>
            </div>

            <!-- 📸 IMAGE UPLOAD CENTER -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-4 border-t border-slate-100 dark:border-slate-800">
                
                <!-- 1. Primary Thumbnail Upload -->
                <div class="space-y-2">
                    <label class="block text-xs font-bold text-slate-800 dark:text-slate-200">
                        Primary Product Thumbnail (Main Photo)
                    </label>
                    <p class="text-[11px] text-slate-400">Supported: JPG, PNG, WEBP, SVG (Max 5MB)</p>

                    <div class="relative border-2 border-dashed border-slate-300 dark:border-slate-700 hover:border-emerald-500 rounded-2xl p-4 text-center transition bg-slate-50 dark:bg-slate-800/40 group cursor-pointer">
                        <input 
                            type="file" 
                            name="thumbnail" 
                            accept="image/*" 
                            @change="previewThumbnail($event)" 
                            class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">

                        <div x-show="!thumbnailPreview" class="space-y-2 py-4">
                            <div class="w-12 h-12 rounded-2xl bg-emerald-50 dark:bg-emerald-950/50 text-emerald-600 flex items-center justify-center mx-auto group-hover:scale-110 transition">
                                <i data-lucide="upload-cloud" class="w-6 h-6"></i>
                            </div>
                            <div class="text-xs font-semibold text-slate-700 dark:text-slate-300">
                                Click or drag & drop to upload main thumbnail
                            </div>
                            <span class="inline-block px-3 py-1 rounded-lg bg-slate-200 dark:bg-slate-700 text-[10px] font-bold text-slate-700 dark:text-slate-300">
                                Browse Image File
                            </span>
                        </div>

                        <!-- Live Preview Box -->
                        <div x-show="thumbnailPreview" x-cloak class="relative rounded-xl overflow-hidden aspect-video bg-slate-900 border border-slate-200 dark:border-slate-700 flex items-center justify-center">
                            <img :src="thumbnailPreview" alt="Thumbnail Preview" class="max-h-44 object-contain">
                            <div class="absolute top-2 right-2 px-2 py-1 rounded bg-slate-900/80 text-white text-[10px] font-bold">
                                New Thumbnail Ready
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 2. Multiple Gallery Images Upload -->
                <div class="space-y-2">
                    <label class="block text-xs font-bold text-slate-800 dark:text-slate-200">
                        Product Gallery Images (Multi-Angles & Pinouts)
                    </label>
                    <p class="text-[11px] text-slate-400">Select multiple hardware angle shots & schematic images</p>

                    <div class="relative border-2 border-dashed border-slate-300 dark:border-slate-700 hover:border-emerald-500 rounded-2xl p-4 text-center transition bg-slate-50 dark:bg-slate-800/40 group cursor-pointer">
                        <input 
                            type="file" 
                            name="gallery_images[]" 
                            multiple 
                            accept="image/*" 
                            @change="previewGallery($event)" 
                            class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">

                        <div class="space-y-2 py-4">
                            <div class="w-12 h-12 rounded-2xl bg-sky-50 dark:bg-sky-950/50 text-sky-600 flex items-center justify-center mx-auto group-hover:scale-110 transition">
                                <i data-lucide="images" class="w-6 h-6"></i>
                            </div>
                            <div class="text-xs font-semibold text-slate-700 dark:text-slate-300">
                                Click or drag & drop to upload gallery images
                            </div>
                            <span class="inline-block px-3 py-1 rounded-lg bg-slate-200 dark:bg-slate-700 text-[10px] font-bold text-slate-700 dark:text-slate-300">
                                Select Multiple Files
                            </span>
                        </div>
                    </div>

                    <!-- Multi-Image Preview Grid -->
                    <div x-show="galleryPreviews.length > 0" x-cloak class="pt-2">
                        <div class="text-[11px] font-bold text-slate-500 mb-1.5 flex items-center gap-1">
                            <span x-text="`${galleryPreviews.length} gallery images selected:`"></span>
                        </div>
                        <div class="flex items-center gap-2 overflow-x-auto pb-2">
                            <template x-for="(imgSrc, i) in galleryPreviews" :key="i">
                                <div class="w-14 h-14 rounded-xl border-2 border-slate-200 dark:border-slate-700 overflow-hidden flex-shrink-0 bg-slate-100 dark:bg-slate-800">
                                    <img :src="imgSrc" class="w-full h-full object-cover">
                                </div>
                            </template>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <!-- 4. Dynamic Variant Generator (Color, Size/Pinout, Price, Stock) -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 border border-slate-200 dark:border-slate-800 shadow-sm space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                        <i data-lucide="layers" class="w-4 h-4 text-purple-500"></i>
                        <span>Dynamic Product Variants</span>
                    </h3>
                    <p class="text-xs text-slate-500">Enable if product has multiple pinouts, PCB colors, or flash sizes</p>
                </div>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="has_variants" x-model="hasVariants" class="rounded text-emerald-600 focus:ring-emerald-500 w-4 h-4">
                    <span class="text-xs font-bold text-slate-800 dark:text-slate-200">Has Variants</span>
                </label>
            </div>

            <div x-show="hasVariants" class="space-y-3 pt-2">
                <template x-for="(variant, idx) in variants" :key="idx">
                    <div class="p-3.5 rounded-xl bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 grid grid-cols-1 sm:grid-cols-6 gap-2.5 items-end">
                        <div class="sm:col-span-2">
                            <label class="block text-[10px] font-bold text-slate-500 mb-1">Variant Name</label>
                            <input type="text" :name="`variants[${idx}][variant_name]`" x-model="variant.name" placeholder="e.g. 16MB Flash - Black PCB" class="w-full px-2.5 py-1.5 rounded-lg border text-xs bg-white dark:bg-slate-800 outline-none">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-500 mb-1">Variant SKU</label>
                            <input type="text" :name="`variants[${idx}][sku]`" x-model="variant.sku" placeholder="SKU-VAR" class="w-full px-2.5 py-1.5 rounded-lg border text-xs font-mono bg-white dark:bg-slate-800 outline-none">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-500 mb-1">Selling Price (৳)</label>
                            <input type="number" step="0.01" :name="`variants[${idx}][selling_price]`" x-model="variant.price" placeholder="0.00" class="w-full px-2.5 py-1.5 rounded-lg border text-xs font-bold code-font bg-white dark:bg-slate-800 outline-none">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-500 mb-1">Stock</label>
                            <input type="number" :name="`variants[${idx}][stock_quantity]`" x-model="variant.stock" placeholder="0" class="w-full px-2.5 py-1.5 rounded-lg border text-xs bg-white dark:bg-slate-800 outline-none">
                        </div>
                        <div class="text-right">
                            <button type="button" @click="removeVariant(idx)" class="px-3 py-1.5 rounded-lg bg-rose-500/10 text-rose-500 hover:bg-rose-500 hover:text-white text-xs font-semibold transition">
                                Remove
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

        <!-- 5. Short Description (Marketing Summary) -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 border border-slate-200 dark:border-slate-800 shadow-sm space-y-3" x-data="{ shortDesc: @js(old('short_description', '')) }">
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
                class="w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800/80 text-xs sm:text-sm outline-none focus:ring-2 focus:ring-emerald-500 transition resize-none"></textarea>
        </div>

        <!-- 6. Detailed Technical Description & Gemini AI Documentation -->
        @include('admin.products.partials.description_editor', ['description' => old('description')])

        <!-- 7. Flags & Placement -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl p-5 border border-slate-200 dark:border-slate-800 shadow-sm flex flex-wrap items-center gap-6">
            <label class="flex items-center gap-2 cursor-pointer text-xs">
                <input type="checkbox" name="is_featured" value="1" class="rounded text-emerald-600 focus:ring-emerald-500 w-4 h-4">
                <span class="font-bold text-slate-700 dark:text-slate-300">Featured Component (Homepage Grid)</span>
            </label>

            <label class="flex items-center gap-2 cursor-pointer text-xs">
                <input type="checkbox" name="is_flash_sale" value="1" class="rounded text-amber-500 focus:ring-amber-500 w-4 h-4">
                <span class="font-bold text-slate-700 dark:text-slate-300">Include in Flash Sale</span>
            </label>
        </div>

        <!-- Submit Toolbar -->
        <div class="flex items-center justify-end gap-3 pt-4">
            <a href="{{ route('admin.products.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 text-xs font-semibold hover:bg-slate-50">Cancel</a>
            <button type="submit" class="px-6 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-lg shadow-emerald-500/20 transition flex items-center gap-2">
                <i data-lucide="save" class="w-4 h-4"></i>
                <span>Publish Component</span>
            </button>
        </div>

    </form>

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
