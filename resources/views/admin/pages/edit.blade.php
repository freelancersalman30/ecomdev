@extends('layouts.admin')

@section('title', 'Edit Page: ' . $page->title)

@section('content')
<div class="max-w-5xl mx-auto space-y-6" x-data="pageEditor()">

    <!-- Header -->
    <div class="flex items-center justify-between bg-white dark:bg-slate-800 p-6 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-xs">
        <div>
            <div class="flex items-center gap-2 text-xs font-bold text-slate-400 mb-1">
                <a href="{{ route('admin.pages.index') }}" class="hover:text-emerald-500 transition">Custom Pages</a>
                <span>/</span>
                <span class="text-slate-900 dark:text-white">Edit Page #{{ $page->id }}</span>
            </div>
            <h1 class="text-xl font-black text-slate-900 dark:text-white">Edit: {{ $page->title }}</h1>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('page.show', $page->slug) }}" target="_blank" class="px-3.5 py-2 rounded-xl bg-emerald-500/10 hover:bg-emerald-500 text-emerald-600 dark:text-emerald-400 hover:text-white text-xs font-bold transition flex items-center gap-1.5">
                <i data-lucide="external-link" class="w-4 h-4"></i>
                <span>Preview Page</span>
            </a>
            <a href="{{ route('admin.pages.index') }}" class="px-3.5 py-2 rounded-xl bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 text-slate-700 dark:text-slate-200 text-xs font-bold transition">
                Back to List
            </a>
        </div>
    </div>

    <!-- Form -->
    <form method="POST" action="{{ route('admin.pages.update', $page) }}" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">

            <!-- Left Main Column (Title, Content, SEO) -->
            <div class="lg:col-span-2 space-y-6">

                <!-- Basic Info Card -->
                <div class="bg-white dark:bg-slate-800 p-6 rounded-2xl border border-slate-200 dark:border-slate-700 space-y-4">
                    <h3 class="font-bold text-sm text-slate-900 dark:text-white border-b border-slate-100 dark:border-slate-700 pb-3 flex items-center gap-2">
                        <i data-lucide="type" class="w-4 h-4 text-emerald-500"></i>
                        <span>Page Title & URL</span>
                    </h3>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Page Title <span class="text-rose-500">*</span></label>
                        <input 
                            type="text" 
                            name="title" 
                            x-model="title" 
                            required 
                            placeholder="e.g. Terms & Warranty Policy" 
                            class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900 text-sm font-semibold text-slate-900 dark:text-white outline-none focus:ring-2 focus:ring-emerald-500">
                        @error('title')
                        <p class="text-xs text-rose-500 mt-1 font-semibold">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Page URL Slug <span class="text-rose-500">*</span></label>
                        <div class="flex items-center">
                            <span class="px-3 py-2.5 bg-slate-100 dark:bg-slate-700 text-slate-500 dark:text-slate-400 text-xs rounded-l-xl border border-r-0 border-slate-200 dark:border-slate-700 font-mono">
                                {{ url('/page') }}/
                            </span>
                            <input 
                                type="text" 
                                name="slug" 
                                x-model="slug" 
                                required 
                                placeholder="terms-and-warranty" 
                                class="w-full px-3 py-2.5 rounded-r-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900 text-xs font-mono font-bold text-slate-900 dark:text-white outline-none focus:ring-2 focus:ring-emerald-500">
                        </div>
                        @error('slug')
                        <p class="text-xs text-rose-500 mt-1 font-semibold">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Content Editor Card -->
                <div class="bg-white dark:bg-slate-800 p-6 rounded-2xl border border-slate-200 dark:border-slate-700 space-y-4">
                    <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-700 pb-3">
                        <h3 class="font-bold text-sm text-slate-900 dark:text-white flex items-center gap-2">
                            <i data-lucide="align-left" class="w-4 h-4 text-emerald-500"></i>
                            <span>Page Body Content</span>
                        </h3>
                        <div class="flex items-center gap-1.5 text-xs text-slate-400">
                            <span>HTML & formatted text supported</span>
                        </div>
                    </div>

                    <!-- Quick HTML formatting buttons -->
                    <div class="flex flex-wrap items-center gap-1.5 p-2 rounded-xl bg-slate-50 dark:bg-slate-900/60 border border-slate-200 dark:border-slate-700">
                        <button type="button" @click="insertTag('<h2>', '</h2>')" class="px-2.5 py-1 rounded-lg bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 text-xs font-bold border border-slate-200 dark:border-slate-700 hover:bg-slate-100" title="Section Heading">H2</button>
                        <button type="button" @click="insertTag('<h3>', '</h3>')" class="px-2.5 py-1 rounded-lg bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 text-xs font-bold border border-slate-200 dark:border-slate-700 hover:bg-slate-100" title="Sub Heading">H3</button>
                        <button type="button" @click="insertTag('<strong>', '</strong>')" class="px-2.5 py-1 rounded-lg bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 text-xs font-bold border border-slate-200 dark:border-slate-700 hover:bg-slate-100" title="Bold Text"><b>B</b></button>
                        <button type="button" @click="insertTag('<p>', '</p>')" class="px-2.5 py-1 rounded-lg bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 text-xs font-bold border border-slate-200 dark:border-slate-700 hover:bg-slate-100" title="Paragraph">¶</button>
                        <button type="button" @click="insertTag('<ul>\n  <li>', '</li>\n</ul>')" class="px-2.5 py-1 rounded-lg bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 text-xs font-bold border border-slate-200 dark:border-slate-700 hover:bg-slate-100" title="Bullet List">• List</button>
                        <button type="button" @click="insertTag('<ol>\n  <li>', '</li>\n</ol>')" class="px-2.5 py-1 rounded-lg bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 text-xs font-bold border border-slate-200 dark:border-slate-700 hover:bg-slate-100" title="Numbered List">1. List</button>
                        <button type="button" @click="insertTag('<blockquote class=\'border-l-4 border-emerald-500 pl-4 italic text-slate-600 my-4\'>', '</blockquote>')" class="px-2.5 py-1 rounded-lg bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 text-xs font-bold border border-slate-200 dark:border-slate-700 hover:bg-slate-100" title="Callout Quote">Quote</button>
                    </div>

                    <div>
                        <textarea 
                            id="contentArea"
                            name="content" 
                            x-model="content" 
                            rows="14" 
                            placeholder="Write your detailed policy, terms, or information content here..." 
                            class="w-full p-4 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900 text-xs font-mono leading-relaxed text-slate-900 dark:text-white outline-none focus:ring-2 focus:ring-emerald-500"></textarea>
                    </div>
                </div>

                <!-- SEO Meta Card -->
                <div class="bg-white dark:bg-slate-800 p-6 rounded-2xl border border-slate-200 dark:border-slate-700 space-y-4">
                    <h3 class="font-bold text-sm text-slate-900 dark:text-white border-b border-slate-100 dark:border-slate-700 pb-3 flex items-center gap-2">
                        <i data-lucide="globe" class="w-4 h-4 text-emerald-500"></i>
                        <span>Search Engine Optimization (SEO)</span>
                    </h3>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Meta Title</label>
                        <input 
                            type="text" 
                            name="meta_title" 
                            x-model="metaTitle" 
                            placeholder="e.g. Terms & Warranty Policy | DREAMERS PCB" 
                            class="w-full px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900 text-xs text-slate-900 dark:text-white outline-none focus:ring-2 focus:ring-emerald-500">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Meta Description</label>
                        <textarea 
                            name="meta_description" 
                            x-model="metaDescription" 
                            rows="2" 
                            placeholder="Brief summary for Google search results (150-160 characters)..." 
                            class="w-full px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900 text-xs text-slate-900 dark:text-white outline-none focus:ring-2 focus:ring-emerald-500"></textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Meta Keywords</label>
                        <input 
                            type="text" 
                            name="meta_keywords" 
                            x-model="metaKeywords" 
                            placeholder="warranty, terms, delivery, return, bangladesh, dreamers pcb" 
                            class="w-full px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900 text-xs text-slate-900 dark:text-white outline-none focus:ring-2 focus:ring-emerald-500">
                    </div>

                    <!-- Live Google Snippet Preview -->
                    <div class="mt-4 p-4 rounded-xl bg-slate-50 dark:bg-slate-900/60 border border-slate-200 dark:border-slate-700">
                        <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider block mb-1.5">Google Search Result Preview</span>
                        <div class="text-xs text-blue-600 dark:text-blue-400 font-medium truncate" x-text="metaTitle || (title ? title + ' | DREAMERS PCB' : 'Page Title')"></div>
                        <div class="text-[11px] text-emerald-700 dark:text-emerald-500 truncate" x-text="'{{ url('/page') }}/' + (slug || 'your-slug')"></div>
                        <div class="text-[11px] text-slate-500 line-clamp-2 mt-0.5" x-text="metaDescription || 'Detailed policy and information page from DREAMERS PCB Bangladesh.'"></div>
                    </div>
                </div>

            </div>

            <!-- Right Sidebar Column (Placement, Sort, Publishing) -->
            <div class="space-y-6">

                <!-- Publishing & Visibility Card -->
                <div class="bg-white dark:bg-slate-800 p-6 rounded-2xl border border-slate-200 dark:border-slate-700 space-y-4">
                    <h3 class="font-bold text-sm text-slate-900 dark:text-white border-b border-slate-100 dark:border-slate-700 pb-3 flex items-center gap-2">
                        <i data-lucide="send" class="w-4 h-4 text-emerald-500"></i>
                        <span>Publish Status</span>
                    </h3>

                    <label class="flex items-center justify-between p-3 rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 cursor-pointer">
                        <div>
                            <span class="text-xs font-bold text-slate-900 dark:text-white block">Active / Published</span>
                            <span class="text-[11px] text-slate-500 block">Make page accessible to public</span>
                        </div>
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $page->is_active) ? 'checked' : '' }} class="w-4 h-4 text-emerald-500 rounded border-slate-300 focus:ring-emerald-500">
                    </label>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Sort Order</label>
                        <input 
                            type="number" 
                            name="sort_order" 
                            value="{{ old('sort_order', $page->sort_order) }}" 
                            min="0" 
                            class="w-full px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900 text-xs font-bold text-slate-900 dark:text-white outline-none focus:ring-2 focus:ring-emerald-500">
                        <p class="text-[11px] text-slate-400 mt-1">Lower numbers appear first in menus.</p>
                    </div>

                    <button type="submit" class="w-full py-3 px-4 rounded-xl bg-emerald-500 hover:bg-emerald-600 text-white font-bold text-xs flex items-center justify-center gap-2 shadow-lg shadow-emerald-500/20 transition transform active:scale-95">
                        <i data-lucide="check" class="w-4 h-4"></i>
                        <span>Save Page Changes</span>
                    </button>
                </div>

                <!-- Menu Placement Card -->
                <div class="bg-white dark:bg-slate-800 p-6 rounded-2xl border border-slate-200 dark:border-slate-700 space-y-4">
                    <h3 class="font-bold text-sm text-slate-900 dark:text-white border-b border-slate-100 dark:border-slate-700 pb-3 flex items-center gap-2">
                        <i data-lucide="layout" class="w-4 h-4 text-emerald-500"></i>
                        <span>Menu Placement</span>
                    </h3>
                    <p class="text-xs text-slate-500">Select where this page link should be automatically displayed:</p>

                    <div class="space-y-2.5">
                        <label class="flex items-start gap-3 p-3 rounded-xl border cursor-pointer transition" :class="placement === 'both' ? 'border-emerald-500 bg-emerald-500/5' : 'border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900'">
                            <input type="radio" name="placement" value="both" x-model="placement" class="mt-0.5 text-emerald-500 focus:ring-emerald-500">
                            <div>
                                <span class="text-xs font-bold text-slate-900 dark:text-white flex items-center gap-1.5">
                                    <i data-lucide="layers" class="w-3.5 h-3.5 text-emerald-500"></i>
                                    <span>Top Menu & Footer Policies</span>
                                </span>
                                <span class="text-[11px] text-slate-500 block mt-0.5">Maximum visibility (recommended for Terms & Warranty).</span>
                            </div>
                        </label>

                        <label class="flex items-start gap-3 p-3 rounded-xl border cursor-pointer transition" :class="placement === 'footer' ? 'border-emerald-500 bg-emerald-500/5' : 'border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900'">
                            <input type="radio" name="placement" value="footer" x-model="placement" class="mt-0.5 text-emerald-500 focus:ring-emerald-500">
                            <div>
                                <span class="text-xs font-bold text-slate-900 dark:text-white flex items-center gap-1.5">
                                    <i data-lucide="panel-bottom" class="w-3.5 h-3.5 text-blue-500"></i>
                                    <span>Footer Policies Only</span>
                                </span>
                                <span class="text-[11px] text-slate-500 block mt-0.5">Appears under Customer Care & Policies column.</span>
                            </div>
                        </label>

                        <label class="flex items-start gap-3 p-3 rounded-xl border cursor-pointer transition" :class="placement === 'header' ? 'border-emerald-500 bg-emerald-500/5' : 'border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900'">
                            <input type="radio" name="placement" value="header" x-model="placement" class="mt-0.5 text-emerald-500 focus:ring-emerald-500">
                            <div>
                                <span class="text-xs font-bold text-slate-900 dark:text-white flex items-center gap-1.5">
                                    <i data-lucide="navigation" class="w-3.5 h-3.5 text-purple-500"></i>
                                    <span>Top Menu Only</span>
                                </span>
                                <span class="text-[11px] text-slate-500 block mt-0.5">Appears on the top header utility bar.</span>
                            </div>
                        </label>

                        <label class="flex items-start gap-3 p-3 rounded-xl border cursor-pointer transition" :class="placement === 'none' ? 'border-emerald-500 bg-emerald-500/5' : 'border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900'">
                            <input type="radio" name="placement" value="none" x-model="placement" class="mt-0.5 text-emerald-500 focus:ring-emerald-500">
                            <div>
                                <span class="text-xs font-bold text-slate-900 dark:text-white flex items-center gap-1.5">
                                    <i data-lucide="link" class="w-3.5 h-3.5 text-slate-400"></i>
                                    <span>Unlisted (Direct Link)</span>
                                </span>
                                <span class="text-[11px] text-slate-500 block mt-0.5">Hidden from menus, accessible via URL.</span>
                            </div>
                        </label>
                    </div>
                </div>

            </div>

        </div>

    </form>

</div>

<script>
function pageEditor() {
    return {
        title: @json(old('title', $page->title)),
        slug: @json(old('slug', $page->slug)),
        content: @json(old('content', $page->content)),
        placement: @json(old('placement', $page->placement)),
        metaTitle: @json(old('meta_title', $page->meta_title)),
        metaDescription: @json(old('meta_description', $page->meta_description)),
        metaKeywords: @json(old('meta_keywords', $page->meta_keywords)),

        insertTag(openTag, closeTag) {
            const textarea = document.getElementById('contentArea');
            const start = textarea.selectionStart;
            const end = textarea.selectionEnd;
            const selectedText = this.content.substring(start, end) || 'Text here';
            const replacement = openTag + selectedText + closeTag;
            
            this.content = this.content.substring(0, start) + replacement + this.content.substring(end);
            
            this.$nextTick(() => {
                textarea.focus();
                textarea.setSelectionRange(start + openTag.length, start + openTag.length + selectedText.length);
            });
        }
    };
}
</script>
@endsection
