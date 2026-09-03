@extends('layouts.admin')

@section('title', '3-Tier Category Management & CRUD')
@section('page-title', 'Categories, Sub-Categories & Child-Categories CRUD')

@section('content')
<div class="space-y-6" x-data="categoryCrudApp()">

    @if(session('success'))
    <div class="p-4 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-600 dark:text-emerald-400 text-xs font-bold flex items-center gap-2">
        <i data-lucide="check-circle" class="w-4 h-4"></i>
        <span>{{ session('success') }}</span>
    </div>
    @endif

    <!-- 3-Column Creation Panels -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- 1. Primary Category Form -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl p-5 border border-slate-200 dark:border-slate-800 shadow-xs space-y-4">
            <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                <span class="w-6 h-6 rounded-full bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-xs font-bold">1</span>
                <span>Add Primary Category</span>
            </h3>
            
            <form method="POST" action="{{ route('admin.categories.store') }}" class="space-y-3">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Category Name *</label>
                    <input type="text" name="name" required placeholder="e.g. Microcontrollers & Dev Boards" class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs outline-none focus:ring-2 focus:ring-emerald-500">
                </div>
                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Lucide Icon</label>
                        <input type="text" name="icon" placeholder="cpu, wrench, activity" class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs outline-none font-mono">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Order</label>
                        <input type="number" name="display_order" value="0" min="0" class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs outline-none">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Description</label>
                    <textarea name="description" rows="2" placeholder="Brief category description..." class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs outline-none"></textarea>
                </div>
                <div class="flex items-center gap-4 text-xs">
                    <label class="flex items-center gap-1.5 cursor-pointer">
                        <input type="checkbox" name="is_featured" value="1" class="rounded text-emerald-600 focus:ring-emerald-500">
                        <span class="text-slate-600 dark:text-slate-400 font-medium">Featured</span>
                    </label>
                    <label class="flex items-center gap-1.5 cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" checked class="rounded text-emerald-600 focus:ring-emerald-500">
                        <span class="text-slate-600 dark:text-slate-400 font-medium">Active</span>
                    </label>
                </div>
                <button type="submit" class="w-full py-2 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl text-xs font-bold shadow-xs transition flex items-center justify-center gap-1.5">
                    <i data-lucide="plus" class="w-4 h-4"></i>
                    <span>Create Primary Category</span>
                </button>
            </form>
        </div>

        <!-- 2. Sub-Category Form -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl p-5 border border-slate-200 dark:border-slate-800 shadow-xs space-y-4">
            <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                <span class="w-6 h-6 rounded-full bg-sky-500/10 text-sky-600 dark:text-sky-400 flex items-center justify-center text-xs font-bold">2</span>
                <span>Add Sub-Category</span>
            </h3>
            
            <form method="POST" action="{{ route('admin.subcategories.store') }}" class="space-y-3">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Select Parent Category *</label>
                    <select name="category_id" required class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs outline-none">
                        <option value="">Select Parent Category</option>
                        @foreach($categories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Sub-Category Name *</label>
                    <input type="text" name="name" required placeholder="e.g. ESP32 & IoT Boards" class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs outline-none focus:ring-2 focus:ring-sky-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Description</label>
                    <textarea name="description" rows="2" placeholder="Brief sub-category description..." class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs outline-none"></textarea>
                </div>
                <div class="flex items-center gap-4 text-xs">
                    <label class="flex items-center gap-1.5 cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" checked class="rounded text-sky-600 focus:ring-sky-500">
                        <span class="text-slate-600 dark:text-slate-400 font-medium">Active Status</span>
                    </label>
                </div>
                <button type="submit" class="w-full py-2 bg-sky-600 hover:bg-sky-500 text-white rounded-xl text-xs font-bold shadow-xs transition flex items-center justify-center gap-1.5">
                    <i data-lucide="plus" class="w-4 h-4"></i>
                    <span>Create Sub-Category</span>
                </button>
            </form>
        </div>

        <!-- 3. Child-Category Form -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl p-5 border border-slate-200 dark:border-slate-800 shadow-xs space-y-4">
            <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                <span class="w-6 h-6 rounded-full bg-purple-500/10 text-purple-600 dark:text-purple-400 flex items-center justify-center text-xs font-bold">3</span>
                <span>Add Child-Category</span>
            </h3>
            
            <form method="POST" action="{{ route('admin.childcategories.store') }}" class="space-y-3">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Select Sub-Category *</label>
                    <select name="sub_category_id" required class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs outline-none">
                        <option value="">Select Parent Sub-Category</option>
                        @foreach($subCategories as $sub)
                        <option value="{{ $sub->id }}">{{ $sub->category->name ?? 'Category' }} &rarr; {{ $sub->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Child-Category Name *</label>
                    <input type="text" name="name" required placeholder="e.g. ESP32-CAM AI Vision" class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs outline-none focus:ring-2 focus:ring-purple-500">
                </div>
                <div class="flex items-center gap-4 text-xs pt-4">
                    <label class="flex items-center gap-1.5 cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" checked class="rounded text-purple-600 focus:ring-purple-500">
                        <span class="text-slate-600 dark:text-slate-400 font-medium">Active Status</span>
                    </label>
                </div>
                <button type="submit" class="w-full py-2 bg-purple-600 hover:bg-purple-500 text-white rounded-xl text-xs font-bold shadow-xs transition flex items-center justify-center gap-1.5">
                    <i data-lucide="plus" class="w-4 h-4"></i>
                    <span>Create Child-Category</span>
                </button>
            </form>
        </div>

    </div>

    <!-- Complete Category Hierarchy Tree & CRUD Table -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-xs overflow-hidden">
        <div class="p-4 border-b border-slate-200 dark:border-slate-800 font-bold text-sm text-slate-900 dark:text-white flex items-center justify-between">
            <div class="flex items-center gap-2">
                <i data-lucide="layers" class="w-4 h-4 text-emerald-600"></i>
                <span>Complete Category Catalog Tree ({{ $categories->count() }} Categories)</span>
            </div>
            <span class="text-xs text-slate-400 font-normal">Click Edit or Delete on any row</span>
        </div>

        <div class="divide-y divide-slate-100 dark:divide-slate-800">
            @forelse($categories as $category)
            <div class="p-4 space-y-3">
                
                <!-- Primary Category Row -->
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 p-3 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700">
                    <div class="flex items-center gap-3 min-w-0">
                        <div class="w-10 h-10 rounded-xl bg-emerald-500/15 text-emerald-600 dark:text-emerald-400 flex items-center justify-center font-bold flex-shrink-0">
                            <i data-lucide="{{ $category->icon ?: 'folder' }}" class="w-5 h-5"></i>
                        </div>
                        <div class="min-w-0">
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="font-bold text-sm text-slate-900 dark:text-white">{{ $category->name }}</span>
                                @if($category->is_active)
                                <span class="px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-700 text-[10px] font-bold">Active</span>
                                @else
                                <span class="px-2 py-0.5 rounded-full bg-rose-100 text-rose-700 text-[10px] font-bold">Inactive</span>
                                @endif

                                @if($category->is_featured)
                                <span class="px-2 py-0.5 rounded-full bg-amber-100 text-amber-700 text-[10px] font-bold">Featured</span>
                                @endif
                                <span class="text-[11px] text-slate-400 font-mono">Order: {{ $category->display_order ?? 0 }}</span>
                            </div>
                            <div class="text-[11px] text-slate-400 font-mono truncate">
                                Slug: /category/{{ $category->slug }} | {{ $category->products_count }} Direct Products | {{ $category->subCategories->count() }} Sub-categories
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex items-center gap-2 self-end sm:self-auto">
                        <button 
                            type="button" 
                            @click="openCategoryEdit({{ json_encode($category) }})" 
                            class="px-3 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-semibold flex items-center gap-1 transition shadow-xs">
                            <i data-lucide="edit-3" class="w-3.5 h-3.5"></i>
                            <span>Edit</span>
                        </button>

                        <form action="{{ route('admin.categories.destroy', $category->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete category \'{{ addslashes($category->name) }}\' and all its subcategories?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="px-3 py-1.5 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-600 dark:bg-rose-950/40 dark:hover:bg-rose-950 text-xs font-semibold flex items-center gap-1 transition">
                                <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                <span>Delete</span>
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Sub-Categories Hierarchy -->
                @if($category->subCategories->count() > 0)
                <div class="pl-4 sm:pl-8 space-y-2.5 border-l-2 border-emerald-500/20 ml-2 sm:ml-4">
                    @foreach($category->subCategories as $subCat)
                    <div class="p-3 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-xs space-y-2">
                        
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                            <div class="flex items-center gap-2 min-w-0">
                                <span class="w-2 h-2 rounded-full bg-sky-500 flex-shrink-0"></span>
                                <span class="font-bold text-xs text-slate-800 dark:text-slate-200 truncate">{{ $subCat->name }}</span>
                                <span class="text-[10px] text-slate-400 font-mono">({{ $subCat->products_count }} prods)</span>
                                @if($subCat->is_active)
                                <span class="px-1.5 py-0.2 rounded bg-sky-100 text-sky-700 text-[9px] font-bold">Active</span>
                                @else
                                <span class="px-1.5 py-0.2 rounded bg-rose-100 text-rose-700 text-[9px] font-bold">Inactive</span>
                                @endif
                            </div>

                            <div class="flex items-center gap-2 self-end sm:self-auto">
                                <button 
                                    type="button" 
                                    @click="openSubCategoryEdit({{ json_encode($subCat) }})" 
                                    class="px-2.5 py-1 rounded-md bg-sky-600 hover:bg-sky-500 text-white text-[11px] font-semibold flex items-center gap-1 transition">
                                    <i data-lucide="edit-2" class="w-3 h-3"></i>
                                    <span>Edit</span>
                                </button>

                                <form action="{{ route('admin.subcategories.destroy', $subCat->id) }}" method="POST" onsubmit="return confirm('Delete sub-category \'{{ addslashes($subCat->name) }}\'?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-2 py-1 rounded-md text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-950/40 text-[11px] font-semibold transition">
                                        <i data-lucide="trash-2" class="w-3 h-3"></i>
                                    </button>
                                </form>
                            </div>
                        </div>

                        <!-- Child Categories Badges & Actions -->
                        @if($subCat->childCategories->count() > 0)
                        <div class="flex flex-wrap gap-2 pt-1 border-t border-slate-100 dark:border-slate-800">
                            @foreach($subCat->childCategories as $child)
                            <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-purple-50 dark:bg-purple-950/40 border border-purple-200 dark:border-purple-800/60 text-xs">
                                <span class="font-medium text-purple-700 dark:text-purple-300">&bull; {{ $child->name }}</span>
                                <span class="text-[10px] text-slate-400 font-mono">({{ $child->products_count }})</span>
                                
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
            <div class="p-8 text-center text-slate-400 text-xs">
                No categories found. Create your first category above!
            </div>
            @endforelse
        </div>
    </div>

    <!-- 1. EDIT PRIMARY CATEGORY MODAL -->
    <div x-cloak x-show="showCatModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/70 backdrop-blur-xs">
        <div @click.away="showCatModal = false" class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 w-full max-w-md p-6 shadow-2xl space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
                <h3 class="font-bold text-sm text-slate-900 dark:text-white flex items-center gap-2">
                    <i data-lucide="edit" class="w-4 h-4 text-emerald-600"></i>
                    <span>Edit Primary Category</span>
                </h3>
                <button @click="showCatModal = false" class="text-slate-400 hover:text-slate-600">&times;</button>
            </div>

            <form :action="'/admin/categories/' + editCat.id" method="POST" class="space-y-3">
                @csrf
                @method('PUT')
                
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Category Name *</label>
                    <input type="text" name="name" x-model="editCat.name" required class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs outline-none focus:ring-2 focus:ring-emerald-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Slug / URL Key</label>
                    <input type="text" name="slug" x-model="editCat.slug" class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs outline-none font-mono">
                </div>

                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Lucide Icon</label>
                        <input type="text" name="icon" x-model="editCat.icon" class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs outline-none font-mono">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Display Order</label>
                        <input type="number" name="display_order" x-model="editCat.display_order" min="0" class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs outline-none">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Description</label>
                    <textarea name="description" x-model="editCat.description" rows="2" class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs outline-none"></textarea>
                </div>

                <div class="flex items-center gap-4 text-xs pt-1">
                    <label class="flex items-center gap-1.5 cursor-pointer">
                        <input type="checkbox" name="is_featured" value="1" :checked="editCat.is_featured" class="rounded text-emerald-600">
                        <span class="text-slate-600 dark:text-slate-400 font-medium">Featured Category</span>
                    </label>
                    <label class="flex items-center gap-1.5 cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" :checked="editCat.is_active" class="rounded text-emerald-600">
                        <span class="text-slate-600 dark:text-slate-400 font-medium">Active</span>
                    </label>
                </div>

                <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100 dark:border-slate-800">
                    <button type="button" @click="showCatModal = false" class="px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold">Cancel</button>
                    <button type="submit" class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold shadow-xs">Save Changes</button>
                </div>
            </form>
        </div>
    </div>

    <!-- 2. EDIT SUB-CATEGORY MODAL -->
    <div x-cloak x-show="showSubModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/70 backdrop-blur-xs">
        <div @click.away="showSubModal = false" class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 w-full max-w-md p-6 shadow-2xl space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
                <h3 class="font-bold text-sm text-slate-900 dark:text-white flex items-center gap-2">
                    <i data-lucide="edit" class="w-4 h-4 text-sky-600"></i>
                    <span>Edit Sub-Category</span>
                </h3>
                <button @click="showSubModal = false" class="text-slate-400 hover:text-slate-600">&times;</button>
            </div>

            <form :action="'/admin/sub-categories/' + editSub.id" method="POST" class="space-y-3">
                @csrf
                @method('PUT')
                
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Parent Category *</label>
                    <select name="category_id" x-model="editSub.category_id" required class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs outline-none">
                        @foreach($categories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Sub-Category Name *</label>
                    <input type="text" name="name" x-model="editSub.name" required class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs outline-none focus:ring-2 focus:ring-sky-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Slug / URL Key</label>
                    <input type="text" name="slug" x-model="editSub.slug" class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs outline-none font-mono">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Description</label>
                    <textarea name="description" x-model="editSub.description" rows="2" class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs outline-none"></textarea>
                </div>

                <div class="flex items-center gap-4 text-xs pt-1">
                    <label class="flex items-center gap-1.5 cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" :checked="editSub.is_active" class="rounded text-sky-600">
                        <span class="text-slate-600 dark:text-slate-400 font-medium">Active Status</span>
                    </label>
                </div>

                <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100 dark:border-slate-800">
                    <button type="button" @click="showSubModal = false" class="px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold">Cancel</button>
                    <button type="submit" class="px-4 py-2 rounded-xl bg-sky-600 hover:bg-sky-500 text-white text-xs font-bold shadow-xs">Save Changes</button>
                </div>
            </form>
        </div>
    </div>

    <!-- 3. EDIT CHILD-CATEGORY MODAL -->
    <div x-cloak x-show="showChildModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/70 backdrop-blur-xs">
        <div @click.away="showChildModal = false" class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 w-full max-w-md p-6 shadow-2xl space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
                <h3 class="font-bold text-sm text-slate-900 dark:text-white flex items-center gap-2">
                    <i data-lucide="edit" class="w-4 h-4 text-purple-600"></i>
                    <span>Edit Child-Category</span>
                </h3>
                <button @click="showChildModal = false" class="text-slate-400 hover:text-slate-600">&times;</button>
            </div>

            <form :action="'/admin/child-categories/' + editChild.id" method="POST" class="space-y-3">
                @csrf
                @method('PUT')
                
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Parent Sub-Category *</label>
                    <select name="sub_category_id" x-model="editChild.sub_category_id" required class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs outline-none">
                        @foreach($subCategories as $sub)
                        <option value="{{ $sub->id }}">{{ $sub->category->name ?? '' }} &rarr; {{ $sub->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Child-Category Name *</label>
                    <input type="text" name="name" x-model="editChild.name" required class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs outline-none focus:ring-2 focus:ring-purple-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Slug / URL Key</label>
                    <input type="text" name="slug" x-model="editChild.slug" class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs outline-none font-mono">
                </div>

                <div class="flex items-center gap-4 text-xs pt-1">
                    <label class="flex items-center gap-1.5 cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" :checked="editChild.is_active" class="rounded text-purple-600">
                        <span class="text-slate-600 dark:text-slate-400 font-medium">Active Status</span>
                    </label>
                </div>

                <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100 dark:border-slate-800">
                    <button type="button" @click="showChildModal = false" class="px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold">Cancel</button>
                    <button type="submit" class="px-4 py-2 rounded-xl bg-purple-600 hover:bg-purple-500 text-white text-xs font-bold shadow-xs">Save Changes</button>
                </div>
            </form>
        </div>
    </div>

</div>

<script>
function categoryCrudApp() {
    return {
        showCatModal: false,
        showSubModal: false,
        showChildModal: false,
        editCat: {},
        editSub: {},
        editChild: {},

        openCategoryEdit(cat) {
            this.editCat = { ...cat };
            this.showCatModal = true;
            this.$nextTick(() => { if (typeof lucide !== 'undefined') lucide.createIcons(); });
        },

        openSubCategoryEdit(sub) {
            this.editSub = { ...sub };
            this.showSubModal = true;
            this.$nextTick(() => { if (typeof lucide !== 'undefined') lucide.createIcons(); });
        },

        openChildCategoryEdit(child) {
            this.editChild = { ...child };
            this.showChildModal = true;
            this.$nextTick(() => { if (typeof lucide !== 'undefined') lucide.createIcons(); });
        }
    };
}
</script>
@endsection
