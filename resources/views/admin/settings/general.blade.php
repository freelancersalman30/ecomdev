@extends('layouts.admin')

@section('title', 'Enterprise Site Settings & Store Control')
@section('page-title', 'Site Settings & System Control')

@section('content')
<div class="max-w-6xl mx-auto space-y-6" x-data="{ activeTab: 'branding' }">

    <!-- Top Breadcrumb & Actions Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white dark:bg-slate-900 p-5 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm">
        <div class="space-y-1">
            <h2 class="text-xl font-extrabold text-slate-900 dark:text-white flex items-center gap-2">
                <i data-lucide="sliders" class="w-6 h-6 text-emerald-500"></i>
                <span>Enterprise Site Settings Hub</span>
            </h2>
            <p class="text-xs text-slate-500 dark:text-slate-400">
                Configure store identity, branding assets, delivery automation, currency, invoices, and system rules.
            </p>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('home') }}" target="_blank" class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 text-xs font-bold transition">
                <i data-lucide="external-link" class="w-4 h-4 text-daraz-orange"></i>
                <span>View Storefront</span>
            </a>
            <button type="submit" form="settingsForm" class="inline-flex items-center gap-2 px-5 py-2 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-500 hover:from-emerald-500 hover:to-teal-400 text-white text-xs font-black shadow-lg shadow-emerald-600/20 transition transform active:scale-95">
                <i data-lucide="save" class="w-4 h-4"></i>
                <span>Save All Changes</span>
            </button>
        </div>
    </div>

    <!-- Alert Notifications -->
    @if(session('success'))
    <div class="p-4 rounded-2xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-600 dark:text-emerald-400 text-xs font-bold flex items-center justify-between shadow-sm">
        <div class="flex items-center gap-2.5">
            <i data-lucide="check-circle" class="w-5 h-5 flex-shrink-0"></i>
            <span>{{ session('success') }}</span>
        </div>
        <button type="button" onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700">&times;</button>
    </div>
    @endif

    @if($errors->any())
    <div class="p-4 rounded-2xl bg-rose-500/10 border border-rose-500/30 text-rose-600 dark:text-rose-400 text-xs space-y-1">
        @foreach($errors->all() as $err)
            <div class="flex items-center gap-2 font-medium">
                <i data-lucide="alert-circle" class="w-4 h-4 text-rose-500 flex-shrink-0"></i>
                <span>{{ $err }}</span>
            </div>
        @endforeach
    </div>
    @endif

    <!-- Main Navigation Tabs Bar -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-1.5 shadow-sm flex flex-wrap gap-1">
        <button 
            type="button" 
            @click="activeTab = 'branding'" 
            :class="activeTab === 'branding' ? 'bg-emerald-500 text-slate-950 font-bold shadow-md shadow-emerald-500/20' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800'"
            class="flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs transition">
            <i data-lucide="store" class="w-4 h-4"></i>
            <span>1. Identity & Logos</span>
        </button>

        <button 
            type="button" 
            @click="activeTab = 'shipping'" 
            :class="activeTab === 'shipping' ? 'bg-emerald-500 text-slate-950 font-bold shadow-md shadow-emerald-500/20' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800'"
            class="flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs transition">
            <i data-lucide="truck" class="w-4 h-4"></i>
            <span>2. Orders & Shipping</span>
        </button>

        <button 
            type="button" 
            @click="activeTab = 'currency'" 
            :class="activeTab === 'currency' ? 'bg-emerald-500 text-slate-950 font-bold shadow-md shadow-emerald-500/20' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800'"
            class="flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs transition">
            <i data-lucide="coins" class="w-4 h-4"></i>
            <span>3. Currency & Localization</span>
        </button>

        <button 
            type="button" 
            @click="activeTab = 'invoice'" 
            :class="activeTab === 'invoice' ? 'bg-emerald-500 text-slate-950 font-bold shadow-md shadow-emerald-500/20' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800'"
            class="flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs transition">
            <i data-lucide="file-text" class="w-4 h-4"></i>
            <span>4. Invoices & Receipts</span>
        </button>

        <button 
            type="button" 
            @click="activeTab = 'notices'" 
            :class="activeTab === 'notices' ? 'bg-emerald-500 text-slate-950 font-bold shadow-md shadow-emerald-500/20' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800'"
            class="flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs transition">
            <i data-lucide="megaphone" class="w-4 h-4"></i>
            <span>5. Notices & Maintenance</span>
        </button>

        <button 
            type="button" 
            @click="activeTab = 'social'" 
            :class="activeTab === 'social' ? 'bg-emerald-500 text-slate-950 font-bold shadow-md shadow-emerald-500/20' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800'"
            class="flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs transition">
            <i data-lucide="share-2" class="w-4 h-4"></i>
            <span>6. Social & Contact</span>
        </button>

        <button 
            type="button" 
            @click="activeTab = 'scripts'" 
            :class="activeTab === 'scripts' ? 'bg-emerald-500 text-slate-950 font-bold shadow-md shadow-emerald-500/20' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800'"
            class="flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs transition">
            <i data-lucide="code" class="w-4 h-4"></i>
            <span>7. Custom Scripts & SEO</span>
        </button>
    </div>

    <!-- MAIN FORM WRAPPER -->
    <form id="settingsForm" method="POST" action="{{ route('admin.settings.general.update') }}" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')

        <!-- ========================================================= -->
        <!-- TAB 1: STORE IDENTITY & BRANDING ASSETS                   -->
        <!-- ========================================================= -->
        <div x-show="activeTab === 'branding'" x-cloak class="space-y-6">
            
            <!-- Store Logos & Favicon Grid -->
            <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 border border-slate-200 dark:border-slate-800 shadow-sm space-y-5">
                <div class="border-b border-slate-100 dark:border-slate-800 pb-3">
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                        <i data-lucide="image" class="w-4 h-4 text-emerald-500"></i>
                        <span>Store Logos & Favicon Assets</span>
                    </h3>
                    <p class="text-xs text-slate-500">Upload primary logo, dark-mode logo, invoice header logo, and favicon.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    
                    <!-- 1. Primary Header Logo -->
                    <div class="p-4 rounded-2xl border border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-950/40 space-y-3">
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">Store Logo (Light Theme)</label>
                        <div class="h-20 rounded-xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 flex items-center justify-center p-2 overflow-hidden">
                            @if(!empty($settings['site_logo']))
                                <img src="{{ asset($settings['site_logo']) }}" alt="Store Logo" class="max-h-16 object-contain">
                            @else
                                <div class="text-center text-[10px] text-slate-400">
                                    <i data-lucide="image" class="w-6 h-6 mx-auto mb-1 opacity-50"></i>
                                    <span>Default SVG Logo in use</span>
                                </div>
                            @endif
                        </div>
                        <input type="file" name="site_logo" accept="image/*" class="w-full text-[11px] text-slate-500 file:mr-2 file:py-1 file:px-2.5 file:rounded-lg file:border-0 file:text-[11px] file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100">
                        @if(!empty($settings['site_logo']))
                        <label class="flex items-center gap-1.5 text-[10px] text-rose-500 cursor-pointer">
                            <input type="checkbox" name="remove_site_logo" value="1" class="rounded">
                            <span>Remove custom logo</span>
                        </label>
                        @endif
                    </div>

                    <!-- 2. Dark Mode Logo -->
                    <div class="p-4 rounded-2xl border border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-950/40 space-y-3">
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">Store Logo (Dark Theme)</label>
                        <div class="h-20 rounded-xl bg-slate-900 border border-slate-700 flex items-center justify-center p-2 overflow-hidden">
                            @if(!empty($settings['site_logo_dark']))
                                <img src="{{ asset($settings['site_logo_dark']) }}" alt="Dark Logo" class="max-h-16 object-contain">
                            @else
                                <div class="text-center text-[10px] text-slate-500">
                                    <i data-lucide="moon" class="w-6 h-6 mx-auto mb-1 opacity-50"></i>
                                    <span>Fallback to Light Logo</span>
                                </div>
                            @endif
                        </div>
                        <input type="file" name="site_logo_dark" accept="image/*" class="w-full text-[11px] text-slate-500 file:mr-2 file:py-1 file:px-2.5 file:rounded-lg file:border-0 file:text-[11px] file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100">
                        @if(!empty($settings['site_logo_dark']))
                        <label class="flex items-center gap-1.5 text-[10px] text-rose-500 cursor-pointer">
                            <input type="checkbox" name="remove_site_logo_dark" value="1" class="rounded">
                            <span>Remove dark logo</span>
                        </label>
                        @endif
                    </div>

                    <!-- 3. Invoice & POS Logo -->
                    <div class="p-4 rounded-2xl border border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-950/40 space-y-3">
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">Invoice & Thermal POS Logo</label>
                        <div class="h-20 rounded-xl bg-white border border-slate-200 flex items-center justify-center p-2 overflow-hidden">
                            @if(!empty($settings['invoice_logo']))
                                <img src="{{ asset($settings['invoice_logo']) }}" alt="Invoice Logo" class="max-h-16 object-contain">
                            @else
                                <div class="text-center text-[10px] text-slate-400">
                                    <i data-lucide="receipt" class="w-6 h-6 mx-auto mb-1 opacity-50"></i>
                                    <span>Uses standard header text</span>
                                </div>
                            @endif
                        </div>
                        <input type="file" name="invoice_logo" accept="image/*" class="w-full text-[11px] text-slate-500 file:mr-2 file:py-1 file:px-2.5 file:rounded-lg file:border-0 file:text-[11px] file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100">
                        @if(!empty($settings['invoice_logo']))
                        <label class="flex items-center gap-1.5 text-[10px] text-rose-500 cursor-pointer">
                            <input type="checkbox" name="remove_invoice_logo" value="1" class="rounded">
                            <span>Remove invoice logo</span>
                        </label>
                        @endif
                    </div>

                    <!-- 4. Favicon -->
                    <div class="p-4 rounded-2xl border border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-950/40 space-y-3">
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">Browser Favicon (16x16 / 32x32)</label>
                        <div class="h-20 rounded-xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 flex items-center justify-center p-2">
                            @if(!empty($settings['site_favicon']))
                                <img src="{{ asset($settings['site_favicon']) }}" alt="Favicon" class="w-8 h-8 object-contain">
                            @else
                                <div class="text-center text-[10px] text-slate-400">
                                    <i data-lucide="globe" class="w-6 h-6 mx-auto mb-1 opacity-50"></i>
                                    <span>Default CPU Favicon</span>
                                </div>
                            @endif
                        </div>
                        <input type="file" name="site_favicon" accept=".ico,.png,.jpg,.svg" class="w-full text-[11px] text-slate-500 file:mr-2 file:py-1 file:px-2.5 file:rounded-lg file:border-0 file:text-[11px] file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100">
                        @if(!empty($settings['site_favicon']))
                        <label class="flex items-center gap-1.5 text-[10px] text-rose-500 cursor-pointer">
                            <input type="checkbox" name="remove_site_favicon" value="1" class="rounded">
                            <span>Remove favicon</span>
                        </label>
                        @endif
                    </div>

                </div>
            </div>

            <!-- Identity & Company Details -->
            <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 border border-slate-200 dark:border-slate-800 shadow-sm space-y-5">
                <div class="border-b border-slate-100 dark:border-slate-800 pb-3">
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                        <i data-lucide="building" class="w-4 h-4 text-emerald-500"></i>
                        <span>Store Identification & Contact Channels</span>
                    </h3>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Company / Store Name *</label>
                        <input type="text" name="site_name" value="{{ $settings['site_name'] ?? $settings['company_name'] ?? 'DREAMERS PCB' }}" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-bold outline-none focus:ring-2 focus:ring-emerald-500">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Store Slogan / Tagline</label>
                        <input type="text" name="site_tagline" value="{{ $settings['site_tagline'] ?? 'Enterprise Electronic Components & PCB Marketplace' }}" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs outline-none focus:ring-2 focus:ring-emerald-500">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Official Support Email *</label>
                        <input type="email" name="site_email" value="{{ $settings['site_email'] ?? $settings['email'] ?? 'support@dreamerspcb.com' }}" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs outline-none focus:ring-2 focus:ring-emerald-500">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Primary Hotline *</label>
                        <input type="text" name="site_phone" value="{{ $settings['site_phone'] ?? $settings['phone'] ?? '+880 1700-112233' }}" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-mono font-bold outline-none focus:ring-2 focus:ring-emerald-500">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">WhatsApp Hotline Number</label>
                        <input type="text" name="whatsapp_phone" value="{{ $settings['whatsapp_phone'] ?? $settings['footer_whatsapp'] ?? '+880 1700-112233' }}" placeholder="+8801700112233" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-mono outline-none focus:ring-2 focus:ring-emerald-500">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Customer Support Hours</label>
                        <input type="text" name="support_hours" value="{{ $settings['support_hours'] ?? 'Sat - Thu: 9:30 AM - 8:30 PM' }}" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs outline-none">
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Physical Warehouse / Office Address</label>
                        <textarea name="site_address" rows="2" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs outline-none focus:ring-2 focus:ring-emerald-500">{{ $settings['site_address'] ?? $settings['address'] ?? 'Level 5, Multiplan Center, Elephant Road, Dhaka, Bangladesh' }}</textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Google Maps Location Link</label>
                        <input type="url" name="google_maps_url" value="{{ $settings['google_maps_url'] ?? '' }}" placeholder="https://maps.google.com/..." class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs outline-none">
                    </div>
                </div>
            </div>
        </div>

        <!-- ========================================================= -->
        <!-- TAB 2: ORDERS, DELIVERY & E-COMMERCE AUTOMATION           -->
        <!-- ========================================================= -->
        <div x-show="activeTab === 'shipping'" x-cloak class="space-y-6">
            <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 border border-slate-200 dark:border-slate-800 shadow-sm space-y-5">
                <div class="border-b border-slate-100 dark:border-slate-800 pb-3">
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                        <i data-lucide="package" class="w-4 h-4 text-emerald-500"></i>
                        <span>Nationwide Delivery Charges & Timing</span>
                    </h3>
                    <p class="text-xs text-slate-500">Set courier charges and estimated arrival timelines shown across cart and checkout.</p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Inside Dhaka Shipping Charge (৳) *</label>
                        <input type="number" step="0.01" name="inside_dhaka_shipping" value="{{ $settings['inside_dhaka_shipping'] ?? $settings['inside_dhaka_charge'] ?? 70 }}" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-bold code-font outline-none focus:ring-2 focus:ring-emerald-500">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Inside Dhaka Expected Days</label>
                        <input type="text" name="inside_dhaka_estimate" value="{{ $settings['inside_dhaka_estimate'] ?? '24 - 48 Hours' }}" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Outside Dhaka Shipping Charge (৳) *</label>
                        <input type="number" step="0.01" name="outside_dhaka_shipping" value="{{ $settings['outside_dhaka_shipping'] ?? $settings['outside_dhaka_charge'] ?? 130 }}" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-bold code-font outline-none focus:ring-2 focus:ring-emerald-500">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Outside Dhaka Expected Days</label>
                        <input type="text" name="outside_dhaka_estimate" value="{{ $settings['outside_dhaka_estimate'] ?? '2 - 4 Business Days' }}" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs outline-none">
                    </div>

                </div>

                <div class="pt-4 border-t border-slate-100 dark:border-slate-800 grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Free Shipping Min. Order (৳)</label>
                        <input type="number" step="1" name="free_shipping_min_amount" value="{{ $settings['free_shipping_min_amount'] ?? 3000 }}" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-bold code-font outline-none">
                        <span class="text-[10px] text-slate-400">Set 0 to disable automated free shipping</span>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Minimum Order Amount (৳)</label>
                        <input type="number" step="1" name="min_order_amount" value="{{ $settings['min_order_amount'] ?? 100 }}" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-bold code-font outline-none">
                        <span class="text-[10px] text-slate-400">Cart payable must be at least this value</span>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Order Invoice Number Prefix</label>
                        <input type="text" name="order_prefix" value="{{ $settings['order_prefix'] ?? 'DPCB-' }}" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-mono font-bold outline-none">
                        <span class="text-[10px] text-slate-400">e.g. DPCB-20260829-001</span>
                    </div>
                </div>

                <!-- Toggles Grid -->
                <div class="pt-4 border-t border-slate-100 dark:border-slate-800 grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <label class="flex items-start gap-3 p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700/60 cursor-pointer">
                        <input type="checkbox" name="cod_enabled" value="1" {{ ($settings['cod_enabled'] ?? '1') === '1' ? 'checked' : '' }} class="mt-1 rounded text-emerald-500 focus:ring-emerald-500">
                        <div>
                            <span class="block text-xs font-bold text-slate-900 dark:text-white">Cash on Delivery (COD)</span>
                            <span class="text-[11px] text-slate-500">Allow customers to pay cash upon parcel handover</span>
                        </div>
                    </label>

                    <label class="flex items-start gap-3 p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700/60 cursor-pointer">
                        <input type="checkbox" name="guest_checkout_enabled" value="1" {{ ($settings['guest_checkout_enabled'] ?? '1') === '1' ? 'checked' : '' }} class="mt-1 rounded text-emerald-500 focus:ring-emerald-500">
                        <div>
                            <span class="block text-xs font-bold text-slate-900 dark:text-white">Guest Checkout</span>
                            <span class="text-[11px] text-slate-500">Allow orders without requiring immediate registration</span>
                        </div>
                    </label>

                    <label class="flex items-start gap-3 p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700/60 cursor-pointer">
                        <input type="checkbox" name="backorder_enabled" value="1" {{ ($settings['backorder_enabled'] ?? '0') === '1' ? 'checked' : '' }} class="mt-1 rounded text-emerald-500 focus:ring-emerald-500">
                        <div>
                            <span class="block text-xs font-bold text-slate-900 dark:text-white">Allow Backorders</span>
                            <span class="text-[11px] text-slate-500">Allow buying items when warehouse stock is zero</span>
                        </div>
                    </label>
                </div>
            </div>
        </div>

        <!-- ========================================================= -->
        <!-- TAB 3: CURRENCY & LOCALIZATION                            -->
        <!-- ========================================================= -->
        <div x-show="activeTab === 'currency'" x-cloak class="space-y-6">
            <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 border border-slate-200 dark:border-slate-800 shadow-sm space-y-5">
                <div class="border-b border-slate-100 dark:border-slate-800 pb-3">
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                        <i data-lucide="dollar-sign" class="w-4 h-4 text-emerald-500"></i>
                        <span>Currency Format & Region Settings</span>
                    </h3>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Currency Symbol *</label>
                        <input type="text" name="currency_symbol" value="{{ $settings['currency_symbol'] ?? '৳' }}" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-bold code-font outline-none focus:ring-2 focus:ring-emerald-500">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Currency ISO Code *</label>
                        <input type="text" name="currency_code" value="{{ $settings['currency_code'] ?? 'BDT' }}" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-mono font-bold outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Currency Position</label>
                        <select name="currency_position" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-semibold outline-none">
                            <option value="left" {{ ($settings['currency_position'] ?? 'left') === 'left' ? 'selected' : '' }}>Left (e.g. ৳ 500)</option>
                            <option value="right" {{ ($settings['currency_position'] ?? '') === 'right' ? 'selected' : '' }}>Right (e.g. 500 ৳)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Timezone</label>
                        <input type="text" name="timezone" value="{{ $settings['timezone'] ?? 'Asia/Dhaka' }}" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-mono outline-none">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-4 border-t border-slate-100 dark:border-slate-800">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Price Decimal Places</label>
                        <select name="decimal_precision" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs outline-none">
                            <option value="2" {{ ($settings['decimal_precision'] ?? '2') === '2' ? 'selected' : '' }}>2 Decimals (৳500.00)</option>
                            <option value="0" {{ ($settings['decimal_precision'] ?? '') === '0' ? 'selected' : '' }}>No Decimals (৳500)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Default Platform Language</label>
                        <select name="default_language" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs outline-none">
                            <option value="en_bn" selected>Dual (English & Bengali)</option>
                            <option value="en">English Only</option>
                            <option value="bn">Bengali Only</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Warehouse Low Stock Alert Level</label>
                        <input type="number" name="low_stock_threshold" value="{{ $settings['low_stock_threshold'] ?? 5 }}" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-bold code-font outline-none">
                    </div>
                </div>
            </div>
        </div>

        <!-- ========================================================= -->
        <!-- TAB 4: INVOICE & POS THERMAL RECEIPT CUSTOMIZATION        -->
        <!-- ========================================================= -->
        <div x-show="activeTab === 'invoice'" x-cloak class="space-y-6">
            <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 border border-slate-200 dark:border-slate-800 shadow-sm space-y-5">
                <div class="border-b border-slate-100 dark:border-slate-800 pb-3">
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                        <i data-lucide="receipt" class="w-4 h-4 text-emerald-500"></i>
                        <span>Invoice & POS Thermal Receipt Formatting</span>
                    </h3>
                    <p class="text-xs text-slate-500">Configure legal business terms, BIN/VAT registration, and return policies printed on receipts.</p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Invoice Header Title</label>
                        <input type="text" name="invoice_title" value="{{ $settings['invoice_title'] ?? 'DREAMERS PCB - INVOICE & CASH MEMO' }}" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-bold outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Trade License / BIN / VAT Number</label>
                        <input type="text" name="invoice_trade_license" value="{{ $settings['invoice_trade_license'] ?? $settings['footer_trade_license'] ?? 'TRAD/DSCC/012948-2025' }}" placeholder="BIN / 001928374-0101" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-mono outline-none">
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Invoice Warranty & Return Policy Note</label>
                        <textarea name="invoice_terms" rows="3" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs outline-none">{{ $settings['invoice_terms'] ?? '1. All electronic components must be inspected within 7 days of delivery. 2. Burnt ICs, damaged solder pads, or mishandled dev boards are strictly non-refundable. 3. Please retain this original memo for warranty validation.' }}</textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Authorized Signatory Title</label>
                        <input type="text" name="invoice_signatory" value="{{ $settings['invoice_signatory'] ?? 'Authorized Officer, Accounts & Dispatch' }}" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">POS Thermal Receipt Footer Note</label>
                        <input type="text" name="pos_footer_note" value="{{ $settings['pos_footer_note'] ?? 'Thank you for choosing DREAMERS PCB! Happy Building!' }}" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs outline-none">
                    </div>
                </div>
            </div>
        </div>

        <!-- ========================================================= -->
        <!-- TAB 5: NOTICES & STOREFRONT CONTROLS                      -->
        <!-- ========================================================= -->
        <div x-show="activeTab === 'notices'" x-cloak class="space-y-6">
            <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 border border-slate-200 dark:border-slate-800 shadow-sm space-y-5">
                <div class="border-b border-slate-100 dark:border-slate-800 pb-3">
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                        <i data-lucide="bell" class="w-4 h-4 text-emerald-500"></i>
                        <span>Storefront Announcement Bar & Emergency Maintenance</span>
                    </h3>
                </div>

                <!-- Announcement Bar -->
                <div class="p-4 rounded-2xl bg-amber-500/10 border border-amber-500/20 space-y-3">
                    <div class="flex items-center justify-between">
                        <label class="flex items-center gap-2.5 cursor-pointer">
                            <input type="checkbox" name="announcement_enabled" value="1" {{ ($settings['announcement_enabled'] ?? '1') === '1' ? 'checked' : '' }} class="rounded text-amber-500 focus:ring-amber-500">
                            <span class="text-xs font-bold text-slate-900 dark:text-white">Enable Top Announcement Bar</span>
                        </label>
                        <span class="text-[10px] font-bold text-amber-500 bg-amber-500/10 px-2 py-0.5 rounded-full">Top Banner</span>
                    </div>
                    <input type="text" name="announcement_text" value="{{ $settings['announcement_text'] ?? '⚡ Bangladesh\'s #1 Electronic Component & PCB Marketplace • Fast Nationwide COD Available!' }}" class="w-full px-3.5 py-2.5 rounded-xl border border-amber-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-semibold outline-none focus:ring-2 focus:ring-amber-500">
                </div>

                <!-- Maintenance Mode -->
                <div class="p-4 rounded-2xl bg-rose-500/10 border border-rose-500/20 space-y-3">
                    <div class="flex items-center justify-between">
                        <label class="flex items-center gap-2.5 cursor-pointer">
                            <input type="checkbox" name="maintenance_mode" value="1" {{ ($settings['maintenance_mode'] ?? '0') === '1' ? 'checked' : '' }} class="rounded text-rose-500 focus:ring-rose-500">
                            <span class="text-xs font-bold text-rose-700 dark:text-rose-400">Emergency Maintenance Mode</span>
                        </label>
                        <span class="text-[10px] font-bold text-rose-500 bg-rose-500/10 px-2 py-0.5 rounded-full">Admin Only Access</span>
                    </div>
                    <input type="text" name="maintenance_message" value="{{ $settings['maintenance_message'] ?? 'We are currently upgrading our warehouse database servers. We will be back online in 15 minutes.' }}" class="w-full px-3.5 py-2.5 rounded-xl border border-rose-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs outline-none focus:ring-2 focus:ring-rose-500">
                </div>

                <!-- Hardware Guarantee Badge -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Hardware Guarantee Badge Text (Sub-Navbar)</label>
                    <input type="text" name="guarantee_badge_text" value="{{ $settings['guarantee_badge_text'] ?? '100% Genuine Hardware Guaranteed' }}" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs outline-none">
                </div>
            </div>
        </div>

        <!-- ========================================================= -->
        <!-- TAB 6: SOCIAL & COMMUNITY CHANNELS                        -->
        <!-- ========================================================= -->
        <div x-show="activeTab === 'social'" x-cloak class="space-y-6">
            <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 border border-slate-200 dark:border-slate-800 shadow-sm space-y-5">
                <div class="border-b border-slate-100 dark:border-slate-800 pb-3">
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                        <i data-lucide="share-2" class="w-4 h-4 text-emerald-500"></i>
                        <span>Social Media & Community Links</span>
                    </h3>
                    <p class="text-xs text-slate-500">Links rendered in footer, mobile menu, and order success confirmation screens.</p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Facebook Page / Group URL</label>
                        <input type="url" name="facebook_url" value="{{ $settings['facebook_url'] ?? $settings['footer_facebook_url'] ?? 'https://facebook.com/dreamerspcb' }}" placeholder="https://facebook.com/..." class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">YouTube Channel URL</label>
                        <input type="url" name="youtube_url" value="{{ $settings['youtube_url'] ?? $settings['footer_youtube_url'] ?? 'https://youtube.com/@dreamerspcb' }}" placeholder="https://youtube.com/@..." class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">LinkedIn Company Page</label>
                        <input type="url" name="linkedin_url" value="{{ $settings['linkedin_url'] ?? $settings['footer_linkedin_url'] ?? '' }}" placeholder="https://linkedin.com/company/..." class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">GitHub Organization / Repositories</label>
                        <input type="url" name="github_url" value="{{ $settings['github_url'] ?? $settings['footer_github_url'] ?? '' }}" placeholder="https://github.com/..." class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Instagram Profile</label>
                        <input type="url" name="instagram_url" value="{{ $settings['instagram_url'] ?? $settings['footer_instagram_url'] ?? '' }}" placeholder="https://instagram.com/..." class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Discord Developers Server Link</label>
                        <input type="url" name="discord_url" value="{{ $settings['discord_url'] ?? $settings['footer_discord_url'] ?? '' }}" placeholder="https://discord.gg/..." class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs outline-none">
                    </div>
                </div>
            </div>
        </div>

        <!-- ========================================================= -->
        <!-- TAB 7: CUSTOM SCRIPTS & ANALYTICS                         -->
        <!-- ========================================================= -->
        <div x-show="activeTab === 'scripts'" x-cloak class="space-y-6">
            <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 border border-slate-200 dark:border-slate-800 shadow-sm space-y-5">
                <div class="border-b border-slate-100 dark:border-slate-800 pb-3">
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                        <i data-lucide="code" class="w-4 h-4 text-emerald-500"></i>
                        <span>Custom Header & Footer Injection Scripts</span>
                    </h3>
                    <p class="text-xs text-slate-500">Inject Google Tag Manager, Google Analytics 4, Meta Pixel, Hotjar, or custom CSS styles.</p>
                </div>

                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Header Scripts (Injected right before &lt;/head&gt;)</label>
                        <textarea name="header_scripts" rows="6" placeholder="<script>/* Google Tag Manager / Analytics */</script>" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-950 text-emerald-400 font-mono text-xs outline-none focus:ring-2 focus:ring-emerald-500">{{ $settings['header_scripts'] ?? '' }}</textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Footer Scripts (Injected right before &lt;/body&gt;)</label>
                        <textarea name="footer_scripts" rows="6" placeholder="<script>/* Live chat widget, Tawk.to, etc. */</script>" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-950 text-emerald-400 font-mono text-xs outline-none focus:ring-2 focus:ring-emerald-500">{{ $settings['footer_scripts'] ?? '' }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sticky Floating Save Actions Bar -->
        <div class="sticky bottom-4 z-30 p-4 rounded-2xl bg-slate-900/90 backdrop-blur-md border border-slate-800 shadow-2xl flex items-center justify-between text-white">
            <div class="flex items-center gap-2 text-xs text-slate-400">
                <i data-lucide="info" class="w-4 h-4 text-emerald-400"></i>
                <span>Changes will be applied immediately across storefront, POS receipts, and invoice generators.</span>
            </div>
            <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-gradient-to-r from-emerald-500 to-teal-400 hover:from-emerald-400 hover:to-teal-300 text-slate-950 font-black text-xs uppercase tracking-wider shadow-lg shadow-emerald-500/20 transition transform active:scale-95">
                <i data-lucide="check" class="w-4 h-4"></i>
                <span>Save Site Settings</span>
            </button>
        </div>

    </form>

</div>
@endsection
