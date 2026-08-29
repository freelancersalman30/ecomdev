@extends('layouts.admin')

@section('title', 'Brand Management')
@section('page-title', 'Brands & Manufacturers')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-3 gap-6">

    <!-- Add Brand Form -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl p-5 border border-slate-200 dark:border-slate-800 shadow-sm space-y-4">
        <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
            <i data-lucide="plus-circle" class="w-4 h-4 text-emerald-500"></i>
            <span>Add New Brand</span>
        </h3>
        
        <form method="POST" action="{{ route('admin.brands.store') }}" class="space-y-3">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-slate-500 mb-1">Brand Name *</label>
                <input type="text" name="name" required placeholder="e.g. STMicroelectronics / Espressif" class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs outline-none focus:ring-2 focus:ring-emerald-500">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-500 mb-1">Website URL</label>
                <input type="url" name="website" placeholder="https://www.st.com" class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs outline-none">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-500 mb-1">Logo URL</label>
                <input type="text" name="logo" placeholder="https://..." class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs outline-none">
            </div>
            <button type="submit" class="w-full py-2 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl text-xs font-bold shadow-md transition">
                + Create Brand
            </button>
        </form>
    </div>

    <!-- Brands List (2 Cols) -->
    <div class="md:col-span-2 bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
        <div class="p-4 border-b border-slate-200 dark:border-slate-800 font-bold text-sm text-slate-900 dark:text-white">
            Registered Brands Directory ({{ $brands->count() }})
        </div>
        <div class="divide-y divide-slate-100 dark:divide-slate-800">
            @forelse($brands as $brand)
            <div class="p-4 flex items-center justify-between hover:bg-slate-50 dark:hover:bg-slate-800/30 transition">
                <div>
                    <div class="font-bold text-xs text-slate-900 dark:text-white">{{ $brand->name }}</div>
                    <div class="text-[10px] text-slate-400 font-mono">{{ $brand->website ?? 'No website URL' }}</div>
                </div>
                <div class="flex items-center gap-4">
                    <span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300">
                        {{ $brand->products_count }} products
                    </span>
                    <form action="{{ route('admin.brands.destroy', $brand->id) }}" method="POST" onsubmit="return confirm('Delete brand?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-xs text-rose-500 hover:underline">Delete</button>
                    </form>
                </div>
            </div>
            @empty
            <div class="p-8 text-center text-slate-400 text-xs">No brands created yet.</div>
            @endforelse
        </div>
    </div>

</div>
@endsection
