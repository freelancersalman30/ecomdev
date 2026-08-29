@extends('layouts.app')

@section('title', 'Shopping Cart - DREAMERS PCB')

@section('content')
<div x-data="cartPageApp()" class="max-w-7xl mx-auto px-4 py-8 space-y-6">

    <div class="flex items-center gap-2 text-xs text-slate-500">
        <a href="{{ route('home') }}" class="hover:text-daraz-orange">Home</a>
        <span>/</span>
        <span class="text-slate-900 font-bold">Shopping Cart</span>
    </div>

    <div class="flex items-center justify-between">
        <h1 class="text-xl font-black text-slate-900 flex items-center gap-2">
            <i data-lucide="shopping-cart" class="w-6 h-6 text-daraz-orange"></i>
            <span>Your Shopping Cart (<span x-text="cartCount"></span> items)</span>
        </h1>
        <a href="{{ route('shop.index') }}" class="text-xs font-bold text-daraz-orange hover:underline">
            &larr; Continue Shopping
        </a>
    </div>

    <!-- Empty Cart State -->
    <template x-if="cartItems.length === 0">
        <div class="bg-white rounded-3xl p-12 text-center border border-slate-200 shadow-sm space-y-4 max-w-lg mx-auto">
            <div class="w-20 h-20 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto">
                <i data-lucide="shopping-bag" class="w-10 h-10"></i>
            </div>
            <h3 class="text-base font-bold text-slate-900">Your shopping cart is currently empty</h3>
            <p class="text-xs text-slate-500">Explore our wide selection of original PCB development boards, ICs, and repair tools.</p>
            <a href="{{ route('shop.index') }}" class="inline-block px-6 py-3 rounded-2xl bg-daraz-orange hover:bg-daraz-orangeHover text-white font-extrabold text-xs shadow-lg transition">
                Start Shopping Now
            </a>
        </div>
    </template>

    <!-- Cart Layout with Items & Summary (2 Cols) -->
    <div x-show="cartItems.length > 0" class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        
        <!-- Cart Items Table (8 Cols) -->
        <div class="lg:col-span-8 bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden divide-y divide-slate-100">
            <div class="p-4 bg-slate-50 border-b border-slate-200 text-xs font-bold text-slate-500 uppercase tracking-wider grid grid-cols-12 gap-4">
                <span class="col-span-6">Item Description</span>
                <span class="col-span-2 text-center">Unit Price</span>
                <span class="col-span-2 text-center">Quantity</span>
                <span class="col-span-2 text-right">Subtotal</span>
            </div>

            <template x-for="item in cartItems" :key="item.cart_key">
                <div class="p-4 sm:p-5 grid grid-cols-12 gap-4 items-center">
                    
                    <!-- Product Info (6 Cols) -->
                    <div class="col-span-12 sm:col-span-6 flex items-center gap-3">
                        <img :src="item.thumbnail" :alt="item.name" class="w-16 h-16 rounded-xl object-cover border flex-shrink-0">
                        <div class="min-w-0">
                            <h4 class="text-xs sm:text-sm font-bold text-slate-900 line-clamp-1" x-text="item.name"></h4>
                            <div class="text-[11px] text-emerald-600 font-semibold mt-0.5" x-show="item.variant_name" x-text="item.variant_name"></div>
                            <div class="text-[10px] text-slate-400 font-mono mt-0.5" x-text="`SKU: ${item.sku}`"></div>
                            <button @click="removeItem(item.cart_key)" class="text-[11px] text-rose-500 hover:underline font-semibold mt-1 inline-block">
                                Remove
                            </button>
                        </div>
                    </div>

                    <!-- Unit Price (2 Cols) -->
                    <div class="col-span-4 sm:col-span-2 text-center font-bold text-xs sm:text-sm text-slate-800 code-font">
                        ৳<span x-text="item.price.toFixed(2)"></span>
                    </div>

                    <!-- Qty Modifier (2 Cols) -->
                    <div class="col-span-4 sm:col-span-2 flex justify-center">
                        <div class="flex items-center border border-slate-200 rounded-xl p-0.5 bg-slate-50">
                            <button @click="updateQty(item.cart_key, item.quantity - 1)" class="w-6 h-6 rounded-lg flex items-center justify-center font-bold text-xs text-slate-600 hover:bg-slate-200">-</button>
                            <span class="w-8 text-center text-xs font-bold code-font" x-text="item.quantity"></span>
                            <button @click="updateQty(item.cart_key, item.quantity + 1)" class="w-6 h-6 rounded-lg flex items-center justify-center font-bold text-xs text-slate-600 hover:bg-slate-200">+</button>
                        </div>
                    </div>

                    <!-- Line Subtotal (2 Cols) -->
                    <div class="col-span-4 sm:col-span-2 text-right font-black text-xs sm:text-sm text-daraz-orange code-font">
                        ৳<span x-text="item.subtotal.toFixed(2)"></span>
                    </div>

                </div>
            </template>
        </div>

        <!-- Order Summary & Checkout Card (4 Cols) -->
        <div class="lg:col-span-4 space-y-4">
            
            <!-- Coupon Code Form -->
            <div class="bg-white rounded-3xl p-5 border border-slate-200 shadow-sm space-y-3">
                <h3 class="text-xs font-bold text-slate-900 uppercase tracking-wider flex items-center gap-1.5">
                    <i data-lucide="ticket-percent" class="w-4 h-4 text-emerald-500"></i>
                    <span>Apply Promo Coupon</span>
                </h3>

                <div class="flex gap-2">
                    <input 
                        type="text" 
                        x-model="couponCode" 
                        placeholder="e.g. FLASH2026" 
                        class="flex-1 px-3.5 py-2 rounded-xl border border-slate-200 bg-slate-50 text-xs font-mono font-bold uppercase outline-none focus:ring-2 focus:ring-emerald-500">
                    <button 
                        @click="applyCouponCode()" 
                        :disabled="isApplyingCoupon"
                        class="px-4 py-2 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-xs font-bold transition">
                        Apply
                    </button>
                </div>

                <div x-show="couponMessage" :class="couponSuccess ? 'text-emerald-600' : 'text-rose-500'" class="text-[11px] font-semibold" x-text="couponMessage"></div>
            </div>

            <!-- Pricing Summary Card -->
            <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-sm space-y-4">
                <h3 class="text-sm font-black text-slate-900 uppercase tracking-wider border-b pb-3">Order Summary</h3>

                <div class="space-y-2 text-xs">
                    <div class="flex justify-between text-slate-600">
                        <span>Items Subtotal:</span>
                        <span class="font-bold text-slate-900 code-font">৳<span x-text="cartSummary.subtotal ? cartSummary.subtotal.toFixed(2) : '0.00'"></span></span>
                    </div>

                    <div x-show="cartSummary.discount > 0" class="flex justify-between text-emerald-600 font-bold">
                        <span>Promo Coupon Discount:</span>
                        <span class="code-font">-৳<span x-text="cartSummary.discount ? cartSummary.discount.toFixed(2) : '0.00'"></span></span>
                    </div>

                    <div class="flex justify-between text-slate-500 pt-1 border-t border-slate-100 text-[11px]">
                        <span>Estimated Shipping:</span>
                        <span class="text-slate-700 font-medium">Calculated at Checkout</span>
                    </div>

                    <div class="flex justify-between text-base font-black text-slate-900 pt-3 border-t border-slate-200">
                        <span>Total Payable:</span>
                        <span class="text-daraz-orange code-font text-lg">৳<span x-text="cartSummary.payable ? cartSummary.payable.toFixed(2) : '0.00'"></span></span>
                    </div>
                </div>

                <a href="{{ route('checkout.index') }}" class="w-full py-3.5 rounded-2xl bg-gradient-to-r from-daraz-orange to-amber-500 hover:from-daraz-orangeHover hover:to-amber-600 text-white font-extrabold text-xs uppercase tracking-wider text-center shadow-xl shadow-daraz-orange/20 transition flex items-center justify-center gap-2">
                    <span>Proceed to Checkout</span>
                    <i data-lucide="arrow-right" class="w-4 h-4"></i>
                </a>
            </div>

        </div>

    </div>

</div>
@endsection

@push('scripts')
<script>
    function cartPageApp() {
        return {
            couponCode: '',
            couponMessage: '',
            couponSuccess: false,
            isApplyingCoupon: false,

            async applyCouponCode() {
                if (!this.couponCode) return;
                this.isApplyingCoupon = true;
                this.couponMessage = '';

                try {
                    const res = await fetch(`{{ route('cart.coupon.apply') }}`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ code: this.couponCode })
                    });
                    const data = await res.json();
                    if (data.success) {
                        this.couponSuccess = true;
                        this.couponMessage = data.message;
                        this.cartSummary = data.summary;
                    } else {
                        this.couponSuccess = false;
                        this.couponMessage = data.message || 'Invalid coupon';
                    }
                } catch (e) {
                    this.couponSuccess = false;
                    this.couponMessage = 'Failed to apply coupon.';
                } finally {
                    this.isApplyingCoupon = false;
                }
            }
        };
    }
</script>
@endpush
