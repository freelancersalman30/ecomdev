@extends('layouts.admin')

@section('title', 'Ready Custom PC Builder & Prebuilts')
@section('page-title', 'Ready Custom PC Building Directory')

@section('content')
<div class="space-y-6">

    <!-- Top Action Bar & Metrics -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-black text-slate-900 dark:text-white tracking-tight flex items-center gap-2">
                <i data-lucide="cpu" class="w-6 h-6 text-indigo-500"></i>
                <span>Ready Custom PC Building Studio</span>
            </h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                Assemble, configure, and publish pre-built custom PC packages, or generate instant customer quotations and POS orders.
            </p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.custom-pc-builds.create') }}" 
               class="px-4 py-2.5 rounded-xl bg-gradient-to-r from-indigo-600 to-emerald-600 hover:from-indigo-500 hover:to-emerald-500 text-white font-bold text-xs shadow-lg shadow-indigo-500/20 transition-all flex items-center gap-2">
                <i data-lucide="plus-circle" class="w-4 h-4"></i>
                <span>+ Build New Custom PC</span>
            </a>
        </div>
    </div>

    <!-- 4 Summary Stat Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="p-4 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-xs font-semibold text-slate-400">Total PC Builds</span>
                <div class="text-2xl font-black text-slate-900 dark:text-white mt-1 code-font">{{ $metrics['total_builds'] }}</div>
            </div>
            <div class="w-11 h-11 rounded-xl bg-indigo-500/10 text-indigo-500 flex items-center justify-center">
                <i data-lucide="cpu" class="w-6 h-6"></i>
            </div>
        </div>

        <div class="p-4 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-xs font-semibold text-slate-400">Published / Live</span>
                <div class="text-2xl font-black text-emerald-500 mt-1 code-font">{{ $metrics['published_builds'] }}</div>
            </div>
            <div class="w-11 h-11 rounded-xl bg-emerald-500/10 text-emerald-500 flex items-center justify-center">
                <i data-lucide="check-circle-2" class="w-6 h-6"></i>
            </div>
        </div>

        <div class="p-4 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-xs font-semibold text-slate-400">Average Build Price</span>
                <div class="text-2xl font-black text-sky-500 mt-1 code-font">৳{{ number_format($metrics['avg_price'], 0) }}</div>
            </div>
            <div class="w-11 h-11 rounded-xl bg-sky-500/10 text-sky-500 flex items-center justify-center">
                <i data-lucide="badge-dollar-sign" class="w-6 h-6"></i>
            </div>
        </div>

        <div class="p-4 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-xs font-semibold text-slate-400">Featured Builds</span>
                <div class="text-2xl font-black text-amber-500 mt-1 code-font">{{ $metrics['featured_count'] }}</div>
            </div>
            <div class="w-11 h-11 rounded-xl bg-amber-500/10 text-amber-500 flex items-center justify-center">
                <i data-lucide="sparkles" class="w-6 h-6"></i>
            </div>
        </div>
    </div>

    <!-- Filter & Search Bar -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl p-4 border border-slate-200 dark:border-slate-800 shadow-sm">
        <form method="GET" action="{{ route('admin.custom-pc-builds.index') }}" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-5 gap-3 text-xs">
            <div class="md:col-span-2">
                <div class="relative">
                    <input type="text" 
                           name="search" 
                           value="{{ request('search') }}" 
                           placeholder="Search by build title, code (e.g. PCB-PC-), specs..." 
                           class="w-full pl-9 pr-4 py-2 rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-950/50 text-slate-900 dark:text-white outline-none focus:ring-2 focus:ring-indigo-500">
                    <i data-lucide="search" class="w-4 h-4 text-slate-400 absolute left-3 top-2.5"></i>
                </div>
            </div>

            <div>
                <select name="category" onchange="this.form.submit()" class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-950/50 text-slate-900 dark:text-white outline-none">
                    <option value="">All Categories</option>
                    <option value="Gaming" {{ request('category') == 'Gaming' ? 'selected' : '' }}>🎮 Gaming Rig</option>
                    <option value="Workstation" {{ request('category') == 'Workstation' ? 'selected' : '' }}>💼 Workstation / 3D</option>
                    <option value="Office" {{ request('category') == 'Office' ? 'selected' : '' }}>🏢 Office & Student</option>
                    <option value="Budget" {{ request('category') == 'Budget' ? 'selected' : '' }}>💰 Budget Build</option>
                    <option value="Editing" {{ request('category') == 'Editing' ? 'selected' : '' }}>🎬 Video Editing</option>
                </select>
            </div>

            <div>
                <select name="performance_level" onchange="this.form.submit()" class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-950/50 text-slate-900 dark:text-white outline-none">
                    <option value="">All Tiers</option>
                    <option value="Entry Level" {{ request('performance_level') == 'Entry Level' ? 'selected' : '' }}>Entry Level</option>
                    <option value="Mid-Range" {{ request('performance_level') == 'Mid-Range' ? 'selected' : '' }}>Mid-Range</option>
                    <option value="High-End" {{ request('performance_level') == 'High-End' ? 'selected' : '' }}>High-End</option>
                    <option value="Enthusiast" {{ request('performance_level') == 'Enthusiast' ? 'selected' : '' }}>Extreme / Enthusiast</option>
                </select>
            </div>

            <div class="flex items-center gap-2">
                <select name="status" onchange="this.form.submit()" class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-950/50 text-slate-900 dark:text-white outline-none">
                    <option value="">Status: All</option>
                    <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Published Only</option>
                    <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Drafts Only</option>
                </select>
                @if(request()->hasAny(['search', 'category', 'performance_level', 'status']))
                <a href="{{ route('admin.custom-pc-builds.index') }}" title="Clear Filters" class="p-2 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-500 hover:text-rose-500 transition">
                    <i data-lucide="rotate-ccw" class="w-4 h-4"></i>
                </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Builds List Table -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 dark:bg-slate-800/60 text-slate-500 font-bold uppercase tracking-wider border-b border-slate-200 dark:border-slate-800">
                    <tr>
                        <th class="px-4 py-3.5">PC Build Title & Code</th>
                        <th class="px-4 py-3.5">Category & Tier</th>
                        <th class="px-4 py-3.5">Components</th>
                        <th class="px-4 py-3.5">Power (Est.)</th>
                        <th class="px-4 py-3.5">Pricing</th>
                        <th class="px-4 py-3.5">Stock & Status</th>
                        <th class="px-4 py-3.5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($builds as $build)
                    <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/40 transition">
                        <td class="px-4 py-3.5">
                            <div class="flex items-center gap-3">
                                <div class="w-12 h-12 rounded-xl bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 flex items-center justify-center flex-shrink-0 overflow-hidden">
                                    @if($build->thumbnail)
                                        <img src="{{ $build->thumbnail }}" alt="{{ $build->title }}" class="w-full h-full object-cover">
                                    @else
                                        <i data-lucide="cpu" class="w-6 h-6 text-indigo-400"></i>
                                    @endif
                                </div>
                                <div class="min-w-0">
                                    <div class="flex items-center gap-1.5">
                                        <a href="{{ route('admin.custom-pc-builds.show', $build->id) }}" class="font-bold text-slate-900 dark:text-white hover:text-indigo-500 transition line-clamp-1">
                                            {{ $build->title }}
                                        </a>
                                        @if($build->is_featured)
                                            <span class="px-1.5 py-0.2 rounded text-[9px] font-extrabold uppercase bg-amber-500/20 text-amber-400 flex items-center gap-0.5">
                                                <i data-lucide="sparkles" class="w-2.5 h-2.5"></i> Featured
                                            </span>
                                        @endif
                                    </div>
                                    <div class="text-[11px] text-slate-400 font-mono mt-0.5 flex items-center gap-2">
                                        <span>{{ $build->build_code }}</span>
                                        <span>•</span>
                                        <span>Updated {{ $build->updated_at->diffForHumans() }}</span>
                                    </div>
                                </div>
                            </div>
                        </td>

                        <td class="px-4 py-3.5">
                            <div class="space-y-1">
                                <span class="px-2 py-0.5 rounded-md text-[10px] font-bold uppercase 
                                    {{ $build->category === 'Gaming' ? 'bg-indigo-500/10 text-indigo-500' : '' }}
                                    {{ $build->category === 'Workstation' ? 'bg-purple-500/10 text-purple-500' : '' }}
                                    {{ $build->category === 'Office' ? 'bg-blue-500/10 text-blue-500' : '' }}
                                    {{ $build->category === 'Budget' ? 'bg-emerald-500/10 text-emerald-500' : '' }}
                                    {{ !in_array($build->category, ['Gaming', 'Workstation', 'Office', 'Budget']) ? 'bg-slate-100 dark:bg-slate-800 text-slate-400' : '' }}">
                                    {{ $build->category ?? 'Custom' }}
                                </span>
                                <div class="text-[11px] font-medium text-slate-500">{{ $build->performance_level ?? 'Standard' }}</div>
                            </div>
                        </td>

                        <td class="px-4 py-3.5">
                            @php
                                $comps = is_array($build->components) ? $build->components : [];
                                $slotCount = count($comps);
                            @endphp
                            <div class="flex items-center gap-1.5">
                                <span class="px-2 py-1 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-mono font-bold text-xs">
                                    {{ $slotCount }} Slots
                                </span>
                                <span class="text-[10px] text-slate-400">Configured</span>
                            </div>
                        </td>

                        <td class="px-4 py-3.5">
                            <div class="flex items-center gap-1.5 text-amber-500 font-bold font-mono">
                                <i data-lucide="zap" class="w-3.5 h-3.5"></i>
                                <span>{{ $build->estimated_wattage ?: 450 }}W</span>
                            </div>
                        </td>

                        <td class="px-4 py-3.5">
                            <div class="space-y-0.5">
                                <div class="font-black text-emerald-500 code-font text-sm">
                                    ৳{{ number_format($build->final_price, 2) }}
                                </div>
                                @if($build->regular_price > $build->final_price)
                                <div class="text-[11px] text-slate-400 line-through code-font">
                                    ৳{{ number_format($build->regular_price, 2) }}
                                </div>
                                @endif
                            </div>
                        </td>

                        <td class="px-4 py-3.5">
                            <div class="space-y-1">
                                <div>
                                    @if($build->is_published)
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/10 text-emerald-500">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                            Published
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-500/10 text-slate-400">
                                            Draft
                                        </span>
                                    @endif
                                </div>
                                <span class="text-[10px] font-medium text-slate-400 capitalize">
                                    {{ str_replace('_', ' ', $build->stock_status) }}
                                </span>
                            </div>
                        </td>

                        <td class="px-4 py-3.5 text-right">
                            <div class="flex items-center justify-end gap-1">
                                <a href="{{ route('admin.custom-pc-builds.show', $build->id) }}" 
                                   title="View Specs & Convert to Order" 
                                   class="p-1.5 rounded-lg text-slate-600 dark:text-slate-300 hover:text-indigo-500 hover:bg-slate-100 dark:hover:bg-slate-800 transition">
                                    <i data-lucide="eye" class="w-4 h-4"></i>
                                </a>
                                
                                <a href="{{ route('admin.custom-pc-builds.edit', $build->id) }}" 
                                   title="Edit Build" 
                                   class="p-1.5 rounded-lg text-slate-600 dark:text-slate-300 hover:text-sky-500 hover:bg-slate-100 dark:hover:bg-slate-800 transition">
                                    <i data-lucide="pencil" class="w-4 h-4"></i>
                                </a>

                                <a href="{{ route('admin.custom-pc-builds.quotation', $build->id) }}" 
                                   target="_blank" 
                                   title="Print Spec Sheet / Quotation" 
                                   class="p-1.5 rounded-lg text-slate-600 dark:text-slate-300 hover:text-emerald-500 hover:bg-slate-100 dark:hover:bg-slate-800 transition">
                                    <i data-lucide="printer" class="w-4 h-4"></i>
                                </a>

                                <form method="POST" action="{{ route('admin.custom-pc-builds.duplicate', $build->id) }}" class="inline">
                                    @csrf
                                    <button type="submit" title="Clone / Duplicate Build" class="p-1.5 rounded-lg text-slate-600 dark:text-slate-300 hover:text-purple-500 hover:bg-slate-100 dark:hover:bg-slate-800 transition">
                                        <i data-lucide="copy" class="w-4 h-4"></i>
                                    </button>
                                </form>

                                <form method="POST" action="{{ route('admin.custom-pc-builds.destroy', $build->id) }}" onsubmit="return confirm('Are you sure you want to delete this custom PC configuration?');" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" title="Delete Build" class="p-1.5 rounded-lg text-slate-400 hover:text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-950/40 transition">
                                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-slate-400">
                            <div class="flex flex-col items-center justify-center gap-2">
                                <i data-lucide="cpu" class="w-8 h-8 text-slate-300 dark:text-slate-700"></i>
                                <p class="text-sm font-semibold text-slate-600 dark:text-slate-300">No Custom PC builds found.</p>
                                <p class="text-xs text-slate-400">Click "+ Build New Custom PC" to configure your first ready PC rig.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-slate-200 dark:border-slate-800">
            {{ $builds->links() }}
        </div>
    </div>

</div>
@endsection
