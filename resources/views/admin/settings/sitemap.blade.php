@extends('layouts.admin')

@section('title', 'XML Sitemap Generator')
@section('page-title', 'XML Sitemap & Search Indexing')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 border border-slate-200 dark:border-slate-800 shadow-sm space-y-4">
        <div class="flex items-center justify-between border-b pb-4">
            <div>
                <h3 class="text-base font-bold text-slate-900 dark:text-white flex items-center gap-2">
                    <i data-lucide="network" class="w-5 h-5 text-emerald-500"></i>
                    <span>XML Sitemap Index</span>
                </h3>
                <p class="text-xs text-slate-500">Automatically ping Google Search Console with dynamic product, category, and landing page URLs</p>
            </div>

            <a href="{{ route('sitemap.xml') }}" target="_blank" class="px-4 py-2 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 text-slate-700 dark:text-slate-300 rounded-xl text-xs font-bold transition flex items-center gap-1.5">
                <i data-lucide="external-link" class="w-4 h-4"></i>
                <span>View sitemap.xml</span>
            </a>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs">
            <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-800/40">
                <span class="text-slate-400">Indexed Products:</span>
                <div class="text-lg font-bold text-slate-900 dark:text-white code-font mt-1">{{ $totalProducts }} URLs</div>
            </div>
            <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-800/40">
                <span class="text-slate-400">Indexed Categories:</span>
                <div class="text-lg font-bold text-slate-900 dark:text-white code-font mt-1">{{ $totalCategories }} URLs</div>
            </div>
            <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-800/40">
                <span class="text-slate-400">Flash Landing Pages:</span>
                <div class="text-lg font-bold text-slate-900 dark:text-white code-font mt-1">{{ $totalLandingPages }} URLs</div>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.settings.sitemap.regenerate') }}" class="pt-4 border-t border-slate-100 dark:border-slate-800 flex justify-end">
            @csrf
            <button type="submit" class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl text-xs font-bold shadow-md transition flex items-center gap-2">
                <i data-lucide="refresh-cw" class="w-4 h-4"></i>
                <span>Regenerate & Ping Google</span>
            </button>
        </form>
    </div>

</div>
@endsection
