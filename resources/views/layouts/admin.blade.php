<!DOCTYPE html>
<html lang="en" x-data="{ darkMode: false, sidebarOpen: true, mobileSidebarOpen: false }" :class="{ 'dark': darkMode }">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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

                <!-- Analytics & Reports -->
                <a href="{{ route('admin.reports.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium text-sm transition {{ request()->routeIs('admin.reports.*') ? 'bg-emerald-500 text-slate-950 font-semibold' : 'hover:bg-slate-800 hover:text-white' }}">
                    <i data-lucide="bar-chart-3" class="w-5 h-5 flex-shrink-0"></i>
                    <span x-show="sidebarOpen" class="whitespace-nowrap">Analytics & Reports</span>
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
                <div class="flex items-center gap-3">

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
    </script>
    @stack('scripts')
</body>
</html>
