@extends('layouts.admin')

@section('title', '3-Tier Category Management')
@section('page-title', 'Product Category Architecture')

@push('styles')
<style>
    [x-cloak] { display: none !important; }
</style>
@endpush

@section('content')
<div class="space-y-6" x-data="categoryApp()" x-cloak>

    <!-- Flash Alerts -->
    @if(session('success'))
    <div class="p-4 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-600 dark:text-emerald-400 text-xs font-bold flex items-center justify-between shadow-xs">
        <div class="flex items-center gap-2">
            <i data-lucide="check-circle" class="w-4 h-4 text-emerald-500"></i>
            <span>{{ session('success') }}</span>
        </div>
        <button type="button" onclick="this.parentElement.remove()" class="text-slate-400 hover:text-slate-600">&times;</button>
    </div>
    @endif

    <!-- Top KPI & Overview Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="p-4 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-xs flex items-center gap-3.5">
            <div class="w-11 h-11 rounded-xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center font-bold">
                <i data-lucide="layers" class="w-5 h-5"></i>
            </div>
            <div>
                <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Primary Categories</div>
                <div class="text-xl font-black text-slate-900 dark:text-white">{{ $totalCategoriesCount }}</div>
            </div>
        </div>

        <div class="p-4 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-xs flex items-center gap-3.5">
            <div class="w-11 h-11 rounded-xl bg-emerald-500/15 text-emerald-600 dark:text-emerald-400 flex items-center justify-center font-bold">
                <i data-lucide="check-circle-2" class="w-5 h-5"></i>
            </div>
            <div>
                <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Active Categories</div>
                <div class="text-xl font-black text-emerald-600 dark:text-emerald-400">{{ $activeCategoriesCount }}</div>
            </div>
        </div>

        <div class="p-4 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-xs flex items-center gap-3.5">
            <div class="w-11 h-11 rounded-xl bg-rose-500/10 text-rose-600 dark:text-rose-400 flex items-center justify-center font-bold">
                <i data-lucide="eye-off" class="w-5 h-5"></i>
            </div>
            <div>
                <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Inactive / Hidden</div>
                <div class="text-xl font-black text-rose-600 dark:text-rose-400">{{ $inactiveCategoriesCount }}</div>
            </div>
        </div>

        <div class="p-4 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-xs flex items-center gap-3.5">
            <div class="w-11 h-11 rounded-xl bg-sky-500/10 text-sky-600 dark:text-sky-400 flex items-center justify-center font-bold">
                <i data-lucide="git-branch" class="w-5 h-5"></i>
            </div>
            <div>
                <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Sub-Categories</div>
                <div class="text-xl font-black text-slate-900 dark:text-white">{{ $subCategories->count() }}</div>
            </div>
        </div>
    </div>

    <!-- Main Workspace with Tabs -->
    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-xs overflow-hidden">
        
        <!-- Tab Bar & Actions -->
        <div class="p-4 sm:p-5 border-b border-slate-100 dark:border-slate-800 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            
            <div class="flex items-center gap-2 p-1 rounded-2xl bg-slate-100 dark:bg-slate-800 w-fit">
                <button 
                    type="button" 
                    @click="activeTab = 'catalog'" 
                    :class="activeTab === 'catalog' ? 'bg-white dark:bg-slate-900 text-slate-900 dark:text-white shadow-xs font-bold' : 'text-slate-500 hover:text-slate-800 dark:hover:text-slate-200 font-semibold'"
                    class="px-4 py-2 rounded-xl text-xs flex items-center gap-2 transition">
                    <i data-lucide="folder-tree" class="w-4 h-4 text-emerald-500"></i>
                    <span>Category Explorer (Tree View)</span>
                </button>

                <button 
                    type="button" 
                    @click="activeTab = 'create'" 
                    :class="activeTab === 'create' ? 'bg-white dark:bg-slate-900 text-slate-900 dark:text-white shadow-xs font-bold' : 'text-slate-500 hover:text-slate-800 dark:hover:text-slate-200 font-semibold'"
                    class="px-4 py-2 rounded-xl text-xs flex items-center gap-2 transition">
                    <i data-lucide="plus-circle" class="w-4 h-4 text-sky-500"></i>
                    <span>Add New Category</span>
                </button>
            </div>

            <!-- Search Filter (Active on catalog view) -->
            <div x-cloak x-show="activeTab === 'catalog'" class="relative w-full sm:w-64">
                <i data-lucide="search" class="w-3.5 h-3.5 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2"></i>
                <input 
                    type="text" 
                    x-model="searchQuery" 
                    placeholder="Search category name..." 
                    class="w-full pl-9 pr-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white outline-none focus:ring-2 focus:ring-emerald-500">
            </div>

        </div>

        <!-- TAB 1: CATEGORY EXPLORER & TREE CRUD -->
        <div x-cloak x-show="activeTab === 'catalog'" class="p-4 sm:p-6 space-y-4">
            
            <div class="text-xs text-slate-500 flex items-center justify-between">
                <span>Click the status badge to <strong>Toggle Active / Inactive</strong> instantly. Click <strong>Edit</strong> for full settings.</span>
                <span class="font-mono text-[11px]">{{ $categories->count() }} Categories Loaded</span>
            </div>

            <div class="space-y-4">
                @forelse($categories as $category)
                <div 
                    x-show="matchesSearch('{{ addslashes(strtolower($category->name)) }}')"
                    class="rounded-2xl border transition duration-200 {{ $category->is_active ? 'border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900' : 'border-rose-200 dark:border-rose-950/60 bg-rose-50/20 dark:bg-rose-950/10' }}">
                    
                    <!-- Primary Category Header Bar -->
                    <div class="p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-100 dark:border-slate-800/80">
                        
                        <!-- Left: Info & Icon -->
                        <div class="flex items-center gap-3.5 min-w-0">
                            <div class="w-10 h-10 rounded-2xl flex items-center justify-center font-bold flex-shrink-0 {{ $category->is_active ? 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400' : 'bg-slate-100 dark:bg-slate-800 text-slate-400' }}">
                                <i data-lucide="{{ $category->icon ?: 'folder' }}" class="w-5 h-5"></i>
                            </div>

                            <div class="min-w-0">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <h4 class="font-bold text-sm text-slate-900 dark:text-white truncate">{{ $category->name }}</h4>
                                    
                                    <!-- 1-Click Instant Active/Inactive Toggle Button -->
                                    <form action="{{ route('admin.categories.toggle', $category->id) }}" method="POST" class="inline">
                                        @csrf
                                        <button 
                                            type="submit" 
                                            title="Click to toggle Active / Inactive" 
                                            class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-extrabold cursor-pointer transition {{ $category->is_active ? 'bg-emerald-100 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-300 hover:bg-emerald-200' : 'bg-rose-100 dark:bg-rose-950 text-rose-700 dark:text-rose-300 hover:bg-rose-200' }}">
                                            <span class="w-1.5 h-1.5 rounded-full {{ $category->is_active ? 'bg-emerald-500' : 'bg-rose-500' }}"></span>
                                            <span>{{ $category->is_active ? 'Active (Live)' : 'Inactive (Hidden)' }}</span>
                                        </button>
                                    </form>

                                    @if($category->is_featured)
                                    <span class="px-2 py-0.5 rounded-full bg-amber-100 dark:bg-amber-950 text-amber-700 dark:text-amber-300 text-[10px] font-bold">Featured</span>
                                    @endif

                                    <span class="text-[11px] text-slate-400 font-mono">Order: {{ $category->display_order ?? 0 }}</span>
                                </div>

                                <div class="text-[11px] text-slate-400 font-mono mt-0.5 truncate">
                                    /category/{{ $category->slug }} &bull; {{ $category->products_count }} Direct Products &bull; {{ $category->subCategories->count() }} Sub-categories
                                </div>
                            </div>
                        </div>

                        <!-- Right Actions -->
                        <div class="flex items-center gap-2 self-end sm:self-auto flex-shrink-0">
                            <button 
                                type="button" 
                                @click="openCategoryEdit({{ json_encode($category) }})" 
                                class="px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-emerald-50 hover:text-emerald-700 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 text-xs font-bold flex items-center gap-1.5 transition">
                                <i data-lucide="edit-3" class="w-3.5 h-3.5"></i>
                                <span>Edit</span>
                            </button>

                            <form action="{{ route('admin.categories.destroy', $category->id) }}" method="POST" onsubmit="return confirm('Delete category \'{{ addslashes($category->name) }}\' and all nested sub-categories?');" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="px-3 py-1.5 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-600 dark:bg-rose-950/40 dark:hover:bg-rose-950 text-xs font-bold flex items-center gap-1.5 transition">
                                    <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                    <span>Delete</span>
                                </button>
                            </form>
                        </div>

                    </div>

                    <!-- Sub-Categories Hierarchy -->
                    @if($category->subCategories->count() > 0)
                    <div class="p-4 bg-slate-50/50 dark:bg-slate-900/50 space-y-2.5">
                        <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider px-2">Sub-Categories & Child-Categories</div>

                        @foreach($category->subCategories as $subCat)
                        <div class="p-3 rounded-2xl bg-white dark:bg-slate-800/80 border border-slate-200/80 dark:border-slate-700/60 shadow-xs space-y-2.5">
                            
                            <!-- Sub-category Bar -->
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                                <div class="flex items-center gap-2.5 min-w-0">
                                    <span class="w-2.5 h-2.5 rounded-full {{ $subCat->is_active ? 'bg-sky-500' : 'bg-slate-300' }} flex-shrink-0"></span>
                                    <span class="font-bold text-xs text-slate-900 dark:text-white truncate">{{ $subCat->name }}</span>
                                    <span class="text-[10px] text-slate-400 font-mono">({{ $subCat->products_count }} prods)</span>
                                    
                                    <!-- Instant SubCategory Status Toggle -->
                                    <form action="{{ route('admin.subcategories.toggle', $subCat->id) }}" method="POST" class="inline">
                                        @csrf
                                        <button 
                                            type="submit" 
                                            class="px-2 py-0.2 rounded-full text-[9px] font-bold transition {{ $subCat->is_active ? 'bg-sky-100 text-sky-700 hover:bg-sky-200' : 'bg-rose-100 text-rose-700 hover:bg-rose-200' }}">
                                            {{ $subCat->is_active ? 'Active' : 'Inactive' }}
                                        </button>
                                    </form>
                                </div>

                                <div class="flex items-center gap-2 self-end sm:self-auto">
                                    <button 
                                        type="button" 
                                        @click="openSubCategoryEdit({{ json_encode($subCat) }})" 
                                        class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-sky-50 text-slate-700 dark:bg-slate-700 dark:text-slate-200 text-[11px] font-bold flex items-center gap-1 transition">
                                        <i data-lucide="edit-2" class="w-3 h-3"></i>
                                        <span>Edit</span>
                                    </button>

                                    <form action="{{ route('admin.subcategories.destroy', $subCat->id) }}" method="POST" onsubmit="return confirm('Delete sub-category \'{{ addslashes($subCat->name) }}\'?');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1 rounded-lg text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-950/40 text-[11px] font-semibold transition" title="Delete Sub-category">
                                            <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>

                            <!-- Child-Categories Pills & Actions -->
                            @if($subCat->childCategories->count() > 0)
                            <div class="flex flex-wrap gap-2 pt-2 border-t border-slate-100 dark:border-slate-700/50 pl-3">
                                @foreach($subCat->childCategories as $child)
                                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-xl bg-purple-50 dark:bg-purple-950/40 border border-purple-200 dark:border-purple-800/60 text-xs">
                                    <span class="font-semibold text-purple-800 dark:text-purple-200">&bull; {{ $child->name }}</span>
                                    <span class="text-[10px] text-slate-400 font-mono">({{ $child->products_count }})</span>
                                    
                                    <!-- Child Status Toggle -->
                                    <form action="{{ route('admin.childcategories.toggle', $child->id) }}" method="POST" class="inline">
                                        @csrf
                                        <button 
                                            type="submit" 
                                            class="px-1.5 py-0.2 rounded text-[9px] font-bold {{ $child->is_active ? 'bg-purple-200 text-purple-800' : 'bg-rose-200 text-rose-800' }}">
                                            {{ $child->is_active ? 'Active' : 'Inactive' }}
                                        </button>
                                    </form>

                                    <button 
                                        type="button" 
                                        @click="openChildCategoryEdit({{ json_encode($child) }})" 
                                        class="text-purple-600 hover:text-purple-800 dark:hover:text-purple-200 ml-1" 
                                        title="Edit Child-Category">
                                        <i data-lucide="edit-2" class="w-3 h-3"></i>
                                    </button>

                                    <form action="{{ route('admin.childcategories.destroy', $child->id) }}" method="POST" onsubmit="return confirm('Delete child-category \'{{ addslashes($child->name) }}\'?');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-rose-500 hover:text-rose-700" title="Delete">
                                            <i data-lucide="trash-2" class="w-3 h-3"></i>
                                        </button>
                                    </form>
                                </div>
                                @endforeach
                            </div>
                            @endif

                        </div>
                        @endforeach
                    </div>
                    @endif

                </div>
                @empty
                <div class="p-12 text-center text-slate-400 text-xs border-2 border-dashed border-slate-200 dark:border-slate-800 rounded-2xl space-y-2">
                    <i data-lucide="folder-x" class="w-8 h-8 mx-auto text-slate-300"></i>
                    <p class="font-bold">No categories in the catalog.</p>
                </div>
                @endforelse
            </div>

        </div>

        <!-- TAB 2: CREATION WORKSPACE (3-TIER COLUMNS) -->
        <div x-cloak x-show="activeTab === 'create'" class="p-4 sm:p-6 space-y-6">
            
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                <!-- 1. Primary Category Form -->
                <div class="bg-slate-50/50 dark:bg-slate-800/40 rounded-2xl p-5 border border-slate-200 dark:border-slate-700/60 shadow-xs space-y-4">
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                        <span class="w-6 h-6 rounded-full bg-emerald-500 text-white flex items-center justify-center text-xs font-bold shadow-xs">1</span>
                        <span>Primary Category (Level 1)</span>
                    </h3>
                    
                    <form method="POST" action="{{ route('admin.categories.store') }}" class="space-y-3">
                        @csrf
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Category Name *</label>
                            <input type="text" name="name" required placeholder="e.g. Microcontrollers & Dev Boards" class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs outline-none focus:ring-2 focus:ring-emerald-500">
                        </div>
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Lucide Icon</label>
                                <input type="text" name="icon" placeholder="cpu, wrench, activity" class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs outline-none font-mono">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Display Order</label>
                                <input type="number" name="display_order" value="0" min="0" class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs outline-none">
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Description</label>
                            <textarea name="description" rows="2" placeholder="Brief category description..." class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs outline-none"></textarea>
                        </div>
                        <div class="flex items-center gap-4 text-xs pt-1">
                            <label class="flex items-center gap-1.5 cursor-pointer select-none">
                                <input type="checkbox" name="is_featured" value="1" class="rounded text-emerald-600 focus:ring-emerald-500">
                                <span class="text-slate-700 dark:text-slate-300 font-semibold">Featured</span>
                            </label>
                            <label class="flex items-center gap-1.5 cursor-pointer select-none">
                                <input type="checkbox" name="is_active" value="1" checked class="rounded text-emerald-600 focus:ring-emerald-500">
                                <span class="text-slate-700 dark:text-slate-300 font-semibold">Active Status</span>
                            </label>
                        </div>
                        <button type="submit" class="w-full py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl text-xs font-bold shadow-xs transition flex items-center justify-center gap-1.5">
                            <i data-lucide="plus" class="w-4 h-4"></i>
                            <span>Create Primary Category</span>
                        </button>
                    </form>
                </div>

                <!-- 2. Sub-Category Form -->
                <div class="bg-slate-50/50 dark:bg-slate-800/40 rounded-2xl p-5 border border-slate-200 dark:border-slate-700/60 shadow-xs space-y-4">
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                        <span class="w-6 h-6 rounded-full bg-sky-500 text-white flex items-center justify-center text-xs font-bold shadow-xs">2</span>
                        <span>Sub-Category (Level 2)</span>
                    </h3>
                    
                    <form method="POST" action="{{ route('admin.subcategories.store') }}" class="space-y-3">
                        @csrf
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Select Parent Category *</label>
                            <select name="category_id" required class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs outline-none font-semibold">
                                <option value="">Select Parent Category</option>
                                @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Sub-Category Name *</label>
                            <input type="text" name="name" required placeholder="e.g. ESP32 & IoT Boards" class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs outline-none focus:ring-2 focus:ring-sky-500">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Description</label>
                            <textarea name="description" rows="2" placeholder="Brief sub-category description..." class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs outline-none"></textarea>
                        </div>
                        <div class="flex items-center gap-4 text-xs pt-1">
                            <label class="flex items-center gap-1.5 cursor-pointer select-none">
                                <input type="checkbox" name="is_active" value="1" checked class="rounded text-sky-600 focus:ring-sky-500">
                                <span class="text-slate-700 dark:text-slate-300 font-semibold">Active Status</span>
                            </label>
                        </div>
                        <button type="submit" class="w-full py-2.5 bg-sky-600 hover:bg-sky-500 text-white rounded-xl text-xs font-bold shadow-xs transition flex items-center justify-center gap-1.5">
                            <i data-lucide="plus" class="w-4 h-4"></i>
                            <span>Create Sub-Category</span>
                        </button>
                    </form>
                </div>

                <!-- 3. Child-Category Form -->
                <div class="bg-slate-50/50 dark:bg-slate-800/40 rounded-2xl p-5 border border-slate-200 dark:border-slate-700/60 shadow-xs space-y-4">
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                        <span class="w-6 h-6 rounded-full bg-purple-500 text-white flex items-center justify-center text-xs font-bold shadow-xs">3</span>
                        <span>Child-Category (Level 3)</span>
                    </h3>
                    
                    <form method="POST" action="{{ route('admin.childcategories.store') }}" class="space-y-3">
                        @csrf
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Select Sub-Category *</label>
                            <select name="sub_category_id" required class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs outline-none font-semibold">
                                <option value="">Select Parent Sub-Category</option>
                                @foreach($subCategories as $sub)
                                <option value="{{ $sub->id }}">{{ $sub->category->name ?? '' }} &rarr; {{ $sub->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Child-Category Name *</label>
                            <input type="text" name="name" required placeholder="e.g. ESP32-CAM AI Vision" class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs outline-none focus:ring-2 focus:ring-purple-500">
                        </div>
                        <div class="flex items-center gap-4 text-xs pt-4">
                            <label class="flex items-center gap-1.5 cursor-pointer select-none">
                                <input type="checkbox" name="is_active" value="1" checked class="rounded text-purple-600 focus:ring-purple-500">
                                <span class="text-slate-700 dark:text-slate-300 font-semibold">Active Status</span>
                            </label>
                        </div>
                        <button type="submit" class="w-full py-2.5 bg-purple-600 hover:bg-purple-500 text-white rounded-xl text-xs font-bold shadow-xs transition flex items-center justify-center gap-1.5">
                            <i data-lucide="plus" class="w-4 h-4"></i>
                            <span>Create Child-Category</span>
                        </button>
                    </form>
                </div>

            </div>

        </div>

    </div>

    <!-- 1. EDIT PRIMARY CATEGORY MODAL -->
    <div x-cloak x-show="showCatModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/70 backdrop-blur-xs">
        <div @click.away="showCatModal = false" class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 w-full max-w-md p-6 shadow-2xl space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
                <h3 class="font-bold text-sm text-slate-900 dark:text-white flex items-center gap-2">
                    <i data-lucide="edit" class="w-4 h-4 text-emerald-600"></i>
                    <span>Edit Primary Category</span>
                </h3>
                <button @click="showCatModal = false" class="text-slate-400 hover:text-slate-600 text-lg">&times;</button>
            </div>

            <form :action="'/admin/categories/' + editCat.id" method="POST" class="space-y-3">
                @csrf
                @method('PUT')
                
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Category Name *</label>
                    <input type="text" name="name" x-model="editCat.name" required class="w-full px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs outline-none focus:ring-2 focus:ring-emerald-500 font-semibold">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Slug / URL Key</label>
                    <input type="text" name="slug" x-model="editCat.slug" class="w-full px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs outline-none font-mono">
                </div>

                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Lucide Icon</label>
                        <input type="text" name="icon" x-model="editCat.icon" class="w-full px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs outline-none font-mono">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Display Order</label>
                        <input type="number" name="display_order" x-model="editCat.display_order" min="0" class="w-full px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs outline-none">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Description</label>
                    <textarea name="description" x-model="editCat.description" rows="2" class="w-full px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs outline-none"></textarea>
                </div>

                <!-- Status & Featured Toggles with Hidden 0 Fallback -->
                <div class="p-3 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 space-y-2">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="text-xs font-bold text-slate-900 dark:text-white">Active Status</div>
                            <div class="text-[10px] text-slate-400">Controls visibility on storefront navbar and catalog</div>
                        </div>
                        <input type="hidden" name="is_active" value="0">
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="is_active" value="1" x-model="editCat.is_active" class="sr-only peer">
                            <div class="w-9 h-5 bg-slate-200 peer-focus:outline-none rounded-full peer dark:bg-slate-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-emerald-600"></div>
                        </label>
                    </div>

                    <div class="flex items-center justify-between pt-2 border-t border-slate-200/60 dark:border-slate-700/60">
                        <div>
                            <div class="text-xs font-bold text-slate-900 dark:text-white">Featured Category</div>
                            <div class="text-[10px] text-slate-400">Promote this category on the homepage</div>
                        </div>
                        <input type="hidden" name="is_featured" value="0">
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="is_featured" value="1" x-model="editCat.is_featured" class="sr-only peer">
                            <div class="w-9 h-5 bg-slate-200 peer-focus:outline-none rounded-full peer dark:bg-slate-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-amber-500"></div>
                        </label>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100 dark:border-slate-800">
                    <button type="button" @click="showCatModal = false" class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold">Cancel</button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold shadow-xs">Update Category</button>
                </div>
            </form>
        </div>
    </div>

    <!-- 2. EDIT SUB-CATEGORY MODAL -->
    <div x-cloak x-show="showSubModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/70 backdrop-blur-xs">
        <div @click.away="showSubModal = false" class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 w-full max-w-md p-6 shadow-2xl space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
                <h3 class="font-bold text-sm text-slate-900 dark:text-white flex items-center gap-2">
                    <i data-lucide="edit" class="w-4 h-4 text-sky-600"></i>
                    <span>Edit Sub-Category</span>
                </h3>
                <button @click="showSubModal = false" class="text-slate-400 hover:text-slate-600 text-lg">&times;</button>
            </div>

            <form :action="'/admin/sub-categories/' + editSub.id" method="POST" class="space-y-3">
                @csrf
                @method('PUT')
                
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Parent Primary Category *</label>
                    <select name="category_id" x-model="editSub.category_id" required class="w-full px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs outline-none font-semibold">
                        @foreach($categories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Sub-Category Name *</label>
                    <input type="text" name="name" x-model="editSub.name" required class="w-full px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs outline-none focus:ring-2 focus:ring-sky-500 font-semibold">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Slug / URL Key</label>
                    <input type="text" name="slug" x-model="editSub.slug" class="w-full px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs outline-none font-mono">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Description</label>
                    <textarea name="description" x-model="editSub.description" rows="2" class="w-full px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs outline-none"></textarea>
                </div>

                <div class="p-3 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 flex items-center justify-between">
                    <div>
                        <div class="text-xs font-bold text-slate-900 dark:text-white">Active Status</div>
                        <div class="text-[10px] text-slate-400">Controls visibility in menus and filter lists</div>
                    </div>
                    <input type="hidden" name="is_active" value="0">
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" x-model="editSub.is_active" class="sr-only peer">
                        <div class="w-9 h-5 bg-slate-200 peer-focus:outline-none rounded-full peer dark:bg-slate-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-sky-600"></div>
                    </label>
                </div>

                <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100 dark:border-slate-800">
                    <button type="button" @click="showSubModal = false" class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold">Cancel</button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-sky-600 hover:bg-sky-500 text-white text-xs font-bold shadow-xs">Update Sub-Category</button>
                </div>
            </form>
        </div>
    </div>

    <!-- 3. EDIT CHILD-CATEGORY MODAL -->
    <div x-cloak x-show="showChildModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/70 backdrop-blur-xs">
        <div @click.away="showChildModal = false" class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 w-full max-w-md p-6 shadow-2xl space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
                <h3 class="font-bold text-sm text-slate-900 dark:text-white flex items-center gap-2">
                    <i data-lucide="edit" class="w-4 h-4 text-purple-600"></i>
                    <span>Edit Child-Category</span>
                </h3>
                <button @click="showChildModal = false" class="text-slate-400 hover:text-slate-600 text-lg">&times;</button>
            </div>

            <form :action="'/admin/child-categories/' + editChild.id" method="POST" class="space-y-3">
                @csrf
                @method('PUT')
                
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Parent Sub-Category *</label>
                    <select name="sub_category_id" x-model="editChild.sub_category_id" required class="w-full px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs outline-none font-semibold">
                        @foreach($subCategories as $sub)
                        <option value="{{ $sub->id }}">{{ $sub->category->name ?? '' }} &rarr; {{ $sub->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Child-Category Name *</label>
                    <input type="text" name="name" x-model="editChild.name" required class="w-full px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs outline-none focus:ring-2 focus:ring-purple-500 font-semibold">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Slug / URL Key</label>
                    <input type="text" name="slug" x-model="editChild.slug" class="w-full px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs outline-none font-mono">
                </div>

                <div class="p-3 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 flex items-center justify-between">
                    <div>
                        <div class="text-xs font-bold text-slate-900 dark:text-white">Active Status</div>
                        <div class="text-[10px] text-slate-400">Controls visibility in child chips</div>
                    </div>
                    <input type="hidden" name="is_active" value="0">
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" x-model="editChild.is_active" class="sr-only peer">
                        <div class="w-9 h-5 bg-slate-200 peer-focus:outline-none rounded-full peer dark:bg-slate-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-purple-600"></div>
                    </label>
                </div>

                <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100 dark:border-slate-800">
                    <button type="button" @click="showChildModal = false" class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold">Cancel</button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-purple-600 hover:bg-purple-500 text-white text-xs font-bold shadow-xs">Update Child-Category</button>
                </div>
            </form>
        </div>
    </div>

</div>

<script>
function categoryApp() {
    return {
        activeTab: 'catalog',
        searchQuery: '',
        showCatModal: false,
        showSubModal: false,
        showChildModal: false,
        editCat: {},
        editSub: {},
        editChild: {},

        matchesSearch(catName) {
            if (!this.searchQuery.trim()) return true;
            return catName.includes(this.searchQuery.toLowerCase().trim());
        },

        openCategoryEdit(cat) {
            this.editCat = {
                id: cat.id,
                name: cat.name,
                slug: cat.slug,
                icon: cat.icon || 'cpu',
                display_order: cat.display_order || 0,
                description: cat.description || '',
                is_active: Boolean(cat.is_active),
                is_featured: Boolean(cat.is_featured)
            };
            this.showCatModal = true;
            this.$nextTick(() => { if (typeof lucide !== 'undefined') lucide.createIcons(); });
        },

        openSubCategoryEdit(sub) {
            this.editSub = {
                id: sub.id,
                category_id: sub.category_id,
                name: sub.name,
                slug: sub.slug,
                description: sub.description || '',
                is_active: Boolean(sub.is_active)
            };
            this.showSubModal = true;
            this.$nextTick(() => { if (typeof lucide !== 'undefined') lucide.createIcons(); });
        },

        openChildCategoryEdit(child) {
            this.editChild = {
                id: child.id,
                sub_category_id: child.sub_category_id,
                name: child.name,
                slug: child.slug,
                is_active: Boolean(child.is_active)
            };
            this.showChildModal = true;
            this.$nextTick(() => { if (typeof lucide !== 'undefined') lucide.createIcons(); });
        }
    };
}
</script>
@endsection
