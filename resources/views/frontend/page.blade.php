@extends('layouts.app')

@section('title', $page->meta_title ?: ($page->title . ' | ' . \App\Models\Setting::get('site_name', 'DREAMERS PCB')))
@section('meta_description', $page->meta_description ?: \Illuminate\Support\Str::limit(strip_tags($page->content), 160))
@section('meta_keywords', $page->meta_keywords ?: ('electronics, pcb, ' . strtolower($page->title)))

@section('content')
<div class="max-w-6xl mx-auto px-4 py-8 space-y-6">

    <!-- Breadcrumb Navigation -->
    <nav class="flex items-center gap-2 text-xs text-slate-500 font-medium">
        <a href="{{ route('home') }}" class="hover:text-daraz-orange transition flex items-center gap-1">
            <i data-lucide="home" class="w-3.5 h-3.5"></i>
            <span>Home</span>
        </a>
        <span class="text-slate-300">/</span>
        <span class="text-slate-400">Policies & Information</span>
        <span class="text-slate-300">/</span>
        <span class="text-slate-900 font-bold truncate">{{ $page->title }}</span>
    </nav>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

        <!-- Main Content Area -->
        <main class="lg:col-span-8 bg-white rounded-2xl border border-slate-200 p-6 sm:p-10 shadow-xs space-y-6">
            
            <!-- Page Header -->
            <div class="border-b border-slate-100 pb-6">
                <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-emerald-500/10 text-emerald-600 text-xs font-bold mb-3">
                    <i data-lucide="shield-check" class="w-3.5 h-3.5"></i>
                    <span>Official DREAMERS PCB Policy</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">
                    {{ $page->title }}
                </h1>
                <div class="flex items-center gap-4 mt-3 text-xs text-slate-400">
                    <span class="flex items-center gap-1">
                        <i data-lucide="clock" class="w-3.5 h-3.5"></i>
                        <span>Last updated: {{ $page->updated_at->format('F d, Y') }}</span>
                    </span>
                    <span>•</span>
                    <span class="flex items-center gap-1 text-emerald-600 font-semibold">
                        <i data-lucide="check" class="w-3.5 h-3.5"></i>
                        <span>Verified & Effective</span>
                    </span>
                </div>
            </div>

            <!-- Page Body Content -->
            <div class="prose prose-slate max-w-none text-slate-700 text-sm leading-relaxed space-y-4 prose-headings:font-black prose-headings:text-slate-900 prose-h2:text-lg prose-h2:mt-6 prose-h2:mb-3 prose-h3:text-base prose-h3:mt-4 prose-ul:list-disc prose-ul:pl-5 prose-ol:list-decimal prose-ol:pl-5 prose-li:my-1 prose-strong:text-slate-900 prose-table:w-full prose-table:border prose-th:bg-slate-50 prose-th:p-2 prose-td:p-2 prose-td:border">
                {!! $page->content !!}
            </div>

            <!-- Page Footer Notice -->
            <div class="mt-8 pt-6 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-slate-500">
                <p>Have questions regarding this policy? Contact our customer support team.</p>
                <a href="tel:{{ \App\Models\Setting::get('site_phone', '+8801700112233') }}" class="px-3.5 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold transition flex items-center gap-1.5 shrink-0">
                    <i data-lucide="phone" class="w-3.5 h-3.5 text-daraz-orange"></i>
                    <span>Call Support</span>
                </a>
            </div>

        </main>

        <!-- Sidebar (Quick Navigation & Support Cards) -->
        <aside class="lg:col-span-4 space-y-6">

            <!-- All Policies Quick Links -->
            @php
                $relatedPages = \App\Models\Page::active()
                    ->where('id', '!=', $page->id)
                    ->orderBy('sort_order', 'asc')
                    ->take(6)
                    ->get();
            @endphp

            @if($relatedPages->isNotEmpty())
            <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-xs">
                <h3 class="font-bold text-xs uppercase tracking-wider text-slate-900 border-b border-slate-100 pb-3 mb-3 flex items-center gap-2">
                    <i data-lucide="book-open" class="w-4 h-4 text-daraz-orange"></i>
                    <span>Other Policies & Guidelines</span>
                </h3>
                <div class="space-y-1 text-xs font-medium">
                    @foreach($relatedPages as $relPage)
                    <a href="{{ route('page.show', $relPage->slug) }}" class="flex items-center justify-between p-2.5 rounded-xl text-slate-700 hover:bg-slate-50 hover:text-daraz-orange transition group">
                        <span>{{ $relPage->title }}</span>
                        <i data-lucide="chevron-right" class="w-3.5 h-3.5 text-slate-400 group-hover:text-daraz-orange group-hover:translate-x-0.5 transition"></i>
                    </a>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Customer Care Help Box -->
            <div class="bg-gradient-to-br from-slate-900 via-slate-950 to-emerald-950 rounded-2xl p-6 text-white space-y-4 shadow-md">
                <div class="w-10 h-10 rounded-xl bg-emerald-500/20 text-emerald-400 flex items-center justify-center font-bold">
                    <i data-lucide="headphones" class="w-5 h-5"></i>
                </div>
                <div>
                    <h4 class="font-bold text-sm text-white">Need Further Assistance?</h4>
                    <p class="text-xs text-slate-400 mt-1 leading-relaxed">
                        Our electronics engineers and customer support specialists are available daily from 9:00 AM to 9:00 PM.
                    </p>
                </div>
                <div class="space-y-2 text-xs pt-1 border-t border-slate-800/80">
                    <div class="flex items-center gap-2 text-slate-300">
                        <i data-lucide="phone-call" class="w-3.5 h-3.5 text-emerald-400"></i>
                        <span>Hotline: <strong class="text-white font-mono">{{ \App\Models\Setting::get('site_phone', '+880 1700-112233') }}</strong></span>
                    </div>
                    <div class="flex items-center gap-2 text-slate-300">
                        <i data-lucide="mail" class="w-3.5 h-3.5 text-emerald-400"></i>
                        <span>Email: <strong class="text-white">{{ \App\Models\Setting::get('site_email', 'support@dreamerspcb.com') }}</strong></span>
                    </div>
                </div>
                <div class="pt-2">
                    <a href="{{ route('order.track') }}" class="w-full py-2.5 rounded-xl bg-daraz-orange hover:bg-daraz-orangeHover text-white font-bold text-xs flex items-center justify-center gap-1.5 shadow-sm transition">
                        <i data-lucide="truck" class="w-4 h-4"></i>
                        <span>Track My Order</span>
                    </a>
                </div>
            </div>

        </aside>

    </div>

</div>
@endsection
