@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-6 sm:py-8 space-y-6">

    <!-- Customer Dashboard Top Breadcrumb & Welcome Banner -->
    <div class="bg-gradient-to-r from-slate-900 via-slate-800 to-slate-900 rounded-3xl p-6 sm:p-8 text-white shadow-md flex flex-col md:flex-row items-start md:items-center justify-between gap-4 border border-slate-700">
        <div class="flex items-center gap-4">
            <div class="w-16 h-16 rounded-2xl bg-gradient-to-tr from-daraz-orange to-amber-500 text-white font-black text-2xl flex items-center justify-center shadow-lg">
                {{ substr(Auth::guard('customer')->user()->name, 0, 1) }}
            </div>
            <div>
                <div class="flex items-center gap-2">
                    <h1 class="text-xl sm:text-2xl font-black">{{ Auth::guard('customer')->user()->name }}</h1>
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
                        Maker Tier
                    </span>
                </div>
                <div class="text-xs text-slate-300 flex items-center gap-4 mt-1 font-mono">
                    <span>📱 {{ Auth::guard('customer')->user()->phone }}</span>
                    @if(Auth::guard('customer')->user()->email)
                    <span class="hidden sm:inline">✉️ {{ Auth::guard('customer')->user()->email }}</span>
                    @endif
                </div>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <div class="p-3 rounded-2xl bg-white/10 backdrop-blur-sm border border-white/10 text-right">
                <span class="text-[10px] text-slate-300 uppercase tracking-wider block">Loyalty Points</span>
                <span class="text-base font-black text-amber-400 font-mono">🪙 {{ Auth::guard('customer')->user()->loyalty_points }} pts</span>
            </div>
            
            <form method="POST" action="{{ route('customer.logout') }}">
                @csrf
                <button type="submit" class="p-3 rounded-2xl bg-rose-600/80 hover:bg-rose-600 text-white font-bold text-xs flex items-center gap-1.5 transition">
                    <i data-lucide="log-out" class="w-4 h-4"></i>
                    <span class="hidden sm:inline">Logout</span>
                </button>
            </form>
        </div>
    </div>

    <!-- 2 Columns: Left Navigation Sidebar + Right Main Section -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        
        <!-- Left Sidebar Navigation -->
        <div class="lg:col-span-3 bg-white rounded-3xl p-4 border border-slate-200 shadow-sm space-y-2 text-xs">
            <div class="font-bold text-slate-400 uppercase tracking-wider text-[10px] px-3 py-1">Customer Menu</div>
            
            <a href="{{ route('customer.dashboard') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl font-bold transition {{ request()->routeIs('customer.dashboard') ? 'bg-daraz-orange text-white shadow-md' : 'text-slate-700 hover:bg-slate-50' }}">
                <i data-lucide="layout-dashboard" class="w-4 h-4"></i>
                <span>Dashboard Overview</span>
            </a>

            <a href="{{ route('customer.orders') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl font-bold transition {{ request()->routeIs('customer.orders*') ? 'bg-daraz-orange text-white shadow-md' : 'text-slate-700 hover:bg-slate-50' }}">
                <i data-lucide="package" class="w-4 h-4"></i>
                <span>My Orders History</span>
            </a>

            <a href="{{ route('customer.warranties') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl font-bold transition {{ request()->routeIs('customer.warranties*') ? 'bg-daraz-orange text-white shadow-md' : 'text-slate-700 hover:bg-slate-50' }}">
                <i data-lucide="shield-check" class="w-4 h-4"></i>
                <span>Warranty Verification</span>
            </a>

            <a href="{{ route('customer.wishlist') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl font-bold transition {{ request()->routeIs('customer.wishlist') ? 'bg-daraz-orange text-white shadow-md' : 'text-slate-700 hover:bg-slate-50' }}">
                <i data-lucide="heart" class="w-4 h-4"></i>
                <span>My Wishlist & Saved</span>
            </a>

            <a href="{{ route('customer.profile') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl font-bold transition {{ request()->routeIs('customer.profile') ? 'bg-daraz-orange text-white shadow-md' : 'text-slate-700 hover:bg-slate-50' }}">
                <i data-lucide="user" class="w-4 h-4"></i>
                <span>Profile & Addresses</span>
            </a>

            <div class="pt-2 border-t border-slate-100">
                <a href="{{ route('order.track') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl font-bold text-slate-700 hover:bg-slate-50 transition">
                    <i data-lucide="map-pin" class="w-4 h-4 text-daraz-orange"></i>
                    <span>Live Order Tracker</span>
                </a>
            </div>
        </div>

        <!-- Right Main Customer Portal Body -->
        <div class="lg:col-span-9 space-y-6">
            @yield('customer_content')
        </div>

    </div>

</div>
@endsection
