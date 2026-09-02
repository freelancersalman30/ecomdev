<!DOCTYPE html>
<html lang="en" x-data="{ darkMode: false, sidebarOpen: true, mobileSidebarOpen: false }" :class="{ 'dark': darkMode }">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Dashboard') - {{ \App\Models\Setting::get('site_name', 'DREAMERS PCB') }}</title>

    @if(\App\Models\Setting::get('site_favicon'))
    <link rel="icon" href="{{ asset(\App\Models\Setting::get('site_favicon')) }}">
    @endif

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        brand: {
                            50: '#f0fdf4',
                            100: '#dcfce7',
                            500: '#22c55e',
                            600: '#16a34a',
                            700: '#15803d',
                            800: '#166534',
                            900: '#14532d',
                        },
                        pcb: {
                            dark: '#0f172a',
                            card: '#1e293b',
                            accent: '#06b6d4',
                            green: '#10b981',
                            circuit: '#334155'
                        }
                    },
                    fontFamily: {
                        sans: ['Inter', 'system-ui', '-apple-system', 'Segoe UI', 'Roboto', 'sans-serif'],
                    }
                }
            }
        }
    </script>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>

    <!-- ApexCharts -->
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

    <!-- Marked.js (Markdown & Gemini AI Live Renderer) -->
    <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>

    <!-- Turndown.js & GFM Plugin (Smart HTML to Markdown Converter for Gemini Copy-Paste) -->
    <script src="https://cdn.jsdelivr.net/npm/turndown@7.2.0/dist/turndown.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/turndown-plugin-gfm@1.0.2/dist/turndown-plugin-gfm.js"></script>

    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
        .code-font {
            font-family: 'JetBrains Mono', monospace;
        }
        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        ::-webkit-scrollbar-track {
            background: rgba(0, 0, 0, 0.05);
        }
        ::-webkit-scrollbar-thumb {
            background: rgba(100, 116, 139, 0.4);
            border-radius: 4px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: rgba(100, 116, 139, 0.7);
        }
    </style>
    @stack('styles')
</head>
<body class="bg-slate-100 dark:bg-slate-950 text-slate-800 dark:text-slate-100 antialiased min-h-screen flex flex-col">

    <div class="flex h-screen overflow-hidden">

        <!-- ================= SIDEBAR NAVIGATION ================= -->
        <aside 
            :class="{
                'w-64': sidebarOpen,
                'w-20': !sidebarOpen,
                'translate-x-0': mobileSidebarOpen,
                '-translate-x-full lg:translate-x-0': !mobileSidebarOpen
            }"
            class="fixed lg:static inset-y-0 left-0 z-40 bg-slate-900 text-slate-300 transition-all duration-300 ease-in-out flex flex-col border-r border-slate-800 shadow-2xl">
            
            <!-- Logo Header -->
            <div class="h-16 flex items-center justify-between px-4 border-b border-slate-800 bg-slate-950/60">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 overflow-hidden">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-emerald-500 to-teal-400 flex items-center justify-center text-slate-950 font-black shadow-lg shadow-emerald-500/20 flex-shrink-0">
                        <i data-lucide="cpu" class="w-6 h-6 text-slate-950"></i>
                    </div>
                    <div x-show="sidebarOpen" x-transition class="whitespace-nowrap">
                        <span class="font-extrabold text-base tracking-wider text-white truncate max-w-[150px] block">{{ \App\Models\Setting::get('site_name', 'DREAMERS PCB') }}</span>
                        <span class="block text-[10px] tracking-widest text-emerald-400/80 uppercase font-semibold">Enterprise Hub</span>
                    </div>
                </a>
                <button @click="sidebarOpen = !sidebarOpen" class="hidden lg:flex p-1.5 rounded-lg text-slate-400 hover:text-white hover:bg-slate-800 transition">
                    <i data-lucide="chevrons-left" :class="{ 'rotate-180': !sidebarOpen }" class="w-5 h-5 transition-transform duration-300"></i>
                </button>
            </div>

            <!-- Navigation Links -->
            <div class="flex-1 overflow-y-auto px-3 py-4 space-y-1">

                <div x-show="sidebarOpen" class="px-3 pt-1 pb-2 text-[10px] font-bold uppercase tracking-wider text-slate-500">Core Operations</div>

                <!-- Dashboard -->
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium text-sm transition {{ request()->routeIs('admin.dashboard') ? 'bg-emerald-500 text-slate-950 font-semibold shadow-md shadow-emerald-500/20' : 'hover:bg-slate-800 hover:text-white' }}">
                    <i data-lucide="layout-dashboard" class="w-5 h-5 flex-shrink-0"></i>
                    <span x-show="sidebarOpen" class="whitespace-nowrap">Dashboard Overview</span>
                </a>

                <!-- POS System -->
                <a href="{{ route('admin.pos.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium text-sm transition {{ request()->routeIs('admin.pos.*') ? 'bg-emerald-500 text-slate-950 font-semibold shadow-md shadow-emerald-500/20' : 'hover:bg-slate-800 hover:text-white text-emerald-400' }}">
                    <i data-lucide="scan-barcode" class="w-5 h-5 flex-shrink-0"></i>
                    <span x-show="sidebarOpen" class="whitespace-nowrap flex items-center justify-between w-full">
                        <span>POS System</span>
                        <span class="text-[10px] px-1.5 py-0.5 rounded-full bg-emerald-400/20 text-emerald-300 font-bold">Counter</span>
                    </span>
                </a>

                <!-- Orders Management -->
                <a href="{{ route('admin.orders.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium text-sm transition {{ request()->routeIs('admin.orders.*') ? 'bg-emerald-500 text-slate-950 font-semibold shadow-md shadow-emerald-500/20' : 'hover:bg-slate-800 hover:text-white' }}">
                    <i data-lucide="shopping-bag" class="w-5 h-5 flex-shrink-0"></i>
                    <span x-show="sidebarOpen" class="whitespace-nowrap">Orders Management</span>
                </a>

                <!-- Product Warranty Verification -->
                <a href="{{ route('admin.warranties.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium text-sm transition {{ request()->routeIs('admin.warranties.*') ? 'bg-emerald-500 text-slate-950 font-semibold shadow-md shadow-emerald-500/20' : 'hover:bg-slate-800 hover:text-white' }}">
                    <i data-lucide="shield-check" class="w-5 h-5 flex-shrink-0 text-emerald-400"></i>
                    <span x-show="sidebarOpen" class="whitespace-nowrap">Warranty Verification</span>
                </a>

                <!-- Notifications & Activity Hub -->
                <a href="{{ route('admin.notifications.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium text-sm transition {{ request()->routeIs('admin.notifications.*') ? 'bg-emerald-500 text-slate-950 font-semibold shadow-md shadow-emerald-500/20' : 'hover:bg-slate-800 hover:text-white' }}">
                    <i data-lucide="bell" class="w-5 h-5 flex-shrink-0 text-amber-400"></i>
                    <span x-show="sidebarOpen" class="whitespace-nowrap flex items-center justify-between w-full">
                        <span>Notifications</span>
                        @php
                            $unreadSidebarCount = 0;
                            try {
                                if (\Illuminate\Support\Facades\Schema::hasTable('notifications')) {
                                    $unreadSidebarCount = Auth::guard('web')->user()?->unreadNotifications()->count() ?? 0;
                                }
                            } catch (\Throwable $e) {}
                        @endphp
                        @if($unreadSidebarCount > 0)
                        <span class="text-[10px] px-1.5 py-0.5 rounded-full bg-rose-500 text-white font-bold">{{ $unreadSidebarCount }}</span>
                        @endif
                    </span>
                </a>

                <!-- Product & Catalog -->
                <div x-data="{ open: {{ request()->routeIs('admin.products.*', 'admin.categories.*', 'admin.brands.*', 'admin.attributes.*') ? 'true' : 'false' }} }">
                    <button @click="open = !open" class="w-full flex items-center justify-between px-3 py-2.5 rounded-xl font-medium text-sm transition hover:bg-slate-800 hover:text-white">
                        <div class="flex items-center gap-3">
                            <i data-lucide="package-search" class="w-5 h-5 flex-shrink-0"></i>
                            <span x-show="sidebarOpen" class="whitespace-nowrap">Products & Catalog</span>
                        </div>
                        <i x-show="sidebarOpen" data-lucide="chevron-down" :class="{ 'rotate-180': open }" class="w-4 h-4 transition-transform"></i>
                    </button>
                    <div x-show="open && sidebarOpen" class="pl-8 pr-2 py-1 space-y-1 text-xs">
                        <a href="{{ route('admin.products.index') }}" class="block px-3 py-1.5 rounded-lg {{ request()->routeIs('admin.products.index') ? 'text-emerald-400 font-semibold' : 'hover:text-white' }}">All Products & Tech Specs</a>
                        <a href="{{ route('admin.products.create') }}" class="block px-3 py-1.5 rounded-lg {{ request()->routeIs('admin.products.create') ? 'text-emerald-400 font-semibold' : 'hover:text-white' }}">Add New Product</a>
                        <a href="{{ route('admin.categories.index') }}" class="block px-3 py-1.5 rounded-lg {{ request()->routeIs('admin.categories.*') ? 'text-emerald-400 font-semibold' : 'hover:text-white' }}">Categories (3-Tier)</a>
                        <a href="{{ route('admin.brands.index') }}" class="block px-3 py-1.5 rounded-lg {{ request()->routeIs('admin.brands.*') ? 'text-emerald-400 font-semibold' : 'hover:text-white' }}">Brands</a>
                        <a href="{{ route('admin.attributes.index') }}" class="block px-3 py-1.5 rounded-lg {{ request()->routeIs('admin.attributes.*') ? 'text-emerald-400 font-semibold' : 'hover:text-white' }}">Colors & Sizes / Pinouts</a>
                    </div>
                </div>

                <div x-show="sidebarOpen" class="px-3 pt-3 pb-2 text-[10px] font-bold uppercase tracking-wider text-slate-500">Procurement & Vendors</div>

                <!-- Purchases & Supplier Due -->
                <a href="{{ route('admin.purchases.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium text-sm transition {{ request()->routeIs('admin.purchases.*') ? 'bg-emerald-500 text-slate-950 font-semibold shadow-md shadow-emerald-500/20' : 'hover:bg-slate-800 hover:text-white' }}">
                    <i data-lucide="truck" class="w-5 h-5 flex-shrink-0"></i>
                    <span x-show="sidebarOpen" class="whitespace-nowrap">Purchases & Supplier Due</span>
                </a>

                <!-- Supplier Management -->
                <a href="{{ route('admin.suppliers.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium text-sm transition {{ request()->routeIs('admin.suppliers.*') ? 'bg-emerald-500 text-slate-950 font-semibold shadow-md shadow-emerald-500/20' : 'hover:bg-slate-800 hover:text-white' }}">
                    <i data-lucide="contact" class="w-5 h-5 flex-shrink-0"></i>
                    <span x-show="sidebarOpen" class="whitespace-nowrap">Supplier Management</span>
                </a>

                <div x-show="sidebarOpen" class="px-3 pt-3 pb-2 text-[10px] font-bold uppercase tracking-wider text-slate-500">Marketing & Growth</div>

                <!-- Coupon Engine -->
                <a href="{{ route('admin.coupons.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium text-sm transition {{ request()->routeIs('admin.coupons.*') ? 'bg-emerald-500 text-slate-950 font-semibold' : 'hover:bg-slate-800 hover:text-white' }}">
                    <i data-lucide="ticket-percent" class="w-5 h-5 flex-shrink-0"></i>
                    <span x-show="sidebarOpen" class="whitespace-nowrap">Coupon & Discounts</span>
                </a>

                <!-- Landing Page Builder -->
                <a href="{{ route('admin.landing-pages.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium text-sm transition {{ request()->routeIs('admin.landing-pages.*') ? 'bg-emerald-500 text-slate-950 font-semibold' : 'hover:bg-slate-800 hover:text-white' }}">
                    <i data-lucide="sparkles" class="w-5 h-5 flex-shrink-0 text-amber-400"></i>
                    <span x-show="sidebarOpen" class="whitespace-nowrap">Landing Page Builder</span>
                </a>

                <!-- Fraud & Risk Check -->
                <a href="{{ route('admin.fraud.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium text-sm transition {{ request()->routeIs('admin.fraud.*') ? 'bg-emerald-500 text-slate-950 font-semibold' : 'hover:bg-slate-800 hover:text-white' }}">
                    <i data-lucide="shield-alert" class="w-5 h-5 flex-shrink-0 text-red-400"></i>
                    <span x-show="sidebarOpen" class="whitespace-nowrap">Fraud & Risk Check</span>
                </a>

                <!-- SMS Marketing -->
                <a href="{{ route('admin.sms.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium text-sm transition {{ request()->routeIs('admin.sms.*') ? 'bg-emerald-500 text-slate-950 font-semibold' : 'hover:bg-slate-800 hover:text-white' }}">
                    <i data-lucide="message-square" class="w-5 h-5 flex-shrink-0 text-sky-400"></i>
                    <span x-show="sidebarOpen" class="whitespace-nowrap">Custom SMS Marketing</span>
                </a>

                <div x-show="sidebarOpen" class="px-3 pt-3 pb-2 text-[10px] font-bold uppercase tracking-wider text-slate-500">Finance & Ledgers</div>

                <!-- Accounts & Funds -->
                <a href="{{ route('admin.accounts.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium text-sm transition {{ request()->routeIs('admin.accounts.*') ? 'bg-emerald-500 text-slate-950 font-semibold' : 'hover:bg-slate-800 hover:text-white' }}">
                    <i data-lucide="wallet" class="w-5 h-5 flex-shrink-0"></i>
                    <span x-show="sidebarOpen" class="whitespace-nowrap">Accounts & Funds</span>
                </a>

                <!-- Expenses & Budgeting -->
                <a href="{{ route('admin.expenses.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium text-sm transition {{ request()->routeIs('admin.expenses.*') ? 'bg-emerald-500 text-slate-950 font-semibold' : 'hover:bg-slate-800 hover:text-white' }}">
                    <i data-lucide="receipt" class="w-5 h-5 flex-shrink-0"></i>
                    <span x-show="sidebarOpen" class="whitespace-nowrap">Expenses & Budgeting</span>
                </a>

                <div x-show="sidebarOpen" class="px-3 pt-3 pb-2 text-[10px] font-bold uppercase tracking-wider text-slate-500">Reports & Intelligence</div>

                <!-- Reports Hub Dropdown -->
                <div x-data="{ open: {{ request()->routeIs('admin.reports.*') ? 'true' : 'false' }} }">
                    <button @click="if (!sidebarOpen) { sidebarOpen = true; open = true; } else { open = !open; }" 
                            class="w-full flex items-center justify-between px-3 py-2.5 rounded-xl font-medium text-sm transition hover:bg-slate-800 hover:text-white {{ request()->routeIs('admin.reports.*') ? 'text-emerald-400 font-semibold bg-slate-800/60' : '' }}"
                            title="Reports (Order, Purchase, Expense, Stock, Profit & Loss)">
                        <div class="flex items-center gap-3">
                            <i data-lucide="bar-chart-3" class="w-5 h-5 flex-shrink-0 text-emerald-400"></i>
                            <span x-show="sidebarOpen" class="whitespace-nowrap font-medium">Reports</span>
                        </div>
                        <div x-show="sidebarOpen" class="flex items-center gap-1.5">
                            <span class="text-[10px] px-1.5 py-0.5 rounded-full bg-emerald-400/20 text-emerald-300 font-bold">5</span>
                            <i data-lucide="chevron-down" :class="{ 'rotate-180': open }" class="w-4 h-4 transition-transform text-slate-400"></i>
                        </div>
                    </button>
                    <div x-show="open && sidebarOpen" class="pl-8 pr-2 py-1 space-y-1 text-xs">
                        <a href="{{ route('admin.reports.orders') }}" class="flex items-center justify-between px-3 py-1.5 rounded-lg {{ (request()->routeIs('admin.reports.orders') || (request()->routeIs('admin.reports.index') && request('type', 'sales') === 'sales')) ? 'text-emerald-400 font-semibold bg-slate-800' : 'hover:text-white text-slate-400' }}">
                            <span>Order Report</span>
                            <i data-lucide="shopping-bag" class="w-3.5 h-3.5 opacity-60"></i>
                        </a>
                        <a href="{{ route('admin.reports.purchases') }}" class="flex items-center justify-between px-3 py-1.5 rounded-lg {{ (request()->routeIs('admin.reports.purchases') || request('type') === 'purchases') ? 'text-emerald-400 font-semibold bg-slate-800' : 'hover:text-white text-slate-400' }}">
                            <span>Purchase Report</span>
                            <i data-lucide="truck" class="w-3.5 h-3.5 opacity-60"></i>
                        </a>
                        <a href="{{ route('admin.reports.expenses') }}" class="flex items-center justify-between px-3 py-1.5 rounded-lg {{ (request()->routeIs('admin.reports.expenses') || request('type') === 'expenses') ? 'text-emerald-400 font-semibold bg-slate-800' : 'hover:text-white text-slate-400' }}">
                            <span>Expense Report</span>
                            <i data-lucide="receipt" class="w-3.5 h-3.5 opacity-60"></i>
                        </a>
                        <a href="{{ route('admin.reports.stock') }}" class="flex items-center justify-between px-3 py-1.5 rounded-lg {{ (request()->routeIs('admin.reports.stock') || request('type') === 'stock') ? 'text-emerald-400 font-semibold bg-slate-800' : 'hover:text-white text-slate-400' }}">
                            <span>Stock Report</span>
                            <i data-lucide="boxes" class="w-3.5 h-3.5 opacity-60"></i>
                        </a>
                        <a href="{{ route('admin.reports.profit_loss') }}" class="flex items-center justify-between px-3 py-1.5 rounded-lg {{ (request()->routeIs('admin.reports.profit_loss') || request('type') === 'profit_loss') ? 'text-emerald-400 font-semibold bg-slate-800' : 'hover:text-white text-slate-400' }}">
                            <span>Profit & Loss</span>
                            <i data-lucide="trending-up" class="w-3.5 h-3.5 text-emerald-400"></i>
                        </a>
                    </div>
                </div>

                <div x-show="sidebarOpen" class="px-3 pt-3 pb-2 text-[10px] font-bold uppercase tracking-wider text-slate-500">Administration & Tools</div>

                <!-- Users & RBAC -->
                <div x-data="{ open: {{ request()->routeIs('admin.users.*', 'admin.roles.*', 'admin.customers.*') ? 'true' : 'false' }} }">
                    <button @click="open = !open" class="w-full flex items-center justify-between px-3 py-2.5 rounded-xl font-medium text-sm transition hover:bg-slate-800 hover:text-white">
                        <div class="flex items-center gap-3">
                            <i data-lucide="users" class="w-5 h-5 flex-shrink-0"></i>
                            <span x-show="sidebarOpen" class="whitespace-nowrap">Users, Roles & CRM</span>
                        </div>
                        <i x-show="sidebarOpen" data-lucide="chevron-down" :class="{ 'rotate-180': open }" class="w-4 h-4 transition-transform"></i>
                    </button>
                    <div x-show="open && sidebarOpen" class="pl-8 pr-2 py-1 space-y-1 text-xs">
                        <a href="{{ route('admin.users.index') }}" class="block px-3 py-1.5 rounded-lg {{ request()->routeIs('admin.users.*') ? 'text-emerald-400 font-semibold' : 'hover:text-white' }}">Admin Users</a>
                        <a href="{{ route('admin.roles.index') }}" class="block px-3 py-1.5 rounded-lg {{ request()->routeIs('admin.roles.*') ? 'text-emerald-400 font-semibold' : 'hover:text-white' }}">Roles & Permissions (RBAC)</a>
                        <a href="{{ route('admin.customers.index') }}" class="block px-3 py-1.5 rounded-lg {{ request()->routeIs('admin.customers.*') ? 'text-emerald-400 font-semibold' : 'hover:text-white' }}">Customers Directory</a>
                    </div>
                </div>

                <!-- General Settings -->
                <a href="{{ route('admin.settings.general') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium text-sm transition {{ request()->routeIs('admin.settings.general') ? 'bg-emerald-500 text-slate-950 font-semibold' : 'hover:bg-slate-800 hover:text-white' }}">
                    <i data-lucide="sliders" class="w-5 h-5 flex-shrink-0"></i>
                    <span x-show="sidebarOpen" class="whitespace-nowrap">General Settings</span>
                </a>

                <!-- Email Configuration -->
                <a href="{{ route('admin.settings.email') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium text-sm transition {{ request()->routeIs('admin.settings.email') ? 'bg-emerald-500 text-slate-950 font-semibold' : 'hover:bg-slate-800 hover:text-white' }}">
                    <i data-lucide="mail" class="w-5 h-5 flex-shrink-0"></i>
                    <span x-show="sidebarOpen" class="whitespace-nowrap">Email Configuration</span>
                </a>

                <!-- Fraud API Manager -->
                <a href="{{ route('admin.settings.fraud') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium text-sm transition {{ request()->routeIs('admin.settings.fraud') ? 'bg-emerald-500 text-slate-950 font-semibold' : 'hover:bg-slate-800 hover:text-white' }}">
                    <i data-lucide="shield-check" class="w-5 h-5 flex-shrink-0"></i>
                    <span x-show="sidebarOpen" class="whitespace-nowrap">Fraud API Manager</span>
                </a>

                <!-- Third-Party API Hub -->
                <a href="{{ route('admin.settings.api_hub') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium text-sm transition {{ request()->routeIs('admin.settings.api_hub') ? 'bg-emerald-500 text-slate-950 font-semibold' : 'hover:bg-slate-800 hover:text-white' }}">
                    <i data-lucide="plug-zap" class="w-5 h-5 flex-shrink-0 text-emerald-400"></i>
                    <span x-show="sidebarOpen" class="whitespace-nowrap">Third-Party API Hub</span>
                </a>

                <!-- Banners & Advertising -->
                <a href="{{ route('admin.banners.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium text-sm transition {{ request()->routeIs('admin.banners.*') ? 'bg-emerald-500 text-slate-950 font-semibold' : 'hover:bg-slate-800 hover:text-white' }}">
                    <i data-lucide="image" class="w-5 h-5 flex-shrink-0"></i>
                    <span x-show="sidebarOpen" class="whitespace-nowrap">Banners & Advertising</span>
                </a>

                <!-- SEO Settings -->
                <a href="{{ route('admin.settings.seo') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium text-sm transition {{ request()->routeIs('admin.settings.seo') ? 'bg-emerald-500 text-slate-950 font-semibold' : 'hover:bg-slate-800 hover:text-white' }}">
                    <i data-lucide="globe" class="w-5 h-5 flex-shrink-0"></i>
                    <span x-show="sidebarOpen" class="whitespace-nowrap">SEO Settings</span>
                </a>

                <!-- Sitemap Settings -->
                <a href="{{ route('admin.settings.sitemap') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium text-sm transition {{ request()->routeIs('admin.settings.sitemap') ? 'bg-emerald-500 text-slate-950 font-semibold' : 'hover:bg-slate-800 hover:text-white' }}">
                    <i data-lucide="network" class="w-5 h-5 flex-shrink-0"></i>
                    <span x-show="sidebarOpen" class="whitespace-nowrap">Sitemap Settings</span>
                </a>

                <!-- System Tools & Cache -->
                <a href="{{ route('admin.system.tools') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium text-sm transition {{ request()->routeIs('admin.system.tools') ? 'bg-emerald-500 text-slate-950 font-semibold' : 'hover:bg-slate-800 hover:text-white' }}">
                    <i data-lucide="wrench" class="w-5 h-5 flex-shrink-0"></i>
                    <span x-show="sidebarOpen" class="whitespace-nowrap">System Tools & Cache</span>
                </a>

                <!-- Footer & CMS Info -->
                <a href="{{ route('admin.settings.footer') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium text-sm transition {{ request()->routeIs('admin.settings.footer*') ? 'bg-emerald-500 text-slate-950 font-semibold' : 'hover:bg-slate-800 hover:text-white' }}">
                    <i data-lucide="layout" class="w-5 h-5 flex-shrink-0 text-amber-400"></i>
                    <span x-show="sidebarOpen" class="whitespace-nowrap">Footer Info & CMS</span>
                </a>

            </div>

            <!-- Sidebar Footer User Status & Logout -->
            <div class="p-3 border-t border-slate-800 bg-slate-950/80 flex items-center justify-between">
                <div class="flex items-center gap-3 overflow-hidden">
                    <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-emerald-500 to-teal-400 flex items-center justify-center text-slate-950 font-black shadow-md flex-shrink-0">
                        {{ strtoupper(substr(Auth::guard('web')->user()->name ?? 'A', 0, 1)) }}
                    </div>
                    <div x-show="sidebarOpen" class="text-xs truncate">
                        <div class="font-bold text-white truncate">{{ Auth::guard('web')->user()->name ?? 'Admin User' }}</div>
                        <div class="text-[10px] text-emerald-400 font-mono">{{ Auth::guard('web')->user()->roles->first()->name ?? 'Super Admin' }}</div>
                    </div>
                </div>
                <div x-show="sidebarOpen">
                    <form method="POST" action="{{ route('admin.logout') }}">
                        @csrf
                        <button type="submit" title="Logout from Admin" class="p-1.5 rounded-lg text-slate-400 hover:text-rose-400 hover:bg-rose-500/10 transition">
                            <i data-lucide="log-out" class="w-4 h-4"></i>
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <!-- ================= MAIN CONTENT WRAPPER ================= -->
        <div class="flex-1 flex flex-col min-w-0 overflow-hidden">

            <!-- Top Navigation Bar -->
            <header class="h-16 bg-white dark:bg-slate-900 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between px-4 lg:px-8 z-20 shadow-sm">
                
                <div class="flex items-center gap-4">
                    <!-- Mobile Hamburger Toggle -->
                    <button @click="mobileSidebarOpen = !mobileSidebarOpen" class="lg:hidden p-2 rounded-xl text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800">
                        <i data-lucide="menu" class="w-6 h-6"></i>
                    </button>

                    <!-- Page Breadcrumb / Title -->
                    <div class="hidden sm:block">
                        <h1 class="text-lg font-bold text-slate-900 dark:text-white flex items-center gap-2">
                            @yield('page-title', 'DREAMERS PCB Admin')
                        </h1>
                    </div>
                </div>

                <!-- Right Actions -->
                <div class="flex items-center gap-2.5 sm:gap-3">

                    <!-- Quick Reports Menu Dropdown -->
                    <div x-data="{ reportsDropdown: false }" class="relative">
                        <button @click="reportsDropdown = !reportsDropdown" @click.away="reportsDropdown = false" 
                                class="inline-flex items-center gap-1.5 sm:gap-2 px-2.5 sm:px-3 py-1.5 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 font-semibold text-xs border border-slate-200 dark:border-slate-700 transition"
                                title="Reports Menu">
                            <i data-lucide="bar-chart-3" class="w-4 h-4 text-emerald-500"></i>
                            <span class="hidden xs:inline sm:inline">Reports</span>
                            <i data-lucide="chevron-down" :class="{ 'rotate-180': reportsDropdown }" class="w-3.5 h-3.5 transition-transform text-slate-400"></i>
                        </button>
                        <div x-show="reportsDropdown" 
                             x-transition:enter="transition ease-out duration-150"
                             x-transition:enter-start="opacity-0 scale-95"
                             x-transition:enter-end="opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-100"
                             x-transition:leave-start="opacity-100 scale-100"
                             x-transition:leave-end="opacity-0 scale-95"
                             class="absolute right-0 mt-2 w-56 bg-white dark:bg-slate-900 rounded-2xl shadow-xl border border-slate-200 dark:border-slate-800 py-1.5 z-50 text-xs">
                            <div class="px-3.5 py-1.5 text-[10px] font-bold uppercase tracking-wider text-slate-400 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
                                <span>Reports Menu</span>
                                <span class="px-1.5 py-0.5 rounded bg-emerald-500/10 text-emerald-500 font-bold text-[9px]">5 Hubs</span>
                            </div>
                            <a href="{{ route('admin.reports.orders') }}" class="flex items-center justify-between px-3.5 py-2 hover:bg-slate-50 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-200 transition">
                                <span class="font-medium">Order Report</span>
                                <i data-lucide="shopping-bag" class="w-3.5 h-3.5 text-emerald-500"></i>
                            </a>
                            <a href="{{ route('admin.reports.purchases') }}" class="flex items-center justify-between px-3.5 py-2 hover:bg-slate-50 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-200 transition">
                                <span class="font-medium">Purchase Report</span>
                                <i data-lucide="truck" class="w-3.5 h-3.5 text-sky-500"></i>
                            </a>
                            <a href="{{ route('admin.reports.expenses') }}" class="flex items-center justify-between px-3.5 py-2 hover:bg-slate-50 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-200 transition">
                                <span class="font-medium">Expense Report</span>
                                <i data-lucide="receipt" class="w-3.5 h-3.5 text-rose-500"></i>
                            </a>
                            <a href="{{ route('admin.reports.stock') }}" class="flex items-center justify-between px-3.5 py-2 hover:bg-slate-50 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-200 transition">
                                <span class="font-medium">Stock Report</span>
                                <i data-lucide="boxes" class="w-3.5 h-3.5 text-amber-500"></i>
                            </a>
                            <a href="{{ route('admin.reports.profit_loss') }}" class="flex items-center justify-between px-3.5 py-2 hover:bg-slate-50 dark:hover:bg-slate-800 text-emerald-600 dark:text-emerald-400 font-semibold border-t border-slate-100 dark:border-slate-800 transition">
                                <span>Profit & Loss</span>
                                <i data-lucide="trending-up" class="w-3.5 h-3.5"></i>
                            </a>
                        </div>
                    </div>

                    <!-- Quick POS Button -->
                    <a href="{{ route('admin.pos.index') }}" class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-semibold text-xs shadow-md shadow-emerald-600/20 transition">
                        <i data-lucide="scan" class="w-4 h-4"></i>
                        <span>POS Counter</span>
                    </a>

                    <!-- Available Fund Badge -->
                    @php
                        $fundBalance = \App\Models\Account::where('is_active', true)->sum('current_balance');
                    @endphp
                    <div class="hidden md:flex items-center gap-2 px-3 py-1.5 rounded-xl bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs">
                        <span class="text-slate-500 dark:text-slate-400">তহবিল ব্যালেন্স:</span>
                        <span class="font-bold text-emerald-600 dark:text-emerald-400 code-font">৳{{ number_format($fundBalance, 2) }}</span>
                    </div>

                    <!-- Dark / Light Toggle -->
                    <button @click="darkMode = !darkMode" class="p-2 rounded-xl text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition" title="Toggle Theme">
                        <i data-lucide="sun" x-show="darkMode" class="w-5 h-5 text-amber-400"></i>
                        <i data-lucide="moon" x-show="!darkMode" class="w-5 h-5 text-slate-600"></i>
                    </button>

                    <!-- View Live Store -->
                    <a href="{{ route('home') }}" target="_blank" class="p-2 rounded-xl text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition" title="Preview Public Storefront">
                        <i data-lucide="external-link" class="w-5 h-5"></i>
                    </a>

                    <!-- Notification Bell Dropdown -->
                    <div x-data="adminNotificationDropdown()" x-init="init()" class="relative">
                        <button 
                            @click="toggleDropdown()" 
                            class="relative p-2 rounded-xl text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition" 
                            title="Notifications & Live Updates">
                            <i data-lucide="bell" class="w-5 h-5"></i>
                            <template x-if="unreadCount > 0">
                                <span class="absolute top-1 right-1 flex h-4 min-w-[16px] px-1 items-center justify-center pointer-events-none">
                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-rose-400 opacity-75"></span>
                                    <span class="relative inline-flex rounded-full h-4 min-w-[16px] px-1 bg-rose-500 text-white text-[10px] font-black items-center justify-center" x-text="unreadCount > 99 ? '99+' : unreadCount"></span>
                                </span>
                            </template>
                        </button>

                        <!-- Dropdown Panel -->
                        <div 
                            x-show="open" 
                            @click.away="open = false" 
                            x-cloak
                            x-transition:enter="transition ease-out duration-150"
                            x-transition:enter-start="opacity-0 scale-95"
                            x-transition:enter-end="opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-100"
                            x-transition:leave-start="opacity-100 scale-100"
                            x-transition:leave-end="opacity-0 scale-95"
                            class="absolute right-0 mt-2 w-80 sm:w-96 bg-white dark:bg-slate-900 rounded-2xl shadow-2xl border border-slate-200 dark:border-slate-800 py-2 z-50 text-xs overflow-hidden">
                            
                            <!-- Header -->
                            <div class="px-4 py-2.5 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <span class="font-bold text-slate-900 dark:text-white text-sm">Notifications</span>
                                    <template x-if="unreadCount > 0">
                                        <span class="px-2 py-0.5 rounded-full bg-rose-500/10 text-rose-500 font-bold text-[10px]" x-text="unreadCount + ' new'"></span>
                                    </template>
                                </div>
                                <div class="flex items-center gap-2">
                                    <template x-if="unreadCount > 0">
                                        <button @click="markAllAsRead()" class="text-[11px] font-semibold text-emerald-600 dark:text-emerald-400 hover:underline">
                                            Mark all read
                                        </button>
                                    </template>
                                    <button @click="fetchLatest()" title="Refresh" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                                        <i data-lucide="refresh-cw" class="w-3.5 h-3.5" :class="{ 'animate-spin': loading }"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- Notifications Feed List -->
                            <div class="max-h-80 overflow-y-auto divide-y divide-slate-100 dark:divide-slate-800/60">
                                <template x-if="loading && items.length === 0">
                                    <div class="py-8 text-center text-slate-400">
                                        <i data-lucide="loader-2" class="w-5 h-5 mx-auto animate-spin mb-1"></i>
                                        <span>Loading updates...</span>
                                    </div>
                                </template>

                                <template x-if="!loading && items.length === 0">
                                    <div class="py-8 text-center text-slate-400 px-4">
                                        <i data-lucide="bell-off" class="w-7 h-7 mx-auto mb-1.5 opacity-40"></i>
                                        <div class="font-medium text-slate-600 dark:text-slate-300">No new notifications</div>
                                        <div class="text-[11px] text-slate-400 mt-0.5">New orders, courier handovers, and delivery updates will appear here</div>
                                    </div>
                                </template>

                                <template x-for="item in items" :key="item.id">
                                    <div 
                                        @click="handleItemClick(item)" 
                                        :class="{ 'bg-emerald-50/40 dark:bg-emerald-950/20': !item.read }"
                                        class="p-3.5 hover:bg-slate-50 dark:hover:bg-slate-800/50 transition cursor-pointer flex items-start gap-3 relative group">
                                        
                                        <!-- Icon -->
                                        <div 
                                            :class="getIconBgClass(item.type)"
                                            class="w-8 h-8 rounded-xl flex items-center justify-center flex-shrink-0 mt-0.5">
                                            <i :data-lucide="item.icon || 'bell'" class="w-4 h-4"></i>
                                        </div>

                                        <!-- Content -->
                                        <div class="flex-1 min-w-0 pr-2">
                                            <div class="flex items-center justify-between gap-1 mb-0.5">
                                                <div class="font-bold text-slate-900 dark:text-white truncate text-xs" x-text="item.title"></div>
                                                <span class="text-[10px] text-slate-400 whitespace-nowrap" x-text="item.time_ago"></span>
                                            </div>
                                            <p class="text-[11px] text-slate-600 dark:text-slate-300 line-clamp-2 leading-relaxed" x-text="item.message"></p>
                                        </div>

                                        <!-- Unread Dot -->
                                        <template x-if="!item.read">
                                            <span class="w-2 h-2 rounded-full bg-emerald-500 self-center flex-shrink-0"></span>
                                        </template>
                                    </div>
                                </template>
                            </div>

                            <!-- Footer -->
                            <div class="p-2 border-t border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-900/50 text-center">
                                <a href="{{ route('admin.notifications.index') }}" class="block py-1 text-xs font-semibold text-emerald-600 dark:text-emerald-400 hover:text-emerald-500 transition">
                                    View All Notifications & Activity Log &rarr;
                                </a>
                            </div>

                        </div>
                    </div>

                    <!-- User Account Dropdown -->
                    <div x-data="{ userMenuOpen: false }" class="relative">
                        <button @click="userMenuOpen = !userMenuOpen" class="flex items-center gap-2 pl-2 pr-3 py-1.5 rounded-xl border border-slate-200 dark:border-slate-700 hover:bg-slate-100 dark:hover:bg-slate-800 transition text-xs">
                            <div class="w-7 h-7 rounded-lg bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 font-black flex items-center justify-center text-xs">
                                {{ strtoupper(substr(Auth::guard('web')->user()->name ?? 'A', 0, 1)) }}
                            </div>
                            <div class="text-left hidden md:block leading-tight">
                                <div class="font-bold text-slate-800 dark:text-slate-200 text-xs">{{ Auth::guard('web')->user()->name ?? 'Admin' }}</div>
                                <div class="text-[10px] text-emerald-600 dark:text-emerald-400 font-semibold">{{ Auth::guard('web')->user()->roles->first()->name ?? 'Super Admin' }}</div>
                            </div>
                            <i data-lucide="chevron-down" class="w-3.5 h-3.5 text-slate-400"></i>
                        </button>

                        <div 
                            x-show="userMenuOpen" 
                            @click.away="userMenuOpen = false" 
                            x-cloak 
                            class="absolute right-0 mt-2 w-56 bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-xl py-2 z-50 text-xs space-y-1">
                            <div class="px-3 py-2 border-b border-slate-100 dark:border-slate-800">
                                <p class="font-bold text-slate-900 dark:text-white truncate">{{ Auth::guard('web')->user()->name ?? 'Administrator' }}</p>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 truncate">{{ Auth::guard('web')->user()->email ?? 'admin@dreamerspcb.com' }}</p>
                            </div>

                            <a href="{{ route('admin.users.index') }}" class="flex items-center gap-2 px-3 py-2 text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 font-medium">
                                <i data-lucide="users" class="w-4 h-4 text-emerald-500"></i>
                                <span>Manage Admin Users</span>
                            </a>
                            <a href="{{ route('admin.settings.general') }}" class="flex items-center gap-2 px-3 py-2 text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 font-medium">
                                <i data-lucide="settings" class="w-4 h-4 text-slate-400"></i>
                                <span>System Settings</span>
                            </a>
                            <a href="{{ route('home') }}" target="_blank" class="flex items-center gap-2 px-3 py-2 text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 font-medium">
                                <i data-lucide="store" class="w-4 h-4 text-amber-500"></i>
                                <span>Visit Online Store</span>
                            </a>

                            <div class="pt-1 border-t border-slate-100 dark:border-slate-800">
                                <form method="POST" action="{{ route('admin.logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full flex items-center gap-2 px-3 py-2 text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950/40 font-bold text-left transition">
                                        <i data-lucide="log-out" class="w-4 h-4"></i>
                                        <span>Sign Out</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

            </header>

            <!-- Notification Alerts -->
            @if(session('success'))
            <div class="bg-emerald-500 text-white px-6 py-3 text-sm font-medium flex items-center justify-between shadow-md">
                <div class="flex items-center gap-2">
                    <i data-lucide="check-circle" class="w-5 h-5"></i>
                    <span>{{ session('success') }}</span>
                </div>
                <button onclick="this.parentElement.remove()" class="text-white/80 hover:text-white">&times;</button>
            </div>
            @endif

            @if(session('error'))
            <div class="bg-rose-600 text-white px-6 py-3 text-sm font-medium flex items-center justify-between shadow-md">
                <div class="flex items-center gap-2">
                    <i data-lucide="alert-triangle" class="w-5 h-5"></i>
                    <span>{{ session('error') }}</span>
                </div>
                <button onclick="this.parentElement.remove()" class="text-white/80 hover:text-white">&times;</button>
            </div>
            @endif

            <!-- Main Page Scrollable Body -->
            <main class="flex-1 overflow-y-auto p-4 lg:p-8 bg-slate-100 dark:bg-slate-950">
                @yield('content')
            </main>

        </div>

    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            lucide.createIcons();
        });

        function adminNotificationDropdown() {
            return {
                open: false,
                unreadCount: {{ (function() {
                    try {
                        return \Illuminate\Support\Facades\Schema::hasTable('notifications') ? (Auth::guard('web')->user()?->unreadNotifications()->count() ?? 0) : 0;
                    } catch (\Throwable $e) {
                        return 0;
                    }
                })() }},
                items: [],
                loading: false,
                pollTimer: null,
                csrfToken: '{{ csrf_token() }}',

                init() {
                    this.fetchLatest();
                    // Live polling every 30 seconds
                    this.pollTimer = setInterval(() => {
                        this.fetchLatest(true);
                    }, 30000);
                },

                toggleDropdown() {
                    this.open = !this.open;
                    if (this.open) {
                        this.fetchLatest();
                    }
                },

                async fetchLatest(silent = false) {
                    if (!silent) this.loading = true;
                    try {
                        const res = await fetch('{{ route('admin.notifications.latest') }}', {
                            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                        });
                        if (res.ok) {
                            const data = await res.json();
                            if (data.success) {
                                const prevCount = this.unreadCount;
                                this.unreadCount = data.unread_count;
                                this.items = data.notifications;
                                this.$nextTick(() => {
                                    if (window.lucide) {
                                        lucide.createIcons();
                                    }
                                });
                                if (silent && data.unread_count > prevCount) {
                                    this.playChime();
                                }
                            }
                        }
                    } catch (e) {
                        console.error('Notification sync error:', e);
                    } finally {
                        if (!silent) this.loading = false;
                    }
                },

                async markAllAsRead() {
                    try {
                        const res = await fetch('{{ route('admin.notifications.mark_all_read') }}', {
                            method: 'POST',
                            headers: {
                                'Accept': 'application/json',
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': this.csrfToken,
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });
                        if (res.ok) {
                            this.unreadCount = 0;
                            this.items.forEach(i => i.read = true);
                        }
                    } catch (e) {
                        console.error('Mark all read error:', e);
                    }
                },

                async handleItemClick(item) {
                    if (!item.read) {
                        fetch(`{{ url('admin/notifications') }}/${item.id}/read`, {
                            method: 'POST',
                            headers: {
                                'Accept': 'application/json',
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': this.csrfToken,
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        }).catch(() => {});
                        item.read = true;
                        if (this.unreadCount > 0) this.unreadCount--;
                    }
                    if (item.action_url) {
                        window.location.href = item.action_url;
                    }
                },

                getIconBgClass(type) {
                    switch (type) {
                        case 'new_order':
                            return 'bg-emerald-500/15 text-emerald-500';
                        case 'courier_assigned':
                        case 'in_courier':
                            return 'bg-sky-500/15 text-sky-500';
                        case 'delivery_done':
                            return 'bg-teal-500/15 text-teal-400';
                        case 'order_cancelled':
                            return 'bg-rose-500/15 text-rose-500';
                        case 'order_returned':
                            return 'bg-amber-500/15 text-amber-500';
                        default:
                            return 'bg-emerald-500/15 text-emerald-500';
                    }
                },

                playChime() {
                    try {
                        const ctx = new (window.AudioContext || window.webkitAudioContext)();
                        const osc = ctx.createOscillator();
                        const gain = ctx.createGain();
                        osc.type = 'sine';
                        osc.frequency.setValueAtTime(587.33, ctx.currentTime);
                        osc.frequency.exponentialRampToValueAtTime(880, ctx.currentTime + 0.15);
                        gain.gain.setValueAtTime(0.08, ctx.currentTime);
                        gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.3);
                        osc.connect(gain);
                        gain.connect(ctx.destination);
                        osc.start();
                        osc.stop(ctx.currentTime + 0.3);
                    } catch (e) {
                        // Audio unavailable
                    }
                }
            };
        }
    </script>
    @stack('scripts')
</body>
</html>
