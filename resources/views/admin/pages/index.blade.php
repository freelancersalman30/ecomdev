@extends('layouts.admin')

@section('title', 'Custom Pages & Policies Management')

@section('content')
<div class="space-y-6">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white dark:bg-slate-800 p-6 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-xs">
        <div>
            <h1 class="text-xl font-black text-slate-900 dark:text-white flex items-center gap-2">
                <i data-lucide="file-text" class="w-6 h-6 text-emerald-500"></i>
                <span>Custom Pages & Policy Builder</span>
            </h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                Create and manage custom legal, policy, and information pages. Link them dynamically to the Top Menu or Footer.
            </p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.pages.create') }}" class="px-4 py-2.5 rounded-xl bg-emerald-500 hover:bg-emerald-600 text-white font-bold text-xs flex items-center gap-2 shadow-lg shadow-emerald-500/20 transition transform active:scale-95">
                <i data-lucide="plus" class="w-4 h-4"></i>
                <span>Create New Page</span>
            </a>
        </div>
    </div>

    <!-- Stats Row -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="p-4 rounded-2xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700">
            <div class="flex items-center justify-between">
                <span class="text-xs text-slate-500 dark:text-slate-400 font-semibold">Total Pages</span>
                <div class="p-2 rounded-xl bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-200">
                    <i data-lucide="files" class="w-4 h-4"></i>
                </div>
            </div>
            <div class="text-2xl font-black text-slate-900 dark:text-white mt-2">{{ $stats['total'] }}</div>
        </div>

        <div class="p-4 rounded-2xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700">
            <div class="flex items-center justify-between">
                <span class="text-xs text-slate-500 dark:text-slate-400 font-semibold">Active & Published</span>
                <div class="p-2 rounded-xl bg-emerald-500/10 text-emerald-500">
                    <i data-lucide="check-circle" class="w-4 h-4"></i>
                </div>
            </div>
            <div class="text-2xl font-black text-emerald-600 dark:text-emerald-400 mt-2">{{ $stats['active'] }}</div>
        </div>

        <div class="p-4 rounded-2xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700">
            <div class="flex items-center justify-between">
                <span class="text-xs text-slate-500 dark:text-slate-400 font-semibold">In Top Menu</span>
                <div class="p-2 rounded-xl bg-purple-500/10 text-purple-500">
                    <i data-lucide="layout-grid" class="w-4 h-4"></i>
                </div>
            </div>
            <div class="text-2xl font-black text-purple-600 dark:text-purple-400 mt-2">{{ $stats['header'] }}</div>
        </div>

        <div class="p-4 rounded-2xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700">
            <div class="flex items-center justify-between">
                <span class="text-xs text-slate-500 dark:text-slate-400 font-semibold">In Footer Policies</span>
                <div class="p-2 rounded-xl bg-blue-500/10 text-blue-500">
                    <i data-lucide="panel-bottom" class="w-4 h-4"></i>
                </div>
            </div>
            <div class="text-2xl font-black text-blue-600 dark:text-blue-400 mt-2">{{ $stats['footer'] }}</div>
        </div>
    </div>

    <!-- Filters & Search Toolbar -->
    <div class="bg-white dark:bg-slate-800 p-4 rounded-2xl border border-slate-200 dark:border-slate-700">
        <form method="GET" action="{{ route('admin.pages.index') }}" class="flex flex-col sm:flex-row items-center gap-3">
            <div class="relative flex-1 w-full">
                <i data-lucide="search" class="w-4 h-4 text-slate-400 absolute left-3.5 top-3"></i>
                <input 
                    type="text" 
                    name="search" 
                    value="{{ request('search') }}" 
                    placeholder="Search by page title or slug..." 
                    class="w-full pl-10 pr-4 py-2 text-xs rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900 text-slate-900 dark:text-white outline-none focus:ring-2 focus:ring-emerald-500">
            </div>

            <div class="w-full sm:w-48">
                <select name="placement" onchange="this.form.submit()" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900 text-slate-900 dark:text-white outline-none focus:ring-2 focus:ring-emerald-500">
                    <option value="">All Placements</option>
                    <option value="both" {{ request('placement') === 'both' ? 'selected' : '' }}>Top Menu & Footer</option>
                    <option value="header" {{ request('placement') === 'header' ? 'selected' : '' }}>Top Menu Only</option>
                    <option value="footer" {{ request('placement') === 'footer' ? 'selected' : '' }}>Footer Only</option>
                    <option value="none" {{ request('placement') === 'none' ? 'selected' : '' }}>Unlisted (Direct Link)</option>
                </select>
            </div>

            <div class="w-full sm:w-36">
                <select name="status" onchange="this.form.submit()" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900 text-slate-900 dark:text-white outline-none focus:ring-2 focus:ring-emerald-500">
                    <option value="">All Status</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Draft / Inactive</option>
                </select>
            </div>

            @if(request()->anyFilled(['search', 'placement', 'status']))
            <a href="{{ route('admin.pages.index') }}" class="px-3 py-2 text-xs font-semibold text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-900/20 rounded-xl transition">
                Reset
            </a>
            @endif
        </form>
    </div>

    <!-- Pages Table -->
    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 dark:bg-slate-900/50 border-b border-slate-200 dark:border-slate-700 text-[11px] font-black uppercase text-slate-500 dark:text-slate-400 tracking-wider">
                        <th class="py-3.5 px-4">Sort</th>
                        <th class="py-3.5 px-4">Page Title & URL</th>
                        <th class="py-3.5 px-4">Menu Placement</th>
                        <th class="py-3.5 px-4 text-center">Status</th>
                        <th class="py-3.5 px-4">Last Updated</th>
                        <th class="py-3.5 px-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700 text-xs">
                    @forelse($pages as $page)
                    <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-750 transition">
                        
                        <!-- Sort Order -->
                        <td class="py-3 px-4 font-mono font-bold text-slate-400">
                            #{{ $page->sort_order }}
                        </td>

                        <!-- Title & Slug -->
                        <td class="py-3 px-4">
                            <div class="font-bold text-slate-900 dark:text-white text-sm">
                                {{ $page->title }}
                            </div>
                            <div class="flex items-center gap-1.5 mt-0.5 text-[11px] font-mono text-slate-400">
                                <span>/page/{{ $page->slug }}</span>
                                <a href="{{ route('page.show', $page->slug) }}" target="_blank" class="text-emerald-500 hover:text-emerald-600 transition" title="Preview Public Page">
                                    <i data-lucide="external-link" class="w-3 h-3"></i>
                                </a>
                            </div>
                        </td>

                        <!-- Placement Badge -->
                        <td class="py-3 px-4">
                            @if($page->placement === 'both')
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-[11px] font-bold bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20">
                                    <i data-lucide="layers" class="w-3 h-3"></i>
                                    <span>Top Menu & Footer</span>
                                </span>
                            @elseif($page->placement === 'header')
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-[11px] font-bold bg-purple-500/10 text-purple-600 dark:text-purple-400 border border-purple-500/20">
                                    <i data-lucide="navigation" class="w-3 h-3"></i>
                                    <span>Top Menu</span>
                                </span>
                            @elseif($page->placement === 'footer')
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-[11px] font-bold bg-blue-500/10 text-blue-600 dark:text-blue-400 border border-blue-500/20">
                                    <i data-lucide="panel-bottom" class="w-3 h-3"></i>
                                    <span>Footer Policies</span>
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-[11px] font-semibold bg-slate-100 dark:bg-slate-700 text-slate-500 dark:text-slate-400">
                                    <i data-lucide="link" class="w-3 h-3"></i>
                                    <span>Unlisted</span>
                                </span>
                            @endif
                        </td>

                        <!-- Status Toggle -->
                        <td class="py-3 px-4 text-center">
                            <form method="POST" action="{{ route('admin.pages.toggle', $page) }}" class="inline-block">
                                @csrf
                                <button type="submit" class="px-2.5 py-1 rounded-lg text-[11px] font-bold transition flex items-center gap-1 {{ $page->is_active ? 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400' : 'bg-slate-100 dark:bg-slate-700 text-slate-400' }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $page->is_active ? 'bg-emerald-500' : 'bg-slate-400' }}"></span>
                                    <span>{{ $page->is_active ? 'Active' : 'Draft' }}</span>
                                </button>
                            </form>
                        </td>

                        <!-- Updated Date -->
                        <td class="py-3 px-4 text-slate-500 text-[11px]">
                            {{ $page->updated_at->format('M d, Y h:i A') }}
                        </td>

                        <!-- Actions -->
                        <td class="py-3 px-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('page.show', $page->slug) }}" target="_blank" class="p-1.5 rounded-lg bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 hover:bg-emerald-500 hover:text-white transition" title="Preview Public Page">
                                    <i data-lucide="eye" class="w-4 h-4"></i>
                                </a>
                                <a href="{{ route('admin.pages.edit', $page) }}" class="p-1.5 rounded-lg bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-600 transition" title="Edit Page">
                                    <i data-lucide="pencil" class="w-4 h-4"></i>
                                </a>
                                <form method="POST" action="{{ route('admin.pages.destroy', $page) }}" onsubmit="return confirm('Are you sure you want to delete this page?');" class="inline-block">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1.5 rounded-lg bg-slate-100 dark:bg-slate-700 text-rose-500 hover:bg-rose-500 hover:text-white transition" title="Delete Page">
                                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                                    </button>
                                </form>
                            </div>
                        </td>

                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-12 text-center text-slate-400">
                            <div class="w-16 h-16 rounded-2xl bg-slate-100 dark:bg-slate-700 flex items-center justify-center mx-auto mb-3">
                                <i data-lucide="file-question" class="w-8 h-8 text-slate-400"></i>
                            </div>
                            <h3 class="font-bold text-slate-700 dark:text-slate-300 text-sm">No custom pages found</h3>
                            <p class="text-xs text-slate-400 mt-1 max-w-sm mx-auto">Get started by creating policy and custom information pages for your customers.</p>
                            <a href="{{ route('admin.pages.create') }}" class="inline-flex items-center gap-2 mt-4 px-4 py-2 rounded-xl bg-emerald-500 text-white font-bold text-xs shadow-md">
                                <i data-lucide="plus" class="w-4 h-4"></i>
                                <span>Create First Page</span>
                            </a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($pages->hasPages())
        <div class="p-4 border-t border-slate-200 dark:border-slate-700">
            {{ $pages->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
