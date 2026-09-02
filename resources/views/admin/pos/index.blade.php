@extends('layouts.admin')

@section('title', 'POS Counter Terminal')
@section('page-title', 'Point of Sale (POS) Counter')

@section('content')
<div x-data="posApp()" class="flex flex-col lg:flex-row gap-5 -m-4 lg:-m-8 p-3 lg:p-5 min-h-[calc(100vh-6.5rem)]">

    <!-- LEFT COLUMN: Product Catalog & Barcode Scanner -->
    <div class="flex-1 flex flex-col bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden h-[calc(100vh-7.5rem)] min-h-[520px]">
        
        <!-- Search & Filter Bar -->
        <div class="p-3.5 sm:p-4 border-b border-slate-200 dark:border-slate-800 space-y-3 bg-slate-50/70 dark:bg-slate-950/60">
            <div class="flex items-center gap-2.5">
                <div class="relative flex-1">
                    <i data-lucide="scan-barcode" class="w-5 h-5 absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400"></i>
                    <input 
                        type="text" 
                        x-model="searchQuery" 
                        @input.debounce.250ms="searchProducts()"
                        @keydown.enter="handleBarcodeScan()"
                        placeholder="Scan barcode, enter SKU, or search products..." 
                        class="w-full pl-11 pr-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs sm:text-sm text-slate-800 dark:text-white placeholder-slate-400 focus:ring-2 focus:ring-emerald-500 focus:border-transparent outline-none transition">
                </div>
                <button @click="searchProducts()" class="px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-white text-xs font-semibold flex items-center gap-1.5 transition shrink-0">
                    <i data-lucide="search" class="w-4 h-4"></i>
                    <span class="hidden sm:inline">Search</span>
                </button>
            </div>

            <!-- Categories Horizontal Scroll -->
            <div class="flex items-center gap-2 overflow-x-auto pb-1 no-scrollbar">
                <button 
                    @click="selectedCategory = null; searchProducts()" 
                    :class="selectedCategory === null ? 'bg-emerald-500 text-slate-950 font-bold shadow-xs' : 'bg-slate-200/80 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-300 dark:hover:bg-slate-700'"
                    class="px-3 py-1.5 rounded-xl text-xs whitespace-nowrap transition">
                    All Categories
                </button>
                @foreach($categories as $cat)
                <button 
                    @click="selectedCategory = {{ $cat->id }}; searchProducts()" 
                    :class="selectedCategory === {{ $cat->id }} ? 'bg-emerald-500 text-slate-950 font-bold shadow-xs' : 'bg-slate-200/80 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-300 dark:hover:bg-slate-700'"
                    class="px-3 py-1.5 rounded-xl text-xs whitespace-nowrap transition">
                    {{ $cat->name }}
                </button>
                @endforeach
            </div>
        </div>

        <!-- Products Grid -->
        <div class="flex-1 overflow-y-auto p-4 custom-scrollbar">
            <div class="grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-4 gap-3.5">
                <template x-for="product in products" :key="product.id">
                    <div 
                        @click="handleProductClick(product)"
                        class="bg-slate-50/70 dark:bg-slate-800/60 rounded-xl p-3 border border-slate-200/80 dark:border-slate-700/60 hover:border-emerald-500 hover:shadow-md cursor-pointer transition flex flex-col justify-between group active:scale-[0.98]">
                        <div>
                            <div class="relative rounded-lg overflow-hidden aspect-video bg-white dark:bg-slate-700 mb-2 border border-slate-100 dark:border-slate-700/60">
                                <img :src="product.thumbnail" :alt="product.name" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                                <span 
                                    x-show="product.stock_quantity <= product.alert_threshold"
                                    class="absolute top-1 right-1 px-1.5 py-0.5 rounded text-[9px] font-bold bg-rose-500 text-white uppercase">
                                    Low
                                </span>
                            </div>
                            <h3 class="text-xs font-semibold text-slate-900 dark:text-white line-clamp-2 leading-snug group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition" x-text="product.name"></h3>
                            <div class="text-[10px] text-slate-400 font-mono mt-0.5 truncate" x-text="product.sku"></div>
                        </div>

                        <div class="mt-3 flex items-center justify-between pt-2 border-t border-slate-200/60 dark:border-slate-700/40">
                            <div>
                                <span class="text-xs font-extrabold text-emerald-600 dark:text-emerald-400 code-font">
                                    ৳<span x-text="product.discount_price && product.discount_price > 0 ? product.discount_price : product.selling_price"></span>
                                </span>
                            </div>
                            <span 
                                :class="product.has_variants ? 'bg-amber-500/10 text-amber-500' : 'bg-emerald-500/10 text-emerald-500'"
                                class="text-[10px] px-1.5 py-0.5 rounded font-bold"
                                x-text="product.has_variants ? 'Variants' : 'Stock: ' + product.stock_quantity">
                            </span>
                        </div>
                    </div>
                </template>
            </div>
            
            <div x-show="products.length === 0" class="h-64 flex flex-col items-center justify-center text-slate-400">
                <i data-lucide="package-open" class="w-12 h-12 mb-2 stroke-1"></i>
                <p class="text-xs font-medium">No matching components or products found.</p>
            </div>
        </div>

    </div>

    <!-- RIGHT COLUMN: Professional Unified Order Terminal -->
    <div class="w-full lg:w-[470px] bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm flex flex-col h-[calc(100vh-7.5rem)] min-h-[520px] overflow-hidden">
        
        <!-- Order Terminal Header -->
        <div class="p-3.5 border-b border-slate-200 dark:border-slate-800 bg-slate-50/70 dark:bg-slate-950/60 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center">
                    <i data-lucide="receipt" class="w-4 h-4"></i>
                </div>
                <div>
                    <h2 class="text-xs sm:text-sm font-bold text-slate-900 dark:text-white leading-tight">Order Terminal</h2>
                    <span class="text-[10px] text-slate-400 font-medium" x-text="cart.length + ' item' + (cart.length === 1 ? '' : 's') + ' in cart'"></span>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <button 
                    @click="clearCart()" 
                    class="px-2.5 py-1 rounded-lg text-xs text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-950/40 font-semibold transition flex items-center gap-1" 
                    x-show="cart.length > 0">
                    <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                    <span>Clear</span>
                </button>
            </div>
        </div>

        <!-- UNIFIED SMOOTH-SCROLLING ORDER BODY -->
        <div class="flex-1 overflow-y-auto p-3.5 sm:p-4 space-y-4 custom-scrollbar">
            
            <!-- SECTION 1: Customer Details Card -->
            <div class="rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50/60 dark:bg-slate-950/40 p-3 space-y-2.5">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-1.5 text-xs font-bold text-slate-800 dark:text-slate-200">
                        <i data-lucide="user" class="w-4 h-4 text-emerald-500"></i>
                        <span>Customer Details</span>
                    </div>

                    <!-- Toggle Customer Details Modal / Drawer -->
                    <button 
                        type="button"
                        @click="customerModalOpen = true"
                        class="px-2.5 py-1 rounded-lg bg-emerald-500/10 hover:bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 text-[11px] font-bold flex items-center gap-1 transition">
                        <i data-lucide="edit-3" class="w-3.5 h-3.5"></i>
                        <span x-text="customer.name || customer.phone ? 'Edit Details' : 'Manual Entry'"></span>
                    </button>
                </div>

                <!-- Customer Display Badge -->
                <div class="p-2.5 rounded-lg bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 flex items-start justify-between gap-3 text-xs">
                    <div class="min-w-0 flex-1">
                        <template x-if="!customer.name && !customer.phone">
                            <div class="flex items-center gap-2 text-slate-500">
                                <span class="w-2 h-2 rounded-full bg-slate-400"></span>
                                <span class="font-medium">Walk-in Customer (Fast Counter Sale)</span>
                            </div>
                        </template>

                        <template x-if="customer.name || customer.phone">
                            <div class="space-y-1">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <span class="font-bold text-slate-900 dark:text-white" x-text="customer.name || 'Customer'"></span>
                                    <span class="px-1.5 py-0.5 rounded bg-emerald-100 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-400 font-mono text-[10px] font-bold" x-text="customer.phone"></span>
                                </div>
                                <div class="text-[11px] text-slate-500 truncate" x-show="customer.address">
                                    <i data-lucide="map-pin" class="w-3 h-3 inline-block -mt-0.5 text-slate-400"></i>
                                    <span x-text="customer.address + (customer.city ? ', ' + customer.city : '')"></span>
                                </div>
                                <div class="text-[10px] text-slate-400 italic truncate" x-show="customer.note" x-text="'Note: ' + customer.note"></div>
                            </div>
                        </template>
                    </div>

                    <div class="flex items-center gap-1 shrink-0">
                        <button 
                            type="button" 
                            x-show="customer.name || customer.phone"
                            @click="resetCustomerToWalkin()" 
                            class="text-[11px] text-slate-400 hover:text-rose-500 transition" 
                            title="Reset to Walk-in">
                            <i data-lucide="x-circle" class="w-4 h-4"></i>
                        </button>
                    </div>
                </div>

                <!-- Quick Select From Registered Customers Dropdown -->
                <div class="flex items-center gap-2">
                    <select 
                        x-model="selectedCustomerId" 
                        @change="onCustomerSelect()" 
                        class="w-full px-2.5 py-1.5 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-[11px] text-slate-700 dark:text-slate-300 outline-none focus:border-emerald-500">
                        <option value="">⚡ Quick Search Registered Customers (Auto-Fill)...</option>
                        @foreach($customers as $cust)
                        <option value="{{ $cust->id }}">
                            {{ $cust->name }} ({{ $cust->phone }})
                        </option>
                        @endforeach
                    </select>

                    <button 
                        type="button"
                        @click="showInlineCustomerForm = !showInlineCustomerForm"
                        :class="showInlineCustomerForm ? 'bg-slate-200 dark:bg-slate-700 text-slate-800 dark:text-white' : 'text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-800'"
                        class="p-1.5 rounded-lg text-xs transition shrink-0"
                        title="Toggle Inline Form View">
                        <i data-lucide="sliders-horizontal" class="w-3.5 h-3.5"></i>
                    </button>
                </div>

                <!-- Optional Inline Expandable Fields -->
                <div x-show="showInlineCustomerForm" x-transition class="space-y-2 pt-2 border-t border-slate-200/60 dark:border-slate-800 text-xs">
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block text-[10px] font-bold text-slate-500 mb-0.5">Name</label>
                            <input type="text" x-model="customer.name" placeholder="Customer Name" class="w-full px-2 py-1 rounded border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-xs outline-none">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-500 mb-0.5">Phone</label>
                            <input type="text" x-model="customer.phone" placeholder="01XXXXXXXXX" class="w-full px-2 py-1 rounded border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-xs outline-none code-font">
                        </div>
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-500 mb-0.5">Customer Address (Street / Area / House)</label>
                        <input type="text" x-model="customer.address" placeholder="Address (House, Road, Area...)" class="w-full px-2 py-1 rounded border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-xs outline-none">
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block text-[10px] font-bold text-slate-500 mb-0.5">City / District</label>
                            <input type="text" x-model="customer.city" placeholder="Dhaka" class="w-full px-2 py-1 rounded border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-xs outline-none">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-500 mb-0.5">Order Notes / Remarks (Optional)</label>
                            <input type="text" x-model="customer.note" placeholder="Warranty or order note" class="w-full px-2 py-1 rounded border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-xs outline-none">
                        </div>
                    </div>
                </div>
            </div>

            <!-- SECTION 2: Cart Items Table -->
            <div class="space-y-2.5">
                <div class="flex items-center justify-between text-xs font-bold text-slate-700 dark:text-slate-300">
                    <span class="flex items-center gap-1.5">
                        <i data-lucide="shopping-cart" class="w-3.5 h-3.5 text-emerald-500"></i>
                        <span>Selected Items</span>
                    </span>
                    <span class="text-slate-400 font-mono text-[11px]" x-text="cart.length + ' Items'"></span>
                </div>

                <div class="space-y-2">
                    <template x-for="(item, index) in cart" :key="index">
                        <div class="p-2.5 rounded-xl bg-slate-50/80 dark:bg-slate-800/60 border border-slate-200/80 dark:border-slate-700/60 flex items-center justify-between gap-3 hover:border-slate-300 transition">
                            <div class="flex-1 min-w-0">
                                <h4 class="text-xs font-bold text-slate-900 dark:text-white truncate" x-text="item.name"></h4>
                                <div class="text-[10px] text-slate-400 font-mono truncate" x-show="item.variant_name" x-text="item.variant_name"></div>
                                <div class="text-xs font-bold text-emerald-600 dark:text-emerald-400 code-font mt-0.5">
                                    ৳<span x-text="Number(item.price).toFixed(2)"></span>
                                </div>
                            </div>

                            <!-- Quantity Stepper with Direct Input -->
                            <div class="flex items-center gap-1 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg p-0.5 shadow-2xs">
                                <button @click="decreaseQty(index)" class="w-6 h-6 rounded-md hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-300 flex items-center justify-center font-bold text-xs transition">-</button>
                                <input 
                                    type="number" 
                                    :value="item.quantity" 
                                    @input="updateQty(index, $event.target.value)"
                                    min="1" 
                                    class="w-8 text-center text-xs font-bold text-slate-900 dark:text-white bg-transparent outline-none code-font">
                                <button @click="increaseQty(index)" class="w-6 h-6 rounded-md hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-300 flex items-center justify-center font-bold text-xs transition">+</button>
                            </div>

                            <!-- Item Subtotal & Delete -->
                            <div class="text-right min-w-[70px]">
                                <div class="text-xs font-black text-slate-900 dark:text-white code-font">
                                    ৳<span x-text="(item.price * item.quantity).toFixed(2)"></span>
                                </div>
                                <button @click="removeItem(index)" class="text-[10px] text-rose-500 hover:text-rose-600 hover:underline font-medium transition">
                                    Remove
                                </button>
                            </div>
                        </div>
                    </template>

                    <div x-show="cart.length === 0" class="py-10 text-center text-slate-400 border border-dashed border-slate-200 dark:border-slate-800 rounded-2xl bg-slate-50/40 dark:bg-slate-950/20">
                        <i data-lucide="shopping-bag" class="w-8 h-8 mx-auto mb-1.5 stroke-1 opacity-50"></i>
                        <p class="text-xs font-semibold text-slate-600 dark:text-slate-400">Cart is empty</p>
                        <p class="text-[11px] text-slate-400 mt-0.5">Click any component or product on the left catalog</p>
                    </div>
                </div>
            </div>

            <!-- SECTION 3: Order Financials & Discount Breakdown -->
            <div class="p-3.5 rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50/80 dark:bg-slate-950/50 space-y-3">
                <div class="space-y-2 text-xs">
                    <div class="flex items-center justify-between text-slate-600 dark:text-slate-400">
                        <span>Cart Subtotal:</span>
                        <span class="font-bold text-slate-900 dark:text-white code-font text-xs sm:text-sm">৳<span x-text="getSubtotal().toFixed(2)"></span></span>
                    </div>

                    <!-- Discount Row + Quick Chips -->
                    <div class="space-y-1.5 pt-1 border-t border-slate-200/60 dark:border-slate-800/60">
                        <div class="flex items-center justify-between gap-3">
                            <span class="text-slate-600 dark:text-slate-400">Discount Amount (৳):</span>
                            <input 
                                type="number" 
                                x-model.number="discount" 
                                min="0" 
                                placeholder="0" 
                                class="w-28 px-2 py-1 text-right text-xs rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white code-font font-bold outline-none focus:border-emerald-500">
                        </div>
                        <div class="flex items-center justify-end gap-1.5">
                            <button type="button" @click="applyQuickDiscount(50)" class="px-1.5 py-0.5 rounded text-[10px] font-bold bg-slate-200 dark:bg-slate-800 hover:bg-emerald-500 hover:text-white transition">৳50</button>
                            <button type="button" @click="applyQuickDiscount(100)" class="px-1.5 py-0.5 rounded text-[10px] font-bold bg-slate-200 dark:bg-slate-800 hover:bg-emerald-500 hover:text-white transition">৳100</button>
                            <button type="button" @click="applyQuickDiscount(5, 'percent')" class="px-1.5 py-0.5 rounded text-[10px] font-bold bg-slate-200 dark:bg-slate-800 hover:bg-emerald-500 hover:text-white transition">5%</button>
                            <button type="button" @click="applyQuickDiscount(10, 'percent')" class="px-1.5 py-0.5 rounded text-[10px] font-bold bg-slate-200 dark:bg-slate-800 hover:bg-emerald-500 hover:text-white transition">10%</button>
                            <button type="button" @click="discount = 0" class="px-1.5 py-0.5 rounded text-[10px] text-rose-500 font-bold hover:underline">Reset</button>
                        </div>
                    </div>

                    <!-- Tax Row -->
                    <div class="flex items-center justify-between gap-3 pt-1 border-t border-slate-200/60 dark:border-slate-800/60">
                        <span class="text-slate-600 dark:text-slate-400">Tax / VAT (৳):</span>
                        <input 
                            type="number" 
                            x-model.number="tax" 
                            min="0" 
                            placeholder="0" 
                            class="w-28 px-2 py-1 text-right text-xs rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white code-font font-bold outline-none focus:border-emerald-500">
                    </div>

                    <!-- Grand Total Banner -->
                    <div class="flex items-center justify-between pt-2.5 border-t border-slate-200 dark:border-slate-700">
                        <div>
                            <span class="text-xs sm:text-sm font-black text-slate-900 dark:text-white uppercase tracking-tight">Grand Total:</span>
                        </div>
                        <div class="text-right">
                            <span class="text-base sm:text-xl font-black text-emerald-600 dark:text-emerald-400 code-font">
                                ৳<span x-text="getGrandTotal().toFixed(2)"></span>
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- SECTION 4: Payment Tender & Change Due Calculator -->
            <div class="p-3.5 rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 space-y-3">
                <div class="flex items-center justify-between text-xs font-bold text-slate-800 dark:text-slate-200">
                    <span class="flex items-center gap-1.5">
                        <i data-lucide="wallet" class="w-3.5 h-3.5 text-emerald-500"></i>
                        <span>Payment Tender</span>
                    </span>
                    <span class="text-[11px] font-mono text-slate-400" x-text="paymentMethod.toUpperCase()"></span>
                </div>

                <!-- Payment Methods Selector -->
                <div class="grid grid-cols-3 gap-2">
                    <button 
                        type="button"
                        @click="paymentMethod = 'pos_cash'"
                        :class="paymentMethod === 'pos_cash' ? 'bg-emerald-600 text-white font-bold border-emerald-600 shadow-xs' : 'bg-slate-50 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border-slate-200 dark:border-slate-700 hover:bg-slate-100'"
                        class="py-2 rounded-xl text-xs border flex flex-col items-center gap-1 transition">
                        <i data-lucide="banknote" class="w-4 h-4"></i>
                        <span>Cash</span>
                    </button>
                    <button 
                        type="button"
                        @click="paymentMethod = 'bkash'"
                        :class="paymentMethod === 'bkash' ? 'bg-pink-600 text-white font-bold border-pink-600 shadow-xs' : 'bg-slate-50 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border-slate-200 dark:border-slate-700 hover:bg-slate-100'"
                        class="py-2 rounded-xl text-xs border flex flex-col items-center gap-1 transition">
                        <i data-lucide="smartphone" class="w-4 h-4"></i>
                        <span>bKash</span>
                    </button>
                    <button 
                        type="button"
                        @click="paymentMethod = 'pos_card'"
                        :class="paymentMethod === 'pos_card' ? 'bg-indigo-600 text-white font-bold border-indigo-600 shadow-xs' : 'bg-slate-50 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border-slate-200 dark:border-slate-700 hover:bg-slate-100'"
                        class="py-2 rounded-xl text-xs border flex flex-col items-center gap-1 transition">
                        <i data-lucide="credit-card" class="w-4 h-4"></i>
                        <span>Card / POS</span>
                    </button>
                </div>

                <!-- Paid Amount Input & Change Due Calculator -->
                <div class="space-y-2 pt-1 border-t border-slate-100 dark:border-slate-800 text-xs">
                    <div class="flex items-center justify-between gap-3">
                        <label class="text-slate-600 dark:text-slate-400 font-semibold">Tender / Paid Amount (৳):</label>
                        <input 
                            type="number" 
                            x-model="paidAmount" 
                            :placeholder="getGrandTotal().toFixed(2)"
                            class="w-32 px-2.5 py-1.5 text-right text-xs rounded-lg border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white code-font font-bold outline-none focus:border-emerald-500">
                    </div>

                    <!-- Quick Cash Tender Chips -->
                    <div class="flex items-center justify-end gap-1.5">
                        <button type="button" @click="setExactCash()" class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-400 hover:bg-emerald-200 transition">Exact Cash</button>
                        <button type="button" @click="addCash(500)" class="px-1.5 py-0.5 rounded text-[10px] font-bold bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 text-slate-700 dark:text-slate-300 transition">+500</button>
                        <button type="button" @click="addCash(1000)" class="px-1.5 py-0.5 rounded text-[10px] font-bold bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 text-slate-700 dark:text-slate-300 transition">+1000</button>
                        <button type="button" @click="addCash(2000)" class="px-1.5 py-0.5 rounded text-[10px] font-bold bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 text-slate-700 dark:text-slate-300 transition">+2000</button>
                    </div>

                    <!-- Change Return Notice -->
                    <div class="p-2.5 rounded-lg flex items-center justify-between text-xs font-bold"
                         :class="getChangeAmount() > 0 ? 'bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800' : (getDueAmount() > 0 ? 'bg-amber-50 dark:bg-amber-950/40 text-amber-700 dark:text-amber-400 border border-amber-200 dark:border-amber-800' : 'bg-slate-50 dark:bg-slate-800 text-slate-500')">
                        <span x-text="getChangeAmount() > 0 ? 'Change to Return:' : (getDueAmount() > 0 ? 'Due Balance:' : 'Status:')"></span>
                        <span class="code-font text-sm">
                            <span x-show="getChangeAmount() > 0" x-text="'৳' + getChangeAmount().toFixed(2)"></span>
                            <span x-show="getChangeAmount() === 0 && getDueAmount() > 0" x-text="'৳' + getDueAmount().toFixed(2)"></span>
                            <span x-show="getChangeAmount() === 0 && getDueAmount() === 0">Full Payment Exact</span>
                        </span>
                    </div>
                </div>
            </div>

            <!-- SECTION 5: Complete Sale & Print Receipt Button -->
            <div class="pt-1">
                <button 
                    @click="submitSale()" 
                    :disabled="cart.length === 0 || isProcessing"
                    class="w-full py-3.5 rounded-xl bg-gradient-to-r from-emerald-600 via-teal-600 to-emerald-500 hover:from-emerald-500 hover:to-teal-400 text-slate-950 font-extrabold text-xs sm:text-sm shadow-lg shadow-emerald-500/20 disabled:opacity-50 disabled:cursor-not-allowed transition flex items-center justify-center gap-2 uppercase tracking-wide cursor-pointer active:scale-[0.99]">
                    <i data-lucide="printer" class="w-5 h-5"></i>
                    <span x-text="isProcessing ? 'Processing Transaction...' : 'Complete Sale & Print Receipt'"></span>
                </button>
                <div class="text-center mt-1.5">
                    <span class="text-[10px] text-slate-400 font-mono">Prints instantly to 80mm / 58mm thermal counter printer</span>
                </div>
            </div>

        </div>

    </div>

    <!-- CLEAN CUSTOMER DETAILS MODAL -->
    <div x-show="customerModalOpen" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/70 backdrop-blur-xs" style="display: none;">
        <div @click.away="customerModalOpen = false" class="bg-white dark:bg-slate-900 rounded-2xl max-w-lg w-full p-5 sm:p-6 border border-slate-200 dark:border-slate-800 shadow-2xl space-y-4">
            <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-800 pb-3">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center">
                        <i data-lucide="user-plus" class="w-4 h-4"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-slate-900 dark:text-white">Customer & Delivery Details</h3>
                        <p class="text-[11px] text-slate-500">Record customer details for delivery, warranty, and receipts</p>
                    </div>
                </div>
                <button @click="customerModalOpen = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-white p-1">&times;</button>
            </div>

            <div class="space-y-3 text-xs">
                <!-- Name & Phone Row -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 mb-1">
                            Customer Name <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <i data-lucide="user" class="w-3.5 h-3.5 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                            <input 
                                type="text" 
                                x-model="customer.name" 
                                placeholder="e.g. Md. Salman Chowdhury" 
                                class="w-full pl-9 pr-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs text-slate-800 dark:text-white outline-none focus:border-emerald-500">
                        </div>
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 mb-1">
                            Phone Number <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <i data-lucide="phone" class="w-3.5 h-3.5 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                            <input 
                                type="text" 
                                x-model="customer.phone" 
                                placeholder="018XXXXXXXX" 
                                class="w-full pl-9 pr-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs text-slate-800 dark:text-white outline-none focus:border-emerald-500 code-font">
                        </div>
                    </div>
                </div>

                <!-- Street / Delivery Address -->
                <div>
                    <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 mb-1">
                        Customer Address (Street / Area / House) <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <i data-lucide="map-pin" class="w-3.5 h-3.5 absolute left-3 top-2.5 text-slate-400"></i>
                        <textarea 
                            x-model="customer.address" 
                            rows="2" 
                            placeholder="e.g. House #42, Road #7, Sector #11, Uttara" 
                            class="w-full pl-9 pr-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs text-slate-800 dark:text-white outline-none focus:border-emerald-500 resize-none"></textarea>
                    </div>
                </div>

                <!-- City & Email Row -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 mb-1">
                            City / District
                        </label>
                        <input 
                            type="text" 
                            x-model="customer.city" 
                            placeholder="e.g. Dhaka" 
                            class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs text-slate-800 dark:text-white outline-none focus:border-emerald-500">
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 mb-1">
                            Email (Optional)
                        </label>
                        <input 
                            type="email" 
                            x-model="customer.email" 
                            placeholder="customer@email.com" 
                            class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs text-slate-800 dark:text-white outline-none focus:border-emerald-500">
                    </div>
                </div>

                <!-- Order Notes / Remarks -->
                <div>
                    <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 mb-1">
                        Order Notes / Remarks (Optional)
                    </label>
                    <input 
                        type="text" 
                        x-model="customer.note" 
                        placeholder="e.g. Deliver with test warranty card" 
                        class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs text-slate-800 dark:text-white outline-none focus:border-emerald-500">
                </div>
            </div>

            <div class="flex items-center justify-between pt-2 border-t border-slate-200 dark:border-slate-800">
                <button 
                    type="button" 
                    @click="resetCustomerToWalkin(); customerModalOpen = false" 
                    class="text-xs text-rose-500 hover:underline">
                    Reset to Walk-in
                </button>
                <button 
                    type="button" 
                    @click="customerModalOpen = false" 
                    class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold transition flex items-center gap-1.5 shadow-sm">
                    <i data-lucide="check" class="w-4 h-4"></i>
                    <span>Apply & Close</span>
                </button>
            </div>
        </div>
    </div>

    <!-- VARIANT SELECTION MODAL -->
    <div x-show="variantModalOpen" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/70 backdrop-blur-xs" style="display: none;">
        <div @click.away="variantModalOpen = false" class="bg-white dark:bg-slate-900 rounded-2xl max-w-md w-full p-6 border border-slate-200 dark:border-slate-800 shadow-2xl space-y-4">
            <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-800 pb-3">
                <h3 class="text-sm font-bold text-slate-900 dark:text-white" x-text="selectedProductForVariant ? selectedProductForVariant.name : ''"></h3>
                <button @click="variantModalOpen = false" class="text-slate-400 hover:text-white text-lg">&times;</button>
            </div>
            
            <p class="text-xs text-slate-500">Select product variant / specification:</p>

            <div class="space-y-2 max-h-60 overflow-y-auto custom-scrollbar">
                <template x-for="variant in (selectedProductForVariant ? selectedProductForVariant.variants : [])" :key="variant.id">
                    <div 
                        @click="addVariantToCart(variant)"
                        class="p-3 rounded-xl border border-slate-200 dark:border-slate-800 hover:border-emerald-500 hover:bg-emerald-500/5 cursor-pointer flex items-center justify-between transition">
                        <div>
                            <div class="text-xs font-bold text-slate-900 dark:text-white" x-text="variant.variant_name"></div>
                            <div class="text-[10px] text-slate-400 font-mono" x-text="variant.sku"></div>
                        </div>
                        <div class="text-right">
                            <div class="text-xs font-bold text-emerald-500 code-font">৳<span x-text="variant.selling_price"></span></div>
                            <div class="text-[10px] text-slate-400">Stock: <span x-text="variant.stock_quantity"></span></div>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
    function posApp() {
        return {
            searchQuery: '',
            selectedCategory: null,
            products: @json($products),
            cart: [],
            discount: 0,
            tax: 0,
            shippingCharge: 0,
            paidAmount: null,
            customerModalOpen: false,
            showInlineCustomerForm: false,
            selectedCustomerId: '',
            customer: {
                name: '',
                phone: '',
                address: '',
                city: 'Dhaka',
                email: '',
                note: ''
            },
            customersList: @json($customers),
            paymentMethod: 'pos_cash',
            isProcessing: false,
            variantModalOpen: false,
            selectedProductForVariant: null,

            resetCustomerToWalkin() {
                this.selectedCustomerId = '';
                this.customer = {
                    name: '',
                    phone: '',
                    address: '',
                    city: 'Dhaka',
                    email: '',
                    note: ''
                };
                this.$nextTick(() => lucide.createIcons());
            },

            onCustomerSelect() {
                if (!this.selectedCustomerId) {
                    this.resetCustomerToWalkin();
                    return;
                }
                const found = this.customersList.find(c => c.id == this.selectedCustomerId);
                if (found) {
                    this.customer.name = found.name || '';
                    this.customer.phone = found.phone || '';
                    this.customer.address = found.address || '';
                    this.customer.city = found.city || 'Dhaka';
                    this.customer.email = found.email || '';
                }
                this.$nextTick(() => lucide.createIcons());
            },

            async searchProducts() {
                try {
                    let url = `{{ route('admin.pos.search') }}?q=${encodeURIComponent(this.searchQuery)}`;
                    if (this.selectedCategory) {
                        url += `&category_id=${this.selectedCategory}`;
                    }
                    const res = await fetch(url);
                    this.products = await res.json();
                    this.$nextTick(() => lucide.createIcons());
                } catch (e) {
                    console.error('POS Search Error:', e);
                }
            },

            handleBarcodeScan() {
                if (this.products.length > 0) {
                    this.handleProductClick(this.products[0]);
                    this.searchQuery = '';
                }
            },

            handleProductClick(product) {
                if (product.has_variants && product.variants && product.variants.length > 0) {
                    this.selectedProductForVariant = product;
                    this.variantModalOpen = true;
                } else {
                    this.addToCart({
                        product_id: product.id,
                        variant_id: null,
                        name: product.name,
                        variant_name: null,
                        price: product.discount_price && product.discount_price > 0 ? product.discount_price : product.selling_price,
                        quantity: 1
                    });
                }
            },

            addVariantToCart(variant) {
                this.addToCart({
                    product_id: this.selectedProductForVariant.id,
                    variant_id: variant.id,
                    name: this.selectedProductForVariant.name,
                    variant_name: variant.variant_name,
                    price: variant.discount_price && variant.discount_price > 0 ? variant.discount_price : variant.selling_price,
                    quantity: 1
                });
                this.variantModalOpen = false;
            },

            addToCart(item) {
                const existingIndex = this.cart.findIndex(c => c.product_id === item.product_id && c.variant_id === item.variant_id);
                if (existingIndex > -1) {
                    this.cart[existingIndex].quantity += 1;
                } else {
                    this.cart.push(item);
                }
                this.$nextTick(() => lucide.createIcons());
            },

            increaseQty(index) {
                this.cart[index].quantity += 1;
            },

            decreaseQty(index) {
                if (this.cart[index].quantity > 1) {
                    this.cart[index].quantity -= 1;
                } else {
                    this.removeItem(index);
                }
            },

            updateQty(index, val) {
                const q = parseInt(val);
                if (isNaN(q) || q < 1) {
                    this.cart[index].quantity = 1;
                } else {
                    this.cart[index].quantity = q;
                }
            },

            removeItem(index) {
                this.cart.splice(index, 1);
            },

            clearCart() {
                this.cart = [];
                this.discount = 0;
                this.tax = 0;
                this.paidAmount = null;
                this.resetCustomerToWalkin();
            },

            applyQuickDiscount(val, type = 'fixed') {
                if (type === 'percent') {
                    const sub = this.getSubtotal();
                    this.discount = Math.round((sub * val) / 100);
                } else {
                    this.discount = val;
                }
            },

            setExactCash() {
                this.paidAmount = this.getGrandTotal();
            },

            addCash(amount) {
                this.paidAmount = (Number(this.paidAmount) || 0) + amount;
            },

            getSubtotal() {
                return this.cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
            },

            getGrandTotal() {
                const sub = this.getSubtotal();
                const total = (sub - (this.discount || 0)) + (this.tax || 0);
                return Math.max(0, total);
            },

            getChangeAmount() {
                const total = this.getGrandTotal();
                const paid = this.paidAmount !== null && this.paidAmount !== '' ? Number(this.paidAmount) : total;
                return Math.max(0, paid - total);
            },

            getDueAmount() {
                const total = this.getGrandTotal();
                const paid = this.paidAmount !== null && this.paidAmount !== '' ? Number(this.paidAmount) : total;
                return Math.max(0, total - paid);
            },

            async submitSale() {
                if (this.cart.length === 0) return;

                const hasCustomerDetails = !!(this.customer.name.trim() || this.customer.phone.trim() || this.customer.address.trim());

                this.isProcessing = true;

                const finalPaid = this.paidAmount !== null && this.paidAmount !== '' ? Number(this.paidAmount) : this.getGrandTotal();

                const payload = {
                    cart: this.cart,
                    discount: this.discount,
                    tax: this.tax,
                    paid_amount: finalPaid,
                    payment_method: this.paymentMethod,
                    customer_id: this.selectedCustomerId || null,
                    customer_name: hasCustomerDetails ? this.customer.name.trim() : null,
                    customer_phone: hasCustomerDetails ? this.customer.phone.trim() : null,
                    customer_address: hasCustomerDetails ? this.customer.address.trim() : null,
                    customer_city: hasCustomerDetails ? this.customer.city.trim() : null,
                    customer_email: hasCustomerDetails ? this.customer.email.trim() : null,
                    customer_note: hasCustomerDetails ? this.customer.note.trim() : null,
                };

                try {
                    const res = await fetch(`{{ route('admin.pos.checkout') }}`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify(payload)
                    });

                    const data = await res.json();
                    if (data.success) {
                        this.clearCart();
                        // Open thermal receipt in new window for instant printing
                        window.open(data.receipt_url, '_blank', 'width=450,height=700');
                    } else {
                        alert('Error: ' + data.message);
                    }
                } catch (e) {
                    alert('Sale processing failed: ' + e.message);
                } finally {
                    this.isProcessing = false;
                }
            }
        };
    }
</script>
@endpush
