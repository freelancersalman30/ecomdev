<!-- Modern Product SEO & Google Search Preview Component -->
<div 
    x-data="productSeoCard({
        initialTitle: @js(old('meta_title', $product->meta_title ?? '')),
        initialDescription: @js(old('meta_description', $product->meta_description ?? '')),
        initialKeywords: @js(old('meta_keywords', $product->meta_keywords ?? ''))
    })" 
    class="bg-white dark:bg-slate-900 rounded-2xl p-5 sm:p-6 border border-slate-200 dark:border-slate-800 shadow-sm space-y-5">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-100 dark:border-slate-800 pb-4">
        <div>
            <div class="flex items-center gap-2">
                <div class="w-7 h-7 rounded-lg bg-gradient-to-tr from-sky-500 to-blue-600 text-white flex items-center justify-center font-black shadow-sm">
                    <i data-lucide="globe" class="w-4 h-4"></i>
                </div>
                <h3 class="text-sm font-black text-slate-900 dark:text-white flex items-center gap-2">
                    <span>Search Engine Optimization (SEO) & Google SERP</span>
                </h3>
            </div>
            <p class="text-xs text-slate-500 mt-0.5">
                Customize how this electronic component appears in Google Search results and social media shares.
            </p>
        </div>

        <!-- 1-Click AI SEO Generator -->
        <button 
            type="button" 
            @click="generateSeoWithAi()" 
            :disabled="isGeneratingSeo"
            class="self-start sm:self-auto px-3 py-1.5 rounded-lg bg-sky-50 dark:bg-sky-950/50 hover:bg-sky-100 dark:hover:bg-sky-900/50 text-sky-700 dark:text-sky-300 font-bold text-[11px] transition flex items-center gap-1.5 border border-sky-500/30 disabled:opacity-50">
            <template x-if="isGeneratingSeo">
                <i data-lucide="loader-2" class="w-3.5 h-3.5 animate-spin"></i>
            </template>
            <template x-if="!isGeneratingSeo">
                <i data-lucide="sparkles" class="w-3.5 h-3.5 text-sky-500"></i>
            </template>
            <span>Auto-Generate SEO with Gemini</span>
        </button>
    </div>

    <!-- Live Google SERP Snippet Preview -->
    <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-950/60 border border-slate-200/80 dark:border-slate-800 space-y-1.5">
        <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider flex items-center gap-1.5">
            <i data-lucide="search" class="w-3 h-3 text-emerald-500"></i>
            <span>Google Search Live Snippet Preview</span>
        </div>

        <div class="bg-white dark:bg-slate-900 p-3.5 rounded-xl border border-slate-200 dark:border-slate-800/80 shadow-xs space-y-1">
            <!-- URL / Breadcrumb -->
            <div class="flex items-center gap-1.5 text-[11px] text-slate-500 dark:text-slate-400 truncate font-sans">
                <div class="w-4 h-4 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-[9px] font-bold text-slate-600">G</div>
                <span class="truncate">{{ request()->getHost() ?: 'dreamerspcb.com' }} &rsaquo; product &rsaquo; <span x-text="getPreviewSlug()"></span></span>
            </div>

            <!-- Clickable Title (Blue) -->
            <h4 class="text-sm font-semibold text-blue-600 dark:text-blue-400 hover:underline cursor-pointer truncate leading-snug"
                x-text="metaTitle || getFallbackTitle()"></h4>

            <!-- Meta Description -->
            <p class="text-xs text-slate-600 dark:text-slate-400 leading-relaxed line-clamp-2"
               x-text="metaDescription || getFallbackDescription()"></p>
        </div>
    </div>

    <!-- Inputs Form -->
    <div class="space-y-4">
        <!-- Meta Title -->
        <div>
            <div class="flex items-center justify-between mb-1">
                <label class="block text-xs font-semibold text-slate-500">
                    SEO Meta Title
                </label>
                <span class="text-[11px] font-mono font-semibold" :class="metaTitle.length > 60 ? 'text-amber-500' : 'text-slate-400'">
                    <span x-text="metaTitle.length"></span> / 60 chars (Recommended)
                </span>
            </div>
            <input 
                type="text" 
                name="meta_title" 
                x-model="metaTitle"
                placeholder="e.g. ESP32-WROOM-32D Dual-Core WiFi & Bluetooth Module - Buy in Bangladesh" 
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800/80 text-xs font-semibold outline-none focus:ring-2 focus:ring-sky-500 transition">
        </div>

        <!-- Meta Description -->
        <div>
            <div class="flex items-center justify-between mb-1">
                <label class="block text-xs font-semibold text-slate-500">
                    SEO Meta Description
                </label>
                <span class="text-[11px] font-mono font-semibold" :class="metaDescription.length > 160 ? 'text-amber-500' : 'text-slate-400'">
                    <span x-text="metaDescription.length"></span> / 160 chars (Recommended)
                </span>
            </div>
            <textarea 
                name="meta_description" 
                x-model="metaDescription"
                rows="3" 
                placeholder="Brief summary containing target search terms (e.g. Buy original ESP32 development board in Bangladesh with technical pinouts and nationwide delivery from DREAMERS PCB)..." 
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800/80 text-xs leading-relaxed outline-none focus:ring-2 focus:ring-sky-500 transition resize-none"></textarea>
        </div>

        <!-- Meta Keywords -->
        <div>
            <label class="block text-xs font-semibold text-slate-500 mb-1">
                SEO Meta Keywords (Comma Separated)
            </label>
            <input 
                type="text" 
                name="meta_keywords" 
                x-model="metaKeywords"
                placeholder="e.g. esp32, wifi module, bluetooth module, iot bangladesh, buy esp32, dreamers pcb" 
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800/80 text-xs outline-none focus:ring-2 focus:ring-sky-500 transition">
        </div>
    </div>

</div>

<!-- Alpine Script for SEO Component -->
<script>
function productSeoCard(config = {}) {
    return {
        metaTitle: config.initialTitle || '',
        metaDescription: config.initialDescription || '',
        metaKeywords: config.initialKeywords || '',
        isGeneratingSeo: false,

        init() {
            // Listen for global AI generation events from the main Gemini button
            window.addEventListener('ai-seo-generated', (event) => {
                if (event.detail) {
                    if (event.detail.meta_title) this.metaTitle = event.detail.meta_title;
                    if (event.detail.meta_description) this.metaDescription = event.detail.meta_description;
                    if (event.detail.meta_keywords) this.metaKeywords = event.detail.meta_keywords;
                }
            });
        },

        getFallbackTitle() {
            const nameInput = document.querySelector('input[name="name"]');
            const name = nameInput ? nameInput.value.trim() : '';
            return name ? (name + ' | DREAMERS PCB') : 'Product Title | DREAMERS PCB Bangladesh';
        },

        getFallbackDescription() {
            const shortInput = document.querySelector('textarea[name="short_description"]');
            const short = shortInput ? shortInput.value.trim() : '';
            return short || 'Buy genuine electronic components, microcontrollers, sensors, and robotics accessories with fast nationwide delivery in Bangladesh.';
        },

        getPreviewSlug() {
            const nameInput = document.querySelector('input[name="name"]');
            const name = nameInput ? nameInput.value.trim() : '';
            return name ? name.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/(^-|-$)+/g, '') : 'esp32-wifi-bluetooth-module';
        },

        async generateSeoWithAi() {
            const nameInput = document.querySelector('input[name="name"]');
            const productName = nameInput ? nameInput.value.trim() : '';

            if (!productName) {
                alert('Please enter or paste the Product / Component Name at the top first!');
                if (nameInput) {
                    nameInput.focus();
                    nameInput.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
                return;
            }

            this.isGeneratingSeo = true;

            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                const response = await fetch("{{ route('admin.products.ai.generate') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken || ''
                    },
                    body: JSON.stringify({
                        name: productName
                    })
                });

                const data = await response.json();

                if (data.success) {
                    if (data.meta_title) this.metaTitle = data.meta_title;
                    if (data.meta_description) this.metaDescription = data.meta_description;
                    if (data.meta_keywords) this.metaKeywords = data.meta_keywords;
                } else {
                    alert(data.message || 'Could not generate SEO metadata.');
                }
            } catch (err) {
                console.error('SEO Generation error:', err);
                alert('Connection error while contacting AI generator.');
            } finally {
                this.isGeneratingSeo = false;
            }
        }
    };
}
</script>
