@extends('layouts.admin')

@section('title', 'Edit Product ' . $product->name)
@section('page-title', 'Edit Product: ' . $product->name)

@section('content')
<div x-data="productEditForm()" class="space-y-6 pb-20 lg:pb-0">

    <!-- Top Breadcrumb & Page Header -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl p-4 sm:p-6 border border-slate-200 dark:border-slate-800 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.products.index') }}" class="p-2 rounded-xl text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-800 transition shrink-0" title="Back to Products">
                <i data-lucide="arrow-left" class="w-5 h-5"></i>
            </a>
            <div>
                <h2 class="text-base sm:text-lg font-black text-slate-900 dark:text-white flex items-center gap-2">
                    <i data-lucide="edit" class="w-5 h-5 text-emerald-500"></i>
                    <span>Edit Component: {{ $product->name }}</span>
                </h2>
                <div class="text-xs text-slate-500 mt-0.5 flex items-center gap-2 font-mono">
                    <span>SKU: {{ $product->sku }}</span>
                    <span>•</span>
                    <span>Created on {{ $product->created_at->format('d M Y') }}</span>
                </div>
            </div>
        </div>

        <!-- Desktop Action Buttons -->
        <div class="hidden sm:flex items-center gap-2.5">
            <a href="{{ route('admin.products.index') }}" class="px-4 py-2 rounded-xl border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 text-xs font-bold hover:bg-slate-50 dark:hover:bg-slate-800 transition">
                Cancel
            </a>
            <button type="submit" form="product-edit-form" class="px-5 py-2 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white text-xs font-black shadow-lg shadow-emerald-500/20 transition flex items-center gap-2">
                <i data-lucide="save" class="w-4 h-4"></i>
                <span>Update Component</span>
            </button>
        </div>
    </div>

    <form id="product-edit-form" method="POST" action="{{ route('admin.products.update', $product->id) }}" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">

            <!-- LEFT 2 COLUMNS: Main Content -->
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
                            <span>Re-Generate with Gemini</span>
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
                                value="{{ $product->name }}" 
                                required 
                                class="w-full px-3.5 py-2.5 sm:py-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs sm:text-sm font-bold text-slate-900 dark:text-white outline-none focus:ring-2 focus:ring-emerald-500 transition">
                        </div>
                    </div>
                </div>

                <!-- 2. Short Marketing Summary -->
                <div class="bg-white dark:bg-slate-900 rounded-2xl p-4 sm:p-6 border border-slate-200 dark:border-slate-800 shadow-sm space-y-3" x-data="{ shortDesc: @js(old('short_description', $product->short_description ?? '')) }">
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
                        class="w-full px-4 py-2.5 sm:py-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs sm:text-sm outline-none focus:ring-2 focus:ring-emerald-500 transition resize-none"></textarea>
                </div>

                <!-- 3. Detailed Technical Description & Gemini AI Documentation -->
                @include('admin.products.partials.description_editor', ['description' => old('description', $product->description)])

                <!-- 4. PCB & Hardware Tech Specifications -->
                <div class="bg-white dark:bg-slate-900 rounded-2xl p-4 sm:p-6 border border-slate-200 dark:border-slate-800 shadow-sm space-y-4">
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2 border-b border-slate-100 dark:border-slate-800 pb-3">
                        <i data-lucide="cpu" class="w-4 h-4 text-sky-500"></i>
                        <span>Hardware & PCB Specifications</span>
                    </h3>

                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3.5">
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 mb-1">PCB Model</label>
                            <input type="text" name="pcb_model" value="{{ $product->pcb_model }}" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-mono outline-none focus:ring-2 focus:ring-sky-500">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 mb-1">Voltage</label>
                            <input type="text" name="voltage" value="{{ $product->voltage }}" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-mono outline-none focus:ring-2 focus:ring-sky-500">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 mb-1">Chipset</label>
                            <input type="text" name="chipset" value="{{ $product->chipset }}" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-mono outline-none focus:ring-2 focus:ring-sky-500">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 mb-1">Dimensions</label>
                            <input type="text" name="dimensions" value="{{ $product->dimensions }}" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs outline-none focus:ring-2 focus:ring-sky-500">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 mb-1">Weight</label>
                            <input type="text" name="weight" value="{{ $product->weight }}" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs outline-none focus:ring-2 focus:ring-sky-500">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 mb-1">Warranty</label>
                            <input type="text" name="warranty" value="{{ $product->warranty }}" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs outline-none focus:ring-2 focus:ring-sky-500">
                        </div>
                    </div>
                </div>

                <!-- 5. Search Engine Optimization (SEO) & Google SERP Preview -->
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
                                <span class="text-xs font-bold text-slate-800 dark:text-slate-200 block">Active in Storefront</span>
                                <span class="text-[10px] text-slate-400 block">Make visible for customers</span>
                            </div>
                            <input type="checkbox" name="is_active" value="1" {{ $product->is_active ? 'checked' : '' }} class="rounded text-emerald-600 focus:ring-emerald-500 w-4 h-4">
                        </label>

                        <label class="flex items-center justify-between p-2.5 rounded-xl bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 cursor-pointer">
                            <div class="space-y-0.5">
                                <span class="text-xs font-bold text-slate-800 dark:text-slate-200 block">Featured Product</span>
                                <span class="text-[10px] text-slate-400 block">Showcase on homepage</span>
                            </div>
                            <input type="checkbox" name="is_featured" value="1" {{ $product->is_featured ? 'checked' : '' }} class="rounded text-emerald-600 focus:ring-emerald-500 w-4 h-4">
                        </label>

                        <label class="flex items-center justify-between p-2.5 rounded-xl bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 cursor-pointer">
                            <div class="space-y-0.5">
                                <span class="text-xs font-bold text-slate-800 dark:text-slate-200 block">Flash Sale</span>
                                <span class="text-[10px] text-slate-400 block">Limited-time sale promo</span>
                            </div>
                            <input type="checkbox" name="is_flash_sale" value="1" {{ $product->is_flash_sale ? 'checked' : '' }} class="rounded text-amber-500 focus:ring-amber-500 w-4 h-4">
                        </label>

                        <button type="submit" class="w-full py-3 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white rounded-xl text-xs font-black uppercase tracking-wider shadow-lg shadow-emerald-500/20 transition flex items-center justify-center gap-2">
                            <i data-lucide="save" class="w-4 h-4"></i>
                            <span>Save Changes</span>
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
                            <label class="block text-xs font-semibold text-slate-500 mb-1">Category *</label>
                            <select name="category_id" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-semibold outline-none focus:ring-2 focus:ring-emerald-500">
                                @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ $product->category_id == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-500 mb-1">Brand / Manufacturer</label>
                            <select name="brand_id" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs outline-none focus:ring-2 focus:ring-emerald-500">
                                <option value="">Select Brand</option>
                                @foreach($brands as $brand)
                                <option value="{{ $brand->id }}" {{ $product->brand_id == $brand->id ? 'selected' : '' }}>{{ $brand->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-500 mb-1">SKU *</label>
                            <input type="text" name="sku" value="{{ $product->sku }}" required class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-mono font-bold uppercase outline-none focus:ring-2 focus:ring-emerald-500">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-500 mb-1">Barcode</label>
                            <input type="text" name="barcode" value="{{ $product->barcode }}" class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-mono outline-none focus:ring-2 focus:ring-emerald-500">
                        </div>
                    </div>
                </div>

                <!-- 3. Pricing & Financials Card -->
                <div class="bg-white dark:bg-slate-900 rounded-2xl p-4 sm:p-5 border border-slate-200 dark:border-slate-800 shadow-sm space-y-4" x-data="{ cost: {{ (float) $product->purchase_price }}, price: {{ (float) $product->selling_price }}, offer: {{ (float) ($product->discount_price ?? 0) }} }">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-500 flex items-center gap-1.5 border-b border-slate-100 dark:border-slate-800 pb-2.5">
                        <i data-lucide="badge-dollar-sign" class="w-4 h-4 text-amber-500"></i>
                        <span>Pricing & Profitability</span>
                    </h3>

                    <div class="space-y-3">
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 mb-1">Purchase / Cost Price (৳) *</label>
                            <input type="number" step="0.01" name="purchase_price" x-model.number="cost" value="{{ $product->purchase_price }}" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-bold code-font outline-none focus:ring-2 focus:ring-emerald-500">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-500 mb-1">Regular Selling Price (৳) *</label>
                            <input type="number" step="0.01" name="selling_price" x-model.number="price" value="{{ $product->selling_price }}" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-bold code-font outline-none focus:ring-2 focus:ring-emerald-500">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-500 mb-1">Discounted Offer Price (৳)</label>
                            <input type="number" step="0.01" name="discount_price" x-model.number="offer" value="{{ $product->discount_price }}" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-bold code-font text-emerald-600 outline-none focus:ring-2 focus:ring-emerald-500">
                        </div>

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

                    <div>
                        <label class="block text-xs font-semibold text-slate-500 mb-1">Stock Quantity</label>
                        <input type="number" name="stock_quantity" value="{{ $product->stock_quantity }}" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-bold code-font outline-none focus:ring-2 focus:ring-emerald-500">
                    </div>
                </div>

                <!-- 5. Media & Photo Uploads Card -->
                <div class="bg-white dark:bg-slate-900 rounded-2xl p-4 sm:p-5 border border-slate-200 dark:border-slate-800 shadow-sm space-y-4">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-500 flex items-center gap-1.5 border-b border-slate-100 dark:border-slate-800 pb-2.5">
                        <i data-lucide="image" class="w-4 h-4 text-sky-500"></i>
                        <span>Product Images</span>
                    </h3>

                    <!-- Current & Replace Thumbnail -->
                    <div class="space-y-3">
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">
                            Current Product Thumbnail
                        </label>

                        <div class="flex items-start gap-3">
                            <div class="w-20 h-20 rounded-2xl overflow-hidden border-2 border-slate-200 dark:border-slate-700 bg-slate-100 dark:bg-slate-800 shrink-0">
                                <img :src="thumbnailPreview" alt="Thumbnail" class="w-full h-full object-cover">
                            </div>

                            <div class="flex-1 space-y-2">
                                <label class="inline-block px-3 py-1.5 rounded-xl bg-slate-900 dark:bg-slate-700 hover:bg-slate-800 text-white text-xs font-bold cursor-pointer transition shadow-xs">
                                    <span>Choose New Thumbnail</span>
                                    <input type="file" name="thumbnail" accept="image/*" @change="previewThumbnail($event)" class="hidden">
                                </label>
                                <p class="text-[10px] text-slate-400">Upload replacement image (JPG, PNG, WEBP).</p>
                                <input type="text" name="thumbnail_url" placeholder="Or enter image URL..." class="w-full px-3 py-1.5 rounded-lg border border-slate-200 dark:border-slate-700 text-xs outline-none bg-slate-50 dark:bg-slate-800">
                            </div>
                        </div>
                    </div>

                    <!-- Existing Gallery Images -->
                    <div class="space-y-2 pt-2 border-t border-slate-100 dark:border-slate-800">
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">
                            Gallery Images ({{ $product->images->count() }})
                        </label>

                        @if($product->images->count() > 0)
                        <div class="flex items-center gap-2 overflow-x-auto pb-1">
                            @foreach($product->images as $img)
                            <div class="w-14 h-14 rounded-xl border border-slate-200 dark:border-slate-700 overflow-hidden shrink-0">
                                <img src="{{ $img->image_path }}" alt="Gallery" class="w-full h-full object-cover">
                            </div>
                            @endforeach
                        </div>
                        @endif

                        <label class="inline-block px-3 py-1.5 rounded-xl bg-sky-600 hover:bg-sky-500 text-white text-xs font-bold cursor-pointer transition shadow-xs">
                            <span>+ Add More Gallery Photos</span>
                            <input type="file" name="gallery_images[]" multiple accept="image/*" @change="previewGallery($event)" class="hidden">
                        </label>

                        <div x-show="galleryPreviews.length > 0" x-cloak class="pt-1">
                            <div class="text-[11px] text-slate-500 font-bold mb-1" x-text="`${galleryPreviews.length} new images selected`"></div>
                            <div class="flex items-center gap-2 overflow-x-auto pb-1">
                                <template x-for="(imgSrc, i) in galleryPreviews" :key="i">
                                    <div class="w-12 h-12 rounded-xl border-2 border-emerald-500 overflow-hidden shrink-0">
                                        <img :src="imgSrc" class="w-full h-full object-cover">
                                    </div>
                                </template>
                            </div>
                        </div>
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
        <button type="submit" form="product-edit-form" class="flex-1 py-2.5 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 text-white text-xs font-black uppercase tracking-wider shadow-md flex items-center justify-center gap-2">
            <i data-lucide="save" class="w-4 h-4"></i>
            <span>Save Changes</span>
        </button>
    </div>

</div>
@endsection

@push('scripts')
<script>
    function productEditForm() {
        return {
            thumbnailPreview: '{{ $product->thumbnail }}',
            galleryPreviews: [],

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
            }
        };
    }
</script>
@endpush
