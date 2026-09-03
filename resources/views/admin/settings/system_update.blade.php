@extends('layouts.admin')

@section('title', 'System & Git Version Update')
@section('page-title', 'Live Server System & Git Updater')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 border border-slate-200 dark:border-slate-800 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-base font-bold text-slate-900 dark:text-white flex items-center gap-2">
                <i data-lucide="git-branch" class="w-5 h-5 text-emerald-500"></i>
                <span>Live Server Version & Git Auto-Updater</span>
            </h2>
            <p class="text-xs text-slate-500 mt-1">Pull the latest updates from GitHub (`origin/main`), run database migrations, and clear cache automatically with 1-click.</p>
        </div>
        <div>
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-emerald-50 dark:bg-emerald-950/50 border border-emerald-200 dark:border-emerald-800 text-emerald-700 dark:text-emerald-300 font-semibold text-xs">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                <span>Branch: {{ $currentBranch }}</span>
            </span>
        </div>
    </div>

    @if(session('success'))
        <div class="p-4 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300 text-xs font-semibold flex items-center gap-2">
            <i data-lucide="check-circle" class="w-4 h-4 text-emerald-600 shrink-0"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="p-4 rounded-xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 text-rose-800 dark:text-rose-300 text-xs font-semibold flex items-center gap-2">
            <i data-lucide="alert-circle" class="w-4 h-4 text-rose-600 shrink-0"></i>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 border border-slate-200 dark:border-slate-800 shadow-sm space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-slate-100 dark:border-slate-800">
            <div>
                <div class="text-xs font-semibold text-slate-500">Current Server Version / Commit:</div>
                <div class="text-sm font-mono font-bold text-slate-900 dark:text-white mt-1">{{ $currentCommit }}</div>
                @if($currentDate)
                    <div class="text-[11px] text-slate-400 mt-0.5">Updated: {{ $currentDate }}</div>
                @endif
            </div>

            <form method="POST" action="{{ route('admin.settings.system_update.pull') }}" onsubmit="this.querySelector('button').disabled=true; this.querySelector('button').innerText='Pulling & Updating...';">
                @csrf
                <button type="submit" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl text-xs font-bold transition flex items-center gap-2 shadow-xs">
                    <i data-lucide="download-cloud" class="w-4 h-4"></i>
                    <span>1-Click Pull & Update from GitHub</span>
                </button>
            </form>
        </div>

        @if(session('update_log'))
            <div>
                <h3 class="text-xs font-bold text-slate-700 dark:text-slate-300 mb-2">Update Execution Output Log:</h3>
                <pre class="p-4 rounded-xl bg-slate-950 text-emerald-400 font-mono text-xs overflow-x-auto whitespace-pre-wrap leading-relaxed border border-slate-800">{{ session('update_log') }}</pre>
            </div>
        @endif

        <div class="bg-slate-50 dark:bg-slate-800/50 p-4 rounded-xl border border-slate-200 dark:border-slate-700 space-y-2">
            <div class="text-xs font-bold text-slate-800 dark:text-slate-200 flex items-center gap-2">
                <i data-lucide="terminal" class="w-4 h-4 text-emerald-600"></i>
                <span>Manual cPanel Terminal Commands (If needed)</span>
            </div>
            <p class="text-[11px] text-slate-500">If your live hosting restricts PHP shell execution, open <strong>cPanel &rarr; Terminal</strong> and run:</p>
            <div class="p-3 bg-slate-900 text-slate-100 rounded-lg font-mono text-xs overflow-x-auto select-all">
git fetch origin main<br>
git reset --hard origin/main<br>
php artisan optimize:clear
            </div>
        </div>
    </div>

</div>
@endsection
