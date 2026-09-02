@extends('layouts.admin')

@section('title', 'POS Counter Terminal')
@section('page-title', 'Point of Sale (POS) Counter')

@section('content')
<div x-data="posApp()" class="h-[calc(100vh-6.5rem)] flex flex-col lg:flex-row gap-6 -m-4 lg:-m-8 p-4 lg:p-6 overflow-hidden">

    <!-- LEFT COLUMN: Product Catalog & Barcode Scanner -->
    <div class="flex-1 flex flex-col bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
        
        <!-- Search & Filter Bar -->
        <div class="p-4 border-b border-slate-200 dark:border-slate-800 space-y-3 bg-slate-50/50 dark:bg-slate-950/40">
            <div class="flex items-center gap-3">
                <div class="relative flex-1">
                    <i data-lucide="scan-barcode" class="w-5 h-5 absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400"></i>
                    <input 
                        type="text" 
                        x-model="searchQuery" 
                        @input.debounce.300ms="searchProducts()"
                        @keydown.enter="handleBarcodeScan()"
                        placeholder="Scan barcode, SKU, or search electronic product..." 
                        class="w-full pl-11 pr-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-transparent outline-none">
                </div>
                <button @click="searchProducts()" class="px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-white text-xs font-semibold flex items-center gap-1.5 transition">
                    <i data-lucide="search" class="w-4 h-4"></i>
                    <span>Search</span>
                </button>
            </div>

            <!-- Categories Horizontal Scroll -->
            <div class="flex items-center gap-2 overflow-x-auto pb-1">
                <button 
                    @click="selectedCategory = null; searchProducts()" 
                    :class="selectedCategory === null ? 'bg-emerald-500 text-slate-950 font-bold shadow-sm' : 'bg-slate-200/80 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-300'"
                    class="px-3 py-1.5 rounded-xl text-xs whitespace-nowrap transition">
                    All Categories
                </button>
                @foreach($categories as $cat)
                <button 
                    @click="selectedCategory = {{ $cat->id }}; searchProducts()" 
                    :class="selectedCategory === {{ $cat->id }} ? 'bg-emerald-500 text-slate-950 font-bold shadow-sm' : 'bg-slate-200/80 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-300'"
                    class="px-3 py-1.5 rounded-xl text-xs whitespace-nowrap transition">
                    {{ $cat->name }}
                </button>
                @endforeach
            </div>
        </div>

        <!-- Products Grid -->
        <div class="flex-1 overflow-y-auto p-4">
            <div class="grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-4 gap-4">
                <template x-for="product in products" :key="product.id">
                    <div 
                        @click="handleProductClick(product)"
                        class="bg-slate-50 dark:bg-slate-800/60 rounded-xl p-3 border border-slate-200 dark:border-slate-700/60 hover:border-emerald-500 hover:shadow-md cursor-pointer transition flex flex-col justify-between group">
                        <div>
                            <div class="relative rounded-lg overflow-hidden aspect-video bg-slate-200 dark:bg-slate-700 mb-2">
                                <img :src="product.thumbnail" :alt="product.name" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                                <span 
                                    x-show="product.stock_quantity <= product.alert_threshold"
                                    class="absolute top-1 right-1 px-1.5 py-0.5 rounded text-[9px] font-bold bg-rose-500 text-white uppercase">
                                    Low
                                </span>
                            </div>
                            <h3 class="text-xs font-semibold text-slate-900 dark:text-white line-clamp-2" x-text="product.name"></h3>
                            <div class="text-[10px] text-slate-500 font-mono mt-0.5" x-text="product.sku"></div>
                        </div>

                        <div class="mt-3 flex items-center justify-between pt-2 border-t border-slate-200/60 dark:border-slate-700/40">
                            <div>
                                <span class="text-xs font-bold text-emerald-600 dark:text-emerald-400 code-font">
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
                <p class="text-xs">No matching components or gadgets found.</p>
            </div>
        </div>

    </div>

    <!-- RIGHT COLUMN: Reactive Checkout Register & Cart -->
    <!-- RIGHT COLUMN: Reactive Checkout Register & Cart -->
    <div class="w-full lg:w-[450px] bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm flex flex-col overflow-hidden">
        
        <!-- Cart Header -->
        <div class="p-3 border-b border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-950/40">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <i data-lucide="shopping-cart" class="w-4 h-4 text-emerald-500"></i>
                    <h2 class="text-xs sm:text-sm font-bold text-slate-900 dark:text-white">Active Sale Terminal</h2>
                </div>
                <button @click="clearCart()" class="text-[11px] text-rose-500 hover:underline font-semibold" x-show="cart.length > 0">
                    Clear Cart
                </button>
            </div>
        </div>

        <!-- Customer Information & Manual Input Panel -->
        <div class="p-3 border-b border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 space-y-2.5">
            <div class="flex items-center justify-between gap-2">
                <div class="flex items-center gap-1.5 text-xs font-bold text-slate-800 dark:text-slate-200">
                    <i data-lucide="user-check" class="w-4 h-4 text-emerald-500"></i>
                    <span>Customer Details</span>
                </div>
                
                <!-- Quick Mode Toggle Pills -->
                <div class="flex items-center bg-slate-100 dark:bg-slate-800 p-0.5 rounded-lg text-[10px] font-bold">
                    <button 
                        type="button"
                        @click="setCustomerMode('walkin')" 
                        :class="customerMode === 'walkin' ? 'bg-white dark:bg-slate-700 text-emerald-600 dark:text-emerald-400 shadow-xs' : 'text-slate-500 hover:text-slate-800 dark:hover:text-slate-200'"
                        class="px-2.5 py-1 rounded-md transition">
                        Walk-in
                    </button>
                    <button 
                        type="button"
                        @click="setCustomerMode('manual')" 
                        :class="customerMode === 'manual' ? 'bg-white dark:bg-slate-700 text-emerald-600 dark:text-emerald-400 shadow-xs' : 'text-slate-500 hover:text-slate-800 dark:hover:text-slate-200'"
                        class="px-2.5 py-1 rounded-md transition">
                        Manual Entry
                    </button>
                </div>
            </div>

            <!-- Existing Customer Quick Search Dropdown -->
            <div class="flex items-center gap-2">
                <div class="relative flex-1">
                    <select 
                        x-model="selectedCustomerId" 
                        @change="onCustomerSelect()" 
                        class="w-full px-2.5 py-1.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/80 text-[11px] text-slate-800 dark:text-slate-200 outline-none focus:ring-1 focus:ring-emerald-500">
                        <option value="">⚡ Select Registered Customer (Auto-fill)...</option>
                        @foreach($customers as $cust)
                        <option value="{{ $cust->id }}">
                            {{ $cust->name }} ({{ $cust->phone }})
                        </option>
                        @endforeach
                    </select>
                </div>
                <button 
                    type="button" 
                    @click="setCustomerMode('manual')" 
                    x-show="customerMode === 'walkin'"
                    class="px-2.5 py-1.5 rounded-xl bg-emerald-50 hover:bg-emerald-100 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-400 text-[10px] font-bold flex items-center gap-1 transition shrink-0"
                    title="Input customer name, address, phone manually">
                    <i data-lucide="user-plus" class="w-3.5 h-3.5"></i>
                    <span>+ New Info</span>
                </button>
            </div>

            <!-- Walk-in Active Notice -->
            <div x-show="customerMode === 'walkin'" class="p-2 rounded-xl bg-slate-50 dark:bg-slate-800/40 border border-dashed border-slate-200 dark:border-slate-700 flex items-center justify-between text-[11px] text-slate-500">
                <span class="flex items-center gap-1.5">
                    <i data-lucide="store" class="w-3.5 h-3.5 text-slate-400"></i>
                    <span>Walk-in Customer (Fast Counter Sale)</span>
                </span>
                <button type="button" @click="setCustomerMode('manual')" class="text-[10px] font-bold text-emerald-600 hover:underline">
                    + Input Details
                </button>
            </div>

            <!-- Manual Customer Details Fields -->
            <div x-show="customerMode === 'manual'" x-transition class="space-y-2 p-2.5 rounded-xl bg-slate-50 dark:bg-slate-800/40 border border-slate-200/80 dark:border-slate-700/80">
                <!-- Name & Phone Row -->
                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="block text-[10px] font-bold text-slate-600 dark:text-slate-400 mb-0.5">
                            Customer Name <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <i data-lucide="user" class="w-3 h-3 absolute left-2 top-1/2 -translate-y-1/2 text-slate-400"></i>
                            <input 
                                type="text" 
                                x-model="customer.name" 
                                placeholder="Customer Name" 
                                class="w-full pl-6 pr-2 py-1.5 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs text-slate-800 dark:text-white outline-none focus:border-emerald-500">
                        </div>
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold text-slate-600 dark:text-slate-400 mb-0.5">
                            Phone Number <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <i data-lucide="phone" class="w-3 h-3 absolute left-2 top-1/2 -translate-y-1/2 text-slate-400"></i>
                            <input 
                                type="text" 
                                x-model="customer.phone" 
                                placeholder="01XXXXXXXXX" 
                                class="w-full pl-6 pr-2 py-1.5 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs text-slate-800 dark:text-white outline-none focus:border-emerald-500 code-font">
                        </div>
                    </div>
                </div>

                <!-- Street / Delivery Address -->
                <div>
                    <label class="block text-[10px] font-bold text-slate-600 dark:text-slate-400 mb-0.5">
                        Customer Address (Street / Area / House) <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <i data-lucide="map-pin" class="w-3 h-3 absolute left-2 top-2 text-slate-400"></i>
                        <textarea 
                            x-model="customer.address" 
                            rows="2" 
                            placeholder="e.g. House #14, Road #3, Sector #11, Uttara" 
                            class="w-full pl-6 pr-2 py-1 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs text-slate-800 dark:text-white outline-none focus:border-emerald-500 resize-none"></textarea>
                    </div>
                </div>

                <!-- City & Email Row -->
                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="block text-[10px] font-bold text-slate-600 dark:text-slate-400 mb-0.5">
                            City / District
                        </label>
                        <input 
                            type="text" 
                            x-model="customer.city" 
                            placeholder="e.g. Dhaka" 
                            class="w-full px-2 py-1.5 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs text-slate-800 dark:text-white outline-none focus:border-emerald-500">
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold text-slate-600 dark:text-slate-400 mb-0.5">
                            Email (Optional)
                        </label>
                        <input 
                            type="email" 
                            x-model="customer.email" 
                            placeholder="customer@email.com" 
                            class="w-full px-2 py-1.5 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs text-slate-800 dark:text-white outline-none focus:border-emerald-500">
                    </div>
                </div>

                <!-- Customer / Order Note -->
                <div>
                    <label class="block text-[10px] font-bold text-slate-600 dark:text-slate-400 mb-0.5">
                        Order Notes / Remarks (Optional)
                    </label>
                    <input 
                        type="text" 
                        x-model="customer.note" 
                        placeholder="e.g. Warranty card attached, special delivery note" 
                        class="w-full px-2 py-1.5 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs text-slate-800 dark:text-white outline-none focus:border-emerald-500">
                </div>

                <div class="flex items-center justify-between pt-1">
                    <span class="text-[10px] text-emerald-600 dark:text-emerald-400 font-semibold flex items-center gap-1">
                        <i data-lucide="check-circle" class="w-3 h-3"></i>
                        <span>Customer will be linked & saved to CRM</span>
                    </span>
                    <button 
                        type="button" 
                        @click="setCustomerMode('walkin')" 
                        class="text-[10px] text-rose-500 hover:underline">
                        Reset to Walk-in
                    </button>
                </div>
            </div>
        </div>

        <!-- Cart Items List -->
        <div class="flex-1 overflow-y-auto p-4 space-y-3">
            <template x-for="(item, index) in cart" :key="index">
                <div class="p-2.5 rounded-xl bg-slate-50 dark:bg-slate-800/50 border border-slate-200/80 dark:border-slate-700/60 flex items-center justify-between gap-3">
                    <div class="flex-1 min-w-0">
                        <h4 class="text-xs font-semibold text-slate-900 dark:text-white truncate" x-text="item.name"></h4>
                        <div class="text-[10px] text-slate-500" x-show="item.variant_name" x-text="item.variant_name"></div>
                        <div class="text-xs font-bold text-emerald-600 dark:text-emerald-400 code-font">
                            ৳<span x-text="item.price"></span>
                        </div>
                    </div>

                    <!-- Quantity Controls -->
                    <div class="flex items-center gap-1.5">
                        <button @click="decreaseQty(index)" class="w-6 h-6 rounded-lg bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300 flex items-center justify-center font-bold text-xs">-</button>
                        <span class="w-8 text-center text-xs font-bold code-font" x-text="item.quantity"></span>
                        <button @click="increaseQty(index)" class="w-6 h-6 rounded-lg bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300 flex items-center justify-center font-bold text-xs">+</button>
                    </div>

                    <div class="text-right min-w-[60px]">
                        <div class="text-xs font-bold text-slate-900 dark:text-white code-font">
                            ৳<span x-text="(item.price * item.quantity).toFixed(2)"></span>
                        </div>
                        <button @click="removeItem(index)" class="text-[10px] text-rose-500 hover:underline">Remove</button>
                    </div>
                </div>
            </template>

            <div x-show="cart.length === 0" class="h-48 flex flex-col items-center justify-center text-slate-400">
                <i data-lucide="shopping-bag" class="w-10 h-10 mb-2 stroke-1"></i>
                <p class="text-xs">Cart is empty. Scan or select products.</p>
            </div>
        </div>

        <!-- Calculation & Checkout Form -->
        <div class="p-4 border-t border-slate-200 dark:border-slate-800 space-y-3 bg-slate-50/80 dark:bg-slate-950/60">
            
            <div class="space-y-1.5 text-xs">
                <div class="flex items-center justify-between text-slate-500">
                    <span>Subtotal:</span>
                    <span class="font-semibold text-slate-800 dark:text-slate-200 code-font">৳<span x-text="getSubtotal().toFixed(2)"></span></span>
                </div>
                <div class="flex items-center justify-between gap-4">
                    <span class="text-slate-500">Discount (৳):</span>
                    <input type="number" x-model.number="discount" class="w-24 px-2 py-1 text-right text-xs rounded border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 code-font">
                </div>
                <div class="flex items-center justify-between gap-4">
                    <span class="text-slate-500">Tax / VAT (৳):</span>
                    <input type="number" x-model.number="tax" class="w-24 px-2 py-1 text-right text-xs rounded border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 code-font">
                </div>
                <div class="flex items-center justify-between text-base font-extrabold pt-2 border-t border-slate-200 dark:border-slate-800">
                    <span class="text-slate-900 dark:text-white">Grand Total:</span>
                    <span class="text-emerald-500 code-font">৳<span x-text="getGrandTotal().toFixed(2)"></span></span>
                </div>
            </div>

            <!-- Payment Method Tender Selector -->
            <div class="grid grid-cols-3 gap-2 pt-2">
                <button 
                    @click="paymentMethod = 'pos_cash'"
                    :class="paymentMethod === 'pos_cash' ? 'bg-emerald-600 text-white font-bold border-emerald-600' : 'bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300 border-slate-200 dark:border-slate-700'"
                    class="py-2 rounded-xl text-xs border flex flex-col items-center gap-1 transition">
                    <i data-lucide="banknote" class="w-4 h-4"></i>
                    <span>Cash</span>
                </button>
                <button 
                    @click="paymentMethod = 'bkash'"
                    :class="paymentMethod === 'bkash' ? 'bg-pink-600 text-white font-bold border-pink-600' : 'bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300 border-slate-200 dark:border-slate-700'"
                    class="py-2 rounded-xl text-xs border flex flex-col items-center gap-1 transition">
                    <i data-lucide="smartphone" class="w-4 h-4"></i>
                    <span>bKash</span>
                </button>
                <button 
                    @click="paymentMethod = 'pos_card'"
                    :class="paymentMethod === 'pos_card' ? 'bg-indigo-600 text-white font-bold border-indigo-600' : 'bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300 border-slate-200 dark:border-slate-700'"
                    class="py-2 rounded-xl text-xs border flex flex-col items-center gap-1 transition">
                    <i data-lucide="credit-card" class="w-4 h-4"></i>
                    <span>Card / POS</span>
                </button>
            </div>

            <!-- Complete & Print Receipt Button -->
            <button 
                @click="submitSale()" 
                :disabled="cart.length === 0 || isProcessing"
                class="w-full py-3 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-500 hover:from-emerald-500 hover:to-teal-400 text-slate-950 font-extrabold text-sm shadow-lg shadow-emerald-500/20 disabled:opacity-50 disabled:cursor-not-allowed transition flex items-center justify-center gap-2">
                <i data-lucide="printer" class="w-5 h-5"></i>
                <span x-text="isProcessing ? 'Processing Transaction...' : 'Complete Sale & Thermal Print'"></span>
            </button>
        </div>

    </div>

    <!-- VARIANT SELECTION MODAL -->
    <div x-show="variantModalOpen" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/70 backdrop-blur-sm" style="display: none;">
        <div @click.away="variantModalOpen = false" class="bg-white dark:bg-slate-900 rounded-2xl max-w-md w-full p-6 border border-slate-200 dark:border-slate-800 shadow-2xl space-y-4">
            <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-800 pb-3">
                <h3 class="text-sm font-bold text-slate-900 dark:text-white" x-text="selectedProductForVariant ? selectedProductForVariant.name : ''"></h3>
                <button @click="variantModalOpen = false" class="text-slate-400 hover:text-white">&times;</button>
            </div>
            
            <p class="text-xs text-slate-500">Select the required PCB color / package specification:</p>

            <div class="space-y-2 max-h-60 overflow-y-auto">
                <template x-for="variant in (selectedProductForVariant ? selectedProductForVariant.variants : [])" :key="variant.id">
                    <div 
                        @click="addVariantToCart(variant)"
                        class="p-3 rounded-xl border border-slate-200 dark:border-slate-800 hover:border-emerald-500 hover:bg-emerald-500/5 cursor-pointer flex items-center justify-between transition">
                        <div>
                            <div class="text-xs font-bold text-slate-900 dark:text-white" x-text="variant.variant_name"></div>
                            <div class="text-[10px] text-slate-500 font-mono" x-text="variant.sku"></div>
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
            customerMode: 'walkin',
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

            setCustomerMode(mode) {
                this.customerMode = mode;
                if (mode === 'walkin') {
                    this.selectedCustomerId = '';
                    this.customer = {
                        name: '',
                        phone: '',
                        address: '',
                        city: 'Dhaka',
                        email: '',
                        note: ''
                    };
                }
                this.$nextTick(() => lucide.createIcons());
            },

            onCustomerSelect() {
                if (!this.selectedCustomerId) {
                    this.setCustomerMode('walkin');
                    return;
                }
                const found = this.customersList.find(c => c.id == this.selectedCustomerId);
                if (found) {
                    this.customer.name = found.name || '';
                    this.customer.phone = found.phone || '';
                    this.customer.address = found.address || '';
                    this.customer.city = found.city || 'Dhaka';
                    this.customer.email = found.email || '';
                    this.customerMode = 'manual';
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

            removeItem(index) {
                this.cart.splice(index, 1);
            },

            clearCart() {
                this.cart = [];
                this.discount = 0;
                this.tax = 0;
                this.customerMode = 'walkin';
                this.selectedCustomerId = '';
                this.customer = {
                    name: '',
                    phone: '',
                    address: '',
                    city: 'Dhaka',
                    email: '',
                    note: ''
                };
            },

            getSubtotal() {
                return this.cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
            },

            getGrandTotal() {
                const sub = this.getSubtotal();
                const total = (sub - (this.discount || 0)) + (this.tax || 0);
                return Math.max(0, total);
            },

            async submitSale() {
                if (this.cart.length === 0) return;

                // Validate manual customer input if in manual mode
                if (this.customerMode === 'manual') {
                    if (!this.customer.name.trim()) {
                        alert('Please enter Customer Name before completing the sale.');
                        return;
                    }
                    if (!this.customer.phone.trim()) {
                        alert('Please enter Customer Phone number before completing the sale.');
                        return;
                    }
                }

                this.isProcessing = true;

                const payload = {
                    cart: this.cart,
                    discount: this.discount,
                    tax: this.tax,
                    paid_amount: this.getGrandTotal(),
                    payment_method: this.paymentMethod,
                    customer_id: this.selectedCustomerId || null,
                    customer_name: this.customerMode === 'manual' ? this.customer.name.trim() : null,
                    customer_phone: this.customerMode === 'manual' ? this.customer.phone.trim() : null,
                    customer_address: this.customerMode === 'manual' ? this.customer.address.trim() : null,
                    customer_city: this.customerMode === 'manual' ? this.customer.city.trim() : null,
                    customer_email: this.customerMode === 'manual' ? this.customer.email.trim() : null,
                    customer_note: this.customerMode === 'manual' ? this.customer.note.trim() : null,
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
                        // Open receipt in new tab / window for instant printing
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
