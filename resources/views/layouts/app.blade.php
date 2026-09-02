<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'DREAMERS PCB | Online Electronic Components & Gadget Mega Store')</title>
    
    <!-- Meta SEO -->
    <meta name="description" content="@yield('meta_description', 'DREAMERS PCB is Bangladesh\'s leading electronics & PCB store. Buy Microcontrollers, STM32, ESP32, Arduino, Soldering Tools & DIY Kits.')">
    <meta name="keywords" content="@yield('meta_keywords', 'electronics, components, arduino, esp32, pcb, bangladesh, sensors, robotics')">
    <meta property="og:title" content="@yield('title', 'DREAMERS PCB | Electronic Components Store')">
    <meta property="og:description" content="@yield('meta_description', 'Bangladesh\'s premier electronic components and PCB store.')">
    <meta property="og:type" content="website">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @if(\App\Models\Setting::get('site_favicon'))
    <link rel="icon" href="{{ asset(\App\Models\Setting::get('site_favicon')) }}">
    @endif

    <!-- Dynamic Google Fonts Loader -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    
    @php
        $fontBody = \App\Models\Setting::get('theme_font_body', 'Plus Jakarta Sans');
        $fontHeading = \App\Models\Setting::get('theme_font_heading', 'Outfit');
        $fontMono = \App\Models\Setting::get('theme_font_mono', 'JetBrains Mono');
        $fontSizeBase = \App\Models\Setting::get('theme_font_size_base', '16px');

        $primaryColor = \App\Models\Setting::get('theme_primary_color', '#f85606');
        $primaryHover = \App\Models\Setting::get('theme_primary_hover', '#d04300');
        $secondaryColor = \App\Models\Setting::get('theme_secondary_color', '#10b981');
        $bodyBg = \App\Models\Setting::get('theme_body_bg', '#f8fafc');
        $textColor = \App\Models\Setting::get('theme_text_color', '#1e293b');
        $headingColor = \App\Models\Setting::get('theme_heading_color', '#0f172a');
        $cardBg = \App\Models\Setting::get('theme_card_bg', '#ffffff');
        $cardBorder = \App\Models\Setting::get('theme_card_border', '#e2e8f0');
        $priceColor = \App\Models\Setting::get('theme_price_color', '#f85606');
        $saleBadgeColor = \App\Models\Setting::get('theme_sale_badge_color', '#ef4444');
        $headerBg = \App\Models\Setting::get('theme_header_bg', '#0f172a');
        $announcementBg = \App\Models\Setting::get('theme_announcement_bg', '#0f172a');
        $announcementText = \App\Models\Setting::get('theme_announcement_text_color', '#fbbf24');
        $footerBg = \App\Models\Setting::get('theme_footer_bg', '#020617');
        $footerText = \App\Models\Setting::get('theme_footer_text_color', '#94a3b8');

        // Dynamically build Google Fonts URL
        $fontList = array_unique([$fontBody, $fontHeading, $fontMono]);
        $fontQueryParts = [];
        foreach ($fontList as $f) {
            $weights = ($f === $fontMono) ? ':wght@400;500;700' : ':wght@300;400;500;600;700;800;900';
            $fontQueryParts[] = 'family=' . str_replace(' ', '+', $f) . $weights;
        }
        $googleFontsUrl = 'https://fonts.googleapis.com/css2?' . implode('&', $fontQueryParts) . '&display=swap';
    @endphp

    <link href="{{ $googleFontsUrl }}" rel="stylesheet">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"{{ $fontBody }}"', 'sans-serif'],
                        heading: ['"{{ $fontHeading }}"', 'sans-serif'],
                        mono: ['"{{ $fontMono }}"', 'monospace']
                    },
                    colors: {
                        brand: {
                            50: '{{ $secondaryColor }}15',
                            100: '{{ $secondaryColor }}25',
                            500: '{{ $secondaryColor }}',
                            600: '{{ $secondaryColor }}',
                            700: '{{ $secondaryColor }}',
                            900: '#064e3b'
                        },
                        daraz: {
                            orange: '{{ $primaryColor }}',
                            orangeHover: '{{ $primaryHover }}',
                            navy: '{{ $headerBg }}',
                            light: '{{ $primaryColor }}15'
                        }
                    }
                }
            }
        }
    </script>

    <!-- Lucide Icons (Fast Global CDN) -->
    <script src="https://cdn.jsdelivr.net/npm/lucide@0.344.0/dist/umd/lucide.min.js"></script>
    
    <!-- Alpine.js Core -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        :root {
            --theme-font-body: '{{ $fontBody }}', sans-serif;
            --theme-font-heading: '{{ $fontHeading }}', sans-serif;
            --theme-font-mono: '{{ $fontMono }}', monospace;
            --theme-font-size: {{ $fontSizeBase }};
            --theme-primary: {{ $primaryColor }};
            --theme-primary-hover: {{ $primaryHover }};
            --theme-secondary: {{ $secondaryColor }};
            --theme-body-bg: {{ $bodyBg }};
            --theme-text: {{ $textColor }};
            --theme-heading: {{ $headingColor }};
            --theme-card-bg: {{ $cardBg }};
            --theme-card-border: {{ $cardBorder }};
            --theme-price: {{ $priceColor }};
            --theme-sale-badge: {{ $saleBadgeColor }};
            --theme-header-bg: {{ $headerBg }};
            --theme-announcement-bg: {{ $announcementBg }};
            --theme-announcement-text: {{ $announcementText }};
            --theme-footer-bg: {{ $footerBg }};
            --theme-footer-text: {{ $footerText }};
        }

        html {
            font-size: var(--theme-font-size);
        }

        body {
            font-family: var(--theme-font-body);
            background-color: var(--theme-body-bg);
            color: var(--theme-text);
        }

        h1, h2, h3, h4, h5, h6, .heading-font {
            font-family: var(--theme-font-heading);
        }

        h1:not([class*="text-"]), h2:not([class*="text-"]), h3:not([class*="text-"]), h4:not([class*="text-"]), h5:not([class*="text-"]), h6:not([class*="text-"]) {
            color: var(--theme-heading);
        }

        .code-font {
            font-family: var(--theme-font-mono);
        }

        [x-cloak] { display: none !important; }
        
        /* Daraz style scrollbar */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: #f1f5f9; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 9999px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
        
        .daraz-shadow {
            box-shadow: 0 2px 12px 0 rgba(0,0,0,.08);
        }
        .mega-menu-shadow {
            box-shadow: 0 8px 24px rgba(15, 23, 42, 0.12);
        }
    </style>
    @stack('styles')

    <!-- Facebook Domain Verification -->
    @if(\App\Models\Setting::get('facebook_domain_verification'))
    <meta name="facebook-domain-verification" content="{{ \App\Models\Setting::get('facebook_domain_verification') }}" />
    @endif

    <!-- Google Search Console Verification -->
    @if(\App\Models\Setting::get('google_site_verification'))
    <meta name="google-site-verification" content="{{ \App\Models\Setting::get('google_site_verification') }}">
    @endif

    <!-- Google Tag Manager -->
    @if((app()->environment('production') || app()->environment('testing')) && \App\Models\Setting::get('google_tag_manager_enabled', '0') === '1' && \App\Models\Setting::get('google_tag_manager_id'))
    <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
    new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
    j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
    'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
    })(window,document,'script','dataLayer','{{ \App\Models\Setting::get("google_tag_manager_id") }}');</script>
    @endif

    <!-- Google Analytics 4 (gtag.js) -->
    @if((app()->environment('production') || app()->environment('testing')) && \App\Models\Setting::get('google_analytics_enabled', '0') === '1' && \App\Models\Setting::get('google_analytics_id'))
    <script async src="https://www.googletagmanager.com/gtag/js?id={{ \App\Models\Setting::get('google_analytics_id') }}"></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());
      gtag('config', '{{ \App\Models\Setting::get("google_analytics_id") }}');
    </script>
    @endif

    <!-- Google Ads Conversion Tag (gtag.js) -->
    @if((app()->environment('production') || app()->environment('testing')) && \App\Models\Setting::get('google_ads_enabled', '0') === '1' && \App\Models\Setting::get('google_ads_id'))
    <script async src="https://www.googletagmanager.com/gtag/js?id={{ \App\Models\Setting::get('google_ads_id') }}"></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());
      gtag('config', '{{ \App\Models\Setting::get("google_ads_id") }}');
    </script>
    @endif

    <!-- Google AdSense Auto Ads -->
    @if((app()->environment('production') || app()->environment('testing')) && \App\Models\Setting::get('google_adsense_enabled', '0') === '1' && \App\Models\Setting::get('google_adsense_id'))
    <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client={{ \App\Models\Setting::get('google_adsense_id') }}" crossorigin="anonymous"></script>
    @endif

    <!-- Meta / Facebook Pixel -->
    @if((app()->environment('production') || app()->environment('testing')) && \App\Models\Setting::get('facebook_pixel_enabled', '0') === '1' && \App\Models\Setting::get('facebook_pixel_id'))
    <script>
    !function(f,b,e,v,n,t,s)
    {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
    n.callMethod.apply(n,arguments):n.queue.push(arguments)};
    if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
    n.queue=[];t=b.createElement(e);t.async=!0;
    t.src=v;s=b.getElementsByTagName(e)[0];
    s.parentNode.insertBefore(t,s)}(window, document,'script',
    'https://connect.facebook.net/en_US/fbevents.js');
    fbq('init', '{{ \App\Models\Setting::get("facebook_pixel_id") }}');
    fbq('track', 'PageView');
    </script>
    <noscript><img height="1" width="1" style="display:none"
    src="https://www.facebook.com/tr?id={{ \App\Models\Setting::get('facebook_pixel_id') }}&ev=PageView&noscript=1"
    /></noscript>
    @endif

    <!-- TikTok Pixel -->
    @if((app()->environment('production') || app()->environment('testing')) && \App\Models\Setting::get('tiktok_pixel_enabled', '0') === '1' && \App\Models\Setting::get('tiktok_pixel_id'))
    <script>
    !function (w, d, t) {
      w.TiktokAnalyticsObject=t;var ttq=w[t]=w[t]||[];ttq.methods=["page","track","identify","instances","debug","on","off","once","ready","alias","group","enableCookie","disableCookie"],ttq.setAndDefer=function(t,e){t[e]=function(){t.push([e].concat(Array.prototype.slice.call(arguments,0)))}};for(var i=0;i<ttq.methods.length;i++)ttq.setAndDefer(ttq,ttq.methods[i]);ttq.instance=function(t){for(var e=ttq._i[t]||[],n=0;n<ttq.methods.length;n++)ttq.setAndDefer(e,ttq.methods[n]);return e};ttq.load=function(e,n){var i="https://analytics.tiktok.com/i18n/pixel/events.js";ttq._i=ttq._i||{},ttq._i[e]=[],ttq._i[e]._u=i,ttq._t=ttq._t||{},ttq._t[e]=+new Date,ttq._o=ttq._o||{},ttq._o[e]=n||{};var o=document.createElement("script");o.type="text/javascript",o.async=!0,o.src=i+"?sdkid="+e+"&lib="+t;var a=document.getElementsByTagName("script")[0];a.parentNode.insertBefore(o,a)};
      ttq.load('{{ \App\Models\Setting::get("tiktok_pixel_id") }}');
      ttq.page();
    }(window, document, 'ttq');
    </script>
    @endif

    <!-- Microsoft / Bing Ads UET Tag -->
    @if((app()->environment('production') || app()->environment('testing')) && \App\Models\Setting::get('bing_ads_enabled', '0') === '1' && \App\Models\Setting::get('bing_ads_id'))
    <script>
    (function(w,d,t,r,u){var f,n,i;w[u]=w[u]||[],f=function(){var o={ti:"{{ \App\Models\Setting::get('bing_ads_id') }}"};o.q=w[u],w[u]=new UET(o),w[u].push("pageLoad")},n=d.createElement(t),n.src=r,n.async=1,n.onload=n.onreadystatechange=function(){var s=this.readyState;s&&s!=="loaded"&&s!=="complete"||(f(),n.onload=n.onreadystatechange=null)},i=d.getElementsByTagName(t)[0],i.parentNode.insertBefore(n,i)})(window,document,"script","//bat.bing.com/bat.js","uetq");
    </script>
    @endif

    {!! \App\Models\Setting::get('header_scripts') !!}
</head>
<body x-data="globalStore()" x-init="initCart()" class="bg-slate-100/70 text-slate-800 antialiased min-h-screen flex flex-col selection:bg-daraz-orange selection:text-white">

    <!-- Google Tag Manager (noscript) -->
    @if((app()->environment('production') || app()->environment('testing')) && \App\Models\Setting::get('google_tag_manager_enabled', '0') === '1' && \App\Models\Setting::get('google_tag_manager_id'))
    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id={{ \App\Models\Setting::get('google_tag_manager_id') }}"
    height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
    @endif

    <!-- 1. TOP NOTICE & UTILITY BAR (Daraz Style) -->
    @if(\App\Models\Setting::get('announcement_enabled', '1') === '1')
    <div style="background-color: {{ $announcementBg }};" class="text-slate-300 text-xs py-1.5 px-4 border-b border-slate-800 hidden sm:block">
        <div class="max-w-7xl mx-auto flex items-center justify-between">
            <div class="flex items-center gap-6">
                <span style="color: {{ $announcementText }};" class="font-semibold flex items-center gap-1.5">
                    <i data-lucide="zap" class="w-3.5 h-3.5"></i>
                    <span>{{ \App\Models\Setting::get('announcement_text', "Bangladesh's #1 Electronic Component & PCB Marketplace") }}</span>
                </span>
                <span class="text-slate-400">Hotline: <strong class="text-slate-200 font-mono">{{ \App\Models\Setting::get('site_phone', \App\Models\Setting::get('phone', '+880 1700-112233')) }}</strong></span>
            </div>
            
            <div class="flex items-center gap-5 text-[11px] font-medium text-slate-400">
                <a href="{{ route('order.track') }}" class="hover:text-amber-400 transition flex items-center gap-1">
                    <i data-lucide="map-pin" class="w-3 h-3 text-daraz-orange"></i>
                    <span>Track My Order</span>
                </a>
                <a href="{{ route('warranty.verify') }}" class="hover:text-emerald-400 transition flex items-center gap-1">
                    <i data-lucide="shield-check" class="w-3 h-3 text-emerald-400"></i>
                    <span>Warranty Check</span>
                </a>
                <a href="{{ route('admin.dashboard') }}" class="hover:text-emerald-400 transition flex items-center gap-1 font-semibold text-emerald-400">
                    <i data-lucide="layout-dashboard" class="w-3 h-3"></i>
                    <span>Admin Panel</span>
                </a>
                <span class="text-slate-600">|</span>
                <span>Language: <strong class="text-slate-200">বাংলা / EN</strong></span>
            </div>
        </div>
    </div>
    @endif

    <!-- 2. MAIN HEADER (Search Bar, Logo, Cart Drawer) -->
    <header class="bg-white sticky top-0 z-40 border-b border-slate-200 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 py-3 sm:py-3.5">
            <div class="flex items-center justify-between gap-3 sm:gap-8">
                
                <!-- Brand Logo -->
                <a href="{{ route('home') }}" class="flex items-center gap-2 flex-shrink-0 group">
                    @if(\App\Models\Setting::get('site_logo'))
                        <img src="{{ asset(\App\Models\Setting::get('site_logo')) }}" alt="{{ \App\Models\Setting::get('site_name', 'DREAMERS PCB') }}" class="h-10 max-w-[180px] object-contain">
                    @else
                        <div class="w-10 h-10 rounded-2xl bg-gradient-to-tr from-slate-950 via-slate-900 to-emerald-900 border border-emerald-500/30 flex items-center justify-center text-emerald-400 shadow-md group-hover:scale-105 transition">
                            <i data-lucide="cpu" class="w-5 h-5 text-emerald-400"></i>
                        </div>
                        <div>
                            <div class="text-xl sm:text-2xl font-black tracking-tight text-slate-900 flex items-center">
                                {{ \App\Models\Setting::get('site_name', 'DREAMERS PCB') }}
                            </div>
                            <p class="text-[9px] uppercase tracking-widest text-slate-400 font-bold -mt-1 hidden sm:block">{{ \App\Models\Setting::get('site_tagline', 'Electronics Mega Mart') }}</p>
                        </div>
                    @endif
                </a>

                <!-- Daraz-Style Centered Large Search Bar -->
                <div class="flex-1 max-w-2xl relative">
                    <form action="{{ route('shop.index') }}" method="GET" class="flex items-center">
                        <div class="relative w-full">
                            <input 
                                type="text" 
                                name="search" 
                                value="{{ request('search') }}"
                                placeholder="Search in DREAMERS PCB (e.g. ESP32, STM32, Arduino, Soldering Station...)" 
                                class="w-full pl-4 pr-12 sm:pr-24 py-2.5 rounded-xl sm:rounded-2xl border-2 border-daraz-orange/80 bg-slate-50 text-xs sm:text-sm font-medium text-slate-900 placeholder:text-slate-400 outline-none focus:bg-white focus:ring-4 focus:ring-daraz-orange/10 transition">
                            
                            <button type="submit" class="absolute right-1 top-1 bottom-1 px-3 sm:px-6 rounded-lg sm:rounded-xl bg-daraz-orange hover:bg-daraz-orangeHover text-white font-bold text-xs flex items-center gap-1.5 transition shadow-sm">
                                <i data-lucide="search" class="w-4 h-4"></i>
                                <span class="hidden sm:inline">Search</span>
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Right Action Buttons (Customer Account, Track, Cart Drawer Trigger) -->
                <div class="flex items-center gap-2 sm:gap-3 flex-shrink-0">
                    
                    <!-- Customer Auth Trigger -->
                    @if(Auth::guard('customer')->check())
                    <div x-data="{ open: false }" class="relative">
                        <button @click="open = !open" class="flex items-center gap-2 px-3 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-xs font-bold text-slate-800 transition">
                            <div class="w-5 h-5 rounded-full bg-daraz-orange text-white text-[10px] font-black flex items-center justify-center">
                                {{ substr(Auth::guard('customer')->user()->name, 0, 1) }}
                            </div>
                            <span class="hidden sm:inline truncate max-w-[100px]">{{ Auth::guard('customer')->user()->name }}</span>
                            <i data-lucide="chevron-down" class="w-3.5 h-3.5 text-slate-400"></i>
                        </button>

                        <!-- Dropdown Menu -->
                        <div x-show="open" @click.away="open = false" x-cloak class="absolute right-0 mt-2 w-48 bg-white rounded-2xl border border-slate-200 shadow-xl py-2 z-50 text-xs space-y-1">
                            <div class="px-3 py-1.5 border-b border-slate-100 font-bold text-slate-900 truncate">
                                {{ Auth::guard('customer')->user()->name }}
                            </div>
                            <a href="{{ route('customer.dashboard') }}" class="flex items-center gap-2 px-3 py-2 text-slate-700 hover:bg-slate-50 hover:text-daraz-orange font-semibold">
                                <i data-lucide="layout-dashboard" class="w-3.5 h-3.5"></i>
                                <span>Dashboard</span>
                            </a>
                            <a href="{{ route('customer.orders') }}" class="flex items-center gap-2 px-3 py-2 text-slate-700 hover:bg-slate-50 hover:text-daraz-orange font-semibold">
                                <i data-lucide="package" class="w-3.5 h-3.5"></i>
                                <span>My Orders</span>
                            </a>
                            <a href="{{ route('customer.wishlist') }}" class="flex items-center gap-2 px-3 py-2 text-slate-700 hover:bg-slate-50 hover:text-daraz-orange font-semibold">
                                <i data-lucide="heart" class="w-3.5 h-3.5"></i>
                                <span>My Wishlist</span>
                            </a>
                            <a href="{{ route('customer.profile') }}" class="flex items-center gap-2 px-3 py-2 text-slate-700 hover:bg-slate-50 hover:text-daraz-orange font-semibold">
                                <i data-lucide="user" class="w-3.5 h-3.5"></i>
                                <span>Profile & Address</span>
                            </a>
                            <div class="pt-1 border-t border-slate-100">
                                <form method="POST" action="{{ route('customer.logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full flex items-center gap-2 px-3 py-1.5 text-rose-600 hover:bg-rose-50 font-bold text-left">
                                        <i data-lucide="log-out" class="w-3.5 h-3.5"></i>
                                        <span>Logout</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    @else
                    <a href="{{ route('customer.login') }}" class="flex items-center gap-1.5 px-3 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-xs font-bold text-slate-800 transition">
                        <i data-lucide="user" class="w-4 h-4 text-daraz-orange"></i>
                        <span>Login / Sign Up</span>
                    </a>
                    @endif

                    <!-- Fast Track Button -->
                    <a href="{{ route('order.track') }}" class="hidden md:flex items-center gap-1.5 px-3 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-xs font-bold text-slate-700 transition">
                        <i data-lucide="truck" class="w-4 h-4 text-daraz-orange"></i>
                        <span>Track</span>
                    </a>

                    <!-- Cart Drawer Trigger Button -->
                    <button 
                        @click="cartOpen = true" 
                        class="relative p-2.5 sm:px-4 sm:py-2.5 rounded-xl sm:rounded-2xl bg-slate-900 hover:bg-slate-800 text-white flex items-center gap-2.5 transition shadow-md group">
                        <i data-lucide="shopping-cart" class="w-5 h-5 text-emerald-400 group-hover:scale-110 transition"></i>
                        <span class="hidden sm:inline text-xs font-bold">Cart</span>
                        <span 
                            x-text="cartCount" 
                            class="w-5 h-5 rounded-full bg-daraz-orange text-white text-[10px] font-black flex items-center justify-center shadow-sm">
                            0
                        </span>
                    </button>

                </div>

            </div>
        </div>

        <!-- Secondary Categories Sub-Navbar (Daraz Yellow/Orange Ribbon) -->
        <nav class="bg-slate-900 text-white text-xs border-t border-slate-800 hidden sm:block">
            <div class="max-w-7xl mx-auto px-4 flex items-center justify-between">
                
                <div class="flex items-center gap-6 py-2">
                    <a href="{{ route('shop.index') }}" class="font-bold flex items-center gap-1.5 text-emerald-400 hover:text-white transition">
                        <i data-lucide="grid" class="w-4 h-4"></i>
                        <span>All Categories</span>
                    </a>
                    <a href="{{ route('shop.index', ['sort' => 'featured']) }}" class="hover:text-amber-400 transition font-medium">🔥 Flash Sale Deals</a>
                    <a href="{{ route('shop.index', ['search' => 'ESP32']) }}" class="hover:text-amber-400 transition font-medium">ESP32 & IoT</a>
                    <a href="{{ route('shop.index', ['search' => 'STM32']) }}" class="hover:text-amber-400 transition font-medium">STM32 ARM Dev Boards</a>
                    <a href="{{ route('shop.index', ['search' => 'Arduino']) }}" class="hover:text-amber-400 transition font-medium">Arduino & Shields</a>
                    <a href="{{ route('shop.index', ['search' => 'Rework']) }}" class="hover:text-amber-400 transition font-medium">Soldering & Rework Stations</a>
                </div>

                <div class="flex items-center gap-4 text-[11px] text-slate-400">
                    <span class="text-emerald-400 font-semibold flex items-center gap-1">
                        <i data-lucide="shield-check" class="w-3.5 h-3.5"></i>
                        <span>{{ \App\Models\Setting::get('guarantee_badge_text', '100% Genuine Hardware Guaranteed') }}</span>
                    </span>
                </div>

            </div>
        </nav>
    </header>

    <!-- Main Content Area -->
    <main class="flex-1">
        
        <!-- Flash Message Alerts -->
        @if(session('success'))
        <div class="max-w-7xl mx-auto px-4 mt-4">
            <div class="p-3.5 rounded-2xl bg-emerald-600 text-white font-bold text-xs flex items-center justify-between shadow-md">
                <div class="flex items-center gap-2">
                    <i data-lucide="check-circle" class="w-4 h-4"></i>
                    <span>{{ session('success') }}</span>
                </div>
                <button onclick="this.parentElement.remove()" class="text-white/80 hover:text-white text-base">&times;</button>
            </div>
        </div>
        @endif

        @if(session('warning'))
        <div class="max-w-7xl mx-auto px-4 mt-4">
            <div class="p-3.5 rounded-2xl bg-amber-500 text-slate-950 font-bold text-xs flex items-center justify-between shadow-md">
                <div class="flex items-center gap-2">
                    <i data-lucide="alert-circle" class="w-4 h-4"></i>
                    <span>{{ session('warning') }}</span>
                </div>
                <button onclick="this.parentElement.remove()" class="text-slate-900 text-base">&times;</button>
            </div>
        </div>
        @endif

        @yield('content')
    </main>

    <!-- 3. SLIDE-OUT CART DRAWER (Alpine.js Responsive) -->
    <div x-cloak x-show="cartOpen" class="fixed inset-0 z-50 overflow-hidden">
        <!-- Backdrop -->
        <div 
            x-show="cartOpen" 
            x-transition:enter="transition-opacity ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition-opacity ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            @click="cartOpen = false" 
            class="fixed inset-0 bg-slate-950/70 backdrop-blur-sm"></div>

        <div class="fixed inset-y-0 right-0 max-w-full flex pl-10">
            <div 
                x-show="cartOpen" 
                x-transition:enter="transform transition ease-in-out duration-300"
                x-transition:enter-start="translate-x-full"
                x-transition:enter-end="translate-x-0"
                x-transition:leave="transform transition ease-in-out duration-200"
                x-transition:leave-start="translate-x-0"
                x-transition:leave-end="translate-x-full"
                class="w-screen max-w-md bg-white shadow-2xl flex flex-col">
                
                <!-- Drawer Header -->
                <div class="p-4 sm:p-5 bg-slate-900 text-white flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <i data-lucide="shopping-bag" class="w-5 h-5 text-emerald-400"></i>
                        <h3 class="font-bold text-sm">Your Shopping Cart (<span x-text="cartCount"></span>)</h3>
                    </div>
                    <button @click="cartOpen = false" class="p-1 rounded-lg text-slate-400 hover:text-white hover:bg-slate-800 transition">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>

                <!-- Drawer Items List -->
                <div class="flex-1 overflow-y-auto p-4 divide-y divide-slate-100">
                    <template x-if="cartItems.length === 0">
                        <div class="py-16 text-center text-slate-400 space-y-3">
                            <i data-lucide="shopping-cart" class="w-12 h-12 mx-auto text-slate-300"></i>
                            <p class="text-sm font-semibold">Your cart is empty</p>
                            <a href="{{ route('shop.index') }}" @click="cartOpen = false" class="inline-block px-4 py-2 rounded-xl bg-daraz-orange text-white text-xs font-bold shadow-md">
                                Start Shopping &rarr;
                            </a>
                        </div>
                    </template>

                    <template x-for="item in cartItems" :key="item.cart_key">
                        <div class="py-3.5 flex items-start gap-3">
                            <img :src="item.thumbnail" :alt="item.name" class="w-14 h-14 object-cover rounded-xl border border-slate-200 flex-shrink-0">
                            
                            <div class="flex-1 min-w-0">
                                <h4 class="text-xs font-bold text-slate-900 truncate" x-text="item.name"></h4>
                                <div class="text-[11px] text-emerald-600 font-medium" x-show="item.variant_name" x-text="item.variant_name"></div>
                                <div class="text-xs font-bold text-slate-900 code-font mt-1">
                                    ৳<span x-text="item.price.toFixed(2)"></span>
                                </div>
                                
                                <div class="flex items-center justify-between mt-2">
                                    <div class="flex items-center gap-1.5 border border-slate-200 rounded-lg p-0.5 bg-slate-50">
                                        <button @click="updateQty(item.cart_key, item.quantity - 1)" class="w-5 h-5 rounded flex items-center justify-center font-bold text-xs text-slate-600 hover:bg-slate-200">-</button>
                                        <span class="w-6 text-center text-xs font-bold code-font" x-text="item.quantity"></span>
                                        <button @click="updateQty(item.cart_key, item.quantity + 1)" class="w-5 h-5 rounded flex items-center justify-center font-bold text-xs text-slate-600 hover:bg-slate-200">+</button>
                                    </div>
                                    
                                    <button @click="removeItem(item.cart_key)" class="text-xs text-rose-500 hover:underline font-semibold">
                                        Remove
                                    </button>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>

                <!-- Drawer Footer Summary -->
                <div x-show="cartItems.length > 0" class="p-4 sm:p-5 bg-slate-50 border-t border-slate-200 space-y-3">
                    <div class="flex justify-between text-xs text-slate-600">
                        <span>Items Subtotal:</span>
                        <span class="font-bold text-slate-900 code-font">৳<span x-text="cartSummary.subtotal ? cartSummary.subtotal.toFixed(2) : '0.00'"></span></span>
                    </div>

                    <div class="flex justify-between text-sm font-black text-slate-900 pt-2 border-t border-slate-200">
                        <span>Total Payable:</span>
                        <span class="text-daraz-orange code-font text-base">৳<span x-text="cartSummary.payable ? cartSummary.payable.toFixed(2) : '0.00'"></span></span>
                    </div>

                    <div class="grid grid-cols-2 gap-2 pt-2">
                        <a href="{{ route('cart.index') }}" @click="cartOpen = false" class="w-full py-2.5 rounded-xl bg-slate-200 hover:bg-slate-300 text-slate-800 font-bold text-xs text-center transition">
                            View Full Cart
                        </a>
                        <a href="{{ route('checkout.index') }}" class="w-full py-2.5 rounded-xl bg-daraz-orange hover:bg-daraz-orangeHover text-white font-extrabold text-xs text-center shadow-lg transition">
                            Fast Checkout &rarr;
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- 4. MODERN FOOTER (Daraz Style Trust Badges, Payment Icons, Categories) -->
    <footer style="background-color: {{ $footerBg }};" class="text-slate-400 text-xs mt-16 border-t border-slate-800">
        
        <!-- Trust Guarantee Top Strip -->
        <div class="border-b border-slate-800/80 py-8 bg-slate-900/60">
            <div class="max-w-7xl mx-auto px-4 grid grid-cols-2 md:grid-cols-4 gap-6">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-daraz-orange/10 text-daraz-orange flex items-center justify-center flex-shrink-0">
                        <i data-lucide="truck" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-slate-200 text-xs">Fast Nationwide COD</h4>
                        <p class="text-[11px] text-slate-500">Dhaka & across all 64 districts</p>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-emerald-500/10 text-emerald-400 flex items-center justify-center flex-shrink-0">
                        <i data-lucide="shield-check" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-slate-200 text-xs">100% Genuine Components</h4>
                        <p class="text-[11px] text-slate-500">Verified factory ICs & modules</p>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-sky-500/10 text-sky-400 flex items-center justify-center flex-shrink-0">
                        <i data-lucide="refresh-cw" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-slate-200 text-xs">7 Days Easy Return</h4>
                        <p class="text-[11px] text-slate-500">Fast replacement on defects</p>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-purple-500/10 text-purple-400 flex items-center justify-center flex-shrink-0">
                        <i data-lucide="headphones" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-slate-200 text-xs">24/7 Tech Support</h4>
                        <p class="text-[11px] text-slate-500">Engineers on standby</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Links Columns -->
        <div class="max-w-7xl mx-auto px-4 py-12 grid grid-cols-1 md:grid-cols-4 gap-8">
            <div class="space-y-3">
                <div class="text-lg font-black text-white flex items-center">
                    DREAMERS<span class="text-daraz-orange ml-1">PCB</span>
                </div>
                <p class="text-xs text-slate-400 leading-relaxed">
                    {{ \App\Models\Setting::get('footer_about', "Bangladesh's enterprise online superstore for hardware developers, robotics researchers, and electronics enthusiasts.") }}
                </p>
                <div class="text-xs text-slate-300 space-y-1">
                    <p><strong>Hotline:</strong> {{ \App\Models\Setting::get('footer_hotline', '+880 1700-112233') }}</p>
                    <p><strong>Email:</strong> {{ \App\Models\Setting::get('footer_email', 'support@dreamerspcb.com') }}</p>
                    <p><strong>Office:</strong> {{ \App\Models\Setting::get('footer_address_office', 'Multiplan Center, Elephant Road, Dhaka') }}</p>
                </div>

                <!-- Social Icons -->
                <div class="flex items-center gap-3 pt-2 text-slate-400">
                    @if(\App\Models\Setting::get('footer_facebook_url'))
                    <a href="{{ \App\Models\Setting::get('footer_facebook_url') }}" target="_blank" class="hover:text-blue-400 transition" title="Facebook">
                        <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                    </a>
                    @endif
                    @if(\App\Models\Setting::get('footer_youtube_url'))
                    <a href="{{ \App\Models\Setting::get('footer_youtube_url') }}" target="_blank" class="hover:text-rose-400 transition" title="YouTube">
                        <i data-lucide="youtube" class="w-4 h-4"></i>
                    </a>
                    @endif
                    @if(\App\Models\Setting::get('footer_linkedin_url'))
                    <a href="{{ \App\Models\Setting::get('footer_linkedin_url') }}" target="_blank" class="hover:text-sky-400 transition" title="LinkedIn">
                        <i data-lucide="linkedin" class="w-4 h-4"></i>
                    </a>
                    @endif
                    @if(\App\Models\Setting::get('footer_github_url'))
                    <a href="{{ \App\Models\Setting::get('footer_github_url') }}" target="_blank" class="hover:text-white transition" title="GitHub">
                        <i data-lucide="github" class="w-4 h-4"></i>
                    </a>
                    @endif
                </div>
            </div>

            <div class="space-y-2">
                <h4 class="font-bold text-white uppercase tracking-wider text-[11px]">Popular Categories</h4>
                <ul class="space-y-1.5 text-xs">
                    <li><a href="{{ route('shop.index', ['search' => 'ESP32']) }}" class="hover:text-daraz-orange transition">ESP32 & IoT Microcontrollers</a></li>
                    <li><a href="{{ route('shop.index', ['search' => 'STM32']) }}" class="hover:text-daraz-orange transition">STM32 ARM Cortex Boards</a></li>
                    <li><a href="{{ route('shop.index', ['search' => 'Arduino']) }}" class="hover:text-daraz-orange transition">Arduino Uno & Mega Kits</a></li>
                    <li><a href="{{ route('shop.index', ['search' => 'Soldering']) }}" class="hover:text-daraz-orange transition">Quick 861DW Rework Stations</a></li>
                    <li><a href="{{ route('shop.index') }}" class="hover:text-daraz-orange transition">Sensors & Relay Modules</a></li>
                </ul>
            </div>

            <div class="space-y-2">
                <h4 class="font-bold text-white uppercase tracking-wider text-[11px]">Customer Care & Policies</h4>
                <ul class="space-y-1.5 text-xs">
                    <li><a href="{{ route('order.track') }}" class="hover:text-daraz-orange transition">Track My Order</a></li>
                    <li><a href="{{ route('warranty.verify') }}" class="hover:text-daraz-orange transition">Warranty Verification</a></li>
                    <li><a href="{{ route('cart.index') }}" class="hover:text-daraz-orange transition">My Shopping Cart</a></li>
                    <li><a href="{{ route('shop.index') }}" class="hover:text-daraz-orange transition">Special Flash Offers</a></li>
                    <li><a href="{{ route('customer.dashboard') }}" class="hover:text-daraz-orange transition">Customer Portal</a></li>
                    @if(\App\Models\Setting::get('footer_custom_link1_title'))
                    <li><a href="{{ \App\Models\Setting::get('footer_custom_link1_url', '#') }}" class="hover:text-emerald-400 transition">{{ \App\Models\Setting::get('footer_custom_link1_title') }}</a></li>
                    @endif
                    @if(\App\Models\Setting::get('footer_custom_link2_title'))
                    <li><a href="{{ \App\Models\Setting::get('footer_custom_link2_url', '#') }}" class="hover:text-emerald-400 transition">{{ \App\Models\Setting::get('footer_custom_link2_title') }}</a></li>
                    @endif
                </ul>
            </div>

            <div class="space-y-3">
                <h4 class="font-bold text-white uppercase tracking-wider text-[11px]">Accepted Payment Methods</h4>
                <div class="flex flex-wrap gap-2 pt-1">
                    <span class="px-2.5 py-1 rounded bg-slate-900 border border-slate-800 text-[10px] font-bold text-pink-400">bKash</span>
                    <span class="px-2.5 py-1 rounded bg-slate-900 border border-slate-800 text-[10px] font-bold text-amber-400">Nagad</span>
                    <span class="px-2.5 py-1 rounded bg-slate-900 border border-slate-800 text-[10px] font-bold text-emerald-400">Cash On Delivery</span>
                    <span class="px-2.5 py-1 rounded bg-slate-900 border border-slate-800 text-[10px] font-bold text-sky-400">Bank Transfer</span>
                </div>
                <div class="pt-2">
                    <p class="text-[11px] text-slate-500">Verified Courier Partners:</p>
                    <p class="font-semibold text-slate-300">{{ \App\Models\Setting::get('footer_courier_partners', 'Steadfast • Pathao Courier • RedX') }}</p>
                </div>
                @if(\App\Models\Setting::get('footer_trade_license'))
                <div class="text-[10px] text-slate-500 font-mono">
                    Trade Lic: {{ \App\Models\Setting::get('footer_trade_license') }}
                </div>
                @endif
            </div>
        </div>

        <!-- Copyright -->
        <div class="border-t border-slate-900 py-4 text-center text-slate-500 text-[11px]">
            <p>{{ \App\Models\Setting::get('footer_copyright', '© ' . date('Y') . ' DREAMERS PCB. All rights reserved. Built for high-speed electronics e-commerce.') }}</p>
        </div>

    </footer>

    <!-- 5. MOBILE BOTTOM NAVIGATION (Daraz Style) -->
    <div class="sm:hidden fixed bottom-0 left-0 right-0 bg-white border-t border-slate-200 z-40 px-4 py-2 flex items-center justify-around text-[10px] font-bold text-slate-600 shadow-lg">
        <a href="{{ route('home') }}" class="flex flex-col items-center gap-0.5 {{ request()->routeIs('home') ? 'text-daraz-orange' : '' }}">
            <i data-lucide="home" class="w-5 h-5"></i>
            <span>Home</span>
        </a>
        <a href="{{ route('shop.index') }}" class="flex flex-col items-center gap-0.5 {{ request()->routeIs('shop.*') ? 'text-daraz-orange' : '' }}">
            <i data-lucide="grid" class="w-5 h-5"></i>
            <span>Categories</span>
        </a>
        <button @click="cartOpen = true" class="flex flex-col items-center gap-0.5 relative text-slate-800">
            <i data-lucide="shopping-cart" class="w-5 h-5 text-emerald-600"></i>
            <span class="absolute -top-1 right-2 w-4 h-4 rounded-full bg-daraz-orange text-white text-[9px] flex items-center justify-center font-bold" x-text="cartCount">0</span>
            <span>Cart</span>
        </button>
        <a href="{{ route('order.track') }}" class="flex flex-col items-center gap-0.5 {{ request()->routeIs('order.track') ? 'text-daraz-orange' : '' }}">
            <i data-lucide="map-pin" class="w-5 h-5"></i>
            <span>Track</span>
        </a>
        <a href="{{ route('admin.dashboard') }}" class="flex flex-col items-center gap-0.5 text-slate-500 hover:text-emerald-500">
            <i data-lucide="user" class="w-5 h-5"></i>
            <span>Admin</span>
        </a>
    </div>

    <!-- Global Alpine Store Script -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            lucide.createIcons();
        });

        function globalStore() {
            return {
                cartOpen: false,
                cartItems: [],
                cartCount: 0,
                cartSummary: { subtotal: 0, payable: 0 },

                async initCart() {
                    try {
                        const res = await fetch(`{{ route('cart.json') }}`);
                        const data = await res.json();
                        if (data.success) {
                            this.cartItems = data.items || [];
                            this.cartCount = data.count || 0;
                            this.cartSummary = data.summary || { subtotal: 0, payable: 0 };
                        }
                    } catch (e) {
                        console.error('Cart sync error', e);
                    }
                },

                async addToCart(productId, variantId = null, quantity = 1) {
                    try {
                        const res = await fetch(`{{ route('cart.add') }}`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({ product_id: productId, variant_id: variantId, quantity: quantity })
                        });
                        const data = await res.json();
                        if (data.success) {
                            this.cartItems = data.items || [];
                            this.cartCount = data.count || 0;
                            this.cartSummary = data.summary || { subtotal: 0, payable: 0 };
                            this.cartOpen = true;
                        }
                    } catch (e) {
                        alert('Could not add to cart');
                    }
                },

                async updateQty(cartKey, qty) {
                    try {
                        const res = await fetch(`{{ route('cart.update') }}`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({ cart_key: cartKey, quantity: qty })
                        });
                        const data = await res.json();
                        if (data.success) {
                            this.cartItems = data.items || [];
                            this.cartCount = data.count || 0;
                            this.cartSummary = data.summary || { subtotal: 0, payable: 0 };
                        }
                    } catch (e) {
                        console.error(e);
                    }
                },

                async removeItem(cartKey) {
                    try {
                        const res = await fetch(`{{ route('cart.remove') }}`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({ cart_key: cartKey })
                        });
                        const data = await res.json();
                        if (data.success) {
                            this.cartItems = data.items || [];
                            this.cartCount = data.count || 0;
                            this.cartSummary = data.summary || { subtotal: 0, payable: 0 };
                        }
                    } catch (e) {
                        console.error(e);
                    }
                }
            };
        }
    </script>

    @stack('scripts')
    {!! \App\Models\Setting::get('footer_scripts') !!}
</body>
</html>
