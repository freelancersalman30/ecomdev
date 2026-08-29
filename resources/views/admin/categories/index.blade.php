@extends('layouts.admin')

@section('title', '3-Tier Category Hierarchy')
@section('page-title', 'Categories, Sub-Categories & Child-Categories')

@section('content')
<div class="space-y-6">

    <!-- 3-Column Creation Panels -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

        <!-- 1. Primary Category Form -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl p-5 border border-slate-200 dark:border-slate-800 shadow-sm space-y-4">
            <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                <span class="w-6 h-6 rounded-full bg-emerald-500/10 text-emerald-500 flex items-center justify-center text-xs font-bold">1</span>
                <span>Add Primary Category</span>
            </h3>
            
            <form method="POST" action="{{ route('admin.categories.store') }}" class="space-y-3">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Category Name *</label>
                    <input type="text" name="name" required placeholder="e.g. Microcontrollers & Dev Boards" class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs outline-none focus:ring-2 focus:ring-emerald-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Lucide Icon Key</label>
                    <input type="text" name="icon" placeholder="cpu / tool / activity / zap" class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs outline-none">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Description</label>
                    <textarea name="description" rows="2" placeholder="Brief category description..." class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs outline-none"></textarea>
                </div>
                <button type="submit" class="w-full py-2 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl text-xs font-bold shadow-md transition">
                    + Create Primary Category
                </button>
            </form>
        </div>

        <!-- 2. Sub-Category Form -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl p-5 border border-slate-200 dark:border-slate-800 shadow-sm space-y-4">
            <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                <span class="w-6 h-6 rounded-full bg-sky-500/10 text-sky-500 flex items-center justify-center text-xs font-bold">2</span>
                <span>Add Sub-Category</span>
            </h3>
            
            <form method="POST" action="{{ route('admin.subcategories.store') }}" class="space-y-3">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Select Parent Category *</label>
                    <select name="category_id" required class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs outline-none">
                        <option value="">Select Parent</option>
                        @foreach($categories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Sub-Category Name *</label>
                    <input type="text" name="name" required placeholder="e.g. ESP32 & IoT Boards" class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs outline-none focus:ring-2 focus:ring-sky-500">
                </div>
                <button type="submit" class="w-full py-2 bg-sky-600 hover:bg-sky-500 text-white rounded-xl text-xs font-bold shadow-md transition">
                    + Create Sub-Category
                </button>
            </form>
        </div>

        <!-- 3. Child-Category Form -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl p-5 border border-slate-200 dark:border-slate-800 shadow-sm space-y-4">
            <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                <span class="w-6 h-6 rounded-full bg-purple-500/10 text-purple-500 flex items-center justify-center text-xs font-bold">3</span>
                <span>Add Child-Category</span>
            </h3>
            
            <form method="POST" action="{{ route('admin.childcategories.store') }}" class="space-y-3">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Select Sub-Category *</label>
                    <select name="sub_category_id" required class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs outline-none">
                        <option value="">Select Sub-Category</option>
                        @foreach($subCategories as $sub)
                        <option value="{{ $sub->id }}">{{ $sub->category->name ?? '' }} &rarr; {{ $sub->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Child-Category Name *</label>
                    <input type="text" name="name" required placeholder="e.g. ESP32-CAM AI Vision" class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs outline-none focus:ring-2 focus:ring-purple-500">
                </div>
                <button type="submit" class="w-full py-2 bg-purple-600 hover:bg-purple-500 text-white rounded-xl text-xs font-bold shadow-md transition">
                    + Create Child-Category
                </button>
            </form>
        </div>

    </div>

    <!-- Tree View & Table of Categories -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
        <div class="p-4 border-b border-slate-200 dark:border-slate-800 font-bold text-sm text-slate-900 dark:text-white flex items-center justify-between">
            <span>Hierarchical Catalog Tree</span>
            <span class="text-xs text-slate-500 font-normal">{{ $categories->count() }} Primary Categories</span>
        </div>

        <div class="divide-y divide-slate-100 dark:divide-slate-800">
            @foreach($categories as $category)
            <div class="p-4 space-y-3">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-emerald-500/10 text-emerald-500 flex items-center justify-center font-bold">
                            <i data-lucide="{{ $category->icon ?? 'folder' }}" class="w-4 h-4"></i>
                        </div>
                        <div>
                            <div class="font-bold text-sm text-slate-900 dark:text-white">{{ $category->name }}</div>
                            <div class="text-[11px] text-slate-400 font-mono">/category/{{ $category->slug }} | {{ $category->products_count }} Products</div>
                        </div>
                    </div>
                    <form action="{{ route('admin.categories.destroy', $category->id) }}" method="POST" onsubmit="return confirm('Delete this entire category hierarchy?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-xs text-rose-500 hover:underline">Delete</button>
                    </form>
                </div>

                <!-- Sub categories tree list -->
                @if($category->subCategories->count() > 0)
                <div class="pl-8 space-y-2 border-l-2 border-slate-100 dark:border-slate-800 ml-4">
                    @foreach($category->subCategories as $subCat)
                    <div class="p-2.5 rounded-xl bg-slate-50 dark:bg-slate-800/40 border border-slate-200/60 dark:border-slate-700/40 space-y-2">
                        <div class="flex items-center justify-between">
                            <div class="font-semibold text-xs text-slate-800 dark:text-slate-200 flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-sky-500"></span>
                                <span>{{ $subCat->name }}</span>
                                <span class="text-[10px] text-slate-400 font-mono">({{ $subCat->products_count }} prods)</span>
                            </div>
                        </div>

                        <!-- Child categories chips -->
                        @if($subCat->childCategories->count() > 0)
                        <div class="flex flex-wrap gap-1.5 pl-4 pt-1">
                            @foreach($subCat->childCategories as $child)
                            <span class="px-2 py-0.5 rounded-md text-[10px] font-medium bg-purple-50 dark:bg-purple-950/40 text-purple-700 dark:text-purple-300 border border-purple-200 dark:border-purple-800">
                                &bull; {{ $child->name }}
                            </span>
                            @endforeach
                        </div>
                        @endif
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
            @endforeach
        </div>
    </div>

</div>
@endsection
