<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $landingPage->title }} - DREAMERS PCB Flash Sale</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        body { font-family: 'Inter', sans-serif; }
        .code-font { font-family: 'JetBrains Mono', monospace; }
        :root { --theme-accent: {{ $landingPage->theme_color ?? '#0ea5e9' }}; }
    </style>

    @if($landingPage->fb_pixel_id)
    <!-- Meta Pixel Code -->
    <script>
    !function(f,b,e,v,n,t,s)
    {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
    n.callMethod.apply(n,arguments):n.queue.push(arguments)};
    if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
    n.queue=[];t=b.createElement(e);t.async=!0;
    t.src=v;s=b.getElementsByTagName(e)[0];
    s.parentNode.insertBefore(t,s)}(window, document,'script',
    'https://connect.facebook.net/en_US/fbevents.js');
    fbq('init', '{{ $landingPage->fb_pixel_id }}');
    fbq('track', 'PageView');
    fbq('track', 'ViewContent', { content_name: '{{ $landingPage->product->name }}' });
    </script>
    @endif
</head>
<body x-data="checkoutApp()" class="bg-slate-950 text-slate-100 antialiased selection:bg-emerald-500 selection:text-slate-950">

    <!-- Flash Sale Emergency Countdown Topbar -->
    <div class="bg-gradient-to-r from-rose-600 via-amber-600 to-rose-600 text-white py-2 px-4 text-center text-xs font-black tracking-wider uppercase shadow-md flex items-center justify-center gap-2">
        <i data-lucide="flame" class="w-4 h-4 text-amber-300 animate-bounce"></i>
        <span>সীমিত সময়ের জন্য বিশেষ অফার! স্টক শেষ হওয়ার আগেই অর্ডার করুন।</span>
    </div>

    <!-- Main Container -->
    <div class="max-w-4xl mx-auto px-4 py-8 space-y-12">
        
        <!-- Header & Branding -->
        <header class="text-center space-y-2">
            <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-slate-800/80 border border-slate-700 text-emerald-400 text-xs font-bold tracking-wider uppercase shadow-sm">
                <i data-lucide="cpu" class="w-4 h-4"></i>
                <span>DREAMERS PCB - Verified Hardware</span>
            </div>
            <h1 class="text-2xl sm:text-4xl font-black text-white leading-tight tracking-tight max-w-3xl mx-auto">
                {{ $landingPage->headline ?? $landingPage->title }}
            </h1>
            <p class="text-sm sm:text-base text-slate-400 max-w-2xl mx-auto">
                {{ $landingPage->sub_headline ?? $landingPage->product->short_description }}
            </p>
        </header>

        <!-- Product Visual Showcase & Video Embed -->
        <div class="bg-slate-900 rounded-3xl p-4 sm:p-6 border border-slate-800 shadow-2xl space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-center">
                
                <!-- HD Thumbnail Display -->
                <div class="relative rounded-2xl overflow-hidden aspect-square bg-slate-800 border border-slate-700/80 shadow-lg">
                    <img src="{{ $landingPage->product->thumbnail }}" alt="{{ $landingPage->product->name }}" class="w-full h-full object-cover">
                    <div class="absolute top-3 left-3 px-3 py-1 rounded-xl bg-rose-600 text-white text-xs font-black uppercase shadow-lg">
                        Flash Sale
                    </div>
                </div>

                <!-- Key Value & Pricing Box -->
                <div class="space-y-5">
                    <div class="space-y-2">
                        <div class="flex items-baseline gap-3">
                            <span class="text-3xl sm:text-4xl font-black text-emerald-400 code-font">
                                ৳{{ number_format($landingPage->product->effective_price, 2) }}
                            </span>
                            @if($landingPage->product->discount_price)
                            <span class="text-lg text-slate-500 line-through code-font">
                                ৳{{ number_format($landingPage->product->selling_price, 2) }}
                            </span>
                            @endif
                        </div>
                        <p class="text-xs text-slate-400">ইনসাইড ঢাকা ডেলিভারি চার্জ মাত্র ৳৭০ | সারা বাংলাদেশে হোম ডেলিভারি</p>
                    </div>

                    <!-- Instant Jump CTA -->
                    <a href="#checkout-section" class="w-full py-4 rounded-2xl bg-gradient-to-r from-emerald-500 to-teal-400 hover:from-emerald-400 hover:to-teal-300 text-slate-950 font-black text-base uppercase tracking-wider shadow-xl shadow-emerald-500/25 transition transform active:scale-95 flex items-center justify-center gap-2 text-center">
                        <i data-lucide="shopping-bag" class="w-5 h-5"></i>
                        <span>অর্ডার করতে এখানে ক্লিক করুন</span>
                    </a>

                    <!-- Warranty & Trust Badges -->
                    <div class="grid grid-cols-2 gap-3 pt-3 border-t border-slate-800 text-xs text-slate-300">
                        <div class="flex items-center gap-2">
                            <i data-lucide="shield-check" class="w-5 h-5 text-emerald-400 flex-shrink-0"></i>
                            <span>{{ $landingPage->product->warranty ?? '১০০% অরিজিনাল কম্পোনেন্ট' }}</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <i data-lucide="truck" class="w-5 h-5 text-sky-400 flex-shrink-0"></i>
                            <span>ক্যাশ অন ডেলিভারি (COD)</span>
                        </div>
                    </div>
                </div>

            </div>

            <!-- YouTube Video Embed (If configured) -->
            @if($landingPage->video_url)
            <div class="pt-4 border-t border-slate-800">
                <h3 class="text-sm font-bold text-slate-300 mb-3 flex items-center gap-2">
                    <i data-lucide="play-circle" class="w-4 h-4 text-rose-500"></i>
                    <span>ভিডিও ডেমো ও প্রজেক্ট রিভিউ:</span>
                </h3>
                <div class="relative aspect-video rounded-2xl overflow-hidden border border-slate-800">
                    <iframe src="{{ $landingPage->video_url }}" class="w-full h-full" allowfullscreen></iframe>
                </div>
            </div>
            @endif
        </div>

        <!-- Technical Features & Specifications -->
        @if(!empty($landingPage->features_list))
        <div class="bg-slate-900 rounded-3xl p-6 sm:p-8 border border-slate-800 space-y-6">
            <h2 class="text-xl font-bold text-white text-center">এই প্রডাক্টটি কেন ব্যবহার করবেন?</h2>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                @foreach($landingPage->features_list as $feature)
                <div class="p-4 rounded-2xl bg-slate-800/50 border border-slate-700/60 flex items-start gap-3">
                    <div class="w-6 h-6 rounded-full bg-emerald-500/20 text-emerald-400 flex items-center justify-center flex-shrink-0 mt-0.5">
                        <i data-lucide="check" class="w-4 h-4"></i>
                    </div>
                    <span class="text-xs sm:text-sm text-slate-200 font-medium">{{ $feature }}</span>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- DIRECT FAST CASH ON DELIVERY CHECKOUT FORM -->
        <div id="checkout-section" class="bg-gradient-to-b from-slate-900 to-slate-950 rounded-3xl p-6 sm:p-10 border-2 border-emerald-500/40 shadow-2xl space-y-6">
            
            <div class="text-center space-y-1">
                <h2 class="text-2xl font-black text-white">অর্ডার কনফার্ম করতে ফর্মটি পূরণ করুন</h2>
                <p class="text-xs text-slate-400">কোন অগ্রিম পেমেন্ট ছাড়া পণ্য হাতে পেয়ে সম্পূর্ণ মূল্য পরিশোধ করুন</p>
            </div>

            <!-- Form -->
            <form method="POST" action="{{ route('admin.pos.checkout') }}" class="space-y-5" @submit="handleCheckout($event)">
                @csrf
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-300 mb-1">আপনার পূর্ণ নাম *</label>
                        <input type="text" x-model="name" required placeholder="আপনার নাম লিখুন..." class="w-full px-4 py-3 rounded-xl border border-slate-700 bg-slate-800 text-sm text-white outline-none focus:ring-2 focus:ring-emerald-500">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-300 mb-1">মোবাইল নাম্বার (১১ ডিজিট) *</label>
                        <input type="tel" x-model="phone" required placeholder="017XXXXXXXX" class="w-full px-4 py-3 rounded-xl border border-slate-700 bg-slate-800 text-sm font-mono text-white outline-none focus:ring-2 focus:ring-emerald-500">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-300 mb-1">সম্পূর্ণ ডেলিভারি ঠিকানা (বাসা/রোড/এলাকা/থানা/জেলা) *</label>
                        <textarea x-model="address" rows="2" required placeholder="আপনার সম্পূর্ণ ঠিকানা এখানে লিখুন..." class="w-full px-4 py-3 rounded-xl border border-slate-700 bg-slate-800 text-sm text-white outline-none focus:ring-2 focus:ring-emerald-500"></textarea>
                    </div>

                    <!-- Delivery Area Selector -->
                    <div>
                        <label class="block text-xs font-bold text-slate-300 mb-2">ডেলিভারি এরিয়া নির্বাচন করুন *</label>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <label class="p-3.5 rounded-xl border cursor-pointer flex items-center justify-between transition" :class="shippingCharge === 70 ? 'bg-emerald-500/10 border-emerald-500 text-emerald-400' : 'bg-slate-800/60 border-slate-700 text-slate-300'">
                                <div class="flex items-center gap-2">
                                    <input type="radio" name="shipping" :checked="shippingCharge === 70" @change="shippingCharge = 70; city = 'Dhaka'" class="text-emerald-500">
                                    <span class="text-xs font-bold">ঢাকার ভিতরে</span>
                                </div>
                                <span class="font-bold code-font">৳৭০</span>
                            </label>
                            <label class="p-3.5 rounded-xl border cursor-pointer flex items-center justify-between transition" :class="shippingCharge === 130 ? 'bg-emerald-500/10 border-emerald-500 text-emerald-400' : 'bg-slate-800/60 border-slate-700 text-slate-300'">
                                <div class="flex items-center gap-2">
                                    <input type="radio" name="shipping" :checked="shippingCharge === 130" @change="shippingCharge = 130; city = 'Outside Dhaka'" class="text-emerald-500">
                                    <span class="text-xs font-bold">ঢাকার বাইরে (সারাদেশ)</span>
                                </div>
                                <span class="font-bold code-font">৳১৩০</span>
                            </label>
                        </div>
                    </div>

                    <!-- Quantity Selector -->
                    <div class="flex items-center justify-between p-3.5 rounded-xl bg-slate-800/60 border border-slate-700">
                        <span class="text-xs font-bold text-slate-300">প্রডাক্টের পরিমাণ (Quantity):</span>
                        <div class="flex items-center gap-3">
                            <button type="button" @click="quantity = Math.max(1, quantity - 1)" class="w-8 h-8 rounded-lg bg-slate-700 text-white font-bold flex items-center justify-center">-</button>
                            <span class="w-8 text-center font-bold text-base code-font" x-text="quantity"></span>
                            <button type="button" @click="quantity = quantity + 1" class="w-8 h-8 rounded-lg bg-slate-700 text-white font-bold flex items-center justify-center">+</button>
                        </div>
                    </div>
                </div>

                <!-- Order Pricing Breakdown Summary -->
                <div class="p-4 rounded-2xl bg-slate-950 border border-slate-800 space-y-2 text-xs">
                    <div class="flex justify-between text-slate-400">
                        <span>প্রডাক্ট মূল্য ({{ $landingPage->product->name }}):</span>
                        <span class="font-bold text-white code-font">৳<span x-text="(unitPrice * quantity).toFixed(2)"></span></span>
                    </div>
                    <div class="flex justify-between text-slate-400">
                        <span>ডেলিভারি চার্জ:</span>
                        <span class="font-bold text-white code-font">+৳<span x-text="shippingCharge.toFixed(2)"></span></span>
                    </div>
                    <div class="flex justify-between text-base font-black pt-2 border-t border-slate-800 text-white">
                        <span>সর্বমোট মূল্য (Total Payable):</span>
                        <span class="text-emerald-400 code-font">৳<span x-text="((unitPrice * quantity) + shippingCharge).toFixed(2)"></span></span>
                    </div>
                </div>

                <!-- Submit Button -->
                <button type="submit" :disabled="isSubmitting" class="w-full py-4 rounded-2xl bg-gradient-to-r from-emerald-500 to-teal-400 hover:from-emerald-400 hover:to-teal-300 text-slate-950 font-black text-lg uppercase tracking-wider shadow-2xl shadow-emerald-500/30 transition transform active:scale-95 flex items-center justify-center gap-2">
                    <i data-lucide="check-circle" class="w-6 h-6"></i>
                    <span x-text="isSubmitting ? 'অর্ডার প্রসেস হচ্ছে...' : 'অর্ডার কনফার্ম করুন (ক্যাশ অন ডেলিভারি)'"></span>
                </button>

            </form>

        </div>

        <!-- Footer -->
        <footer class="text-center text-xs text-slate-500 space-y-1 pb-8">
            <p>&copy; {{ date('Y') }} DREAMERS PCB. All rights reserved.</p>
            <p>Customer Support: +880 1700-112233 | Multiplan Center, Dhaka</p>
        </footer>

    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            lucide.createIcons();
        });

        function checkoutApp() {
            return {
                unitPrice: {{ (float) $landingPage->product->effective_price }},
                productId: {{ $landingPage->product->id }},
                quantity: 1,
                shippingCharge: 70,
                city: 'Dhaka',
                name: '',
                phone: '',
                address: '',
                isSubmitting: false,

                async handleCheckout(e) {
                    e.preventDefault();
                    if (!this.name || !this.phone || !this.address) return;
                    this.isSubmitting = true;

                    // Trigger FB Pixel Purchase Event if configured
                    if (typeof fbq === 'function') {
                        fbq('track', 'InitiateCheckout', {
                            content_name: '{{ $landingPage->product->name }}',
                            value: (this.unitPrice * this.quantity) + this.shippingCharge,
                            currency: 'BDT'
                        });
                    }

                    const payload = {
                        customer_name: this.name,
                        customer_phone: this.phone,
                        shipping_address: this.address,
                        shipping_city: this.city,
                        shipping_charge: this.shippingCharge,
                        paid_amount: 0,
                        discount: 0,
                        payment_method: 'cash_on_delivery',
                        cart: [
                            {
                                product_id: this.productId,
                                variant_id: null,
                                quantity: this.quantity
                            }
                        ]
                    };

                    try {
                        const res = await fetch(`{{ route('admin.pos.checkout') }}`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify(payload)
                        });

                        const data = await res.json();
                        if (data.success) {
                            alert('অভিনন্দন! আপনার অর্ডারটি সফলভাবে গ্রহণ করা হয়েছে। অর্ডার আইডি: ' + data.order_no);
                            window.location.reload();
                        } else {
                            alert('Error: ' + data.message);
                        }
                    } catch (err) {
                        alert('অর্ডার সম্পন্ন করতে সমস্যা হয়েছে। অনুগ্রহ করে আবার চেষ্টা করুন।');
                    } finally {
                        this.isSubmitting = false;
                    }
                }
            };
        }
    </script>
</body>
</html>
