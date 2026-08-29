@extends('layouts.admin')

@section('title', 'SEO Meta Settings')
@section('page-title', 'Search Engine Optimization (SEO)')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <form method="POST" action="{{ route('admin.settings.seo.update') }}" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 border border-slate-200 dark:border-slate-800 shadow-sm space-y-4">
            <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                <i data-lucide="globe" class="w-4 h-4 text-emerald-500"></i>
                <span>Global Metadata & Search Snippet</span>
            </h3>

            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Global Meta Title *</label>
                    <input type="text" name="meta_title" value="{{ $seo['meta_title'] ?? 'DREAMERS PCB | Electronic Components & Gadget Solutions' }}" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-bold outline-none focus:ring-2 focus:ring-emerald-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Meta Keywords (Comma separated)</label>
                    <input type="text" name="meta_keywords" value="{{ $seo['meta_keywords'] ?? 'pcb bangladesh, esp32, arduino, soldering rework, stm32, electronic gadgets' }}" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs outline-none">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Meta Description</label>
                    <textarea name="meta_description" rows="3" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs outline-none">{{ $seo['meta_description'] ?? 'Buy high-quality electronic components, development boards, and professional PCB repair equipment in Bangladesh with cash on delivery.' }}</textarea>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 border border-slate-200 dark:border-slate-800 shadow-sm space-y-4">
            <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                <i data-lucide="share-2" class="w-4 h-4 text-sky-500"></i>
                <span>Open Graph (Facebook / WhatsApp Share Card)</span>
            </h3>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">OG Title</label>
                    <input type="text" name="og_title" value="{{ $seo['og_title'] ?? 'DREAMERS PCB - Enterprise Gadgets' }}" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs outline-none">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">OG Image URL</label>
                    <input type="text" name="og_image" value="{{ $seo['og_image'] ?? 'https://images.unsplash.com/photo-1518770660439-4636190af475?w=1200' }}" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs outline-none">
                </div>
            </div>
        </div>

        <div class="flex items-center justify-end">
            <button type="submit" class="px-6 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-extrabold text-xs shadow-lg transition">
                Save SEO Settings
            </button>
        </div>

    </form>

</div>
@endsection
