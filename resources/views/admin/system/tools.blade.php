@extends('layouts.admin')

@section('title', 'System Tools & Cache Management')
@section('page-title', 'System Tools, Diagnostics & Cache Manager')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">

    <!-- System Diagnostics Information -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 border border-slate-200 dark:border-slate-800 shadow-sm space-y-4">
        <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
            <i data-lucide="cpu" class="w-4 h-4 text-sky-500"></i>
            <span>Environment & Server Diagnostics</span>
        </h3>

        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-xs">
            <div class="p-3.5 rounded-xl bg-slate-50 dark:bg-slate-800/40">
                <span class="text-slate-400">Laravel Framework</span>
                <div class="font-bold text-slate-900 dark:text-white code-font mt-1">v{{ app()->version() }}</div>
            </div>
            <div class="p-3.5 rounded-xl bg-slate-50 dark:bg-slate-800/40">
                <span class="text-slate-400">PHP Version</span>
                <div class="font-bold text-emerald-500 code-font mt-1">v{{ PHP_VERSION }}</div>
            </div>
            <div class="p-3.5 rounded-xl bg-slate-50 dark:bg-slate-800/40">
                <span class="text-slate-400">Database Engine</span>
                <div class="font-bold text-slate-900 dark:text-white code-font mt-1">{{ strtoupper(config('database.default')) }}</div>
            </div>
            <div class="p-3.5 rounded-xl bg-slate-50 dark:bg-slate-800/40">
                <span class="text-slate-400">App Environment</span>
                <div class="font-bold text-purple-500 code-font mt-1">{{ strtoupper(config('app.env')) }}</div>
            </div>
        </div>
    </div>

    <!-- Cache Management Cards -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 border border-slate-200 dark:border-slate-800 shadow-sm space-y-4">
        <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
            <i data-lucide="zap" class="w-4 h-4 text-amber-500"></i>
            <span>Cache Flushing & Optimization Tools</span>
        </h3>
        <p class="text-xs text-slate-500">Flush application runtime caches to refresh routes, config values, or compiled views after updates</p>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 pt-2">
            
            <!-- Clear All -->
            <form method="POST" action="{{ route('admin.system.cache.clear') }}">
                @csrf
                <input type="hidden" name="type" value="all">
                <button type="submit" class="w-full p-4 rounded-xl bg-rose-500/10 hover:bg-rose-500/20 border border-rose-500/30 text-left transition flex items-center justify-between">
                    <div>
                        <div class="font-bold text-xs text-rose-500">Flush Entire Cache</div>
                        <div class="text-[10px] text-slate-400">Wipes app, config & views</div>
                    </div>
                    <i data-lucide="trash" class="w-4 h-4 text-rose-500"></i>
                </button>
            </form>

            <!-- Config Cache -->
            <form method="POST" action="{{ route('admin.system.cache.clear') }}">
                @csrf
                <input type="hidden" name="type" value="config">
                <button type="submit" class="w-full p-4 rounded-xl bg-slate-50 dark:bg-slate-800/50 hover:bg-slate-100 dark:hover:bg-slate-800 border border-slate-200 dark:border-slate-700 text-left transition flex items-center justify-between">
                    <div>
                        <div class="font-bold text-xs text-slate-900 dark:text-white">Clear Config Cache</div>
                        <div class="text-[10px] text-slate-400">artisan config:clear</div>
                    </div>
                    <i data-lucide="settings" class="w-4 h-4 text-slate-400"></i>
                </button>
            </form>

            <!-- Route Cache -->
            <form method="POST" action="{{ route('admin.system.cache.clear') }}">
                @csrf
                <input type="hidden" name="type" value="routes">
                <button type="submit" class="w-full p-4 rounded-xl bg-slate-50 dark:bg-slate-800/50 hover:bg-slate-100 dark:hover:bg-slate-800 border border-slate-200 dark:border-slate-700 text-left transition flex items-center justify-between">
                    <div>
                        <div class="font-bold text-xs text-slate-900 dark:text-white">Clear Route Cache</div>
                        <div class="text-[10px] text-slate-400">artisan route:clear</div>
                    </div>
                    <i data-lucide="network" class="w-4 h-4 text-slate-400"></i>
                </button>
            </form>

            <!-- View Cache -->
            <form method="POST" action="{{ route('admin.system.cache.clear') }}">
                @csrf
                <input type="hidden" name="type" value="views">
                <button type="submit" class="w-full p-4 rounded-xl bg-slate-50 dark:bg-slate-800/50 hover:bg-slate-100 dark:hover:bg-slate-800 border border-slate-200 dark:border-slate-700 text-left transition flex items-center justify-between">
                    <div>
                        <div class="font-bold text-xs text-slate-900 dark:text-white">Clear Blade Views</div>
                        <div class="text-[10px] text-slate-400">artisan view:clear</div>
                    </div>
                    <i data-lucide="layout" class="w-4 h-4 text-slate-400"></i>
                </button>
            </form>

            <!-- Symlink Storage -->
            <form method="POST" action="{{ route('admin.system.cache.clear') }}">
                @csrf
                <input type="hidden" name="type" value="storage_link">
                <button type="submit" class="w-full p-4 rounded-xl bg-slate-50 dark:bg-slate-800/50 hover:bg-slate-100 dark:hover:bg-slate-800 border border-slate-200 dark:border-slate-700 text-left transition flex items-center justify-between">
                    <div>
                        <div class="font-bold text-xs text-emerald-500">Link Storage Disk</div>
                        <div class="text-[10px] text-slate-400">artisan storage:link</div>
                    </div>
                    <i data-lucide="link" class="w-4 h-4 text-emerald-500"></i>
                </button>
            </form>

        </div>
    </div>

</div>
@endsection
