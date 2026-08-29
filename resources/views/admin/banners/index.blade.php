@extends('layouts.admin')

@section('title', 'Banners & Advertising')
@section('page-title', 'Banners & Promotional Sliders')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-3 gap-6">

    <!-- Add Banner Form -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl p-5 border border-slate-200 dark:border-slate-800 shadow-sm space-y-4">
        <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
            <i data-lucide="image" class="w-4 h-4 text-emerald-500"></i>
            <span>Create Promotional Banner</span>
        </h3>
        
        <form method="POST" action="{{ route('admin.banners.store') }}" class="space-y-3">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-slate-500 mb-1">Banner Title *</label>
                <input type="text" name="title" required placeholder="e.g. Mega Robotics Sale" class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs outline-none focus:ring-2 focus:ring-emerald-500">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-500 mb-1">Banner Placement *</label>
                <select name="placement" class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs outline-none">
                    <option value="main_slider">Main Homepage Hero Slider</option>
                    <option value="promo_strip">Promo Top Strip Banner</option>
                    <option value="sidebar">Sidebar Gadget Ad</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-500 mb-1">Image URL *</label>
                <input type="text" name="image" required placeholder="https://images.unsplash.com/..." class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs outline-none">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-500 mb-1">Target Link URL</label>
                <input type="text" name="link_url" placeholder="/offers/stm32-promo" class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs outline-none">
            </div>
            <button type="submit" class="w-full py-2 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl text-xs font-bold shadow-md transition">
                + Publish Banner
            </button>
        </form>
    </div>

    <!-- Active Banners List (2 Cols) -->
    <div class="md:col-span-2 bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
        <div class="p-4 border-b border-slate-200 dark:border-slate-800 font-bold text-sm text-slate-900 dark:text-white">
            Active Banners & Sliders ({{ $banners->count() }})
        </div>
        <div class="divide-y divide-slate-100 dark:divide-slate-800">
            @forelse($banners as $banner)
            <div class="p-4 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 hover:bg-slate-50 dark:hover:bg-slate-800/30 transition">
                <div class="flex items-center gap-4">
                    <img src="{{ $banner->image }}" alt="{{ $banner->title }}" class="w-24 h-14 object-cover rounded-xl border border-slate-200 dark:border-slate-700">
                    <div>
                        <div class="font-bold text-xs text-slate-900 dark:text-white">{{ $banner->title }}</div>
                        <div class="text-[10px] text-slate-400 font-mono">{{ $banner->link_url ?? 'No click target' }}</div>
                        <span class="inline-block mt-1 px-2 py-0.5 rounded text-[9px] font-bold uppercase bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300">
                            {{ str_replace('_', ' ', $banner->placement) }}
                        </span>
                    </div>
                </div>

                <form action="{{ route('admin.banners.destroy', $banner->id) }}" method="POST" onsubmit="return confirm('Delete banner?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-xs text-rose-500 hover:underline">Delete</button>
                </form>
            </div>
            @empty
            <div class="p-8 text-center text-slate-400 text-xs">No banners created.</div>
            @endforelse
        </div>
    </div>

</div>
@endsection
