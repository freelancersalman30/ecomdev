@extends('layouts.admin')

@section('title', 'Edit Custom PC: ' . $customPcBuild->title)
@section('page-title', 'Edit Custom PC: ' . $customPcBuild->title)

@section('content')
<div x-data="pcBuilderStudioEdit()" class="space-y-6">

    <!-- Top Breadcrumb & Actions Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.custom-pc-builds.show', $customPcBuild->id) }}" class="p-2 rounded-xl text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-800 transition">
                <i data-lucide="arrow-left" class="w-5 h-5"></i>
            </a>
            <div>
                <h1 class="text-xl font-black text-slate-900 dark:text-white flex items-center gap-2">
                    <i data-lucide="wrench" class="w-5 h-5 text-sky-500"></i>
                    <span>Editing Custom PC: <span class="text-indigo-400">{{ $customPcBuild->title }}</span></span>
                </h1>
                <p class="text-xs text-slate-500 dark:text-slate-400 font-mono">Build Code: {{ $customPcBuild->build_code }}</p>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('admin.custom-pc-builds.show', $customPcBuild->id) }}" class="px-4 py-2 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 text-xs font-semibold hover:bg-slate-200 transition">
                Cancel
            </a>
            <button type="button" @click="submitForm()" class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-sky-600 to-indigo-600 hover:from-sky-500 hover:to-indigo-500 text-white text-xs font-bold shadow-lg shadow-sky-500/20 transition flex items-center gap-2">
                <i data-lucide="save" class="w-4 h-4"></i>
                <span>Update PC Build</span>
            </button>
        </div>
    </div>

    <!-- Main Builder Grid -->
    <form id="pcBuilderForm" method="POST" action="{{ route('admin.custom-pc-builds.update', $customPcBuild->id) }}" enctype="multipart/form-data" class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        @csrf
        @method('PUT')
        <input type="hidden" name="components_json" :value="JSON.stringify(slots)">
        <input type="hidden" name="regular_price" :value="regularTotal">
        <input type="hidden" name="final_price" :value="finalPrice">
        <input type="hidden" name="estimated_wattage" :value="totalWattage">

        <!-- Left 8 Cols: Component Slots Assembly Area -->
        <div class="lg:col-span-8 space-y-4">
            
            <!-- Quick Slot Summary Bar -->
            <div class="bg-indigo-950/40 border border-indigo-500/20 rounded-2xl p-4 flex flex-wrap items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-indigo-500/20 text-indigo-400 flex items-center justify-center font-bold">
                        <i data-lucide="layers" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <div class="text-xs font-bold text-slate-200">
                            Components Configured: <span class="text-emerald-400" x-text="filledSlotsCount"></span> / <span x-text="slots.length"></span> Slots
                        </div>
                        <div class="text-[11px] text-slate-400">Click on any slot to replace or adjust components with real-time stock sync.</div>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <button type="button" @click="addCustomSlot()" class="px-3 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-semibold flex items-center gap-1.5 transition">
                        <i data-lucide="plus" class="w-3.5 h-3.5"></i>
                        <span>Add Extra Slot</span>
                    </button>
                    <button type="button" @click="clearAllSlots()" class="px-3 py-1.5 rounded-xl bg-rose-950/40 hover:bg-rose-900/60 text-rose-400 text-xs font-semibold flex items-center gap-1.5 transition">
                        <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                        <span>Reset</span>
                    </button>
                </div>
            </div>

            <!-- Dynamic Component Slots List -->
            <div class="space-y-3">
                <template x-for="(slot, index) in slots" :key="index">
                    <div class="bg-white dark:bg-slate-900 rounded-2xl p-4 border transition-all duration-200"
                         :class="slot.product_id || slot.custom_item_name ? 'border-emerald-500/30 dark:border-emerald-500/20 shadow-sm' : 'border-slate-200 dark:border-slate-800'">
                        
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            
                            <!-- Slot Label & Icon -->
                            <div class="flex items-center gap-3 min-w-[200px]">
                                <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0"
                                     :class="slot.product_id || slot.custom_item_name ? 'bg-emerald-500/10 text-emerald-500' : 'bg-slate-100 dark:bg-slate-800 text-slate-400'">
                                    <i data-lucide="cpu" class="w-5 h-5" x-show="slot.slot_key === 'processor'"></i>
                                    <i data-lucide="circuit-board" class="w-5 h-5" x-show="slot.slot_key === 'motherboard'"></i>
                                    <i data-lucide="memory-stick" class="w-5 h-5" x-show="slot.slot_key === 'ram'"></i>
                                    <i data-lucide="monitor-smartphone" class="w-5 h-5" x-show="slot.slot_key === 'graphics_card'"></i>
                                    <i data-lucide="hard-drive" class="w-5 h-5" x-show="slot.slot_key === 'storage_primary'"></i>
                                    <i data-lucide="database" class="w-5 h-5" x-show="slot.slot_key === 'storage_secondary'"></i>
                                    <i data-lucide="zap" class="w-5 h-5" x-show="slot.slot_key === 'power_supply'"></i>
                                    <i data-lucide="box" class="w-5 h-5" x-show="slot.slot_key === 'casing'"></i>
                                    <i data-lucide="fan" class="w-5 h-5" x-show="slot.slot_key === 'cooler'"></i>
                                    <i data-lucide="monitor" class="w-5 h-5" x-show="slot.slot_key === 'monitor'"></i>
                                    <i data-lucide="keyboard" class="w-5 h-5" x-show="slot.slot_key === 'peripherals'"></i>
                                    <i data-lucide="plug-zap" class="w-5 h-5" x-show="slot.slot_key === 'ups_accessory' || slot.slot_key.startsWith('custom')"></i>
                                </div>
                                <div>
                                    <div class="flex items-center gap-1.5">
                                        <span class="font-bold text-xs text-slate-900 dark:text-white" x-text="slot.slot_name"></span>
                                        <span x-show="slot.required" class="text-rose-500 text-xs font-bold" title="Required Core Component">*</span>
                                    </div>
                                    <div class="text-[10px] text-slate-400 font-mono" x-text="slot.wattage ? slot.wattage + 'W Est. Power' : '0W'"></div>
                                </div>
                            </div>

                            <!-- Selected Component Display OR Choose Button -->
                            <div class="flex-1">
                                <!-- If Component is Selected -->
                                <template x-if="slot.product_id || slot.custom_item_name">
                                    <div class="bg-slate-50 dark:bg-slate-800/60 rounded-xl p-3 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 border border-slate-200 dark:border-slate-700/50">
                                        <div class="flex items-center gap-3 min-w-0">
                                            <template x-if="slot.thumbnail">
                                                <img :src="slot.thumbnail" class="w-10 h-10 rounded-lg object-cover bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 flex-shrink-0">
                                            </template>
                                            <div class="min-w-0">
                                                <div class="font-bold text-xs text-slate-900 dark:text-white truncate" x-text="slot.custom_item_name || slot.product_name"></div>
                                                <div class="text-[11px] text-slate-400 flex items-center gap-2 mt-0.5">
                                                    <span x-show="slot.sku" class="font-mono" x-text="'SKU: ' + slot.sku"></span>
                                                    <span x-show="slot.warranty" class="text-emerald-500 font-semibold" x-text="'• ' + slot.warranty"></span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="flex items-center gap-4 w-full sm:w-auto justify-between sm:justify-end">
                                            <!-- Qty selector -->
                                            <div class="flex items-center gap-1 bg-white dark:bg-slate-900 rounded-lg border border-slate-200 dark:border-slate-700 p-0.5">
                                                <button type="button" @click="decrementQty(index)" class="w-6 h-6 flex items-center justify-center text-slate-400 hover:text-white font-bold text-xs">-</button>
                                                <span class="w-6 text-center text-xs font-mono font-bold" x-text="slot.quantity"></span>
                                                <button type="button" @click="incrementQty(index)" class="w-6 h-6 flex items-center justify-center text-slate-400 hover:text-white font-bold text-xs">+</button>
                                            </div>

                                            <!-- Subtotal -->
                                            <div class="text-right min-w-[90px]">
                                                <div class="text-xs font-black text-emerald-500 code-font">
                                                    ৳<span x-text="numberFormat(slot.unit_price * slot.quantity)"></span>
                                                </div>
                                                <div class="text-[10px] text-slate-400 code-font" x-show="slot.quantity > 1">
                                                    @ ৳<span x-text="numberFormat(slot.unit_price)"></span>
                                                </div>
                                            </div>

                                            <!-- Clear Slot -->
                                            <button type="button" @click="clearSlot(index)" class="p-1.5 rounded-lg text-slate-400 hover:text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-950/40 transition" title="Remove Component">
                                                <i data-lucide="x" class="w-4 h-4"></i>
                                            </button>
                                        </div>
                                    </div>
                                </template>

                                <!-- If Slot is Empty -->
                                <template x-if="!slot.product_id && !slot.custom_item_name">
                                    <div class="flex items-center gap-2">
                                        <button type="button" 
                                                @click="openProductPicker(index)" 
                                                class="flex-1 py-2 px-3 rounded-xl border border-dashed border-slate-300 dark:border-slate-700 hover:border-indigo-500 dark:hover:border-indigo-500 bg-slate-50/50 dark:bg-slate-950/30 hover:bg-indigo-50/20 dark:hover:bg-indigo-950/20 text-slate-500 dark:text-slate-400 hover:text-indigo-500 text-xs font-semibold transition flex items-center justify-center gap-1.5">
                                            <i data-lucide="plus-circle" class="w-4 h-4"></i>
                                            <span>Choose <span x-text="slot.slot_name"></span></span>
                                        </button>
                                        <button type="button" 
                                                @click="openCustomEntry(index)" 
                                                title="Enter Custom / Non-Inventory Part"
                                                class="py-2 px-3 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 text-slate-600 dark:text-slate-300 text-xs font-semibold transition">
                                            Custom
                                        </button>
                                    </div>
                                </template>
                            </div>

                        </div>
                    </div>
                </template>
            </div>

        </div>

        <!-- Right 4 Cols: Specifications & Financial Summary -->
        <div class="lg:col-span-4 space-y-4">

            <!-- Power & Thermal Calculation Card -->
            <div class="bg-white dark:bg-slate-900 rounded-2xl p-5 border border-slate-200 dark:border-slate-800 shadow-sm space-y-4">
                <div class="flex items-center justify-between">
                    <h3 class="font-bold text-xs text-slate-900 dark:text-white uppercase tracking-wider flex items-center gap-1.5">
                        <i data-lucide="zap" class="w-4 h-4 text-amber-500"></i>
                        <span>Power & Wattage Meter</span>
                    </h3>
                    <span class="text-xs font-black text-amber-500 font-mono" x-text="totalWattage + ' Watts'"></span>
                </div>

                <!-- Wattage Visual Meter -->
                <div class="space-y-1.5">
                    <div class="w-full bg-slate-100 dark:bg-slate-800 h-2.5 rounded-full overflow-hidden">
                        <div class="bg-gradient-to-r from-emerald-500 via-amber-500 to-rose-500 h-full transition-all duration-300"
                             :style="'width: ' + Math.min(100, (totalWattage / 850) * 100) + '%'"></div>
                    </div>
                    <div class="flex justify-between text-[10px] text-slate-400 font-mono">
                        <span>0W</span>
                        <span>Estimated Load</span>
                        <span>850W+</span>
                    </div>
                </div>

                <div class="p-3 rounded-xl bg-amber-50 dark:bg-amber-950/20 border border-amber-200 dark:border-amber-900/40 text-[11px] text-amber-800 dark:text-amber-300 flex items-start gap-2">
                    <i data-lucide="info" class="w-4 h-4 flex-shrink-0 mt-0.5"></i>
                    <div>
                        Recommended Minimum PSU: <strong class="code-font" x-text="recommendedPsuWattage + 'W 80+ Bronze/Gold'"></strong>
                    </div>
                </div>
            </div>

            <!-- Pricing & Combo Discount Card -->
            <div class="bg-white dark:bg-slate-900 rounded-2xl p-5 border border-slate-200 dark:border-slate-800 shadow-sm space-y-4">
                <h3 class="font-bold text-xs text-slate-900 dark:text-white uppercase tracking-wider flex items-center gap-1.5">
                    <i data-lucide="calculator" class="w-4 h-4 text-emerald-500"></i>
                    <span>Financial & Combo Pricing</span>
                </h3>

                <div class="space-y-2 text-xs divide-y divide-slate-100 dark:divide-slate-800">
                    <div class="flex justify-between pt-1">
                        <span class="text-slate-400">Total Component Value:</span>
                        <span class="font-bold text-slate-900 dark:text-white code-font">
                            ৳<span x-text="numberFormat(regularTotal)"></span>
                        </span>
                    </div>

                    <!-- Discount Type & Amount -->
                    <div class="pt-2 space-y-2">
                        <label class="block text-slate-400 font-medium text-[11px]">Special Combo Discount:</label>
                        <div class="grid grid-cols-2 gap-2">
                            <select name="discount_type" x-model="discountType" @change="calculateTotals()" class="px-2.5 py-1.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs outline-none">
                                <option value="none">No Discount</option>
                                <option value="fixed">Fixed (৳ Off)</option>
                                <option value="percentage">Percentage (% Off)</option>
                            </select>
                            <input type="number" 
                                   name="discount_amount" 
                                   x-model.number="discountAmount" 
                                   @input="calculateTotals()" 
                                   :disabled="discountType === 'none'"
                                   placeholder="Amount" 
                                   min="0"
                                   class="px-2.5 py-1.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs code-font outline-none">
                        </div>
                    </div>

                    <div class="flex justify-between pt-2 text-rose-500" x-show="discountSavings > 0">
                        <span>Package Discount Savings:</span>
                        <span class="font-bold code-font">- ৳<span x-text="numberFormat(discountSavings)"></span></span>
                    </div>

                    <div class="flex items-baseline justify-between pt-3 border-t-2 border-slate-200 dark:border-slate-800">
                        <div>
                            <span class="text-xs font-black text-slate-900 dark:text-white">FINAL PACKAGE PRICE</span>
                            <div class="text-[10px] text-emerald-500 font-semibold">Storefront & Counter Rate</div>
                        </div>
                        <div class="text-xl font-black text-emerald-500 code-font">
                            ৳<span x-text="numberFormat(finalPrice)"></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Build Metadata & Publishing -->
            <div class="bg-white dark:bg-slate-900 rounded-2xl p-5 border border-slate-200 dark:border-slate-800 shadow-sm space-y-3 text-xs">
                <h3 class="font-bold text-xs text-slate-900 dark:text-white uppercase tracking-wider">
                    Build Information & Settings
                </h3>

                <div>
                    <label class="block font-semibold text-slate-500 mb-1">PC Build Title *</label>
                    <input type="text" name="title" value="{{ old('title', $customPcBuild->title) }}" required class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs outline-none focus:ring-2 focus:ring-indigo-500">
                </div>

                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="block font-semibold text-slate-500 mb-1">Category</label>
                        <select name="category" class="w-full px-2.5 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs outline-none">
                            <option value="Gaming" {{ $customPcBuild->category === 'Gaming' ? 'selected' : '' }}>🎮 Gaming Rig</option>
                            <option value="Workstation" {{ $customPcBuild->category === 'Workstation' ? 'selected' : '' }}>💼 Workstation / 3D</option>
                            <option value="Office" {{ $customPcBuild->category === 'Office' ? 'selected' : '' }}>🏢 Office & Student</option>
                            <option value="Budget" {{ $customPcBuild->category === 'Budget' ? 'selected' : '' }}>💰 Budget Build</option>
                            <option value="Editing" {{ $customPcBuild->category === 'Editing' ? 'selected' : '' }}>🎬 Video Editing</option>
                            <option value="Custom" {{ $customPcBuild->category === 'Custom' ? 'selected' : '' }}>⚙️ Custom Rig</option>
                        </select>
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-500 mb-1">Performance Tier</label>
                        <select name="performance_level" class="w-full px-2.5 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs outline-none">
                            <option value="Entry Level" {{ $customPcBuild->performance_level === 'Entry Level' ? 'selected' : '' }}>Entry Level</option>
                            <option value="Mid-Range" {{ $customPcBuild->performance_level === 'Mid-Range' ? 'selected' : '' }}>Mid-Range</option>
                            <option value="High-End" {{ $customPcBuild->performance_level === 'High-End' ? 'selected' : '' }}>High-End</option>
                            <option value="Enthusiast" {{ $customPcBuild->performance_level === 'Enthusiast' ? 'selected' : '' }}>Extreme Enthusiast</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="block font-semibold text-slate-500 mb-1">Stock Status</label>
                        <select name="stock_status" class="w-full px-2.5 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs outline-none">
                            <option value="in_stock" {{ $customPcBuild->stock_status === 'in_stock' ? 'selected' : '' }}>In Stock / Ready</option>
                            <option value="pre_order" {{ $customPcBuild->stock_status === 'pre_order' ? 'selected' : '' }}>Pre-Order / Custom</option>
                            <option value="out_of_stock" {{ $customPcBuild->stock_status === 'out_of_stock' ? 'selected' : '' }}>Out of Stock</option>
                        </select>
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-500 mb-1">Upload Photo</label>
                        <input type="file" name="thumbnail" accept="image/*" class="w-full text-[11px] text-slate-500 file:mr-2 file:py-1 file:px-2 file:rounded-lg file:border-0 file:text-[11px] file:font-semibold file:bg-indigo-50 file:text-indigo-600 hover:file:bg-indigo-100">
                    </div>
                </div>

                <div>
                    <label class="block font-semibold text-slate-500 mb-1">Or Image URL</label>
                    <input type="url" name="thumbnail_url" value="{{ old('thumbnail_url', $customPcBuild->thumbnail) }}" placeholder="https://..." class="w-full px-3 py-1.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs outline-none">
                </div>

                <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-800/40 border border-slate-200 dark:border-slate-700/50 space-y-2">
                    <div class="flex items-center gap-2">
                        <input type="checkbox" id="is_published" name="is_published" value="1" {{ $customPcBuild->is_published ? 'checked' : '' }} class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                        <label for="is_published" class="font-semibold text-slate-700 dark:text-slate-300 cursor-pointer">
                            Publish to Storefront (Visible to Customers)
                        </label>
                    </div>

                    <div class="flex items-center gap-2">
                        <input type="checkbox" id="is_featured" name="is_featured" value="1" {{ $customPcBuild->is_featured ? 'checked' : '' }} class="rounded border-slate-300 text-amber-500 focus:ring-amber-500">
                        <label for="is_featured" class="font-semibold text-slate-700 dark:text-slate-300 cursor-pointer">
                            Mark as Featured PC Build
                        </label>
                    </div>
                </div>

                <div>
                    <label class="block font-semibold text-slate-500 mb-1">Public Description / Highlights</label>
                    <textarea name="description" rows="2" class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs outline-none">{{ old('description', $customPcBuild->description) }}</textarea>
                </div>

                <div>
                    <label class="block font-semibold text-slate-500 mb-1">Admin Internal Notes</label>
                    <textarea name="notes" rows="2" class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs outline-none">{{ old('notes', $customPcBuild->notes) }}</textarea>
                </div>

                <div class="pt-2">
                    <button type="button" @click="submitForm()" class="w-full py-3 rounded-xl bg-gradient-to-r from-sky-600 to-indigo-600 hover:from-sky-500 hover:to-indigo-500 text-white font-bold text-xs shadow-lg shadow-sky-500/20 transition flex items-center justify-center gap-2">
                        <i data-lucide="check" class="w-4 h-4"></i>
                        <span>Update Custom PC Build</span>
                    </button>
                </div>
            </div>

        </div>
    </form>

    <!-- PRODUCT PICKER MODAL -->
    <div x-show="pickerOpen" 
         x-cloak 
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/75 backdrop-blur-sm"
         style="display: none;">
        <div class="bg-white dark:bg-slate-900 rounded-2xl max-w-2xl w-full p-6 border border-slate-200 dark:border-slate-800 shadow-2xl space-y-4 max-h-[85vh] flex flex-col">
            
            <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-800 pb-3">
                <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                    <i data-lucide="search" class="w-4 h-4 text-indigo-500"></i>
                    <span>Select Component for: <strong class="text-indigo-400" x-text="activeSlot ? activeSlot.slot_name : ''"></strong></span>
                </h3>
                <button type="button" @click="closeProductPicker()" class="text-slate-400 hover:text-white text-lg font-bold">&times;</button>
            </div>

            <!-- Search input -->
            <div class="relative">
                <input type="text" 
                       x-model="searchQuery" 
                       @input.debounce.250ms="fetchProducts()" 
                       placeholder="Search products in inventory by name, SKU, specs..." 
                       class="w-full pl-9 pr-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs outline-none focus:ring-2 focus:ring-indigo-500 text-slate-900 dark:text-white">
                <i data-lucide="search" class="w-4 h-4 text-slate-400 absolute left-3 top-3"></i>
            </div>

            <!-- Products List (Scrollable) -->
            <div class="flex-1 overflow-y-auto divide-y divide-slate-100 dark:divide-slate-800 pr-1 space-y-1">
                <template x-if="loadingProducts">
                    <div class="p-8 text-center text-slate-400 text-xs">
                        <div class="animate-spin inline-block w-6 h-6 border-2 border-indigo-500 border-t-transparent rounded-full mb-2"></div>
                        <div>Loading inventory products...</div>
                    </div>
                </template>

                <template x-for="prod in searchResults" :key="prod.id">
                    <div class="p-3 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-800/80 transition flex items-center justify-between gap-3 cursor-pointer"
                         @click="selectProduct(prod)">
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="w-11 h-11 rounded-lg bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 flex items-center justify-center flex-shrink-0 overflow-hidden">
                                <template x-if="prod.thumbnail">
                                    <img :src="prod.thumbnail" class="w-full h-full object-cover">
                                </template>
                                <template x-if="!prod.thumbnail">
                                    <i data-lucide="cpu" class="w-5 h-5 text-slate-400"></i>
                                </template>
                            </div>
                            <div class="min-w-0">
                                <div class="font-bold text-xs text-slate-900 dark:text-white truncate" x-text="prod.name"></div>
                                <div class="text-[11px] text-slate-400 flex items-center gap-2 mt-0.5">
                                    <span class="font-mono" x-text="'SKU: ' + prod.sku"></span>
                                    <span>•</span>
                                    <span class="text-slate-500" x-text="prod.category_name"></span>
                                    <span>•</span>
                                    <span :class="prod.stock > 0 ? 'text-emerald-500 font-semibold' : 'text-rose-500 font-semibold'"
                                          x-text="prod.stock > 0 ? prod.stock + ' in Stock' : 'Out of Stock'"></span>
                                </div>
                            </div>
                        </div>

                        <div class="text-right flex-shrink-0">
                            <div class="font-black text-emerald-500 code-font text-xs">
                                ৳<span x-text="numberFormat(prod.price)"></span>
                            </div>
                            <button type="button" class="mt-1 px-3 py-1 rounded-lg bg-indigo-600 hover:bg-indigo-500 text-white text-[11px] font-bold">
                                Select
                            </button>
                        </div>
                    </div>
                </template>

                <template x-if="!loadingProducts && searchResults.length === 0">
                    <div class="p-8 text-center text-slate-400 text-xs">
                        No products found matching "<span x-text="searchQuery"></span>". Try another search or enter a custom item.
                    </div>
                </template>
            </div>

            <div class="pt-3 border-t border-slate-200 dark:border-slate-800 flex justify-between items-center text-xs">
                <button type="button" @click="openCustomEntry(activeSlotIndex)" class="text-indigo-400 hover:underline font-semibold">
                    + Enter custom item details instead
                </button>
                <button type="button" @click="closeProductPicker()" class="px-4 py-2 rounded-xl bg-slate-200 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-semibold">
                    Close
                </button>
            </div>

        </div>
    </div>

    <!-- CUSTOM COMPONENT MANUAL ENTRY MODAL -->
    <div x-show="customModalOpen" 
         x-cloak 
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/75 backdrop-blur-sm"
         style="display: none;">
        <div class="bg-white dark:bg-slate-900 rounded-2xl max-w-md w-full p-6 border border-slate-200 dark:border-slate-800 shadow-2xl space-y-4">
            
            <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-800 pb-3">
                <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                    <i data-lucide="edit-3" class="w-4 h-4 text-emerald-500"></i>
                    <span>Custom Part: <strong class="text-indigo-400" x-text="activeSlot ? activeSlot.slot_name : ''"></strong></span>
                </h3>
                <button type="button" @click="customModalOpen = false" class="text-slate-400 hover:text-white text-lg font-bold">&times;</button>
            </div>

            <div class="space-y-3 text-xs">
                <div>
                    <label class="block font-semibold text-slate-500 mb-1">Item / Component Name *</label>
                    <input type="text" x-model="customForm.name" placeholder="e.g. Corsair Vengeance 16GB DDR5 5600MHz" class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 outline-none">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-slate-500 mb-1">Unit Price (৳) *</label>
                        <input type="number" x-model.number="customForm.price" min="0" placeholder="0.00" class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 code-font outline-none">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-500 mb-1">Quantity</label>
                        <input type="number" x-model.number="customForm.quantity" min="1" class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 code-font outline-none">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-slate-500 mb-1">Part SKU / Code</label>
                        <input type="text" x-model="customForm.sku" placeholder="SKU-..." class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 font-mono outline-none">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-500 mb-1">Warranty</label>
                        <input type="text" x-model="customForm.warranty" placeholder="3 Years Replacement" class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 outline-none">
                    </div>
                </div>

                <div>
                    <label class="block font-semibold text-slate-500 mb-1">Estimated Power Consumption (Watts)</label>
                    <input type="number" x-model.number="customForm.wattage" min="0" placeholder="e.g. 65" class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 font-mono outline-none">
                </div>
            </div>

            <div class="pt-3 border-t border-slate-200 dark:border-slate-800 flex justify-end gap-2 text-xs">
                <button type="button" @click="customModalOpen = false" class="px-4 py-2 rounded-xl bg-slate-200 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-semibold">
                    Cancel
                </button>
                <button type="button" @click="saveCustomEntry()" class="px-5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold">
                    Apply to Slot
                </button>
            </div>

        </div>
    </div>

</div>

@push('scripts')
<script>
function pcBuilderStudioEdit() {
    const existingComponents = @js($customPcBuild->components ?: []);
    const defaultSlots = @js($defaultSlots);

    // Merge existing components or default slots
    let slots = [];
    if (existingComponents && existingComponents.length > 0) {
        slots = existingComponents.map(item => ({
            slot_key: item.slot_key || 'custom',
            slot_name: item.slot_name || 'Component',
            icon: item.icon || 'cpu',
            required: Boolean(item.required),
            default_wattage: item.default_wattage || 0,
            wattage: item.wattage || item.default_wattage || 0,
            product_id: item.product_id || null,
            product_name: item.product_name || '',
            custom_item_name: item.custom_item_name || '',
            sku: item.sku || '',
            thumbnail: item.thumbnail || '',
            warranty: item.warranty || 'Standard Warranty',
            unit_price: Number(item.unit_price) || 0,
            quantity: Number(item.quantity) || 1,
            subtotal: Number(item.subtotal) || 0
        }));
    } else {
        slots = defaultSlots.map(slot => ({
            slot_key: slot.key,
            slot_name: slot.name,
            icon: slot.icon,
            required: Boolean(slot.required),
            default_wattage: slot.default_wattage || 0,
            wattage: slot.default_wattage || 0,
            product_id: null,
            product_name: '',
            custom_item_name: '',
            sku: '',
            thumbnail: '',
            warranty: '',
            unit_price: 0,
            quantity: 1,
            subtotal: 0
        }));
    }

    return {
        slots: slots,

        pickerOpen: false,
        customModalOpen: false,
        activeSlotIndex: null,
        searchQuery: '',
        searchResults: [],
        loadingProducts: false,

        customForm: {
            name: '',
            price: 0,
            quantity: 1,
            sku: '',
            warranty: 'Standard Warranty',
            wattage: 0
        },

        discountType: "{{ $customPcBuild->discount_type ?? 'none' }}",
        discountAmount: {{ (float) ($customPcBuild->discount_amount ?? 0) }},
        regularTotal: {{ (float) ($customPcBuild->regular_price ?? 0) }},
        discountSavings: 0,
        finalPrice: {{ (float) ($customPcBuild->final_price ?? 0) }},
        totalWattage: {{ (int) ($customPcBuild->estimated_wattage ?? 450) }},
        recommendedPsuWattage: 550,

        init() {
            this.calculateTotals();
        },

        get activeSlot() {
            return this.activeSlotIndex !== null ? this.slots[this.activeSlotIndex] : null;
        },

        get filledSlotsCount() {
            return this.slots.filter(s => s.product_id || s.custom_item_name).length;
        },

        openProductPicker(index) {
            this.activeSlotIndex = index;
            this.searchQuery = '';
            this.pickerOpen = true;
            this.fetchProducts();
        },

        closeProductPicker() {
            this.pickerOpen = false;
        },

        async fetchProducts() {
            this.loadingProducts = true;
            try {
                const url = new URL("{{ route('admin.custom-pc-builds.search-products') }}", window.location.origin);
                if (this.searchQuery) {
                    url.searchParams.set('q', this.searchQuery);
                }
                const res = await fetch(url.toString());
                const data = await res.json();
                this.searchResults = data;
            } catch (err) {
                console.error('Failed to search products', err);
            } finally {
                this.loadingProducts = false;
            }
        },

        selectProduct(prod) {
            if (this.activeSlotIndex === null) return;

            const slot = this.slots[this.activeSlotIndex];
            slot.product_id = prod.id;
            slot.product_name = prod.name;
            slot.custom_item_name = '';
            slot.sku = prod.sku || '';
            slot.thumbnail = prod.thumbnail || '';
            slot.warranty = prod.warranty || '1 Year Official';
            slot.unit_price = Number(prod.price) || 0;
            slot.quantity = 1;
            slot.subtotal = slot.unit_price;

            this.closeProductPicker();
            this.calculateTotals();
        },

        openCustomEntry(index) {
            this.activeSlotIndex = index;
            const slot = this.slots[index];
            this.customForm = {
                name: slot.custom_item_name || slot.product_name || '',
                price: slot.unit_price || 0,
                quantity: slot.quantity || 1,
                sku: slot.sku || '',
                warranty: slot.warranty || 'Official Warranty',
                wattage: slot.wattage || slot.default_wattage || 0
            };
            this.pickerOpen = false;
            this.customModalOpen = true;
        },

        saveCustomEntry() {
            if (!this.customForm.name) {
                alert('Please enter a component name.');
                return;
            }

            const slot = this.slots[this.activeSlotIndex];
            slot.product_id = null;
            slot.custom_item_name = this.customForm.name;
            slot.product_name = this.customForm.name;
            slot.sku = this.customForm.sku || '';
            slot.warranty = this.customForm.warranty || 'Standard Warranty';
            slot.unit_price = Number(this.customForm.price) || 0;
            slot.quantity = Number(this.customForm.quantity) || 1;
            slot.wattage = Number(this.customForm.wattage) || slot.default_wattage || 0;
            slot.subtotal = slot.unit_price * slot.quantity;

            this.customModalOpen = false;
            this.calculateTotals();
        },

        clearSlot(index) {
            const slot = this.slots[index];
            slot.product_id = null;
            slot.product_name = '';
            slot.custom_item_name = '';
            slot.sku = '';
            slot.thumbnail = '';
            slot.warranty = '';
            slot.unit_price = 0;
            slot.quantity = 1;
            slot.subtotal = 0;
            slot.wattage = slot.default_wattage || 0;
            this.calculateTotals();
        },

        incrementQty(index) {
            this.slots[index].quantity++;
            this.calculateTotals();
        },

        decrementQty(index) {
            if (this.slots[index].quantity > 1) {
                this.slots[index].quantity--;
                this.calculateTotals();
            }
        },

        addCustomSlot() {
            const num = this.slots.length + 1;
            this.slots.push({
                slot_key: 'custom_' + Date.now(),
                slot_name: 'Custom Part #' + num,
                icon: 'plug-zap',
                required: false,
                default_wattage: 10,
                wattage: 10,
                product_id: null,
                product_name: '',
                custom_item_name: '',
                sku: '',
                thumbnail: '',
                warranty: '',
                unit_price: 0,
                quantity: 1,
                subtotal: 0
            });
        },

        clearAllSlots() {
            if (confirm('Are you sure you want to clear all configured slots?')) {
                this.slots.forEach((_, idx) => this.clearSlot(idx));
            }
        },

        calculateTotals() {
            let sum = 0;
            let watts = 50;

            this.slots.forEach(slot => {
                if (slot.product_id || slot.custom_item_name) {
                    slot.subtotal = Number(slot.unit_price) * Number(slot.quantity);
                    sum += slot.subtotal;
                    watts += (Number(slot.wattage) || Number(slot.default_wattage) || 10) * Number(slot.quantity);
                }
            });

            this.regularTotal = sum;
            this.totalWattage = Math.max(250, watts);
            this.recommendedPsuWattage = Math.ceil((this.totalWattage * 1.3) / 50) * 50;

            let discount = 0;
            if (this.discountType === 'fixed') {
                discount = Number(this.discountAmount) || 0;
            } else if (this.discountType === 'percentage') {
                discount = (sum * (Number(this.discountAmount) || 0)) / 100;
            }

            this.discountSavings = Math.min(sum, Math.max(0, discount));
            this.finalPrice = Math.max(0, sum - this.discountSavings);
        },

        submitForm() {
            const form = document.getElementById('pcBuilderForm');
            if (!form.reportValidity()) return;
            form.submit();
        },

        numberFormat(val) {
            return Number(val || 0).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        }
    };
}
</script>
@endpush
@endsection
