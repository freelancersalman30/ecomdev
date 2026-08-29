@extends('layouts.app')

@section('title', 'Fast One-Page Checkout - DREAMERS PCB')

@section('content')
<div x-data="checkoutPageApp()" class="max-w-7xl mx-auto px-4 py-8 space-y-6">

    <div class="flex items-center gap-2 text-xs text-slate-500">
        <a href="{{ route('home') }}" class="hover:text-daraz-orange">Home</a>
        <span>/</span>
        <a href="{{ route('cart.index') }}" class="hover:text-daraz-orange">Cart</a>
        <span>/</span>
        <span class="text-slate-900 font-bold">Fast One-Page Checkout</span>
    </div>

    <h1 class="text-xl font-black text-slate-900 flex items-center gap-2">
        <i data-lucide="shield-check" class="w-6 h-6 text-emerald-600"></i>
        <span>Secure One-Page Checkout</span>
    </h1>

    <form method="POST" action="{{ route('checkout.process') }}" class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        @csrf

        <!-- Left: Shipping & Payment Details (7 Cols) -->
        <div class="lg:col-span-7 space-y-6">
            
            <!-- 1. Delivery Address Card -->
            <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-sm space-y-4">
                <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2 border-b pb-3">
                    <i data-lucide="map-pin" class="w-4 h-4 text-daraz-orange"></i>
                    <span>1. Shipping Destination & Customer Info</span>
                </h3>

                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Full Name *</label>
                        <input type="text" name="shipping_name" value="{{ old('shipping_name') }}" required placeholder="e.g. Salman Chowdhury" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-slate-50 text-xs font-semibold outline-none focus:ring-2 focus:ring-daraz-orange/20">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Mobile Phone Number (11 Digits) *</label>
                        <input type="tel" name="shipping_phone" value="{{ old('shipping_phone') }}" required placeholder="01711223344" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-slate-50 text-xs font-mono font-bold outline-none focus:ring-2 focus:ring-daraz-orange/20">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Delivery City / District *</label>
                        <input type="text" name="shipping_city" value="{{ old('shipping_city', 'Dhaka') }}" required placeholder="e.g. Dhaka / Chittagong / Rajshahi" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-slate-50 text-xs outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Full Delivery Address (House / Road / Area) *</label>
                        <textarea name="shipping_address" rows="2" required placeholder="Detailed address for courier dispatch..." class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-slate-50 text-xs outline-none">{{ old('shipping_address') }}</textarea>
                    </div>

                    <!-- Delivery Area Selector with Live Delivery Fee Update -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-2">Select Delivery Location & Shipping Charge *</label>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <label class="p-3.5 rounded-2xl border-2 cursor-pointer flex items-center justify-between transition" :class="shippingArea === 'inside_dhaka' ? 'border-daraz-orange bg-daraz-light text-slate-900' : 'border-slate-200 text-slate-700'">
                                <div class="flex items-center gap-2">
                                    <input type="radio" name="shipping_area" value="inside_dhaka" :checked="shippingArea === 'inside_dhaka'" @change="shippingArea = 'inside_dhaka'; shippingCharge = {{ $insideDhaka }}" class="text-daraz-orange focus:ring-daraz-orange">
                                    <span class="text-xs font-bold">Inside Dhaka (1-2 Days)</span>
                                </div>
                                <span class="font-bold code-font text-xs">৳{{ $insideDhaka }}</span>
                            </label>

                            <label class="p-3.5 rounded-2xl border-2 cursor-pointer flex items-center justify-between transition" :class="shippingArea === 'outside_dhaka' ? 'border-daraz-orange bg-daraz-light text-slate-900' : 'border-slate-200 text-slate-700'">
                                <div class="flex items-center gap-2">
                                    <input type="radio" name="shipping_area" value="outside_dhaka" :checked="shippingArea === 'outside_dhaka'" @change="shippingArea = 'outside_dhaka'; shippingCharge = {{ $outsideDhaka }}" class="text-daraz-orange focus:ring-daraz-orange">
                                    <span class="text-xs font-bold">Outside Dhaka (2-4 Days)</span>
                                </div>
                                <span class="font-bold code-font text-xs">৳{{ $outsideDhaka }}</span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 2. Payment Method Card -->
            <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-sm space-y-4">
                <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2 border-b pb-3">
                    <i data-lucide="credit-card" class="w-4 h-4 text-emerald-600"></i>
                    <span>2. Payment Option</span>
                </h3>

                <div class="space-y-3">
                    <label class="p-4 rounded-2xl border-2 cursor-pointer flex items-center justify-between transition" :class="paymentMethod === 'cash_on_delivery' ? 'border-emerald-500 bg-emerald-50 text-slate-900' : 'border-slate-200 text-slate-700'">
                        <div class="flex items-center gap-3">
                            <input type="radio" name="payment_method" value="cash_on_delivery" :checked="paymentMethod === 'cash_on_delivery'" @change="paymentMethod = 'cash_on_delivery'" class="text-emerald-600 focus:ring-emerald-500">
                            <div>
                                <div class="text-xs font-extrabold text-slate-900">Cash On Delivery (COD)</div>
                                <div class="text-[11px] text-slate-500">Pay cash in hand when you receive the hardware parcel</div>
                            </div>
                        </div>
                        <i data-lucide="banknote" class="w-5 h-5 text-emerald-600"></i>
                    </label>

                    <label class="p-4 rounded-2xl border-2 cursor-pointer flex items-center justify-between transition" :class="paymentMethod === 'bkash' ? 'border-pink-500 bg-pink-50 text-slate-900' : 'border-slate-200 text-slate-700'">
                        <div class="flex items-center gap-3">
                            <input type="radio" name="payment_method" value="bkash" :checked="paymentMethod === 'bkash'" @change="paymentMethod = 'bkash'" class="text-pink-600 focus:ring-pink-500">
                            <div>
                                <div class="text-xs font-extrabold text-slate-900">bKash Online Payment</div>
                                <div class="text-[11px] text-slate-500">Pay via bKash Merchant Gateway / Personal Wallet</div>
                            </div>
                        </div>
                        <span class="font-bold text-xs text-pink-600 font-mono">bKash</span>
                    </label>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Order Notes (Optional instructions for delivery guy)</label>
                    <input type="text" name="notes" placeholder="e.g. Call before delivery or leave with security" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-slate-50 text-xs outline-none">
                </div>
            </div>

        </div>

        <!-- Right: Order Review & Instant Placement (5 Cols) -->
        <div class="lg:col-span-5 bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-sm space-y-6">
            <h3 class="text-sm font-black text-slate-900 uppercase tracking-wider border-b pb-3 flex items-center justify-between">
                <span>Order Review</span>
                <span class="text-xs text-slate-400 font-normal">{{ count($cart) }} line items</span>
            </h3>

            <!-- Mini Items List -->
            <div class="max-h-60 overflow-y-auto divide-y divide-slate-100 pr-1 text-xs">
                @foreach($cart as $item)
                <div class="py-3 flex items-center justify-between gap-3">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <img src="{{ $item['thumbnail'] }}" alt="{{ $item['name'] }}" class="w-10 h-10 rounded-lg object-cover border flex-shrink-0">
                        <div class="truncate">
                            <div class="font-bold text-slate-900 truncate">{{ $item['name'] }}</div>
                            <div class="text-[11px] text-slate-400">{{ $item['quantity'] }}x ৳{{ number_format($item['price'], 2) }}</div>
                        </div>
                    </div>
                    <div class="font-black text-slate-900 code-font whitespace-nowrap">
                        ৳{{ number_format($item['subtotal'], 2) }}
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Financial Calculation Breakdown -->
            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-2 text-xs">
                <div class="flex justify-between text-slate-600">
                    <span>Items Subtotal:</span>
                    <span class="font-bold text-slate-900 code-font">৳{{ number_format($subtotal, 2) }}</span>
                </div>

                @if($discount > 0)
                <div class="flex justify-between text-emerald-600 font-bold">
                    <span>Coupon Discount ({{ $coupon['code'] }}):</span>
                    <span class="code-font">-৳{{ number_format($discount, 2) }}</span>
                </div>
                @endif

                <div class="flex justify-between text-slate-600">
                    <span>Shipping Fee:</span>
                    <span class="font-bold text-slate-900 code-font">+৳<span x-text="shippingCharge.toFixed(2)"></span></span>
                </div>

                <div class="flex justify-between text-base font-black text-slate-900 pt-3 border-t border-slate-200">
                    <span>Total Amount Payable:</span>
                    <span class="text-daraz-orange code-font text-xl">৳<span x-text="(subtotal - discount + shippingCharge).toFixed(2)"></span></span>
                </div>
            </div>

            <!-- Submit Order Button -->
            <button type="submit" class="w-full py-4 rounded-2xl bg-gradient-to-r from-daraz-orange to-amber-500 hover:from-daraz-orangeHover hover:to-amber-600 text-white font-black text-sm uppercase tracking-wider shadow-xl shadow-daraz-orange/25 transition transform active:scale-95 flex items-center justify-center gap-2">
                <i data-lucide="check-circle" class="w-5 h-5"></i>
                <span>Place Order Now</span>
            </button>

            <!-- Trust Badges Strip -->
            <div class="text-[11px] text-slate-400 text-center space-y-1 pt-2">
                <p>🔒 256-Bit SSL Encrypted & Secure Checkout</p>
                <p>By placing this order, you agree to our 7-Day Easy Return Policy.</p>
            </div>
        </div>

    </form>

</div>
@endsection

@push('scripts')
<script>
    function checkoutPageApp() {
        return {
            subtotal: {{ (float) $subtotal }},
            discount: {{ (float) $discount }},
            shippingArea: 'inside_dhaka',
            shippingCharge: {{ (float) $insideDhaka }},
            paymentMethod: 'cash_on_delivery'
        };
    }
</script>
@endpush
