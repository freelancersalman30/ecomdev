@extends('layouts.admin')

@section('title', 'Enterprise Site Settings & Store Control')
@section('page-title', 'Site Settings & System Control')

@section('content')
<div class="max-w-6xl mx-auto space-y-6" x-data="{ 
    activeTab: 'branding',
    primaryColor: '{{ $settings['theme_primary_color'] ?? '#f85606' }}',
    primaryHover: '{{ $settings['theme_primary_hover'] ?? '#d04300' }}',
    secondaryColor: '{{ $settings['theme_secondary_color'] ?? '#10b981' }}',
    headerBg: '{{ $settings['theme_header_bg'] ?? '#0f172a' }}',
    announcementBg: '{{ $settings['theme_announcement_bg'] ?? '#0f172a' }}',
    announcementText: '{{ $settings['theme_announcement_text_color'] ?? '#fbbf24' }}',
    footerBg: '{{ $settings['theme_footer_bg'] ?? '#020617' }}',
    applyPreset(p, ph, s, h, a, at, f) {
        this.primaryColor = p;
        this.primaryHover = ph;
        this.secondaryColor = s;
        this.headerBg = h;
        this.announcementBg = a;
        this.announcementText = at;
        this.footerBg = f;
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
            <span class="font-bold">2. Theme & Colors</span>
        </button>

        <button 
            type="button" 
            @click="activeTab = 'shipping'" 
            :class="activeTab === 'shipping' ? 'bg-emerald-500 text-slate-950 font-bold shadow-md shadow-emerald-500/20' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800'"
            class="flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs transition">
            <i data-lucide="truck" class="w-4 h-4"></i>
            <span>3. Orders & Shipping</span>
        </button>

        <button 
            type="button" 
            @click="activeTab = 'currency'" 
            :class="activeTab === 'currency' ? 'bg-emerald-500 text-slate-950 font-bold shadow-md shadow-emerald-500/20' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800'"
            class="flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs transition">
            <i data-lucide="coins" class="w-4 h-4"></i>
            <span>4. Currency & Region</span>
        </button>

        <button 
            type="button" 
            @click="activeTab = 'invoice'" 
            :class="activeTab === 'invoice' ? 'bg-emerald-500 text-slate-950 font-bold shadow-md shadow-emerald-500/20' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800'"
            class="flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs transition">
            <i data-lucide="file-text" class="w-4 h-4"></i>
            <span>5. Invoices & Receipts</span>
        </button>

        <button 
            type="button" 
            @click="activeTab = 'notices'" 
            :class="activeTab === 'notices' ? 'bg-emerald-500 text-slate-950 font-bold shadow-md shadow-emerald-500/20' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800'"
            class="flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs transition">
            <i data-lucide="megaphone" class="w-4 h-4"></i>
            <span>6. Notices & Maintenance</span>
        </button>

        <button 
            type="button" 
            @click="activeTab = 'social'" 
            :class="activeTab === 'social' ? 'bg-emerald-500 text-slate-950 font-bold shadow-md shadow-emerald-500/20' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800'"
            class="flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs transition">
            <i data-lucide="share-2" class="w-4 h-4"></i>
            <span>7. Social & Contact</span>
        </button>

        <button 
            type="button" 
            @click="activeTab = 'tracking'" 
            :class="activeTab === 'tracking' ? 'bg-emerald-500 text-slate-950 font-bold shadow-md shadow-emerald-500/20' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800'"
            class="flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs transition">
            <i data-lucide="activity" class="w-4 h-4 text-sky-400"></i>
            <span class="font-bold">8. Google & Pixel Setup</span>
        </button>

        <button 
            type="button" 
            @click="activeTab = 'scripts'" 
            :class="activeTab === 'scripts' ? 'bg-emerald-500 text-slate-950 font-bold shadow-md shadow-emerald-500/20' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800'"
            class="flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs transition">
            <i data-lucide="code" class="w-4 h-4"></i>
            <span>9. Custom Scripts & SEO</span>
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
        <!-- TAB 2: THEME & WEBSITE COLOR CONTROL                      -->
        <!-- ========================================================= -->
        <div x-show="activeTab === 'theme'" x-cloak class="space-y-6">
            
            <!-- Curated 1-Click Color Presets -->
            <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 border border-slate-200 dark:border-slate-800 shadow-sm space-y-5">
                <div class="border-b border-slate-100 dark:border-slate-800 pb-3">
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                        <i data-lucide="sparkles" class="w-4 h-4 text-amber-500"></i>
                        <span>1-Click Curated Color Presets</span>
                    </h3>
                    <p class="text-xs text-slate-500">Select any industry-crafted palette to instantly re-theme your store, or customize each color below.</p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3.5">
                    
                    <!-- Preset 1: Daraz Orange & PCB Tech (Default) -->
                    <div 
                        @click="applyPreset('#f85606', '#d04300', '#10b981', '#0f172a', '#0f172a', '#fbbf24', '#020617')" 
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
                        @click="applyPreset('#10b981', '#059669', '#06b6d4', '#022c22', '#022c22', '#34d399', '#021a14')" 
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
                        @click="applyPreset('#2563eb', '#1d4ed8', '#f59e0b', '#0f172a', '#1e293b', '#93c5fd', '#0b1120')" 
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
                        @click="applyPreset('#e11d48', '#be123c', '#f97316', '#1c1917', '#292524', '#fda4af', '#0c0a09')" 
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

                    <!-- Preset 5: Robotics Purple / AI Tech -->
                    <div 
                        @click="applyPreset('#7c3aed', '#6d28d9', '#06b6d4', '#18181b', '#27272a', '#c084fc', '#09090b')" 
                        class="p-4 rounded-2xl border-2 border-slate-200 dark:border-slate-800 hover:border-emerald-500 bg-slate-50/60 dark:bg-slate-950/40 cursor-pointer transition space-y-2.5 group">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-slate-900 dark:text-white group-hover:text-emerald-400 transition">Robotics Purple & AI</span>
                            <span class="text-[10px] uppercase font-bold px-1.5 py-0.5 rounded bg-purple-500/10 text-purple-600">Futuristic</span>
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
                        @click="applyPreset('#f59e0b', '#d97706', '#38bdf8', '#000000', '#171717', '#fde68a', '#000000')" 
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

            <!-- Custom Color Pickers Grid -->
            <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 border border-slate-200 dark:border-slate-800 shadow-sm space-y-5">
                <div class="border-b border-slate-100 dark:border-slate-800 pb-3">
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                        <i data-lucide="sliders" class="w-4 h-4 text-emerald-500"></i>
                        <span>Fine-Tuned Color Palette Pickers</span>
                    </h3>
                    <p class="text-xs text-slate-500">Pick any exact HEX color code or use the native color wheel picker.</p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    
                    <!-- 1. Primary Color -->
                    <div class="p-4 rounded-2xl border border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-950/40 space-y-2">
                        <label class="block text-xs font-bold text-slate-800 dark:text-slate-200">Primary Brand / Button Color *</label>
                        <p class="text-[10px] text-slate-400">Main buttons, active badges, order placement CTAs</p>
                        <div class="flex items-center gap-2 pt-1">
                            <input type="color" x-model="primaryColor" class="w-10 h-10 rounded-xl cursor-pointer border-0 bg-transparent p-0">
                            <input type="text" name="theme_primary_color" x-model="primaryColor" required class="flex-1 px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-mono font-bold uppercase outline-none">
                        </div>
                    </div>

                    <!-- 2. Primary Hover Color -->
                    <div class="p-4 rounded-2xl border border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-950/40 space-y-2">
                        <label class="block text-xs font-bold text-slate-800 dark:text-slate-200">Primary Hover State Color *</label>
                        <p class="text-[10px] text-slate-400">Darkened state on mouse hover</p>
                        <div class="flex items-center gap-2 pt-1">
                            <input type="color" x-model="primaryHover" class="w-10 h-10 rounded-xl cursor-pointer border-0 bg-transparent p-0">
                            <input type="text" name="theme_primary_hover" x-model="primaryHover" required class="flex-1 px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-mono font-bold uppercase outline-none">
                        </div>
                    </div>

                    <!-- 3. Secondary Tech Accent -->
                    <div class="p-4 rounded-2xl border border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-950/40 space-y-2">
                        <label class="block text-xs font-bold text-slate-800 dark:text-slate-200">Secondary / Hardware Accent Color *</label>
                        <p class="text-[10px] text-slate-400">Verified badges, PCB icons, stock status pills</p>
                        <div class="flex items-center gap-2 pt-1">
                            <input type="color" x-model="secondaryColor" class="w-10 h-10 rounded-xl cursor-pointer border-0 bg-transparent p-0">
                            <input type="text" name="theme_secondary_color" x-model="secondaryColor" required class="flex-1 px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-mono font-bold uppercase outline-none">
                        </div>
                    </div>

                    <!-- 4. Header Background -->
                    <div class="p-4 rounded-2xl border border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-950/40 space-y-2">
                        <label class="block text-xs font-bold text-slate-800 dark:text-slate-200">Top Navigation Navy Background</label>
                        <p class="text-[10px] text-slate-400">Utility strip and sub-navbar containers</p>
                        <div class="flex items-center gap-2 pt-1">
                            <input type="color" x-model="headerBg" class="w-10 h-10 rounded-xl cursor-pointer border-0 bg-transparent p-0">
                            <input type="text" name="theme_header_bg" x-model="headerBg" class="flex-1 px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-mono font-bold uppercase outline-none">
                        </div>
                    </div>

                    <!-- 5. Announcement Bar BG -->
                    <div class="p-4 rounded-2xl border border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-950/40 space-y-2">
                        <label class="block text-xs font-bold text-slate-800 dark:text-slate-200">Top Announcement Banner Background</label>
                        <p class="text-[10px] text-slate-400">Flash announcement stripe at very top of website</p>
                        <div class="flex items-center gap-2 pt-1">
                            <input type="color" x-model="announcementBg" class="w-10 h-10 rounded-xl cursor-pointer border-0 bg-transparent p-0">
                            <input type="text" name="theme_announcement_bg" x-model="announcementBg" class="flex-1 px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-mono font-bold uppercase outline-none">
                        </div>
                    </div>

                    <!-- 6. Announcement Bar Text Color -->
                    <div class="p-4 rounded-2xl border border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-950/40 space-y-2">
                        <label class="block text-xs font-bold text-slate-800 dark:text-slate-200">Announcement Banner Text Color</label>
                        <p class="text-[10px] text-slate-400">Font color for text inside top announcement</p>
                        <div class="flex items-center gap-2 pt-1">
                            <input type="color" x-model="announcementText" class="w-10 h-10 rounded-xl cursor-pointer border-0 bg-transparent p-0">
                            <input type="text" name="theme_announcement_text_color" x-model="announcementText" class="flex-1 px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-mono font-bold uppercase outline-none">
                        </div>
                    </div>

                    <!-- 7. Footer Background -->
                    <div class="p-4 rounded-2xl border border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-950/40 space-y-2">
                        <label class="block text-xs font-bold text-slate-800 dark:text-slate-200">Storefront Footer Background Color</label>
                        <p class="text-[10px] text-slate-400">Deep background for bottom mega footer</p>
                        <div class="flex items-center gap-2 pt-1">
                            <input type="color" x-model="footerBg" class="w-10 h-10 rounded-xl cursor-pointer border-0 bg-transparent p-0">
                            <input type="text" name="theme_footer_bg" x-model="footerBg" class="flex-1 px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-mono font-bold uppercase outline-none">
                        </div>
                    </div>

                </div>
            </div>

            <!-- Live Interactive Storefront Preview Sandbox -->
            <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 border border-slate-200 dark:border-slate-800 shadow-sm space-y-4">
                <div class="border-b border-slate-100 dark:border-slate-800 pb-3 flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                            <i data-lucide="eye" class="w-4 h-4 text-emerald-500"></i>
                            <span>Live Storefront Component Simulation</span>
                        </h3>
                        <p class="text-xs text-slate-500">Real-time simulation of buttons, badges, announcement bar, and headers using your selected colors.</p>
                    </div>
                    <span class="text-[10px] font-bold px-2.5 py-1 rounded-full bg-emerald-500/10 text-emerald-600 dark:text-emerald-400">Live Preview</span>
                </div>

                <!-- Simulation Sandbox View -->
                <div class="rounded-2xl border border-slate-200 dark:border-slate-800 overflow-hidden shadow-md">
                    
                    <!-- Simulated Announcement Bar -->
                    <div :style="{ backgroundColor: announcementBg, color: announcementText }" class="px-4 py-2 text-xs font-semibold flex items-center justify-between transition-colors duration-200">
                        <div class="flex items-center gap-2">
                            <i data-lucide="zap" class="w-3.5 h-3.5"></i>
                            <span>⚡ Flash Sale Live: Up to 40% OFF All Robotics Components!</span>
                        </div>
                        <span class="text-[11px] opacity-80 hidden sm:block">Hotline: +880 1700-112233</span>
                    </div>

                    <!-- Simulated Header & Product Card Area -->
                    <div class="p-6 bg-slate-50 dark:bg-slate-950 space-y-6">
                        
                        <!-- Mini Header Preview -->
                        <div class="flex items-center justify-between bg-white dark:bg-slate-900 p-4 rounded-xl border border-slate-200 dark:border-slate-800">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 rounded-lg flex items-center justify-center text-white font-black text-xs shadow" :style="{ backgroundColor: primaryColor }">
                                    <i data-lucide="cpu" class="w-4 h-4 text-white"></i>
                                </div>
                                <div class="font-black text-base text-slate-900 dark:text-white">
                                    DREAMERS <span :style="{ color: primaryColor }">PCB</span>
                                </div>
                            </div>

                            <div class="flex items-center gap-3">
                                <div class="relative hidden sm:block">
                                    <input type="text" placeholder="Search components..." readonly class="px-3 py-1.5 text-xs rounded-lg border-2 bg-slate-50 dark:bg-slate-800 w-48 outline-none" :style="{ borderColor: primaryColor }">
                                </div>
                                <button type="button" class="px-3.5 py-1.5 rounded-xl text-white text-xs font-bold shadow transition" :style="{ backgroundColor: primaryColor }">
                                    <span>Cart (2)</span>
                                </button>
                            </div>
                        </div>

                        <!-- Simulated Component Swatches -->
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            
                            <!-- Card 1: Button State -->
                            <div class="bg-white dark:bg-slate-900 p-4 rounded-xl border border-slate-200 dark:border-slate-800 space-y-3">
                                <span class="text-[11px] font-bold text-slate-400 uppercase">Primary CTA Action</span>
                                <button type="button" class="w-full py-2.5 rounded-xl text-white font-extrabold text-xs shadow-md transition transform active:scale-95 flex items-center justify-center gap-2" :style="{ backgroundColor: primaryColor }">
                                    <i data-lucide="shopping-cart" class="w-4 h-4"></i>
                                    <span>Add to Cart - ৳450</span>
                                </button>
                                <button type="button" class="w-full py-2 rounded-xl text-xs font-bold border transition" :style="{ borderColor: primaryColor, color: primaryColor }">
                                    <span>Quick Order Now</span>
                                </button>
                            </div>

                            <!-- Card 2: Badges & Tags -->
                            <div class="bg-white dark:bg-slate-900 p-4 rounded-xl border border-slate-200 dark:border-slate-800 space-y-3">
                                <span class="text-[11px] font-bold text-slate-400 uppercase">Badges & Accents</span>
                                <div class="flex flex-wrap gap-2 pt-1">
                                    <span class="px-2.5 py-1 rounded-full text-white text-[10px] font-black" :style="{ backgroundColor: primaryColor }">
                                        -35% OFF
                                    </span>
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold flex items-center gap-1" :style="{ backgroundColor: secondaryColor + '20', color: secondaryColor }">
                                        <i data-lucide="check" class="w-3 h-3"></i>
                                        <span>In Stock (50+)</span>
                                    </span>
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold text-white" :style="{ backgroundColor: secondaryColor }">
                                        Genuine PCB
                                    </span>
                                </div>
                            </div>

                            <!-- Card 3: Price Display -->
                            <div class="bg-white dark:bg-slate-900 p-4 rounded-xl border border-slate-200 dark:border-slate-800 space-y-2">
                                <span class="text-[11px] font-bold text-slate-400 uppercase">Price Highlighting</span>
                                <div>
                                    <div class="text-2xl font-black font-mono" :style="{ color: primaryColor }">
                                        ৳1,250.00
                                    </div>
                                    <span class="text-xs text-slate-400 line-through">৳1,800.00</span>
                                </div>
                            </div>

                        </div>

                        <!-- Simulated Mini Footer -->
                        <div :style="{ backgroundColor: footerBg }" class="p-4 rounded-xl text-slate-400 text-xs flex items-center justify-between transition-colors duration-200">
                            <div>
                                <span class="font-bold text-white">DREAMERS PCB</span> &bull; <span>Footer Theme Area</span>
                            </div>
                            <span class="text-[11px] opacity-75">© 2026 DREAMERS PCB</span>
                        </div>

                    </div>

                </div>
            </div>

        </div>

        <!-- ========================================================= -->
        <!-- TAB 3: ORDERS, DELIVERY & E-COMMERCE AUTOMATION           -->
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
        <!-- TAB 8: GOOGLE & PIXEL TRACKING SETUP                      -->
        <!-- ========================================================= -->
        <div x-show="activeTab === 'tracking'" x-cloak class="space-y-6">
            
            <!-- Google Services Suite -->
            <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 border border-slate-200 dark:border-slate-800 shadow-sm space-y-5">
                <div class="border-b border-slate-100 dark:border-slate-800 pb-3 flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                            <i data-lucide="globe" class="w-4 h-4 text-sky-500"></i>
                            <span>Google Services Integration (Analytics, GTM & Webmaster)</span>
                        </h3>
                        <p class="text-xs text-slate-500">Connect Google Analytics 4, Tag Manager, and Search Console site verification.</p>
                    </div>
                    <span class="text-[10px] font-bold px-2.5 py-1 rounded-full bg-sky-500/10 text-sky-600 dark:text-sky-400">Google Ecosystem</span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    
                    <!-- 1. Google Analytics 4 -->
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
                        <p class="text-[11px] text-slate-500">Tracks visitor traffic, product pageviews, search queries, and conversions.</p>
                        <div>
                            <label class="block text-[11px] font-bold text-slate-600 dark:text-slate-400 mb-1">GA4 Measurement ID</label>
                            <input type="text" name="google_analytics_id" value="{{ $settings['google_analytics_id'] ?? '' }}" placeholder="G-XXXXXXXXXX" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-mono font-bold outline-none focus:ring-2 focus:ring-sky-500">
                        </div>
                    </div>

                    <!-- 2. Google Tag Manager -->
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
                        <p class="text-[11px] text-slate-500">Automatically injects the head container and body noscript fallback tag.</p>
                        <div>
                            <label class="block text-[11px] font-bold text-slate-600 dark:text-slate-400 mb-1">GTM Container ID</label>
                            <input type="text" name="google_tag_manager_id" value="{{ $settings['google_tag_manager_id'] ?? '' }}" placeholder="GTM-XXXXXXX" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-mono font-bold outline-none focus:ring-2 focus:ring-sky-500">
                        </div>
                    </div>

                    <!-- 3. Google Search Console Verification -->
                    <div class="p-4 rounded-2xl border border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-950/40 space-y-3 md:col-span-2">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-slate-800 dark:text-slate-200">Google Search Console Verification Token</span>
                            <span class="text-[10px] text-slate-400 font-mono">HTML Meta Tag Verification</span>
                        </div>
                        <p class="text-[11px] text-slate-500">Paste only the verification string or code provided by Google Search Console Webmaster Tools.</p>
                        <input type="text" name="google_site_verification" value="{{ $settings['google_site_verification'] ?? '' }}" placeholder="e.g. 4vX_randomStringToken_abcdef12345" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-mono outline-none focus:ring-2 focus:ring-sky-500">
                    </div>

                </div>
            </div>

            <!-- Social Pixel Tracking Suite (Meta / Facebook & TikTok) -->
            <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 border border-slate-200 dark:border-slate-800 shadow-sm space-y-5">
                <div class="border-b border-slate-100 dark:border-slate-800 pb-3 flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                            <i data-lucide="crosshair" class="w-4 h-4 text-emerald-500"></i>
                            <span>Advertising Pixel Tracking (Meta / Facebook & TikTok)</span>
                        </h3>
                        <p class="text-xs text-slate-500">Track standard e-commerce events: PageView, AddToCart, and Purchases for targeted ad campaigns.</p>
                    </div>
                    <span class="text-[10px] font-bold px-2.5 py-1 rounded-full bg-emerald-500/10 text-emerald-600 dark:text-emerald-400">Ad Retargeting</span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    
                    <!-- 1. Meta / Facebook Pixel -->
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
                        <p class="text-[11px] text-slate-500">Enables standard Meta Pixel base code and triggers automatic PageView events.</p>
                        <div>
                            <label class="block text-[11px] font-bold text-slate-600 dark:text-slate-400 mb-1">Meta Pixel ID (15-16 digits)</label>
                            <input type="text" name="facebook_pixel_id" value="{{ $settings['facebook_pixel_id'] ?? '' }}" placeholder="e.g. 1234567890123456" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-mono font-bold outline-none focus:ring-2 focus:ring-emerald-500">
                        </div>
                    </div>

                    <!-- 2. TikTok Pixel -->
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
                        <p class="text-[11px] text-slate-500">Tracks high-converting TikTok video ad traffic and hardware buyer conversions.</p>
                        <div>
                            <label class="block text-[11px] font-bold text-slate-600 dark:text-slate-400 mb-1">TikTok Pixel Code / ID</label>
                            <input type="text" name="tiktok_pixel_id" value="{{ $settings['tiktok_pixel_id'] ?? '' }}" placeholder="e.g. C1234567890ABCDEF" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-mono font-bold outline-none focus:ring-2 focus:ring-emerald-500">
                        </div>
                    </div>

                </div>
            </div>

            <!-- Pexels Free Stock Photo API -->
            <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 border border-slate-200 dark:border-slate-800 shadow-sm space-y-5">
                <div class="border-b border-slate-100 dark:border-slate-800 pb-3 flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                            <i data-lucide="camera" class="w-4 h-4 text-teal-500"></i>
                            <span>Pexels Stock Photography & Media API</span>
                        </h3>
                        <p class="text-xs text-slate-500">Integrate royalty-free high-resolution photography for banners and catalog imagery.</p>
                    </div>
                    <a href="https://www.pexels.com/api/" target="_blank" class="text-[10px] font-bold text-teal-600 dark:text-teal-400 hover:underline flex items-center gap-1">
                        <span>Get Free Pexels Key</span>
                        <i data-lucide="external-link" class="w-3 h-3"></i>
                    </a>
                </div>

                <div class="space-y-2">
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">Pexels Authorization API Key</label>
                    <input type="password" name="pexels_api_key" value="{{ $settings['pexels_api_key'] ?? '' }}" placeholder="e.g. 563492ad6f91700001000001..." class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-mono outline-none focus:ring-2 focus:ring-teal-500">
                    <p class="text-[11px] text-slate-400">Used by media tools to search and fetch license-free electronics, robotics, and hardware imagery.</p>
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
