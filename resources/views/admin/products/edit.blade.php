@extends('layouts.admin')

@section('title', 'Edit Product ' . $product->name)
@section('page-title', 'Edit Product: ' . $product->name)

@section('content')
<div x-data="productEditForm()" class="max-w-5xl mx-auto space-y-6">

    <form method="POST" action="{{ route('admin.products.update', $product->id) }}" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')

        <!-- 1. General & Classification -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 border border-slate-200 dark:border-slate-800 shadow-sm space-y-4">
            <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                <i data-lucide="info" class="w-4 h-4 text-emerald-500"></i>
                <span>General Information</span>
            </h3>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="sm:col-span-2">
                    <div class="flex items-center justify-between mb-1">
                        <label class="block text-xs font-semibold text-slate-500">Product Name *</label>
                        <button type="button" @click="window.dispatchEvent(new CustomEvent('trigger-ai-generate'))" class="text-[11px] font-bold text-emerald-600 dark:text-emerald-400 hover:text-emerald-500 flex items-center gap-1.5 transition">
                            <i data-lucide="sparkles" class="w-3.5 h-3.5"></i>
                            <span>Re-Generate with Gemini</span>
                        </button>
                    </div>
                    <input type="text" name="name" value="{{ $product->name }}" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-semibold outline-none focus:ring-2 focus:ring-emerald-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">SKU *</label>
                    <input type="text" name="sku" value="{{ $product->sku }}" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-mono font-bold uppercase outline-none">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Barcode</label>
                    <input type="text" name="barcode" value="{{ $product->barcode }}" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-mono outline-none">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Category *</label>
                    <select name="category_id" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-semibold outline-none">
                        @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ $product->category_id == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Brand</label>
                    <select name="brand_id" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs outline-none">
                        <option value="">Select Brand</option>
                        @foreach($brands as $brand)
                        <option value="{{ $brand->id }}" {{ $product->brand_id == $brand->id ? 'selected' : '' }}>{{ $brand->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <!-- 2. Tech Specs -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 border border-slate-200 dark:border-slate-800 shadow-sm space-y-4">
            <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                <i data-lucide="cpu" class="w-4 h-4 text-sky-500"></i>
                <span>PCB & Hardware Tech Specs</span>
            </h3>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">PCB Model</label>
                    <input type="text" name="pcb_model" value="{{ $product->pcb_model }}" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs outline-none">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Voltage</label>
                    <input type="text" name="voltage" value="{{ $product->voltage }}" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs outline-none">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Chipset</label>
                    <input type="text" name="chipset" value="{{ $product->chipset }}" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs outline-none">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Dimensions</label>
                    <input type="text" name="dimensions" value="{{ $product->dimensions }}" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs outline-none">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Weight</label>
                    <input type="text" name="weight" value="{{ $product->weight }}" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs outline-none">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Warranty</label>
                    <input type="text" name="warranty" value="{{ $product->warranty }}" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs outline-none">
                </div>
            </div>
        </div>

        <!-- 3. Pricing, Stock & Media Upload Center -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 border border-slate-200 dark:border-slate-800 shadow-sm space-y-6">
            <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2 border-b dark:border-slate-800 pb-3">
                <i data-lucide="badge-dollar-sign" class="w-4 h-4 text-amber-500"></i>
                <span>Pricing, Stock & Images</span>
            </h3>

            <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Purchase Cost (৳) *</label>
                    <input type="number" step="0.01" name="purchase_price" value="{{ $product->purchase_price }}" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-bold code-font outline-none">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Selling Price (৳) *</label>
                    <input type="number" step="0.01" name="selling_price" value="{{ $product->selling_price }}" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-bold code-font outline-none">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Discount Price (৳)</label>
                    <input type="number" step="0.01" name="discount_price" value="{{ $product->discount_price }}" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs code-font outline-none">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Stock Quantity</label>
                    <input type="number" name="stock_quantity" value="{{ $product->stock_quantity }}" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-bold code-font outline-none">
                </div>
            </div>

            <!-- 📸 IMAGE UPLOAD & GALLERY MANAGEMENT -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-4 border-t border-slate-100 dark:border-slate-800">
                
                <!-- 1. Thumbnail Upload / Replace -->
                <div class="space-y-3">
                    <label class="block text-xs font-bold text-slate-800 dark:text-slate-200">
                        Product Thumbnail Image
                    </label>

                    <div class="flex items-start gap-4">
                        <div class="w-24 h-24 rounded-2xl overflow-hidden border-2 border-slate-200 dark:border-slate-700 bg-slate-100 dark:bg-slate-800 flex-shrink-0">
                            <img :src="thumbnailPreview" alt="Thumbnail" class="w-full h-full object-cover">
                        </div>

                        <div class="flex-1 space-y-2">
                            <label class="inline-block px-3.5 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold cursor-pointer transition shadow-sm">
                                <span>Choose New Thumbnail</span>
                                <input type="file" name="thumbnail" accept="image/*" @change="previewThumbnail($event)" class="hidden">
                            </label>
                            <p class="text-[11px] text-slate-400">Upload new image file to replace the current thumbnail.</p>
                            <input type="text" name="thumbnail_url" placeholder="Or enter image URL..." class="w-full px-3 py-1.5 rounded-lg border text-xs outline-none bg-slate-50 dark:bg-slate-800">
                        </div>
                    </div>
                </div>

                <!-- 2. Gallery Images Upload / Additional -->
                <div class="space-y-3">
                    <label class="block text-xs font-bold text-slate-800 dark:text-slate-200">
                        Upload Additional Gallery Images
                    </label>

                    <div class="flex items-center gap-2 overflow-x-auto pb-1">
                        @foreach($product->images as $img)
                        <div class="w-16 h-16 rounded-xl border border-slate-200 dark:border-slate-700 overflow-hidden flex-shrink-0">
                            <img src="{{ $img->image_path }}" alt="Gallery" class="w-full h-full object-cover">
                        </div>
                        @endforeach
                    </div>

                    <label class="inline-block px-3.5 py-2 rounded-xl bg-sky-600 hover:bg-sky-500 text-white text-xs font-bold cursor-pointer transition shadow-sm">
                        <span>+ Add Gallery Images</span>
                        <input type="file" name="gallery_images[]" multiple accept="image/*" @change="previewGallery($event)" class="hidden">
                    </label>

                    <!-- Preview of newly selected files -->
                    <div x-show="galleryPreviews.length > 0" x-cloak class="pt-1">
                        <div class="text-[11px] text-slate-500 font-bold mb-1" x-text="`${galleryPreviews.length} new images selected`"></div>
                        <div class="flex items-center gap-2 overflow-x-auto pb-1">
                            <template x-for="(imgSrc, i) in galleryPreviews" :key="i">
                                <div class="w-14 h-14 rounded-xl border-2 border-emerald-500 overflow-hidden flex-shrink-0">
                                    <img :src="imgSrc" class="w-full h-full object-cover">
                                </div>
                            </template>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <!-- 4. Short Description (Marketing Summary) -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 border border-slate-200 dark:border-slate-800 shadow-sm space-y-3" x-data="{ shortDesc: @js(old('short_description', $product->short_description ?? '')) }">
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
                class="w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800/80 text-xs sm:text-sm outline-none focus:ring-2 focus:ring-emerald-500 transition resize-none"></textarea>
        </div>

        <!-- 5. Detailed Technical Description & Gemini AI Documentation -->
        @include('admin.products.partials.description_editor', ['description' => old('description', $product->description)])

        <!-- 6. Flags & Visibility Status -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl p-5 border border-slate-200 dark:border-slate-800 shadow-sm flex flex-wrap items-center gap-6">
            <label class="flex items-center gap-2 cursor-pointer text-xs">
                <input type="checkbox" name="is_featured" value="1" {{ $product->is_featured ? 'checked' : '' }} class="rounded text-emerald-600 focus:ring-emerald-500 w-4 h-4">
                <span class="font-bold text-slate-700 dark:text-slate-300">Featured Component</span>
            </label>

            <label class="flex items-center gap-2 cursor-pointer text-xs">
                <input type="checkbox" name="is_flash_sale" value="1" {{ $product->is_flash_sale ? 'checked' : '' }} class="rounded text-amber-500 focus:ring-amber-500 w-4 h-4">
                <span class="font-bold text-slate-700 dark:text-slate-300">Flash Sale</span>
            </label>

            <label class="flex items-center gap-2 cursor-pointer text-xs">
                <input type="checkbox" name="is_active" value="1" {{ $product->is_active ? 'checked' : '' }} class="rounded text-emerald-600 focus:ring-emerald-500 w-4 h-4">
                <span class="font-bold text-slate-700 dark:text-slate-300">Active (Visible in Storefront)</span>
            </label>
        </div>

        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('admin.products.index') }}" class="px-5 py-2.5 rounded-xl bg-slate-200 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-semibold text-xs transition">
                Cancel
            </a>
            <button type="submit" class="px-6 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-extrabold text-xs shadow-lg transition">
                Update Product
            </button>
        </div>

    </form>

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
