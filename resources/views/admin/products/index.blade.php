@extends('layouts.admin')

@section('title', 'Product Catalog & PCB Specs')
@section('page-title', 'Product & Catalog Management')

@section('content')
<div class="space-y-6">

    <!-- Header & Action Toolbar -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl p-4 border border-slate-200 dark:border-slate-800 shadow-sm flex flex-col sm:flex-row items-center justify-between gap-4">
        
        <form method="GET" action="{{ route('admin.products.index') }}" class="flex flex-wrap items-center gap-3 w-full sm:w-auto">
            <div class="relative w-full sm:w-72">
                <i data-lucide="search" class="w-4 h-4 absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400"></i>
                <input 
                    type="text" 
                    name="search" 
                    value="{{ $search }}" 
                    placeholder="Search name, SKU, PCB model..." 
                    class="w-full pl-10 pr-4 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs outline-none focus:ring-2 focus:ring-emerald-500">
            </div>

            <select name="category_id" onchange="this.form.submit()" class="px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs outline-none">
                <option value="">All Categories</option>
                @foreach($categories as $cat)
                <option value="{{ $cat->id }}" {{ $categoryId == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                @endforeach
            </select>

            <select name="stock_filter" onchange="this.form.submit()" class="px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs outline-none">
                <option value="">All Stock Levels</option>
                <option value="low_stock" {{ $stockFilter == 'low_stock' ? 'selected' : '' }}>Low Stock Alerts Only</option>
            </select>

            <button type="submit" class="px-3.5 py-2 rounded-xl bg-slate-800 text-white text-xs font-semibold hover:bg-slate-700 transition">
                Filter
            </button>
            @if($search || $categoryId || $stockFilter)
            <a href="{{ route('admin.products.index') }}" class="text-xs text-rose-500 hover:underline">Reset</a>
            @endif
        </form>

        <a href="{{ route('admin.products.create') }}" class="px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs shadow-md shadow-emerald-600/20 transition flex items-center gap-1.5 whitespace-nowrap">
            <i data-lucide="plus-circle" class="w-4 h-4"></i>
            <span>Add New Product / PCB</span>
        </a>

    </div>

    <!-- Products Data Table -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 dark:bg-slate-800/50 text-slate-500 text-xs uppercase font-semibold">
                    <tr>
                        <th class="px-4 py-3.5">Product & Tech Specs</th>
                        <th class="px-4 py-3.5">SKU & Barcode</th>
                        <th class="px-4 py-3.5">Category & Brand</th>
                        <th class="px-4 py-3.5">Purchase Cost</th>
                        <th class="px-4 py-3.5">Selling Price</th>
                        <th class="px-4 py-3.5">Stock Status</th>
                        <th class="px-4 py-3.5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($products as $product)
                    <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition">
                        <td class="px-4 py-3.5">
                            <div class="flex items-center gap-3">
                                <img src="{{ $product->thumbnail }}" alt="{{ $product->name }}" class="w-12 h-12 rounded-xl object-cover border border-slate-200 dark:border-slate-700 flex-shrink-0">
                                <div>
                                    <div class="font-bold text-xs text-slate-900 dark:text-white line-clamp-1">
                                        <a href="{{ route('admin.products.edit', $product->id) }}" class="hover:text-emerald-500">
                                            {{ $product->name }}
                                        </a>
                                    </div>
                                    @if($product->pcb_model || $product->chipset)
                                    <div class="text-[10px] text-slate-500 mt-0.5">
                                        {{ $product->pcb_model }} | Chip: {{ $product->chipset }} | {{ $product->voltage }}
                                    </div>
                                    @endif
                                    @if($product->has_variants)
                                    <span class="inline-block mt-1 px-1.5 py-0.5 rounded text-[9px] font-bold bg-purple-100 text-purple-700 dark:bg-purple-950 dark:text-purple-300">
                                        {{ $product->variants->count() }} Variants
                                    </span>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3.5 font-mono text-xs">
                            <div class="font-bold text-slate-900 dark:text-white">{{ $product->sku }}</div>
                            <div class="text-[10px] text-slate-400">{{ $product->barcode ?? 'No Barcode' }}</div>
                        </td>
                        <td class="px-4 py-3.5 text-xs">
                            <div class="font-medium text-slate-800 dark:text-slate-200">{{ $product->category->name ?? 'Uncategorized' }}</div>
                            <div class="text-[11px] text-slate-500">{{ $product->brand->name ?? 'Generic' }}</div>
                        </td>
                        <td class="px-4 py-3.5 font-semibold text-xs text-slate-600 dark:text-slate-400 code-font">
                            ৳{{ number_format($product->purchase_price, 2) }}
                        </td>
                        <td class="px-4 py-3.5 text-xs">
                            <div class="font-bold text-emerald-600 dark:text-emerald-400 code-font">
                                ৳{{ number_format($product->selling_price, 2) }}
                            </div>
                            @if($product->discount_price)
                            <div class="text-[10px] text-slate-400 line-through code-font">
                                ৳{{ number_format($product->discount_price, 2) }}
                            </div>
                            @endif
                        </td>
                        <td class="px-4 py-3.5">
                            @if($product->has_variants)
                            <div class="text-xs font-bold text-slate-900 dark:text-white code-font">
                                {{ $product->total_stock }} Total
                            </div>
                            @else
                            <span class="px-2 py-0.5 rounded-full text-xs font-bold code-font {{ $product->stock_quantity <= $product->alert_threshold ? 'bg-rose-100 text-rose-700 dark:bg-rose-950 dark:text-rose-300' : 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300' }}">
                                {{ $product->stock_quantity }} in stock
                            </span>
                            @endif
                        </td>
                        <td class="px-4 py-3.5 text-right space-x-1 whitespace-nowrap">
                            <a href="{{ route('admin.products.edit', $product->id) }}" class="p-1.5 rounded-lg text-slate-600 dark:text-slate-300 hover:text-emerald-500 hover:bg-slate-100 dark:hover:bg-slate-800 inline-flex">
                                <i data-lucide="edit" class="w-4 h-4"></i>
                            </a>
                            <form action="{{ route('admin.products.destroy', $product->id) }}" method="POST" class="inline" onsubmit="return confirm('Delete this product?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-1.5 rounded-lg text-slate-600 dark:text-slate-300 hover:text-rose-500 hover:bg-slate-100 dark:hover:bg-slate-800 inline-flex">
                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-slate-400 text-xs">
                            No products found in catalog.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-slate-200 dark:border-slate-800">
            {{ $products->links() }}
        </div>
    </div>

</div>
@endsection
