@extends('layouts.admin')

@section('title', 'Product Attributes')
@section('page-title', 'Colors & Sizes / Pinouts')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-2 gap-6">

    <!-- 1. Colors & Masking -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 border border-slate-200 dark:border-slate-800 shadow-sm space-y-4">
        <div class="flex items-center justify-between">
            <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                <i data-lucide="palette" class="w-4 h-4 text-emerald-500"></i>
                <span>PCB Colors & Solder Masks</span>
            </h3>
        </div>

        <form method="POST" action="{{ route('admin.attributes.color.store') }}" class="flex items-center gap-2">
            @csrf
            <input type="text" name="name" required placeholder="Color Name (e.g. Matte Black)" class="flex-1 px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs outline-none">
            <input type="color" name="code" value="#10b981" class="w-10 h-8 rounded-lg cursor-pointer border p-0.5">
            <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl text-xs font-bold shadow-md transition">
                Add Color
            </button>
        </form>

        <div class="divide-y divide-slate-100 dark:divide-slate-800 pt-2">
            @foreach($colors as $color)
            <div class="py-2.5 flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <span class="w-4 h-4 rounded-full border shadow-sm" style="background-color: {{ $color->code ?? '#000' }};"></span>
                    <span class="text-xs font-semibold text-slate-800 dark:text-slate-200">{{ $color->name }}</span>
                </div>
                <span class="text-[11px] text-slate-400 font-mono">{{ $color->variants_count }} variants linked</span>
            </div>
            @endforeach
        </div>
    </div>

    <!-- 2. Sizes, Packages & Pinouts -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 border border-slate-200 dark:border-slate-800 shadow-sm space-y-4">
        <div class="flex items-center justify-between">
            <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                <i data-lucide="maximize-2" class="w-4 h-4 text-sky-500"></i>
                <span>Packages, Sizes & Pinouts</span>
            </h3>
        </div>

        <form method="POST" action="{{ route('admin.attributes.size.store') }}" class="flex items-center gap-2">
            @csrf
            <input type="text" name="name" required placeholder="Size / IC Package (e.g. DIP-28 / SMD 0805)" class="flex-1 px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs outline-none">
            <button type="submit" class="px-4 py-2 bg-sky-600 hover:bg-sky-500 text-white rounded-xl text-xs font-bold shadow-md transition">
                Add Size / Package
            </button>
        </form>

        <div class="divide-y divide-slate-100 dark:divide-slate-800 pt-2">
            @foreach($sizes as $size)
            <div class="py-2.5 flex items-center justify-between">
                <span class="text-xs font-semibold text-slate-800 dark:text-slate-200">{{ $size->name }}</span>
                <span class="text-[11px] text-slate-400 font-mono">{{ $size->variants_count }} variants linked</span>
            </div>
            @endforeach
        </div>
    </div>

</div>
@endsection
