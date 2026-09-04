@extends('layouts.admin')

@section('title', 'Create Custom Page')

@section('content')
<div class="max-w-5xl mx-auto space-y-6" x-data="pageBuilder()">

    <!-- Header -->
    <div class="flex items-center justify-between bg-white dark:bg-slate-800 p-6 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-xs">
        <div>
            <div class="flex items-center gap-2 text-xs font-bold text-slate-400 mb-1">
                <a href="{{ route('admin.pages.index') }}" class="hover:text-emerald-500 transition">Custom Pages</a>
                <span>/</span>
                <span class="text-slate-900 dark:text-white">Create New Page</span>
            </div>
            <h1 class="text-xl font-black text-slate-900 dark:text-white">New Custom Page Builder</h1>
        </div>
        <a href="{{ route('admin.pages.index') }}" class="px-3.5 py-2 rounded-xl bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 text-slate-700 dark:text-slate-200 text-xs font-bold transition">
            Back to List
        </a>
    </div>

    <!-- Quick Preset Buttons -->
    <div class="bg-gradient-to-r from-emerald-500/10 via-teal-500/10 to-transparent p-4 rounded-2xl border border-emerald-500/20">
        <div class="flex items-center gap-2 mb-2">
            <i data-lucide="sparkles" class="w-4 h-4 text-emerald-500"></i>
            <span class="text-xs font-bold text-slate-900 dark:text-white uppercase tracking-wider">Quick Legal & Policy Presets</span>
        </div>
        <p class="text-xs text-slate-500 dark:text-slate-400 mb-3">Click any preset below to automatically populate the title, slug, placement, and starter policy content:</p>
        <div class="flex flex-wrap gap-2">
            <button type="button" @click="loadPreset('terms')" class="px-3 py-1.5 rounded-xl bg-white dark:bg-slate-800 border border-emerald-500/30 hover:border-emerald-500 text-xs font-bold text-slate-800 dark:text-slate-200 shadow-xs transition">
                📜 Terms & Warranty Policy
            </button>
            <button type="button" @click="loadPreset('delivery')" class="px-3 py-1.5 rounded-xl bg-white dark:bg-slate-800 border border-emerald-500/30 hover:border-emerald-500 text-xs font-bold text-slate-800 dark:text-slate-200 shadow-xs transition">
                🚚 Delivery & Shipping Policy
            </button>
            <button type="button" @click="loadPreset('refunds')" class="px-3 py-1.5 rounded-xl bg-white dark:bg-slate-800 border border-emerald-500/30 hover:border-emerald-500 text-xs font-bold text-slate-800 dark:text-slate-200 shadow-xs transition">
                🔄 Refunds & Returns Policy
            </button>
            <button type="button" @click="loadPreset('privacy')" class="px-3 py-1.5 rounded-xl bg-white dark:bg-slate-800 border border-emerald-500/30 hover:border-emerald-500 text-xs font-bold text-slate-800 dark:text-slate-200 shadow-xs transition">
                🔒 Privacy & Security Policy
            </button>
        </div>
    </div>

    <!-- Form -->
    <form method="POST" action="{{ route('admin.pages.store') }}" class="space-y-6">
        @csrf

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
                            @input="generateSlug()" 
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
                        <input type="checkbox" name="is_active" value="1" checked class="w-4 h-4 text-emerald-500 rounded border-slate-300 focus:ring-emerald-500">
                    </label>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Sort Order</label>
                        <input 
                            type="number" 
                            name="sort_order" 
                            value="0" 
                            min="0" 
                            class="w-full px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900 text-xs font-bold text-slate-900 dark:text-white outline-none focus:ring-2 focus:ring-emerald-500">
                        <p class="text-[11px] text-slate-400 mt-1">Lower numbers appear first in menus.</p>
                    </div>

                    <button type="submit" class="w-full py-3 px-4 rounded-xl bg-emerald-500 hover:bg-emerald-600 text-white font-bold text-xs flex items-center justify-center gap-2 shadow-lg shadow-emerald-500/20 transition transform active:scale-95">
                        <i data-lucide="check" class="w-4 h-4"></i>
                        <span>Publish Custom Page</span>
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
function pageBuilder() {
    return {
        title: '{{ old('title', '') }}',
        slug: '{{ old('slug', '') }}',
        content: `{!! old('content', '') !!}`,
        placement: '{{ old('placement', 'footer') }}',
        metaTitle: '{{ old('meta_title', '') }}',
        metaDescription: '{{ old('meta_description', '') }}',
        metaKeywords: '{{ old('meta_keywords', '') }}',

        generateSlug() {
            if (!this.slug || this.slug === '') {
                this.slug = this.title
                    .toLowerCase()
                    .replace(/[^a-z0-9]+/g, '-')
                    .replace(/(^-|-$)/g, '');
            }
        },

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
        },

        loadPreset(type) {
            if (type === 'terms') {
                this.title = 'Terms & Warranty Policy';
                this.slug = 'terms-and-warranty';
                this.placement = 'both';
                this.metaTitle = 'Terms & Warranty Policy | DREAMERS PCB';
                this.metaDescription = 'Read our official terms of service, hardware warranty claim procedures, and replacement guidelines.';
                this.metaKeywords = 'warranty, terms, guarantee, hardware replacement, dreamers pcb';
                this.content = `<h2>1. Hardware Warranty Guidelines</h2>
<p>All microcontrollers, ICs, development boards, and modules sold at DREAMERS PCB undergo quality checks prior to dispatch.</p>
<ul>
  <li><strong>Warranty Period:</strong> Standard 30-day testing warranty unless specified otherwise on product specs.</li>
  <li><strong>Coverage:</strong> Factory defects and dead-on-arrival (DOA) units.</li>
  <li><strong>Exclusions:</strong> Physical burning, reverse polarity, and short circuits caused by improper soldering.</li>
</ul>

<h2>2. How to Claim Warranty</h2>
<p>To submit a claim, visit our <strong>Warranty Check</strong> page with your order ID or serial number.</p>`;
            } else if (type === 'delivery') {
                this.title = 'Delivery & Shipping Policy';
                this.slug = 'delivery-policy';
                this.placement = 'footer';
                this.metaTitle = 'Nationwide Delivery & Shipping Policy | DREAMERS PCB';
                this.metaDescription = 'Fast delivery in Dhaka within 24-48 hours and nationwide courier across all 64 districts in Bangladesh.';
                this.metaKeywords = 'delivery charge, shipping time, steadfast, pathao, courier, dhaka, bangladesh';
                this.content = `<h2>1. Delivery Coverage & Timelines</h2>
<ul>
  <li><strong>Inside Dhaka City:</strong> 24 to 48 Hours (৳60 delivery charge).</li>
  <li><strong>Outside Dhaka / Nationwide:</strong> 48 to 72 Hours (৳120 delivery charge).</li>
</ul>

<h2>2. Safe Anti-Static Packaging</h2>
<p>All sensitive electronic chips and components are sealed inside ESD anti-static bags with protective bubble wraps.</p>`;
            } else if (type === 'refunds') {
                this.title = 'Refunds & Returns Policy';
                this.slug = 'refunds-and-returns';
                this.placement = 'footer';
                this.metaTitle = '7-Day Refund and Return Policy | DREAMERS PCB';
                this.metaDescription = 'Understand our 7-day hassle-free return and refund policy for electronic components and tools.';
                this.metaKeywords = 'refund, return policy, replacement, exchange, money back, dreamers pcb';
                this.content = `<h2>1. 7-Day Easy Returns</h2>
<p>If you receive an incorrect part or defective item, request an exchange or full refund within <strong>7 days</strong> of delivery.</p>

<h2>2. Refund Processing</h2>
<p>Approved refunds are credited back to your bKash, Nagad, or Bank account within 3 to 5 business days.</p>`;
            } else if (type === 'privacy') {
                this.title = 'Privacy Policy';
                this.slug = 'privacy-policy';
                this.placement = 'footer';
                this.metaTitle = 'Privacy Policy | DREAMERS PCB';
                this.metaDescription = 'Learn how we protect and secure your personal information and transaction data.';
                this.metaKeywords = 'privacy, data security, ssl encryption, dreamers pcb';
                this.content = `<h2>1. Data Protection</h2>
<p>Your personal information is encrypted using industry standard 256-bit SSL encryption. We never sell or share your information with third-party advertisers.</p>`;
            }
        }
    };
}
</script>
@endsection
