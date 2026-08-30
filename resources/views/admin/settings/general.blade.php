@extends('layouts.admin')

@section('title', 'Enterprise Site Settings & Store Control')
@section('page-title', 'Site Settings & System Control')

@section('content')
<div class="max-w-6xl mx-auto space-y-6" x-data="{ 
    activeTab: 'branding',
    fontBody: '{{ $settings['theme_font_body'] ?? 'Plus Jakarta Sans' }}',
    fontHeading: '{{ $settings['theme_font_heading'] ?? 'Outfit' }}',
    fontMono: '{{ $settings['theme_font_mono'] ?? 'JetBrains Mono' }}',
    fontSizeBase: '{{ $settings['theme_font_size_base'] ?? '16px' }}',
    primaryColor: '{{ $settings['theme_primary_color'] ?? '#f85606' }}',
    primaryHover: '{{ $settings['theme_primary_hover'] ?? '#d04300' }}',
    secondaryColor: '{{ $settings['theme_secondary_color'] ?? '#10b981' }}',
    bodyBg: '{{ $settings['theme_body_bg'] ?? '#f8fafc' }}',
    textColor: '{{ $settings['theme_text_color'] ?? '#1e293b' }}',
    headingColor: '{{ $settings['theme_heading_color'] ?? '#0f172a' }}',
    cardBg: '{{ $settings['theme_card_bg'] ?? '#ffffff' }}',
    cardBorder: '{{ $settings['theme_card_border'] ?? '#e2e8f0' }}',
    priceColor: '{{ $settings['theme_price_color'] ?? '#f85606' }}',
    saleBadgeColor: '{{ $settings['theme_sale_badge_color'] ?? '#ef4444' }}',
    headerBg: '{{ $settings['theme_header_bg'] ?? '#0f172a' }}',
    announcementBg: '{{ $settings['theme_announcement_bg'] ?? '#0f172a' }}',
    announcementText: '{{ $settings['theme_announcement_text_color'] ?? '#fbbf24' }}',
    footerBg: '{{ $settings['theme_footer_bg'] ?? '#020617' }}',
    footerText: '{{ $settings['theme_footer_text_color'] ?? '#94a3b8' }}',
    applyPreset(p, ph, s, h, a, at, f, bbg = '#f8fafc', txt = '#1e293b', hdg = '#0f172a') {
        this.primaryColor = p;
        this.primaryHover = ph;
        this.secondaryColor = s;
        this.headerBg = h;
        this.announcementBg = a;
        this.announcementText = at;
        this.footerBg = f;
        this.bodyBg = bbg;
        this.textColor = txt;
        this.headingColor = hdg;
    },
    applyFontPreset(b, h, m) {
        this.fontBody = b;
        this.fontHeading = h;
        this.fontMono = m;
    }
}">

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
            @click="activeTab = 'theme'" 
            :class="activeTab === 'theme' ? 'bg-emerald-500 text-slate-950 font-bold shadow-md shadow-emerald-500/20' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800'"
            class="flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs transition">
            <i data-lucide="palette" class="w-4 h-4 text-amber-500"></i>
            <span class="font-bold">2. Typography & Colors</span>
        </button>

        <button 
            type="button" 
            @click="activeTab = 'slider'" 
            :class="activeTab === 'slider' ? 'bg-emerald-500 text-slate-950 font-bold shadow-md shadow-emerald-500/20' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800'"
            class="flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs transition">
            <i data-lucide="sliders" class="w-4 h-4 text-daraz-orange"></i>
            <span class="font-bold">3. Slider & Hero Banners</span>
        </button>

        <button 
            type="button" 
            @click="activeTab = 'shipping'" 
            :class="activeTab === 'shipping' ? 'bg-emerald-500 text-slate-950 font-bold shadow-md shadow-emerald-500/20' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800'"
            class="flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs transition">
            <i data-lucide="truck" class="w-4 h-4"></i>
            <span>4. Orders & Shipping</span>
        </button>

        <button 
            type="button" 
            @click="activeTab = 'currency'" 
            :class="activeTab === 'currency' ? 'bg-emerald-500 text-slate-950 font-bold shadow-md shadow-emerald-500/20' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800'"
            class="flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs transition">
            <i data-lucide="coins" class="w-4 h-4"></i>
            <span>5. Currency & Region</span>
        </button>

        <button 
            type="button" 
            @click="activeTab = 'invoice'" 
            :class="activeTab === 'invoice' ? 'bg-emerald-500 text-slate-950 font-bold shadow-md shadow-emerald-500/20' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800'"
            class="flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs transition">
            <i data-lucide="file-text" class="w-4 h-4"></i>
            <span>6. Invoices & Receipts</span>
        </button>

        <button 
            type="button" 
            @click="activeTab = 'notices'" 
            :class="activeTab === 'notices' ? 'bg-emerald-500 text-slate-950 font-bold shadow-md shadow-emerald-500/20' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800'"
            class="flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs transition">
            <i data-lucide="megaphone" class="w-4 h-4"></i>
            <span>7. Notices & Maintenance</span>
        </button>

        <button 
            type="button" 
            @click="activeTab = 'social'" 
            :class="activeTab === 'social' ? 'bg-emerald-500 text-slate-950 font-bold shadow-md shadow-emerald-500/20' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800'"
            class="flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs transition">
            <i data-lucide="share-2" class="w-4 h-4"></i>
            <span>8. Social & Contact</span>
        </button>

        <button 
            type="button" 
            @click="activeTab = 'tracking'" 
            :class="activeTab === 'tracking' ? 'bg-emerald-500 text-slate-950 font-bold shadow-md shadow-emerald-500/20' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800'"
            class="flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs transition">
            <i data-lucide="crosshair" class="w-4 h-4 text-sky-400"></i>
            <span class="font-bold">9. Ad Setup & Connect</span>
        </button>

        <button 
            type="button" 
            @click="activeTab = 'scripts'" 
            :class="activeTab === 'scripts' ? 'bg-emerald-500 text-slate-950 font-bold shadow-md shadow-emerald-500/20' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800'"
            class="flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs transition">
            <i data-lucide="code" class="w-4 h-4"></i>
            <span>10. Custom Scripts & SEO</span>
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
        <!-- TAB 2: TYPOGRAPHY & FULL WEBSITE COLOR STUDIO             -->
        <!-- ========================================================= -->
        <div x-show="activeTab === 'theme'" x-cloak class="space-y-6">

            <!-- 1. TYPOGRAPHY & FONT FAMILY STUDIO -->
            <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 border border-slate-200 dark:border-slate-800 shadow-sm space-y-5">
                <div class="border-b border-slate-100 dark:border-slate-800 pb-3 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                    <div>
                        <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                            <i data-lucide="type" class="w-4 h-4 text-emerald-500"></i>
                            <span>Typography & Google Fonts Family Control</span>
                        </h3>
                        <p class="text-xs text-slate-500">Select Google font families for body text, headings, and specifications. Dynamic webfonts load automatically.</p>
                    </div>
                    <span class="text-[10px] font-bold px-2.5 py-1 rounded-full bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 self-start">Dynamic Font Engine</span>
                </div>

                <!-- 1-Click Font Combos -->
                <div class="space-y-2">
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">1-Click Typography Presets</label>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                        
                        <!-- Preset 1 -->
                        <div 
                            @click="applyFontPreset('Inter', 'Outfit', 'JetBrains Mono')"
                            :class="fontBody === 'Inter' && fontHeading === 'Outfit' ? 'border-emerald-500 ring-2 ring-emerald-500/20 bg-emerald-50/20' : 'border-slate-200 dark:border-slate-800 hover:border-slate-400'"
                            class="p-3.5 rounded-2xl border bg-slate-50/50 dark:bg-slate-950/40 cursor-pointer transition space-y-1.5">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-bold text-slate-900 dark:text-white">Silicon Valley Tech</span>
                                <span class="text-[9px] font-mono font-bold px-1.5 py-0.5 rounded bg-sky-500/10 text-sky-600">Modern</span>
                            </div>
                            <p class="text-[10px] text-slate-500">Body: Inter &bull; Headings: Outfit</p>
                        </div>

                        <!-- Preset 2 -->
                        <div 
                            @click="applyFontPreset('Space Grotesk', 'Space Grotesk', 'Fira Code')"
                            :class="fontBody === 'Space Grotesk' && fontHeading === 'Space Grotesk' ? 'border-emerald-500 ring-2 ring-emerald-500/20 bg-emerald-50/20' : 'border-slate-200 dark:border-slate-800 hover:border-slate-400'"
                            class="p-3.5 rounded-2xl border bg-slate-50/50 dark:bg-slate-950/40 cursor-pointer transition space-y-1.5">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-bold text-slate-900 dark:text-white">Cyberpunk Hardware</span>
                                <span class="text-[9px] font-mono font-bold px-1.5 py-0.5 rounded bg-emerald-500/10 text-emerald-600">Robotics</span>
                            </div>
                            <p class="text-[10px] text-slate-500">Body & Hdg: Space Grotesk</p>
                        </div>

                        <!-- Preset 3 -->
                        <div 
                            @click="applyFontPreset('Hind Siliguri', 'Poppins', 'JetBrains Mono')"
                            :class="fontBody === 'Hind Siliguri' && fontHeading === 'Poppins' ? 'border-emerald-500 ring-2 ring-emerald-500/20 bg-emerald-50/20' : 'border-slate-200 dark:border-slate-800 hover:border-slate-400'"
                            class="p-3.5 rounded-2xl border bg-slate-50/50 dark:bg-slate-950/40 cursor-pointer transition space-y-1.5">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-bold text-slate-900 dark:text-white">Bangladesh Bilingual</span>
                                <span class="text-[9px] font-mono font-bold px-1.5 py-0.5 rounded bg-amber-500/10 text-amber-600">বাংলা+EN</span>
                            </div>
                            <p class="text-[10px] text-slate-500">Body: Hind Siliguri &bull; Hdg: Poppins</p>
                        </div>

                        <!-- Preset 4 -->
                        <div 
                            @click="applyFontPreset('Plus Jakarta Sans', 'Plus Jakarta Sans', 'Roboto Mono')"
                            :class="fontBody === 'Plus Jakarta Sans' && fontHeading === 'Plus Jakarta Sans' ? 'border-emerald-500 ring-2 ring-emerald-500/20 bg-emerald-50/20' : 'border-slate-200 dark:border-slate-800 hover:border-slate-400'"
                            class="p-3.5 rounded-2xl border bg-slate-50/50 dark:bg-slate-950/40 cursor-pointer transition space-y-1.5">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-bold text-slate-900 dark:text-white">Clean Minimal Lab</span>
                                <span class="text-[9px] font-mono font-bold px-1.5 py-0.5 rounded bg-purple-500/10 text-purple-600">Standard</span>
                            </div>
                            <p class="text-[10px] text-slate-500">Body & Hdg: Plus Jakarta Sans</p>
                        </div>

                    </div>
                </div>

                <!-- Font Selectors Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 pt-2">
                    
                    <!-- 1. Body Font -->
                    <div class="p-4 rounded-2xl border border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-950/40 space-y-2">
                        <label class="block text-xs font-bold text-slate-800 dark:text-slate-200">Main Body Font Family *</label>
                        <p class="text-[10px] text-slate-400">Buttons, descriptions, menu items, table data</p>
                        <select name="theme_font_body" x-model="fontBody" class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-bold outline-none focus:ring-2 focus:ring-emerald-500">
                            <option value="Plus Jakarta Sans">Plus Jakarta Sans (Default)</option>
                            <option value="Inter">Inter (Ultra-Clean)</option>
                            <option value="Roboto">Roboto (Google Standard)</option>
                            <option value="Poppins">Poppins (Friendly Geometric)</option>
                            <option value="Outfit">Outfit (Brand Sans)</option>
                            <option value="Open Sans">Open Sans (High Legibility)</option>
                            <option value="Hind Siliguri">Hind Siliguri (Bengali & English)</option>
                            <option value="Space Grotesk">Space Grotesk (Tech Hardware)</option>
                        </select>
                    </div>

                    <!-- 2. Heading Font -->
                    <div class="p-4 rounded-2xl border border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-950/40 space-y-2">
                        <label class="block text-xs font-bold text-slate-800 dark:text-slate-200">Headings & Titles Font *</label>
                        <p class="text-[10px] text-slate-400">Hero titles, product titles, section banners</p>
                        <select name="theme_font_heading" x-model="fontHeading" class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-bold outline-none focus:ring-2 focus:ring-emerald-500">
                            <option value="Outfit">Outfit (Default)</option>
                            <option value="Space Grotesk">Space Grotesk (Hardware Accent)</option>
                            <option value="Plus Jakarta Sans">Plus Jakarta Sans</option>
                            <option value="Inter">Inter</option>
                            <option value="Montserrat">Montserrat (Strong Bold)</option>
                            <option value="Poppins">Poppins</option>
                            <option value="Syne">Syne (High-End Design)</option>
                        </select>
                    </div>

                    <!-- 3. Monospace / Code Font -->
                    <div class="p-4 rounded-2xl border border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-950/40 space-y-2">
                        <label class="block text-xs font-bold text-slate-800 dark:text-slate-200">Specs & SKU Code Font *</label>
                        <p class="text-[10px] text-slate-400">Product SKU, PCB part numbers, invoice bills</p>
                        <select name="theme_font_mono" x-model="fontMono" class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-mono font-bold outline-none focus:ring-2 focus:ring-emerald-500">
                            <option value="JetBrains Mono">JetBrains Mono (Default)</option>
                            <option value="Fira Code">Fira Code</option>
                            <option value="Roboto Mono">Roboto Mono</option>
                        </select>
                    </div>

                    <!-- 4. Base Font Size -->
                    <div class="p-4 rounded-2xl border border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-950/40 space-y-2">
                        <label class="block text-xs font-bold text-slate-800 dark:text-slate-200">Base Root Font Scaling *</label>
                        <p class="text-[10px] text-slate-400">Controls overall website typography scale</p>
                        <select name="theme_font_size_base" x-model="fontSizeBase" class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-bold outline-none focus:ring-2 focus:ring-emerald-500">
                            <option value="15px">15px (Compact - High Density)</option>
                            <option value="16px">16px (Standard Default)</option>
                            <option value="17px">17px (Spacious & High Readability)</option>
                        </select>
                    </div>

                </div>
            </div>

            <!-- 2. CURATED 1-CLICK THEME PALETTES -->
            <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 border border-slate-200 dark:border-slate-800 shadow-sm space-y-5">
                <div class="border-b border-slate-100 dark:border-slate-800 pb-3">
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                        <i data-lucide="sparkles" class="w-4 h-4 text-amber-500"></i>
                        <span>1-Click Curated Color & Surface Presets</span>
                    </h3>
                    <p class="text-xs text-slate-500">Select any industry-crafted palette to instantly re-theme your store, or customize each color below.</p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3.5">
                    
                    <!-- Preset 1: Daraz Orange & PCB Tech (Default) -->
                    <div 
                        @click="applyPreset('#f85606', '#d04300', '#10b981', '#0f172a', '#0f172a', '#fbbf24', '#020617', '#f8fafc', '#1e293b', '#0f172a')" 
                        class="p-4 rounded-2xl border-2 border-slate-200 dark:border-slate-800 hover:border-emerald-500 bg-slate-50/60 dark:bg-slate-950/40 cursor-pointer transition space-y-2.5 group">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-slate-900 dark:text-white group-hover:text-emerald-400 transition">Daraz Orange & Tech</span>
                            <span class="text-[10px] uppercase font-bold px-1.5 py-0.5 rounded bg-amber-500/10 text-amber-600">Default</span>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <span class="w-5 h-5 rounded-full shadow-sm" style="background-color: #f85606;" title="Primary #f85606"></span>
                            <span class="w-5 h-5 rounded-full shadow-sm" style="background-color: #10b981;" title="Secondary #10b981"></span>
                            <span class="w-5 h-5 rounded-full shadow-sm" style="background-color: #0f172a;" title="Header #0f172a"></span>
                            <span class="w-5 h-5 rounded-full shadow-sm" style="background-color: #020617;" title="Footer #020617"></span>
                        </div>
                    </div>

                    <!-- Preset 2: Cyberpunk PCB Emerald -->
                    <div 
                        @click="applyPreset('#10b981', '#059669', '#06b6d4', '#022c22', '#022c22', '#34d399', '#021a14', '#f0fdf4', '#064e3b', '#022c22')" 
                        class="p-4 rounded-2xl border-2 border-slate-200 dark:border-slate-800 hover:border-emerald-500 bg-slate-50/60 dark:bg-slate-950/40 cursor-pointer transition space-y-2.5 group">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-slate-900 dark:text-white group-hover:text-emerald-400 transition">Cyberpunk Emerald PCB</span>
                            <span class="text-[10px] uppercase font-bold px-1.5 py-0.5 rounded bg-emerald-500/10 text-emerald-600">Hardware</span>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <span class="w-5 h-5 rounded-full shadow-sm" style="background-color: #10b981;"></span>
                            <span class="w-5 h-5 rounded-full shadow-sm" style="background-color: #06b6d4;"></span>
                            <span class="w-5 h-5 rounded-full shadow-sm" style="background-color: #022c22;"></span>
                            <span class="w-5 h-5 rounded-full shadow-sm" style="background-color: #021a14;"></span>
                        </div>
                    </div>

                    <!-- Preset 3: Electric Royal Blue (DigiKey Style) -->
                    <div 
                        @click="applyPreset('#2563eb', '#1d4ed8', '#f59e0b', '#0f172a', '#1e293b', '#93c5fd', '#0b1120', '#f8fafc', '#1e293b', '#0f172a')" 
                        class="p-4 rounded-2xl border-2 border-slate-200 dark:border-slate-800 hover:border-emerald-500 bg-slate-50/60 dark:bg-slate-950/40 cursor-pointer transition space-y-2.5 group">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-slate-900 dark:text-white group-hover:text-emerald-400 transition">Electric Royal Blue</span>
                            <span class="text-[10px] uppercase font-bold px-1.5 py-0.5 rounded bg-blue-500/10 text-blue-600">Enterprise</span>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <span class="w-5 h-5 rounded-full shadow-sm" style="background-color: #2563eb;"></span>
                            <span class="w-5 h-5 rounded-full shadow-sm" style="background-color: #f59e0b;"></span>
                            <span class="w-5 h-5 rounded-full shadow-sm" style="background-color: #0f172a;"></span>
                            <span class="w-5 h-5 rounded-full shadow-sm" style="background-color: #0b1120;"></span>
                        </div>
                    </div>

                    <!-- Preset 4: Crimson Red High-Performance -->
                    <div 
                        @click="applyPreset('#e11d48', '#be123c', '#f97316', '#1c1917', '#292524', '#fda4af', '#0c0a09', '#fff1f2', '#881337', '#4c0519')" 
                        class="p-4 rounded-2xl border-2 border-slate-200 dark:border-slate-800 hover:border-emerald-500 bg-slate-50/60 dark:bg-slate-950/40 cursor-pointer transition space-y-2.5 group">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-slate-900 dark:text-white group-hover:text-emerald-400 transition">Crimson High-Power</span>
                            <span class="text-[10px] uppercase font-bold px-1.5 py-0.5 rounded bg-rose-500/10 text-rose-600">Dynamic</span>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <span class="w-5 h-5 rounded-full shadow-sm" style="background-color: #e11d48;"></span>
                            <span class="w-5 h-5 rounded-full shadow-sm" style="background-color: #f97316;"></span>
                            <span class="w-5 h-5 rounded-full shadow-sm" style="background-color: #1c1917;"></span>
                            <span class="w-5 h-5 rounded-full shadow-sm" style="background-color: #0c0a09;"></span>
                        </div>
                    </div>

                    <!-- Preset 5: Robotics Purple AI Lab -->
                    <div 
                        @click="applyPreset('#7c3aed', '#6d28d9', '#06b6d4', '#18181b', '#27272a', '#c4b5fd', '#09090b', '#faf5ff', '#3b0764', '#18181b')" 
                        class="p-4 rounded-2xl border-2 border-slate-200 dark:border-slate-800 hover:border-emerald-500 bg-slate-50/60 dark:bg-slate-950/40 cursor-pointer transition space-y-2.5 group">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-slate-900 dark:text-white group-hover:text-emerald-400 transition">Robotics Purple & AI</span>
                            <span class="text-[10px] uppercase font-bold px-1.5 py-0.5 rounded bg-purple-500/10 text-purple-600">Innovation</span>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <span class="w-5 h-5 rounded-full shadow-sm" style="background-color: #7c3aed;"></span>
                            <span class="w-5 h-5 rounded-full shadow-sm" style="background-color: #06b6d4;"></span>
                            <span class="w-5 h-5 rounded-full shadow-sm" style="background-color: #18181b;"></span>
                            <span class="w-5 h-5 rounded-full shadow-sm" style="background-color: #09090b;"></span>
                        </div>
                    </div>

                    <!-- Preset 6: Stealth Midnight Gold -->
                    <div 
                        @click="applyPreset('#f59e0b', '#d97706', '#38bdf8', '#000000', '#171717', '#fde68a', '#000000', '#f8fafc', '#18181b', '#000000')" 
                        class="p-4 rounded-2xl border-2 border-slate-200 dark:border-slate-800 hover:border-emerald-500 bg-slate-50/60 dark:bg-slate-950/40 cursor-pointer transition space-y-2.5 group">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-slate-900 dark:text-white group-hover:text-emerald-400 transition">Stealth Midnight & Amber</span>
                            <span class="text-[10px] uppercase font-bold px-1.5 py-0.5 rounded bg-amber-500/10 text-amber-600">Luxury</span>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <span class="w-5 h-5 rounded-full shadow-sm" style="background-color: #f59e0b;"></span>
                            <span class="w-5 h-5 rounded-full shadow-sm" style="background-color: #38bdf8;"></span>
                            <span class="w-5 h-5 rounded-full shadow-sm" style="background-color: #000000;"></span>
                            <span class="w-5 h-5 rounded-full shadow-sm" style="background-color: #171717;"></span>
                        </div>
                    </div>

                </div>
            </div>

            <!-- 3. COMPREHENSIVE FULL WEBSITE COLOR MATRIX -->
            <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 border border-slate-200 dark:border-slate-800 shadow-sm space-y-6">
                <div class="border-b border-slate-100 dark:border-slate-800 pb-3 flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                            <i data-lucide="sliders" class="w-4 h-4 text-emerald-500"></i>
                            <span>Complete Website Color Matrix</span>
                        </h3>
                        <p class="text-xs text-slate-500">Fine-tune brand colors, page background, card surfaces, price highlights, and header/footer tones.</p>
                    </div>
                    <span class="text-[10px] font-bold px-2.5 py-1 rounded-full bg-emerald-500/10 text-emerald-600 dark:text-emerald-400">14 Dynamic Tokens</span>
                </div>

                <!-- Group A: Brand & Buttons -->
                <div class="space-y-3">
                    <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400 flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                        <span>A. Brand, CTAs & Accent Colors</span>
                    </h4>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                        
                        <!-- Primary Color -->
                        <div class="p-4 rounded-2xl border border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-950/40 space-y-2">
                            <label class="block text-xs font-bold text-slate-800 dark:text-slate-200">Primary Brand / Button Color *</label>
                            <p class="text-[10px] text-slate-400">Buttons, active tabs, checkout CTAs</p>
                            <div class="flex items-center gap-2 pt-1">
                                <input type="color" x-model="primaryColor" class="w-10 h-10 rounded-xl cursor-pointer border-0 bg-transparent p-0">
                                <input type="text" name="theme_primary_color" x-model="primaryColor" required class="flex-1 px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-mono font-bold uppercase outline-none">
                            </div>
                        </div>

                        <!-- Primary Hover -->
                        <div class="p-4 rounded-2xl border border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-950/40 space-y-2">
                            <label class="block text-xs font-bold text-slate-800 dark:text-slate-200">Primary Hover State Color *</label>
                            <p class="text-[10px] text-slate-400">Button hover and click interaction state</p>
                            <div class="flex items-center gap-2 pt-1">
                                <input type="color" x-model="primaryHover" class="w-10 h-10 rounded-xl cursor-pointer border-0 bg-transparent p-0">
                                <input type="text" name="theme_primary_hover" x-model="primaryHover" required class="flex-1 px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-mono font-bold uppercase outline-none">
                            </div>
                        </div>

                        <!-- Secondary Tech Accent -->
                        <div class="p-4 rounded-2xl border border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-950/40 space-y-2">
                            <label class="block text-xs font-bold text-slate-800 dark:text-slate-200">Secondary Accent Color *</label>
                            <p class="text-[10px] text-slate-400">Verified badges, in-stock pills, success highlights</p>
                            <div class="flex items-center gap-2 pt-1">
                                <input type="color" x-model="secondaryColor" class="w-10 h-10 rounded-xl cursor-pointer border-0 bg-transparent p-0">
                                <input type="text" name="theme_secondary_color" x-model="secondaryColor" required class="flex-1 px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-mono font-bold uppercase outline-none">
                            </div>
                        </div>

                        <!-- Price Color -->
                        <div class="p-4 rounded-2xl border border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-950/40 space-y-2">
                            <label class="block text-xs font-bold text-slate-800 dark:text-slate-200">Product Price Highlight Color *</label>
                            <p class="text-[10px] text-slate-400">Discount price tag, grand total amounts</p>
                            <div class="flex items-center gap-2 pt-1">
                                <input type="color" x-model="priceColor" class="w-10 h-10 rounded-xl cursor-pointer border-0 bg-transparent p-0">
                                <input type="text" name="theme_price_color" x-model="priceColor" required class="flex-1 px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-mono font-bold uppercase outline-none">
                            </div>
                        </div>

                        <!-- Sale Badge Color -->
                        <div class="p-4 rounded-2xl border border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-950/40 space-y-2">
                            <label class="block text-xs font-bold text-slate-800 dark:text-slate-200">Discount / Sale Badge Color *</label>
                            <p class="text-[10px] text-slate-400">'-25% OFF', flash sale ribbons</p>
                            <div class="flex items-center gap-2 pt-1">
                                <input type="color" x-model="saleBadgeColor" class="w-10 h-10 rounded-xl cursor-pointer border-0 bg-transparent p-0">
                                <input type="text" name="theme_sale_badge_color" x-model="saleBadgeColor" required class="flex-1 px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-mono font-bold uppercase outline-none">
                            </div>
                        </div>

                    </div>
                </div>

                <!-- Group B: Surfaces & Backgrounds -->
                <div class="space-y-3 pt-2">
                    <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400 flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-sky-500"></span>
                        <span>B. Surfaces, Canvas & Card Containers</span>
                    </h4>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                        
                        <!-- Body Background -->
                        <div class="p-4 rounded-2xl border border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-950/40 space-y-2">
                            <label class="block text-xs font-bold text-slate-800 dark:text-slate-200">Storefront Canvas Background</label>
                            <p class="text-[10px] text-slate-400">Overall page backdrop (e.g. #f8fafc)</p>
                            <div class="flex items-center gap-2 pt-1">
                                <input type="color" x-model="bodyBg" class="w-10 h-10 rounded-xl cursor-pointer border-0 bg-transparent p-0">
                                <input type="text" name="theme_body_bg" x-model="bodyBg" class="flex-1 px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-mono font-bold uppercase outline-none">
                            </div>
                        </div>

                        <!-- Card Background -->
                        <div class="p-4 rounded-2xl border border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-950/40 space-y-2">
                            <label class="block text-xs font-bold text-slate-800 dark:text-slate-200">Product Card Container Background</label>
                            <p class="text-[10px] text-slate-400">Cards, checkout forms, dialog containers</p>
                            <div class="flex items-center gap-2 pt-1">
                                <input type="color" x-model="cardBg" class="w-10 h-10 rounded-xl cursor-pointer border-0 bg-transparent p-0">
                                <input type="text" name="theme_card_bg" x-model="cardBg" class="flex-1 px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-mono font-bold uppercase outline-none">
                            </div>
                        </div>

                        <!-- Card Border -->
                        <div class="p-4 rounded-2xl border border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-950/40 space-y-2">
                            <label class="block text-xs font-bold text-slate-800 dark:text-slate-200">Card & Container Border Color</label>
                            <p class="text-[10px] text-slate-400">Divider lines and product tile outlines</p>
                            <div class="flex items-center gap-2 pt-1">
                                <input type="color" x-model="cardBorder" class="w-10 h-10 rounded-xl cursor-pointer border-0 bg-transparent p-0">
                                <input type="text" name="theme_card_border" x-model="cardBorder" class="flex-1 px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-mono font-bold uppercase outline-none">
                            </div>
                        </div>

                    </div>
                </div>

                <!-- Group C: Text & Header/Footer Elements -->
                <div class="space-y-3 pt-2">
                    <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400 flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                        <span>C. Typography Tones, Navigation & Footer</span>
                    </h4>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                        
                        <!-- Body Text Color -->
                        <div class="p-4 rounded-2xl border border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-950/40 space-y-2">
                            <label class="block text-xs font-bold text-slate-800 dark:text-slate-200">Main Body Text Color</label>
                            <p class="text-[10px] text-slate-400">Paragraphs, specifications, secondary labels</p>
                            <div class="flex items-center gap-2 pt-1">
                                <input type="color" x-model="textColor" class="w-10 h-10 rounded-xl cursor-pointer border-0 bg-transparent p-0">
                                <input type="text" name="theme_text_color" x-model="textColor" class="flex-1 px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-mono font-bold uppercase outline-none">
                            </div>
                        </div>

                        <!-- Heading Text Color -->
                        <div class="p-4 rounded-2xl border border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-950/40 space-y-2">
                            <label class="block text-xs font-bold text-slate-800 dark:text-slate-200">Headings & Section Titles Color</label>
                            <p class="text-[10px] text-slate-400">H1, H2, product names, category headlines</p>
                            <div class="flex items-center gap-2 pt-1">
                                <input type="color" x-model="headingColor" class="w-10 h-10 rounded-xl cursor-pointer border-0 bg-transparent p-0">
                                <input type="text" name="theme_heading_color" x-model="headingColor" class="flex-1 px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-mono font-bold uppercase outline-none">
                            </div>
                        </div>

                        <!-- Header Background -->
                        <div class="p-4 rounded-2xl border border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-950/40 space-y-2">
                            <label class="block text-xs font-bold text-slate-800 dark:text-slate-200">Top Navigation Navy Background</label>
                            <p class="text-[10px] text-slate-400">Utility strip and header navbar container</p>
                            <div class="flex items-center gap-2 pt-1">
                                <input type="color" x-model="headerBg" class="w-10 h-10 rounded-xl cursor-pointer border-0 bg-transparent p-0">
                                <input type="text" name="theme_header_bg" x-model="headerBg" class="flex-1 px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-mono font-bold uppercase outline-none">
                            </div>
                        </div>

                        <!-- Announcement Bar BG -->
                        <div class="p-4 rounded-2xl border border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-950/40 space-y-2">
                            <label class="block text-xs font-bold text-slate-800 dark:text-slate-200">Top Announcement Banner Background</label>
                            <p class="text-[10px] text-slate-400">Flash announcement stripe at top of storefront</p>
                            <div class="flex items-center gap-2 pt-1">
                                <input type="color" x-model="announcementBg" class="w-10 h-10 rounded-xl cursor-pointer border-0 bg-transparent p-0">
                                <input type="text" name="theme_announcement_bg" x-model="announcementBg" class="flex-1 px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-mono font-bold uppercase outline-none">
                            </div>
                        </div>

                        <!-- Announcement Bar Text -->
                        <div class="p-4 rounded-2xl border border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-950/40 space-y-2">
                            <label class="block text-xs font-bold text-slate-800 dark:text-slate-200">Announcement Banner Text Color</label>
                            <p class="text-[10px] text-slate-400">Font color for text inside announcement bar</p>
                            <div class="flex items-center gap-2 pt-1">
                                <input type="color" x-model="announcementText" class="w-10 h-10 rounded-xl cursor-pointer border-0 bg-transparent p-0">
                                <input type="text" name="theme_announcement_text_color" x-model="announcementText" class="flex-1 px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-mono font-bold uppercase outline-none">
                            </div>
                        </div>

                        <!-- Footer Background -->
                        <div class="p-4 rounded-2xl border border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-950/40 space-y-2">
                            <label class="block text-xs font-bold text-slate-800 dark:text-slate-200">Storefront Footer Background Color</label>
                            <p class="text-[10px] text-slate-400">Deep background for bottom mega footer</p>
                            <div class="flex items-center gap-2 pt-1">
                                <input type="color" x-model="footerBg" class="w-10 h-10 rounded-xl cursor-pointer border-0 bg-transparent p-0">
                                <input type="text" name="theme_footer_bg" x-model="footerBg" class="flex-1 px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-mono font-bold uppercase outline-none">
                            </div>
                        </div>

                        <!-- Footer Text Color -->
                        <div class="p-4 rounded-2xl border border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-950/40 space-y-2">
                            <label class="block text-xs font-bold text-slate-800 dark:text-slate-200">Footer Text Color</label>
                            <p class="text-[10px] text-slate-400">Copyright, links, and text in mega footer</p>
                            <div class="flex items-center gap-2 pt-1">
                                <input type="color" x-model="footerText" class="w-10 h-10 rounded-xl cursor-pointer border-0 bg-transparent p-0">
                                <input type="text" name="theme_footer_text_color" x-model="footerText" class="flex-1 px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-mono font-bold uppercase outline-none">
                            </div>
                        </div>

                    </div>
                </div>

            </div>

            <!-- 4. LIVE INTERACTIVE STOREFRONT PREVIEW SANDBOX -->
            <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 border border-slate-200 dark:border-slate-800 shadow-sm space-y-4">
                <div class="border-b border-slate-100 dark:border-slate-800 pb-3 flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                            <i data-lucide="eye" class="w-4 h-4 text-emerald-500"></i>
                            <span>Live Typography & Color Component Simulation</span>
                        </h3>
                        <p class="text-xs text-slate-500">Real-time simulation rendered using selected Google fonts (<span class="font-bold text-emerald-600" x-text="fontBody"></span> &amp; <span class="font-bold text-emerald-600" x-text="fontHeading"></span>) and custom color palette.</p>
                    </div>
                    <span class="text-[10px] font-bold px-2.5 py-1 rounded-full bg-emerald-500/10 text-emerald-600 dark:text-emerald-400">Dynamic Preview</span>
                </div>

                <!-- Simulation Sandbox View -->
                <div class="rounded-2xl border overflow-hidden shadow-lg transition-all duration-200" :style="{ borderColor: cardBorder }">
                    
                    <!-- Simulated Announcement Bar -->
                    <div :style="{ backgroundColor: announcementBg, color: announcementText }" class="px-4 py-2 text-xs font-semibold flex items-center justify-between transition-colors duration-200">
                        <div class="flex items-center gap-2">
                            <i data-lucide="zap" class="w-3.5 h-3.5"></i>
                            <span>⚡ Flash Sale Live: 30% OFF On Robotics &amp; Electronic Hardware!</span>
                        </div>
                        <span class="text-[11px] opacity-80 hidden sm:block">Hotline: +880 1700-112233</span>
                    </div>

                    <!-- Simulated Header & Product Card Area -->
                    <div class="p-6 space-y-6 transition-colors duration-200" :style="{ backgroundColor: bodyBg, color: textColor }">
                        
                        <!-- Mini Header Preview -->
                        <div class="flex items-center justify-between p-4 rounded-xl border transition-colors duration-200" :style="{ backgroundColor: headerBg, borderColor: cardBorder }">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 rounded-lg flex items-center justify-center text-white font-black text-xs shadow" :style="{ backgroundColor: primaryColor }">
                                    <i data-lucide="cpu" class="w-4 h-4 text-white"></i>
                                </div>
                                <div class="font-black text-base text-white" :style="{ fontFamily: fontHeading }">
                                    DREAMERS <span :style="{ color: primaryColor }">PCB</span>
                                </div>
                            </div>

                            <div class="flex items-center gap-3">
                                <div class="relative hidden sm:block">
                                    <input type="text" placeholder="Search 5,000+ components..." readonly class="px-3 py-1.5 text-xs rounded-lg border-2 bg-slate-900 text-white w-52 outline-none" :style="{ borderColor: primaryColor, fontFamily: fontBody }">
                                </div>
                                <button type="button" class="px-3.5 py-1.5 rounded-xl text-white text-xs font-bold shadow transition" :style="{ backgroundColor: primaryColor, fontFamily: fontBody }">
                                    <span>Cart (2)</span>
                                </button>
                            </div>
                        </div>

                        <!-- Simulated Component Swatches -->
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            
                            <!-- Card 1: Button State -->
                            <div class="p-4 rounded-xl border space-y-3 transition-colors duration-200" :style="{ backgroundColor: cardBg, borderColor: cardBorder }">
                                <h4 class="text-sm font-black" :style="{ fontFamily: fontHeading, color: headingColor }">
                                    ESP32-WROOM-32D IoT Microcontroller
                                </h4>
                                <p class="text-xs" :style="{ fontFamily: fontBody, color: textColor }">
                                    Dual-core 240MHz Wi-Fi and Bluetooth BLE development board with integrated antenna.
                                </p>
                                <button type="button" class="w-full py-2.5 rounded-xl text-white font-extrabold text-xs shadow-md transition transform active:scale-95 flex items-center justify-center gap-2" :style="{ backgroundColor: primaryColor, fontFamily: fontBody }">
                                    <i data-lucide="shopping-cart" class="w-4 h-4"></i>
                                    <span>Add to Cart &bull; Buy Now</span>
                                </button>
                            </div>

                            <!-- Card 2: Badges & SKU Specs -->
                            <div class="p-4 rounded-xl border space-y-3 transition-colors duration-200" :style="{ backgroundColor: cardBg, borderColor: cardBorder }">
                                <span class="text-[10px] font-mono font-bold px-2 py-0.5 rounded" :style="{ fontFamily: fontMono, backgroundColor: secondaryColor + '20', color: secondaryColor }">
                                    SKU: ESP32-WRM-32D-BD
                                </span>
                                <div class="flex flex-wrap gap-2 pt-1">
                                    <span class="px-2.5 py-1 rounded-full text-white text-[10px] font-black" :style="{ backgroundColor: saleBadgeColor, fontFamily: fontBody }">
                                        -35% FLASH SALE
                                    </span>
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold flex items-center gap-1" :style="{ backgroundColor: secondaryColor + '20', color: secondaryColor, fontFamily: fontBody }">
                                        <i data-lucide="check" class="w-3 h-3"></i>
                                        <span>In Stock (500+ Units)</span>
                                    </span>
                                </div>
                                <p class="text-xs" :style="{ fontFamily: fontBody, color: textColor }">
                                    High-power LDO regulator and Silicon Labs CP2102 USB-to-UART bridge.
                                </p>
                            </div>

                            <!-- Card 3: Price Display -->
                            <div class="p-4 rounded-xl border space-y-2 transition-colors duration-200" :style="{ backgroundColor: cardBg, borderColor: cardBorder }">
                                <span class="text-[11px] font-bold uppercase" :style="{ color: textColor, opacity: 0.7 }">Real-Time Price Formatting</span>
                                <div>
                                    <div class="text-2xl font-black font-mono" :style="{ color: priceColor, fontFamily: fontMono }">
                                        ৳450.00
                                    </div>
                                    <span class="text-xs line-through" :style="{ color: textColor, opacity: 0.5 }">৳650.00</span>
                                </div>
                                <button type="button" class="w-full py-2 rounded-xl text-xs font-bold border transition" :style="{ borderColor: primaryColor, color: primaryColor, fontFamily: fontBody }">
                                    <span>Quick Order with COD</span>
                                </button>
                            </div>

                        </div>

                        <!-- Simulated Mini Footer -->
                        <div :style="{ backgroundColor: footerBg, color: footerText, fontFamily: fontBody }" class="p-4 rounded-xl text-xs flex items-center justify-between transition-colors duration-200">
                            <div>
                                <span class="font-bold text-white" :style="{ fontFamily: fontHeading }">DREAMERS PCB</span> &bull; <span>The Hardware Engineer's Choice</span>
                            </div>
                            <span class="text-[11px] opacity-75">© 2026 DREAMERS PCB &bull; All Rights Reserved</span>
                        </div>

                    </div>

                </div>
            </div>

        </div>

        <!-- ========================================================= -->
        <!-- TAB 3: HOMEPAGE SLIDER & HERO BANNERS                     -->
        <!-- ========================================================= -->
        <div x-show="activeTab === 'slider'" x-cloak class="space-y-6">

            <!-- General Slider Timing & Behaviour -->
            <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 border border-slate-200 dark:border-slate-800 shadow-sm space-y-4">
                <div class="border-b border-slate-100 dark:border-slate-800 pb-3 flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                            <i data-lucide="sliders" class="w-4 h-4 text-daraz-orange"></i>
                            <span>Homepage Slider Behaviour & Autoplay</span>
                        </h3>
                        <p class="text-xs text-slate-500">Configure slide rotation speeds and active slide cards.</p>
                    </div>
                    <span class="text-[10px] font-bold px-2.5 py-1 rounded-full bg-daraz-orange/10 text-daraz-orange">Hero Main Slider</span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="p-4 rounded-2xl border border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-950/40 space-y-2">
                        <label class="block text-xs font-bold text-slate-800 dark:text-slate-200">Autoplay Rotation Interval (Milliseconds)</label>
                        <p class="text-[10px] text-slate-400">Duration each slide is shown (default: 5000ms = 5 seconds)</p>
                        <input type="number" name="slider_autoplay_interval" value="{{ $settings['slider_autoplay_interval'] ?? '5000' }}" min="2000" max="20000" step="500" class="w-full px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-mono font-bold outline-none focus:ring-2 focus:ring-emerald-500/20">
                    </div>

                    <div class="p-4 rounded-2xl border border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-950/40 flex items-center gap-3">
                        <i data-lucide="info" class="w-8 h-8 text-emerald-500 shrink-0"></i>
                        <p class="text-xs text-slate-500 leading-relaxed">
                            <strong>Tip:</strong> For best visual quality on modern high-DPI displays, use <strong>21:9 or 16:9</strong> widescreen landscape images (recommended: 1920x820px or 1200x520px). You can either upload an image file from your device or provide a direct web image URL.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Slide 1 Configuration Card -->
            <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 border border-slate-200 dark:border-slate-800 shadow-sm space-y-5">
                <div class="border-b border-slate-100 dark:border-slate-800 pb-3 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="w-6 h-6 rounded-full bg-daraz-orange text-white text-xs font-black flex items-center justify-center">1</span>
                        <h3 class="text-sm font-bold text-slate-900 dark:text-white">Hero Slide 1</h3>
                    </div>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="slider_1_active" value="1" {{ ($settings['slider_1_active'] ?? '1') === '1' ? 'checked' : '' }} class="rounded text-emerald-600 focus:ring-emerald-500">
                        <span class="text-xs font-bold text-slate-700 dark:text-slate-300">Slide Active</span>
                    </label>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-12 gap-5">
                    <!-- Left: Form Inputs -->
                    <div class="lg:col-span-8 space-y-4">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Badge Tag</label>
                                <input type="text" name="slider_1_badge" value="{{ $settings['slider_1_badge'] ?? 'Verified Electronic Component' }}" placeholder="e.g. Verified Electronic Component" class="w-full px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-semibold outline-none focus:ring-2 focus:ring-emerald-500/20">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Button Text</label>
                                <input type="text" name="slider_1_button_text" value="{{ $settings['slider_1_button_text'] ?? 'Explore Collection' }}" placeholder="e.g. Explore Collection" class="w-full px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-semibold outline-none focus:ring-2 focus:ring-emerald-500/20">
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Slide Heading Title *</label>
                            <input type="text" name="slider_1_title" value="{{ $settings['slider_1_title'] ?? 'STM32 & ESP32-S3 IoT Development Boards' }}" placeholder="e.g. STM32 & ESP32-S3 IoT Development Boards" class="w-full px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-bold outline-none focus:ring-2 focus:ring-emerald-500/20">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Subtitle / Description</label>
                            <textarea name="slider_1_subtitle" rows="2" placeholder="Brief promotional description..." class="w-full px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs outline-none focus:ring-2 focus:ring-emerald-500/20">{{ $settings['slider_1_subtitle'] ?? 'Official Enterprise Electronics Distribution in Bangladesh' }}</textarea>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Target Link URL</label>
                                <input type="text" name="slider_1_link" value="{{ $settings['slider_1_link'] ?? '/shop' }}" placeholder="/shop or https://..." class="w-full px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-mono outline-none focus:ring-2 focus:ring-emerald-500/20">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Or Image Direct URL</label>
                                <input type="url" name="slider_1_image_url" placeholder="https://images.unsplash.com/..." value="{{ filter_var($settings['slider_1_image'] ?? '', FILTER_VALIDATE_URL) ? $settings['slider_1_image'] : '' }}" class="w-full px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-mono outline-none focus:ring-2 focus:ring-emerald-500/20">
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Upload New Slide Image File</label>
                            <input type="file" name="slider_1_image" accept="image/*" class="w-full text-xs text-slate-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100">
                        </div>
                    </div>

                    <!-- Right: Image Preview Box -->
                    <div class="lg:col-span-4 space-y-2">
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">Live Preview</label>
                        <div class="aspect-[21/9] sm:aspect-[16/9] rounded-2xl overflow-hidden bg-slate-900 border border-slate-200 dark:border-slate-800 relative shadow-inner">
                            @php $img1 = $settings['slider_1_image'] ?? 'https://images.unsplash.com/photo-1518770660439-4636190af475?w=1200&auto=format&fit=crop&q=80'; @endphp
                            <img src="{{ Str::startsWith($img1, 'http') ? $img1 : asset($img1) }}" alt="Slide 1 Preview" class="w-full h-full object-cover">
                            <div class="absolute inset-0 bg-gradient-to-t from-slate-950/90 via-slate-950/30 to-transparent p-3 flex flex-col justify-end text-white">
                                <span class="text-[9px] font-black uppercase text-daraz-orange">{{ $settings['slider_1_badge'] ?? 'Verified' }}</span>
                                <h5 class="text-xs font-black line-clamp-1">{{ $settings['slider_1_title'] ?? 'Slide 1 Title' }}</h5>
                            </div>
                        </div>
                        @if(!empty($settings['slider_1_image']))
                        <label class="flex items-center gap-1.5 text-[10px] text-rose-500 cursor-pointer pt-1">
                            <input type="checkbox" name="remove_slider_1_image" value="1" class="rounded">
                            <span>Reset Slide 1 image to default</span>
                        </label>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Slide 2 Configuration Card -->
            <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 border border-slate-200 dark:border-slate-800 shadow-sm space-y-5">
                <div class="border-b border-slate-100 dark:border-slate-800 pb-3 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="w-6 h-6 rounded-full bg-emerald-600 text-white text-xs font-black flex items-center justify-center">2</span>
                        <h3 class="text-sm font-bold text-slate-900 dark:text-white">Hero Slide 2</h3>
                    </div>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="slider_2_active" value="1" {{ ($settings['slider_2_active'] ?? '1') === '1' ? 'checked' : '' }} class="rounded text-emerald-600 focus:ring-emerald-500">
                        <span class="text-xs font-bold text-slate-700 dark:text-slate-300">Slide Active</span>
                    </label>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-12 gap-5">
                    <div class="lg:col-span-8 space-y-4">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Badge Tag</label>
                                <input type="text" name="slider_2_badge" value="{{ $settings['slider_2_badge'] ?? 'Premium Hardware' }}" placeholder="e.g. Premium Hardware" class="w-full px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-semibold outline-none focus:ring-2 focus:ring-emerald-500/20">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Button Text</label>
                                <input type="text" name="slider_2_button_text" value="{{ $settings['slider_2_button_text'] ?? 'Shop Equipment' }}" placeholder="e.g. Shop Equipment" class="w-full px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-semibold outline-none focus:ring-2 focus:ring-emerald-500/20">
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Slide Heading Title *</label>
                            <input type="text" name="slider_2_title" value="{{ $settings['slider_2_title'] ?? 'Professional Quick 861DW Soldering Rework Stations' }}" placeholder="e.g. Professional Quick 861DW Soldering Rework Stations" class="w-full px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-bold outline-none focus:ring-2 focus:ring-emerald-500/20">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Subtitle / Description</label>
                            <textarea name="slider_2_subtitle" rows="2" placeholder="Brief promotional description..." class="w-full px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs outline-none focus:ring-2 focus:ring-emerald-500/20">{{ $settings['slider_2_subtitle'] ?? '1000W High Power Digital SMD Rework Master Kit' }}</textarea>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Target Link URL</label>
                                <input type="text" name="slider_2_link" value="{{ $settings['slider_2_link'] ?? '/shop' }}" placeholder="/shop or https://..." class="w-full px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-mono outline-none focus:ring-2 focus:ring-emerald-500/20">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Or Image Direct URL</label>
                                <input type="url" name="slider_2_image_url" placeholder="https://images.unsplash.com/..." value="{{ filter_var($settings['slider_2_image'] ?? '', FILTER_VALIDATE_URL) ? $settings['slider_2_image'] : '' }}" class="w-full px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-mono outline-none focus:ring-2 focus:ring-emerald-500/20">
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Upload New Slide Image File</label>
                            <input type="file" name="slider_2_image" accept="image/*" class="w-full text-xs text-slate-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100">
                        </div>
                    </div>

                    <div class="lg:col-span-4 space-y-2">
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">Live Preview</label>
                        <div class="aspect-[21/9] sm:aspect-[16/9] rounded-2xl overflow-hidden bg-slate-900 border border-slate-200 dark:border-slate-800 relative shadow-inner">
                            @php $img2 = $settings['slider_2_image'] ?? 'https://images.unsplash.com/photo-1581092160607-ee22621dd758?w=1200&auto=format&fit=crop&q=80'; @endphp
                            <img src="{{ Str::startsWith($img2, 'http') ? $img2 : asset($img2) }}" alt="Slide 2 Preview" class="w-full h-full object-cover">
                            <div class="absolute inset-0 bg-gradient-to-t from-slate-950/90 via-slate-950/30 to-transparent p-3 flex flex-col justify-end text-white">
                                <span class="text-[9px] font-black uppercase text-emerald-400">{{ $settings['slider_2_badge'] ?? 'Hardware' }}</span>
                                <h5 class="text-xs font-black line-clamp-1">{{ $settings['slider_2_title'] ?? 'Slide 2 Title' }}</h5>
                            </div>
                        </div>
                        @if(!empty($settings['slider_2_image']))
                        <label class="flex items-center gap-1.5 text-[10px] text-rose-500 cursor-pointer pt-1">
                            <input type="checkbox" name="remove_slider_2_image" value="1" class="rounded">
                            <span>Reset Slide 2 image to default</span>
                        </label>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Slide 3 Configuration Card -->
            <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 border border-slate-200 dark:border-slate-800 shadow-sm space-y-5">
                <div class="border-b border-slate-100 dark:border-slate-800 pb-3 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="w-6 h-6 rounded-full bg-sky-600 text-white text-xs font-black flex items-center justify-center">3</span>
                        <h3 class="text-sm font-bold text-slate-900 dark:text-white">Hero Slide 3</h3>
                    </div>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="slider_3_active" value="1" {{ ($settings['slider_3_active'] ?? '0') === '1' ? 'checked' : '' }} class="rounded text-emerald-600 focus:ring-emerald-500">
                        <span class="text-xs font-bold text-slate-700 dark:text-slate-300">Slide Active</span>
                    </label>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-12 gap-5">
                    <div class="lg:col-span-8 space-y-4">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Badge Tag</label>
                                <input type="text" name="slider_3_badge" value="{{ $settings['slider_3_badge'] ?? 'New Arrival' }}" placeholder="e.g. New Arrival" class="w-full px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-semibold outline-none focus:ring-2 focus:ring-emerald-500/20">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Button Text</label>
                                <input type="text" name="slider_3_button_text" value="{{ $settings['slider_3_button_text'] ?? 'View Deals' }}" placeholder="e.g. View Deals" class="w-full px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-semibold outline-none focus:ring-2 focus:ring-emerald-500/20">
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Slide Heading Title *</label>
                            <input type="text" name="slider_3_title" value="{{ $settings['slider_3_title'] ?? 'Raspberry Pi 4 Model B & High-Speed Sensors' }}" placeholder="e.g. Raspberry Pi 4 Model B & High-Speed Sensors" class="w-full px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-bold outline-none focus:ring-2 focus:ring-emerald-500/20">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Subtitle / Description</label>
                            <textarea name="slider_3_subtitle" rows="2" placeholder="Brief promotional description..." class="w-full px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs outline-none focus:ring-2 focus:ring-emerald-500/20">{{ $settings['slider_3_subtitle'] ?? 'Industrial Grade Single Board Computers & Robotics Kits' }}</textarea>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Target Link URL</label>
                                <input type="text" name="slider_3_link" value="{{ $settings['slider_3_link'] ?? '/shop' }}" placeholder="/shop or https://..." class="w-full px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-mono outline-none focus:ring-2 focus:ring-emerald-500/20">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Or Image Direct URL</label>
                                <input type="url" name="slider_3_image_url" placeholder="https://images.unsplash.com/..." value="{{ filter_var($settings['slider_3_image'] ?? '', FILTER_VALIDATE_URL) ? $settings['slider_3_image'] : '' }}" class="w-full px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-mono outline-none focus:ring-2 focus:ring-emerald-500/20">
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Upload New Slide Image File</label>
                            <input type="file" name="slider_3_image" accept="image/*" class="w-full text-xs text-slate-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100">
                        </div>
                    </div>

                    <div class="lg:col-span-4 space-y-2">
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">Live Preview</label>
                        <div class="aspect-[21/9] sm:aspect-[16/9] rounded-2xl overflow-hidden bg-slate-900 border border-slate-200 dark:border-slate-800 relative shadow-inner">
                            @php $img3 = $settings['slider_3_image'] ?? 'https://images.unsplash.com/photo-1550751827-4bd374c3f58b?w=1200&auto=format&fit=crop&q=80'; @endphp
                            <img src="{{ Str::startsWith($img3, 'http') ? $img3 : asset($img3) }}" alt="Slide 3 Preview" class="w-full h-full object-cover">
                            <div class="absolute inset-0 bg-gradient-to-t from-slate-950/90 via-slate-950/30 to-transparent p-3 flex flex-col justify-end text-white">
                                <span class="text-[9px] font-black uppercase text-sky-400">{{ $settings['slider_3_badge'] ?? 'Robotics' }}</span>
                                <h5 class="text-xs font-black line-clamp-1">{{ $settings['slider_3_title'] ?? 'Slide 3 Title' }}</h5>
                            </div>
                        </div>
                        @if(!empty($settings['slider_3_image']))
                        <label class="flex items-center gap-1.5 text-[10px] text-rose-500 cursor-pointer pt-1">
                            <input type="checkbox" name="remove_slider_3_image" value="1" class="rounded">
                            <span>Reset Slide 3 image to default</span>
                        </label>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Promo Mini Strips Configuration -->
            <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 border border-slate-200 dark:border-slate-800 shadow-sm space-y-5">
                <div class="border-b border-slate-100 dark:border-slate-800 pb-3">
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                        <i data-lucide="tag" class="w-4 h-4 text-emerald-500"></i>
                        <span>Promo Mini Strips (Beneath Main Slider)</span>
                    </h3>
                    <p class="text-xs text-slate-500">The two promotional callout strips directly below the homepage slider.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    
                    <!-- Strip 1 -->
                    <div class="p-5 rounded-2xl border border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-950/40 space-y-3">
                        <div class="flex items-center gap-2">
                            <span class="w-5 h-5 rounded-full bg-sky-500 text-white text-[10px] font-black flex items-center justify-center">1</span>
                            <h4 class="text-xs font-bold text-slate-800 dark:text-slate-200">Left Promo Strip</h4>
                        </div>

                        <div>
                            <label class="block text-[11px] font-semibold text-slate-600 dark:text-slate-400 mb-1">Small Tag / Category</label>
                            <input type="text" name="promo_strip_1_tag" value="{{ $settings['promo_strip_1_tag'] ?? 'IoT Dev Boards' }}" class="w-full px-3 py-1.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-semibold outline-none">
                        </div>

                        <div>
                            <label class="block text-[11px] font-semibold text-slate-600 dark:text-slate-400 mb-1">Main Headline</label>
                            <input type="text" name="promo_strip_1_title" value="{{ $settings['promo_strip_1_title'] ?? 'ESP32-S3 AI Vision Modules' }}" class="w-full px-3 py-1.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-bold outline-none">
                        </div>

                        <div>
                            <label class="block text-[11px] font-semibold text-slate-600 dark:text-slate-400 mb-1">Offer / Highlight Text</label>
                            <input type="text" name="promo_strip_1_offer" value="{{ $settings['promo_strip_1_offer'] ?? 'From ৳650 Only' }}" class="w-full px-3 py-1.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs text-emerald-600 font-bold outline-none">
                        </div>

                        <div>
                            <label class="block text-[11px] font-semibold text-slate-600 dark:text-slate-400 mb-1">Link URL</label>
                            <input type="text" name="promo_strip_1_link" value="{{ $settings['promo_strip_1_link'] ?? '/shop?search=ESP32' }}" class="w-full px-3 py-1.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-mono outline-none">
                        </div>
                    </div>

                    <!-- Strip 2 -->
                    <div class="p-5 rounded-2xl border border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-950/40 space-y-3">
                        <div class="flex items-center gap-2">
                            <span class="w-5 h-5 rounded-full bg-rose-500 text-white text-[10px] font-black flex items-center justify-center">2</span>
                            <h4 class="text-xs font-bold text-slate-800 dark:text-slate-200">Right Promo Strip</h4>
                        </div>

                        <div>
                            <label class="block text-[11px] font-semibold text-slate-600 dark:text-slate-400 mb-1">Small Tag / Category</label>
                            <input type="text" name="promo_strip_2_tag" value="{{ $settings['promo_strip_2_tag'] ?? 'Soldering Equipment' }}" class="w-full px-3 py-1.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-semibold outline-none">
                        </div>

                        <div>
                            <label class="block text-[11px] font-semibold text-slate-600 dark:text-slate-400 mb-1">Main Headline</label>
                            <input type="text" name="promo_strip_2_title" value="{{ $settings['promo_strip_2_title'] ?? 'Quick 861DW 1000W Rework' }}" class="w-full px-3 py-1.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-bold outline-none">
                        </div>

                        <div>
                            <label class="block text-[11px] font-semibold text-slate-600 dark:text-slate-400 mb-1">Offer / Highlight Text</label>
                            <input type="text" name="promo_strip_2_offer" value="{{ $settings['promo_strip_2_offer'] ?? 'Official 1-Year Warranty' }}" class="w-full px-3 py-1.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs text-amber-600 font-bold outline-none">
                        </div>

                        <div>
                            <label class="block text-[11px] font-semibold text-slate-600 dark:text-slate-400 mb-1">Link URL</label>
                            <input type="text" name="promo_strip_2_link" value="{{ $settings['promo_strip_2_link'] ?? '/shop?search=Quick' }}" class="w-full px-3 py-1.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-mono outline-none">
                        </div>
                    </div>

                </div>
            </div>

        </div>

        <!-- ========================================================= -->
        <!-- TAB 4: ORDERS, DELIVERY & E-COMMERCE AUTOMATION           -->
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
        <!-- TAB 8: AD SETUP & CONNECT (GOOGLE, FACEBOOK, ETC)         -->
        <!-- ========================================================= -->
        <div x-show="activeTab === 'tracking'" x-cloak class="space-y-6">
            
            <!-- Live Catalog Feeds for Facebook & Google Ads -->
            <div class="bg-gradient-to-br from-slate-900 to-slate-950 text-white rounded-3xl p-6 border border-slate-800 shadow-xl space-y-4">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-800 pb-4">
                    <div>
                        <span class="text-[10px] font-mono uppercase tracking-widest text-emerald-400 font-bold">Dynamic Product Catalog Feeds</span>
                        <h3 class="text-base font-extrabold text-white flex items-center gap-2 mt-0.5">
                            <i data-lucide="rss" class="w-5 h-5 text-emerald-400"></i>
                            <span>Facebook Catalog & Google Merchant Center Live Sync</span>
                        </h3>
                        <p class="text-xs text-slate-400 mt-1">Copy these automated XML feed URLs into Facebook Commerce Manager and Google Merchant Center to run dynamic retargeting ads and Google Shopping campaigns.</p>
                    </div>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 text-xs font-mono font-bold self-start">
                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                        <span>Auto-Sync Active</span>
                    </span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-1">
                    <!-- Facebook Feed Card -->
                    <div class="p-4 rounded-2xl bg-slate-800/60 border border-slate-700/80 space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-sky-400 flex items-center gap-1.5">
                                <i data-lucide="facebook" class="w-4 h-4"></i>
                                <span>Meta / Facebook & Instagram Catalog Feed</span>
                            </span>
                            <a href="{{ route('feed.facebook') }}" target="_blank" class="text-[11px] font-bold text-slate-300 hover:text-white hover:underline flex items-center gap-1">
                                <span>View XML</span>
                                <i data-lucide="external-link" class="w-3 h-3"></i>
                            </a>
                        </div>
                        <div class="flex items-center gap-2">
                            <input type="text" readonly value="{{ route('feed.facebook') }}" id="fbFeedInput" class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-700 text-[11px] font-mono text-emerald-300 select-all outline-none">
                            <button type="button" onclick="navigator.clipboard.writeText(document.getElementById('fbFeedInput').value); alert('Facebook Catalog Feed URL copied to clipboard!');" class="px-3 py-2 rounded-xl bg-sky-600 hover:bg-sky-500 text-white text-xs font-bold shrink-0 transition flex items-center gap-1">
                                <i data-lucide="copy" class="w-3.5 h-3.5"></i>
                                <span>Copy</span>
                            </button>
                        </div>
                        <p class="text-[10px] text-slate-400">Paste in <strong>Meta Business Suite &rarr; Commerce &rarr; Data Sources &rarr; Data Feed URL</strong>.</p>
                    </div>

                    <!-- Google Merchant Feed Card -->
                    <div class="p-4 rounded-2xl bg-slate-800/60 border border-slate-700/80 space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-amber-400 flex items-center gap-1.5">
                                <i data-lucide="shopping-bag" class="w-4 h-4"></i>
                                <span>Google Merchant Center Shopping Feed</span>
                            </span>
                            <a href="{{ route('feed.google') }}" target="_blank" class="text-[11px] font-bold text-slate-300 hover:text-white hover:underline flex items-center gap-1">
                                <span>View XML</span>
                                <i data-lucide="external-link" class="w-3 h-3"></i>
                            </a>
                        </div>
                        <div class="flex items-center gap-2">
                            <input type="text" readonly value="{{ route('feed.google') }}" id="googleFeedInput" class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-700 text-[11px] font-mono text-emerald-300 select-all outline-none">
                            <button type="button" onclick="navigator.clipboard.writeText(document.getElementById('googleFeedInput').value); alert('Google Merchant Shopping Feed URL copied to clipboard!');" class="px-3 py-2 rounded-xl bg-amber-600 hover:bg-amber-500 text-white text-xs font-bold shrink-0 transition flex items-center gap-1">
                                <i data-lucide="copy" class="w-3.5 h-3.5"></i>
                                <span>Copy</span>
                            </button>
                        </div>
                        <p class="text-[10px] text-slate-400">Paste in <strong>Google Merchant Center &rarr; Products &rarr; Feeds &rarr; Scheduled Fetch URL</strong>.</p>
                    </div>
                </div>
            </div>

            <!-- Facebook & Meta Marketing Suite -->
            <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 border border-slate-200 dark:border-slate-800 shadow-sm space-y-5">
                <div class="border-b border-slate-100 dark:border-slate-800 pb-3 flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                            <i data-lucide="facebook" class="w-4 h-4 text-blue-600"></i>
                            <span>Facebook / Meta Ads Setup & Domain Verification</span>
                        </h3>
                        <p class="text-xs text-slate-500">Connect Facebook Pixel, Meta Conversions API (CAPI), and verify your custom domain.</p>
                    </div>
                    <span class="text-[10px] font-bold px-2.5 py-1 rounded-full bg-blue-500/10 text-blue-600 dark:text-blue-400">Meta Marketing</span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <!-- Meta Pixel ID -->
                    <div class="p-4 rounded-2xl border border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-950/40 space-y-3">
                        <div class="flex items-center justify-between">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="facebook_pixel_enabled" value="1" {{ ($settings['facebook_pixel_enabled'] ?? '0') === '1' ? 'checked' : '' }} class="rounded text-emerald-500 focus:ring-emerald-500">
                                <span class="text-xs font-bold text-slate-800 dark:text-slate-200">Meta / Facebook Pixel</span>
                            </label>
                            <span class="text-[10px] font-bold px-2 py-0.5 rounded-full {{ ($settings['facebook_pixel_enabled'] ?? '0') === '1' ? 'bg-emerald-500/10 text-emerald-600' : 'bg-slate-200 dark:bg-slate-800 text-slate-400' }}">
                                {{ ($settings['facebook_pixel_enabled'] ?? '0') === '1' ? 'Active' : 'Disabled' }}
                            </span>
                        </div>
                        <p class="text-[11px] text-slate-500">Automatically tracks PageView, AddToCart, and Purchase conversions.</p>
                        <div>
                            <label class="block text-[11px] font-bold text-slate-600 dark:text-slate-400 mb-1">Meta Pixel ID (15-16 digits)</label>
                            <input type="text" name="facebook_pixel_id" value="{{ $settings['facebook_pixel_id'] ?? '' }}" placeholder="e.g. 1234567890123456" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-mono font-bold outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>

                    <!-- Facebook Domain Verification -->
                    <div class="p-4 rounded-2xl border border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-950/40 space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-slate-800 dark:text-slate-200">Facebook Domain Verification</span>
                            <span class="text-[10px] font-mono text-slate-400">Meta Business Manager</span>
                        </div>
                        <p class="text-[11px] text-slate-500">Required by Apple iOS 14.5+ App Tracking Transparency to verify domain ownership.</p>
                        <div>
                            <label class="block text-[11px] font-bold text-slate-600 dark:text-slate-400 mb-1">Verification Meta Content String</label>
                            <input type="text" name="facebook_domain_verification" value="{{ $settings['facebook_domain_verification'] ?? '' }}" placeholder="e.g. 1234567890abcdefghijklmnopqrstuv" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-mono outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>

                    <!-- Meta Conversions API (CAPI) Token -->
                    <div class="p-4 rounded-2xl border border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-950/40 space-y-3">
                        <div class="flex items-center justify-between">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="facebook_capi_enabled" value="1" {{ ($settings['facebook_capi_enabled'] ?? '0') === '1' ? 'checked' : '' }} class="rounded text-emerald-500 focus:ring-emerald-500">
                                <span class="text-xs font-bold text-slate-800 dark:text-slate-200">Conversions API (CAPI Server Token)</span>
                            </label>
                            <span class="text-[10px] font-bold px-2 py-0.5 rounded-full {{ ($settings['facebook_capi_enabled'] ?? '0') === '1' ? 'bg-emerald-500/10 text-emerald-600' : 'bg-slate-200 dark:bg-slate-800 text-slate-400' }}">
                                {{ ($settings['facebook_capi_enabled'] ?? '0') === '1' ? 'Active' : 'Disabled' }}
                            </span>
                        </div>
                        <p class="text-[11px] text-slate-500">Bypasses ad-blockers and iOS privacy restrictions with server-to-server tracking.</p>
                        <div>
                            <label class="block text-[11px] font-bold text-slate-600 dark:text-slate-400 mb-1">Meta CAPI Access Token</label>
                            <input type="password" name="facebook_conversions_api_token" value="{{ $settings['facebook_conversions_api_token'] ?? '' }}" placeholder="e.g. EAABwz..." class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-mono outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>

                    <!-- Facebook Ad Account ID / App ID -->
                    <div class="p-4 rounded-2xl border border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-950/40 space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-slate-800 dark:text-slate-200">Facebook Ad Account & App ID</span>
                            <span class="text-[10px] font-mono text-slate-400">Meta Marketing API</span>
                        </div>
                        <p class="text-[11px] text-slate-500">Used for campaign reporting and social login app linking.</p>
                        <div>
                            <label class="block text-[11px] font-bold text-slate-600 dark:text-slate-400 mb-1">Ad Account ID / Meta App ID</label>
                            <input type="text" name="facebook_ad_account_id" value="{{ $settings['facebook_ad_account_id'] ?? '' }}" placeholder="act_1234567890 or App ID" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-mono outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Google Ads & Marketing Suite -->
            <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 border border-slate-200 dark:border-slate-800 shadow-sm space-y-5">
                <div class="border-b border-slate-100 dark:border-slate-800 pb-3 flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                            <i data-lucide="target" class="w-4 h-4 text-amber-500"></i>
                            <span>Google Ads & Conversion Tracking Setup</span>
                        </h3>
                        <p class="text-xs text-slate-500">Track Google Search, Display, and Shopping campaign purchase conversions.</p>
                    </div>
                    <span class="text-[10px] font-bold px-2.5 py-1 rounded-full bg-amber-500/10 text-amber-600 dark:text-amber-400">Google Ads</span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <!-- Google Ads Conversion ID -->
                    <div class="p-4 rounded-2xl border border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-950/40 space-y-3">
                        <div class="flex items-center justify-between">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="google_ads_enabled" value="1" {{ ($settings['google_ads_enabled'] ?? '0') === '1' ? 'checked' : '' }} class="rounded text-emerald-500 focus:ring-emerald-500">
                                <span class="text-xs font-bold text-slate-800 dark:text-slate-200">Google Ads Conversion Tag</span>
                            </label>
                            <span class="text-[10px] font-bold px-2 py-0.5 rounded-full {{ ($settings['google_ads_enabled'] ?? '0') === '1' ? 'bg-emerald-500/10 text-emerald-600' : 'bg-slate-200 dark:bg-slate-800 text-slate-400' }}">
                                {{ ($settings['google_ads_enabled'] ?? '0') === '1' ? 'Active' : 'Disabled' }}
                            </span>
                        </div>
                        <p class="text-[11px] text-slate-500">Injects Google Ads global site tag and tracks sales conversions.</p>
                        <div>
                            <label class="block text-[11px] font-bold text-slate-600 dark:text-slate-400 mb-1">Google Ads Conversion ID</label>
                            <input type="text" name="google_ads_id" value="{{ $settings['google_ads_id'] ?? '' }}" placeholder="AW-XXXXXXXXX" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-mono font-bold outline-none focus:ring-2 focus:ring-amber-500">
                        </div>
                    </div>

                    <!-- Google Ads Purchase Conversion Label -->
                    <div class="p-4 rounded-2xl border border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-950/40 space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-slate-800 dark:text-slate-200">Purchase Conversion Label</span>
                            <span class="text-[10px] font-mono text-slate-400">Order Success Trigger</span>
                        </div>
                        <p class="text-[11px] text-slate-500">Sent on successful order placement alongside the invoice grand total.</p>
                        <div>
                            <label class="block text-[11px] font-bold text-slate-600 dark:text-slate-400 mb-1">Conversion Label Code</label>
                            <input type="text" name="google_ads_purchase_label" value="{{ $settings['google_ads_purchase_label'] ?? '' }}" placeholder="e.g. AB12CD34EF56GH78" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-mono outline-none focus:ring-2 focus:ring-amber-500">
                        </div>
                    </div>

                    <!-- Google Analytics 4 (GA4) -->
                    <div class="p-4 rounded-2xl border border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-950/40 space-y-3">
                        <div class="flex items-center justify-between">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="google_analytics_enabled" value="1" {{ ($settings['google_analytics_enabled'] ?? '0') === '1' ? 'checked' : '' }} class="rounded text-emerald-500 focus:ring-emerald-500">
                                <span class="text-xs font-bold text-slate-800 dark:text-slate-200">Google Analytics 4 (GA4)</span>
                            </label>
                            <span class="text-[10px] font-bold px-2 py-0.5 rounded-full {{ ($settings['google_analytics_enabled'] ?? '0') === '1' ? 'bg-emerald-500/10 text-emerald-600' : 'bg-slate-200 dark:bg-slate-800 text-slate-400' }}">
                                {{ ($settings['google_analytics_enabled'] ?? '0') === '1' ? 'Active' : 'Disabled' }}
                            </span>
                        </div>
                        <p class="text-[11px] text-slate-500">Tracks visitor demographics, traffic sources, and behavioral engagement.</p>
                        <div>
                            <label class="block text-[11px] font-bold text-slate-600 dark:text-slate-400 mb-1">GA4 Measurement ID</label>
                            <input type="text" name="google_analytics_id" value="{{ $settings['google_analytics_id'] ?? '' }}" placeholder="G-XXXXXXXXXX" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-mono font-bold outline-none focus:ring-2 focus:ring-amber-500">
                        </div>
                    </div>

                    <!-- Google Tag Manager (GTM) -->
                    <div class="p-4 rounded-2xl border border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-950/40 space-y-3">
                        <div class="flex items-center justify-between">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="google_tag_manager_enabled" value="1" {{ ($settings['google_tag_manager_enabled'] ?? '0') === '1' ? 'checked' : '' }} class="rounded text-emerald-500 focus:ring-emerald-500">
                                <span class="text-xs font-bold text-slate-800 dark:text-slate-200">Google Tag Manager (GTM)</span>
                            </label>
                            <span class="text-[10px] font-bold px-2 py-0.5 rounded-full {{ ($settings['google_tag_manager_enabled'] ?? '0') === '1' ? 'bg-emerald-500/10 text-emerald-600' : 'bg-slate-200 dark:bg-slate-800 text-slate-400' }}">
                                {{ ($settings['google_tag_manager_enabled'] ?? '0') === '1' ? 'Active' : 'Disabled' }}
                            </span>
                        </div>
                        <p class="text-[11px] text-slate-500">Dual injection: head container script and body noscript fallback.</p>
                        <div>
                            <label class="block text-[11px] font-bold text-slate-600 dark:text-slate-400 mb-1">GTM Container ID</label>
                            <input type="text" name="google_tag_manager_id" value="{{ $settings['google_tag_manager_id'] ?? '' }}" placeholder="GTM-XXXXXXX" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-mono font-bold outline-none focus:ring-2 focus:ring-amber-500">
                        </div>
                    </div>

                    <!-- Google AdSense Auto Ads -->
                    <div class="p-4 rounded-2xl border border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-950/40 space-y-3">
                        <div class="flex items-center justify-between">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="google_adsense_enabled" value="1" {{ ($settings['google_adsense_enabled'] ?? '0') === '1' ? 'checked' : '' }} class="rounded text-emerald-500 focus:ring-emerald-500">
                                <span class="text-xs font-bold text-slate-800 dark:text-slate-200">Google AdSense Monetization</span>
                            </label>
                            <span class="text-[10px] font-bold px-2 py-0.5 rounded-full {{ ($settings['google_adsense_enabled'] ?? '0') === '1' ? 'bg-emerald-500/10 text-emerald-600' : 'bg-slate-200 dark:bg-slate-800 text-slate-400' }}">
                                {{ ($settings['google_adsense_enabled'] ?? '0') === '1' ? 'Active' : 'Disabled' }}
                            </span>
                        </div>
                        <p class="text-[11px] text-slate-500">Display automatic banner ads and monetize blog or product review visitors.</p>
                        <div>
                            <label class="block text-[11px] font-bold text-slate-600 dark:text-slate-400 mb-1">AdSense Publisher ID</label>
                            <input type="text" name="google_adsense_id" value="{{ $settings['google_adsense_id'] ?? '' }}" placeholder="ca-pub-1234567890123456" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-mono outline-none focus:ring-2 focus:ring-amber-500">
                        </div>
                    </div>

                    <!-- Google Search Console Verification -->
                    <div class="p-4 rounded-2xl border border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-950/40 space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-slate-800 dark:text-slate-200">Search Console Verification Token</span>
                            <span class="text-[10px] text-slate-400 font-mono">Google Webmaster</span>
                        </div>
                        <p class="text-[11px] text-slate-500">Verification string or code provided by Google Search Console.</p>
                        <div>
                            <label class="block text-[11px] font-bold text-slate-600 dark:text-slate-400 mb-1">Verification String</label>
                            <input type="text" name="google_site_verification" value="{{ $settings['google_site_verification'] ?? '' }}" placeholder="e.g. 4vX_randomStringToken_abcdef12345" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-mono outline-none focus:ring-2 focus:ring-amber-500">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Other Ad Networks (TikTok, Bing/Microsoft Ads & Pexels) -->
            <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 border border-slate-200 dark:border-slate-800 shadow-sm space-y-5">
                <div class="border-b border-slate-100 dark:border-slate-800 pb-3 flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                            <i data-lucide="layers" class="w-4 h-4 text-teal-500"></i>
                            <span>Other Ad Networks & Media APIs (TikTok, Microsoft Bing & Pexels)</span>
                        </h3>
                        <p class="text-xs text-slate-500">Expand reach across TikTok video campaigns, Bing Search Ads, and stock media.</p>
                    </div>
                    <span class="text-[10px] font-bold px-2.5 py-1 rounded-full bg-teal-500/10 text-teal-600 dark:text-teal-400">Multi-Channel Ads</span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                    <!-- TikTok Pixel -->
                    <div class="p-4 rounded-2xl border border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-950/40 space-y-3">
                        <div class="flex items-center justify-between">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="tiktok_pixel_enabled" value="1" {{ ($settings['tiktok_pixel_enabled'] ?? '0') === '1' ? 'checked' : '' }} class="rounded text-emerald-500 focus:ring-emerald-500">
                                <span class="text-xs font-bold text-slate-800 dark:text-slate-200">TikTok Pixel</span>
                            </label>
                            <span class="text-[10px] font-bold px-2 py-0.5 rounded-full {{ ($settings['tiktok_pixel_enabled'] ?? '0') === '1' ? 'bg-emerald-500/10 text-emerald-600' : 'bg-slate-200 dark:bg-slate-800 text-slate-400' }}">
                                {{ ($settings['tiktok_pixel_enabled'] ?? '0') === '1' ? 'Active' : 'Disabled' }}
                            </span>
                        </div>
                        <p class="text-[11px] text-slate-500">Track viral TikTok hardware video ad buyers.</p>
                        <div>
                            <label class="block text-[11px] font-bold text-slate-600 dark:text-slate-400 mb-1">TikTok Pixel Code</label>
                            <input type="text" name="tiktok_pixel_id" value="{{ $settings['tiktok_pixel_id'] ?? '' }}" placeholder="e.g. C1234567890ABCDEF" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-mono outline-none focus:ring-2 focus:ring-teal-500">
                        </div>
                    </div>

                    <!-- Microsoft / Bing Ads -->
                    <div class="p-4 rounded-2xl border border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-950/40 space-y-3">
                        <div class="flex items-center justify-between">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="bing_ads_enabled" value="1" {{ ($settings['bing_ads_enabled'] ?? '0') === '1' ? 'checked' : '' }} class="rounded text-emerald-500 focus:ring-emerald-500">
                                <span class="text-xs font-bold text-slate-800 dark:text-slate-200">Microsoft / Bing Ads</span>
                            </label>
                            <span class="text-[10px] font-bold px-2 py-0.5 rounded-full {{ ($settings['bing_ads_enabled'] ?? '0') === '1' ? 'bg-emerald-500/10 text-emerald-600' : 'bg-slate-200 dark:bg-slate-800 text-slate-400' }}">
                                {{ ($settings['bing_ads_enabled'] ?? '0') === '1' ? 'Active' : 'Disabled' }}
                            </span>
                        </div>
                        <p class="text-[11px] text-slate-500">Bing Universal Event Tracking (UET).</p>
                        <div>
                            <label class="block text-[11px] font-bold text-slate-600 dark:text-slate-400 mb-1">Bing Tag ID (UET)</label>
                            <input type="text" name="bing_ads_id" value="{{ $settings['bing_ads_id'] ?? '' }}" placeholder="e.g. 12345678" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-mono outline-none focus:ring-2 focus:ring-teal-500">
                        </div>
                    </div>

                    <!-- Pexels Stock Photo API -->
                    <div class="p-4 rounded-2xl border border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-950/40 space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-slate-800 dark:text-slate-200">Pexels Free Stock Media</span>
                            <a href="https://www.pexels.com/api/" target="_blank" class="text-[10px] font-bold text-teal-600 hover:underline flex items-center gap-1">
                                <span>Get Key</span>
                                <i data-lucide="external-link" class="w-3 h-3"></i>
                            </a>
                        </div>
                        <p class="text-[11px] text-slate-500">Royalty-free photos for banners and ads.</p>
                        <div>
                            <label class="block text-[11px] font-bold text-slate-600 dark:text-slate-400 mb-1">Pexels API Key</label>
                            <input type="password" name="pexels_api_key" value="{{ $settings['pexels_api_key'] ?? '' }}" placeholder="e.g. 563492ad..." class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-mono outline-none focus:ring-2 focus:ring-teal-500">
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- ========================================================= -->
        <!-- TAB 9: CUSTOM SCRIPTS & ANALYTICS                         -->
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
