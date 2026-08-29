@extends('layouts.admin')

@section('title', 'Landing Page & Campaign Builder')
@section('page-title', 'Single-Product Flash Sales Landing Pages')

@section('content')
<div class="space-y-6">

    <!-- Header Toolbar -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 border border-slate-200 dark:border-slate-800 shadow-sm flex flex-col sm:flex-row items-center justify-between gap-4">
        <div>
            <h2 class="text-base font-bold text-slate-900 dark:text-white flex items-center gap-2">
                <i data-lucide="sparkles" class="w-5 h-5 text-amber-400"></i>
                <span>High-Converting Single-Product Landing Pages</span>
            </h2>
            <p class="text-xs text-slate-500">Optimized for Facebook Ads campaigns, direct COD checkouts, and high conversion rates</p>
        </div>

        <a href="{{ route('admin.landing-pages.create') }}" class="px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs shadow-md shadow-emerald-600/20 transition flex items-center gap-1.5">
            <i data-lucide="plus" class="w-4 h-4"></i>
            <span>+ Build New Landing Page</span>
        </a>
    </div>

    <!-- Landing Pages Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
        @forelse($landingPages as $page)
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden flex flex-col justify-between hover:border-emerald-500/50 transition">
            <div>
                <div class="relative aspect-video bg-slate-100 dark:bg-slate-800">
                    <img src="{{ $page->product->thumbnail ?? 'https://images.unsplash.com/photo-1518770660439-4636190af475?w=500' }}" alt="{{ $page->title }}" class="w-full h-full object-cover">
                    <div class="absolute inset-0 bg-gradient-to-t from-slate-950/80 via-transparent to-transparent"></div>
                    <div class="absolute bottom-3 left-3 right-3 text-white">
                        <h3 class="text-sm font-bold line-clamp-1">{{ $page->title }}</h3>
                        <div class="text-[11px] text-emerald-400 font-mono">/offer/{{ $page->slug }}</div>
                    </div>
                </div>

                <div class="p-5 space-y-3">
                    <p class="text-xs text-slate-500 line-clamp-2">{{ $page->headline ?? 'Exclusive Gadget Flash Sale Promo' }}</p>

                    <div class="grid grid-cols-2 gap-2 p-3 rounded-xl bg-slate-50 dark:bg-slate-800/40 text-xs">
                        <div>
                            <span class="text-slate-400">Total Views:</span>
                            <div class="font-bold text-slate-900 dark:text-white code-font">{{ number_format($page->views_count) }}</div>
                        </div>
                        <div>
                            <span class="text-slate-400">Conversions:</span>
                            <div class="font-bold text-emerald-500 code-font">{{ number_format($page->conversions_count) }} Orders</div>
                        </div>
                    </div>

                    @if($page->fb_pixel_id)
                    <div class="flex items-center gap-1.5 text-[11px] text-slate-400">
                        <i data-lucide="activity" class="w-3.5 h-3.5 text-blue-500"></i>
                        <span>Pixel ID: <strong class="font-mono">{{ $page->fb_pixel_id }}</strong></span>
                    </div>
                    @endif
                </div>
            </div>

            <div class="p-4 border-t border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-950/40 flex items-center justify-between">
                <a href="{{ route('landing.preview', $page->slug) }}" target="_blank" class="text-xs font-bold text-emerald-600 dark:text-emerald-400 hover:underline flex items-center gap-1">
                    <i data-lucide="external-link" class="w-3.5 h-3.5"></i>
                    <span>Live Preview</span>
                </a>
                <div class="flex items-center gap-2">
                    <a href="{{ route('admin.landing-pages.edit', $page->id) }}" class="p-1.5 rounded-lg text-slate-500 hover:text-emerald-500 hover:bg-slate-200 dark:hover:bg-slate-800">
                        <i data-lucide="edit" class="w-4 h-4"></i>
                    </a>
                    <form action="{{ route('admin.landing-pages.destroy', $page->id) }}" method="POST" onsubmit="return confirm('Delete landing page?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="p-1.5 rounded-lg text-slate-500 hover:text-rose-500 hover:bg-slate-200 dark:hover:bg-slate-800">
                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-3 p-12 text-center text-slate-400 bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800">
            <i data-lucide="sparkles" class="w-12 h-12 mx-auto mb-2 text-slate-300"></i>
            <p class="text-sm font-semibold">No landing pages created yet.</p>
            <a href="{{ route('admin.landing-pages.create') }}" class="mt-2 inline-block text-xs text-emerald-500 hover:underline">
                Create your first single-product flash page &rarr;
            </a>
        </div>
        @endforelse
    </div>

</div>
@endsection
