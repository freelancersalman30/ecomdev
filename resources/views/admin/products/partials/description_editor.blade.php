<!-- Modern Gemini AI & Markdown Description Editor Component -->
<div 
    x-data="geminiDescriptionEditor({
        initialContent: @js(old('description', $description ?? ''))
    })" 
    class="bg-white dark:bg-slate-900 rounded-2xl p-5 sm:p-6 border border-slate-200 dark:border-slate-800 shadow-sm space-y-5">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-100 dark:border-slate-800 pb-4">
        <div>
            <div class="flex items-center gap-2">
                <div class="w-7 h-7 rounded-lg bg-gradient-to-tr from-emerald-500 to-teal-400 text-slate-950 flex items-center justify-center font-black shadow-sm">
                    <i data-lucide="sparkles" class="w-4 h-4"></i>
                </div>
                <h3 class="text-sm font-black text-slate-900 dark:text-white">
                    Technical Description & Gemini AI Documentation
                </h3>
            </div>
            <p class="text-xs text-slate-500 mt-0.5">
                Copy and paste formatted specs directly from Google Gemini. Real-time Markdown, tables & pinouts rendering.
            </p>
        </div>

        <!-- View Mode Switcher -->
        <div class="flex items-center gap-1 bg-slate-100 dark:bg-slate-800/80 p-1 rounded-xl border border-slate-200 dark:border-slate-700/80 self-start sm:self-auto text-xs">
            <button 
                type="button" 
                @click="viewMode = 'write'" 
                :class="viewMode === 'write' ? 'bg-white dark:bg-slate-900 text-emerald-600 dark:text-emerald-400 font-bold shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white'"
                class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg transition">
                <i data-lucide="edit-3" class="w-3.5 h-3.5"></i>
                <span>Write</span>
            </button>

            <button 
                type="button" 
                @click="viewMode = 'preview'" 
                :class="viewMode === 'preview' ? 'bg-white dark:bg-slate-900 text-emerald-600 dark:text-emerald-400 font-bold shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white'"
                class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg transition">
                <i data-lucide="eye" class="w-3.5 h-3.5"></i>
                <span>Preview</span>
            </button>

            <button 
                type="button" 
                @click="viewMode = 'split'" 
                :class="viewMode === 'split' ? 'bg-white dark:bg-slate-900 text-emerald-600 dark:text-emerald-400 font-bold shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white'"
                class="hidden md:flex items-center gap-1.5 px-3 py-1.5 rounded-lg transition">
                <i data-lucide="columns-2" class="w-3.5 h-3.5"></i>
                <span>Split View</span>
            </button>
        </div>
    </div>

    <!-- Formatting & Gemini Quick Tools Bar -->
    <div class="flex flex-wrap items-center justify-between gap-2 bg-slate-50 dark:bg-slate-800/40 p-2.5 rounded-xl border border-slate-200/80 dark:border-slate-800 text-xs">
        
        <!-- Markdown Formatting Buttons -->
        <div class="flex flex-wrap items-center gap-1">
            <button type="button" @click="insertSyntax('**', '**', 'bold text')" class="p-1.5 rounded-lg hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 transition" title="Bold (**text**)">
                <i data-lucide="bold" class="w-4 h-4"></i>
            </button>
            <button type="button" @click="insertSyntax('*', '*', 'italic text')" class="p-1.5 rounded-lg hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 transition" title="Italic (*text*)">
                <i data-lucide="italic" class="w-4 h-4"></i>
            </button>
            <div class="h-4 w-px bg-slate-300 dark:bg-slate-700 mx-1"></div>

            <button type="button" @click="insertSyntax('## ', '', 'Section Heading')" class="px-2 py-1 rounded-lg hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-bold font-mono text-[11px] transition" title="Heading 2 (## Heading)">
                H2
            </button>
            <button type="button" @click="insertSyntax('### ', '', 'Subheading')" class="px-2 py-1 rounded-lg hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-bold font-mono text-[11px] transition" title="Heading 3 (### Subheading)">
                H3
            </button>
            <div class="h-4 w-px bg-slate-300 dark:bg-slate-700 mx-1"></div>

            <button type="button" @click="insertSyntax('- ', '', 'Bullet feature')" class="p-1.5 rounded-lg hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 transition" title="Bullet List (- item)">
                <i data-lucide="list" class="w-4 h-4"></i>
            </button>
            <button type="button" @click="insertSyntax('1. ', '', 'Numbered feature')" class="p-1.5 rounded-lg hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 transition" title="Numbered List (1. item)">
                <i data-lucide="list-ordered" class="w-4 h-4"></i>
            </button>
            <button type="button" @click="insertTableTemplate()" class="p-1.5 rounded-lg hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 transition" title="Insert Table">
                <i data-lucide="table" class="w-4 h-4"></i>
            </button>
            <button type="button" @click="insertSyntax('```\n', '\n```', '// Pinout or Code')" class="p-1.5 rounded-lg hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 transition" title="Code / Spec Block (```code```)">
                <i data-lucide="code" class="w-4 h-4"></i>
            </button>
            <button type="button" @click="insertSyntax('> **Note:** ', '', 'Important operating notice')" class="p-1.5 rounded-lg hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 transition" title="Note Blockquote">
                <i data-lucide="quote" class="w-4 h-4"></i>
            </button>
        </div>

        <!-- Gemini AI Helpers -->
        <div class="flex items-center gap-1.5">
            <button 
                type="button" 
                @click="insertGeminiTemplate()" 
                class="px-2.5 py-1.5 rounded-lg bg-emerald-500/10 hover:bg-emerald-500/20 text-emerald-700 dark:text-emerald-400 font-bold text-[11px] transition flex items-center gap-1.5 border border-emerald-500/30">
                <i data-lucide="sparkles" class="w-3.5 h-3.5"></i>
                <span>Gemini Hardware Template</span>
            </button>

            <button 
                type="button" 
                @click="cleanPastedText()" 
                class="px-2.5 py-1.5 rounded-lg bg-slate-200 dark:bg-slate-700 hover:bg-slate-300 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 font-semibold text-[11px] transition flex items-center gap-1"
                title="Normalize spaces, fix markdown indents and remove invalid characters">
                <i data-lucide="wand-2" class="w-3.5 h-3.5"></i>
                <span>Clean Format</span>
            </button>

            <button 
                type="button" 
                @click="confirmClear()" 
                class="p-1.5 rounded-lg hover:bg-rose-100 dark:hover:bg-rose-950/50 text-slate-400 hover:text-rose-600 transition" 
                title="Clear Description">
                <i data-lucide="trash-2" class="w-4 h-4"></i>
            </button>
        </div>
    </div>

    <!-- Main Workspace (Write, Preview, or Split) -->
    <div class="relative">

        <!-- 1. WRITE ONLY MODE -->
        <div x-show="viewMode === 'write'" class="space-y-1">
            <textarea 
                x-ref="editorTextarea"
                name="description" 
                x-model="content"
                @input="updateStats()"
                rows="14"
                placeholder="Paste your product description from Google Gemini or write markdown here...&#10;&#10;Example:&#10;## Product Overview&#10;High-performance microcontroller for IoT...&#10;&#10;### Key Specifications&#10;| Parameter | Value |&#10;|---|---|&#10;| Voltage | 3.3V - 5V |"
                class="w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 text-xs sm:text-sm font-mono leading-relaxed outline-none focus:ring-2 focus:ring-emerald-500 transition resize-y min-h-[320px] shadow-inner"></textarea>
        </div>

        <!-- 2. PREVIEW ONLY MODE -->
        <div 
            x-show="viewMode === 'preview'" 
            x-cloak
            class="w-full p-5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-950/60 min-h-[320px] max-h-[550px] overflow-y-auto">
            
            <template x-if="content.trim().length === 0">
                <div class="h-64 flex flex-col items-center justify-center text-center text-slate-400">
                    <i data-lucide="file-code" class="w-10 h-10 opacity-30 mb-2"></i>
                    <p class="text-xs font-semibold">No description entered yet.</p>
                    <p class="text-[11px] text-slate-400 mt-0.5">Switch to "Write" mode or click "Gemini Hardware Template" above.</p>
                </div>
            </template>

            <div 
                x-show="content.trim().length > 0"
                x-html="renderedHtml"
                class="gemini-rendered-preview prose prose-slate max-w-none text-xs sm:text-sm text-slate-800 dark:text-slate-200 leading-relaxed">
            </div>
        </div>

        <!-- 3. SPLIT VIEW MODE (Side-by-Side) -->
        <div 
            x-show="viewMode === 'split'" 
            x-cloak
            class="grid grid-cols-1 md:grid-cols-2 gap-4">
            
            <!-- Left: Editor Textarea -->
            <div class="space-y-1">
                <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider flex items-center justify-between px-1">
                    <span>Markdown Source</span>
                    <span class="text-emerald-500 font-mono">Live Sync</span>
                </div>
                <textarea 
                    name="description" 
                    x-model="content"
                    @input="updateStats()"
                    rows="14"
                    class="w-full px-3.5 py-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 text-xs font-mono leading-relaxed outline-none focus:ring-2 focus:ring-emerald-500 transition resize-y min-h-[340px] max-h-[550px]"></textarea>
            </div>

            <!-- Right: Real-time Rendered Preview -->
            <div class="space-y-1">
                <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider flex items-center justify-between px-1">
                    <span>Gemini Live Preview</span>
                    <span class="text-slate-400 font-mono">Storefront View</span>
                </div>
                <div class="p-4 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-950/60 min-h-[340px] max-h-[550px] overflow-y-auto">
                    <template x-if="content.trim().length === 0">
                        <div class="h-64 flex flex-col items-center justify-center text-center text-slate-400">
                            <i data-lucide="sparkles" class="w-8 h-8 opacity-30 mb-2"></i>
                            <p class="text-xs">Type or paste Markdown on the left to see live Gemini preview here.</p>
                        </div>
                    </template>
                    <div 
                        x-show="content.trim().length > 0"
                        x-html="renderedHtml"
                        class="gemini-rendered-preview prose prose-slate max-w-none text-xs sm:text-sm text-slate-800 dark:text-slate-200 leading-relaxed">
                    </div>
                </div>
            </div>

        </div>

    </div>

    <!-- Bottom Status Bar -->
    <div class="flex flex-wrap items-center justify-between gap-3 pt-3 border-t border-slate-100 dark:border-slate-800 text-[11px] text-slate-500">
        <div class="flex items-center gap-4">
            <span>Words: <strong class="text-slate-700 dark:text-slate-300 font-mono" x-text="stats.words"></strong></span>
            <span>Characters: <strong class="text-slate-700 dark:text-slate-300 font-mono" x-text="stats.chars"></strong></span>
            <span>Est. Read: <strong class="text-slate-700 dark:text-slate-300 font-mono" x-text="stats.readingTime"></strong></span>
        </div>

        <div class="flex items-center gap-2 text-emerald-600 dark:text-emerald-400 font-medium">
            <i data-lucide="check-circle" class="w-3.5 h-3.5"></i>
            <span>Optimized for Google Gemini & Storefront Display</span>
        </div>
    </div>

</div>

<!-- Styles for Rendered Gemini Preview -->
<style>
.gemini-rendered-preview h1,
.gemini-rendered-preview h2 {
    font-size: 1.15rem;
    font-weight: 800;
    margin-top: 1.25rem;
    margin-bottom: 0.5rem;
    padding-bottom: 0.35rem;
    border-bottom: 1px solid rgba(148, 163, 184, 0.2);
    color: #0f172a;
}
.dark .gemini-rendered-preview h1,
.dark .gemini-rendered-preview h2 {
    color: #f8fafc;
    border-bottom-color: rgba(51, 65, 85, 0.5);
}
.gemini-rendered-preview h3 {
    font-size: 0.95rem;
    font-weight: 700;
    margin-top: 1rem;
    margin-bottom: 0.35rem;
    color: #059669;
}
.dark .gemini-rendered-preview h3 {
    color: #34d399;
}
.gemini-rendered-preview p {
    margin-bottom: 0.75rem;
    line-height: 1.6;
}
.gemini-rendered-preview ul {
    list-style-type: disc;
    padding-left: 1.25rem;
    margin-bottom: 0.75rem;
}
.gemini-rendered-preview ol {
    list-style-type: decimal;
    padding-left: 1.25rem;
    margin-bottom: 0.75rem;
}
.gemini-rendered-preview li {
    margin-bottom: 0.25rem;
}
.gemini-rendered-preview table {
    width: 100%;
    border-collapse: collapse;
    margin: 1rem 0;
    border-radius: 0.75rem;
    overflow: hidden;
    border: 1px solid #e2e8f0;
    font-size: 0.75rem;
}
.dark .gemini-rendered-preview table {
    border-color: #334155;
}
.gemini-rendered-preview th {
    background: #f1f5f9;
    padding: 0.5rem 0.75rem;
    font-weight: 700;
    text-align: left;
    border-bottom: 1px solid #cbd5e1;
    color: #0f172a;
}
.dark .gemini-rendered-preview th {
    background: #1e293b;
    border-bottom-color: #475569;
    color: #f8fafc;
}
.gemini-rendered-preview td {
    padding: 0.5rem 0.75rem;
    border-bottom: 1px solid #f1f5f9;
}
.dark .gemini-rendered-preview td {
    border-bottom-color: #1e293b;
}
.gemini-rendered-preview tr:last-child td {
    border-bottom: none;
}
.gemini-rendered-preview tr:hover {
    background: rgba(248, 250, 252, 0.8);
}
.dark .gemini-rendered-preview tr:hover {
    background: rgba(30, 41, 59, 0.4);
}
.gemini-rendered-preview blockquote {
    border-left: 4px solid #10b981;
    background: rgba(16, 185, 129, 0.08);
    padding: 0.6rem 0.9rem;
    border-radius: 0 0.5rem 0.5rem 0;
    margin: 0.75rem 0;
    font-style: italic;
    font-size: 0.75rem;
}
.gemini-rendered-preview code {
    background: #f1f5f9;
    color: #059669;
    padding: 0.15rem 0.35rem;
    border-radius: 0.25rem;
    font-family: 'JetBrains Mono', monospace;
    font-size: 0.75rem;
}
.dark .gemini-rendered-preview code {
    background: #1e293b;
    color: #34d399;
}
.gemini-rendered-preview pre {
    background: #0f172a;
    color: #e2e8f0;
    padding: 0.85rem;
    border-radius: 0.75rem;
    overflow-x: auto;
    margin: 0.75rem 0;
    font-family: 'JetBrains Mono', monospace;
    font-size: 0.75rem;
}
.gemini-rendered-preview strong {
    font-weight: 700;
    color: #0f172a;
}
.dark .gemini-rendered-preview strong {
    color: #f8fafc;
}
</style>

<!-- Gemini Editor Alpine Component -->
<script>
function geminiDescriptionEditor(config = {}) {
    return {
        content: config.initialContent || '',
        viewMode: 'write', // 'write' | 'preview' | 'split'
        renderedHtml: '',
        stats: {
            words: 0,
            chars: 0,
            readingTime: '0 min'
        },

        init() {
            this.updateStats();
            this.renderContent();

            // Watch for changes and re-render
            this.$watch('content', () => {
                this.updateStats();
                this.renderContent();
            });

            this.$watch('viewMode', () => {
                this.renderContent();
                this.$nextTick(() => {
                    if (typeof lucide !== 'undefined') lucide.createIcons();
                });
            });
        },

        renderContent() {
            if (!this.content || this.content.trim() === '') {
                this.renderedHtml = '';
                return;
            }

            if (typeof marked !== 'undefined' && marked.parse) {
                try {
                    this.renderedHtml = marked.parse(this.content, { gfm: true, breaks: true });
                } catch (e) {
                    this.renderedHtml = this.basicMarkdownFallback(this.content);
                }
            } else {
                this.renderedHtml = this.basicMarkdownFallback(this.content);
            }
        },

        basicMarkdownFallback(text) {
            let html = text
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;');

            // Headers
            html = html.replace(/^### (.*$)/gim, '<h3>$1</h3>');
            html = html.replace(/^## (.*$)/gim, '<h2>$1</h2>');
            html = html.replace(/^# (.*$)/gim, '<h1>$1</h1>');

            // Bold & Italic
            html = html.replace(/\*\*(.*?)\*\*/gim, '<strong>$1</strong>');
            html = html.replace(/\*(.*?)\*/gim, '<em>$1</em>');

            // Lists
            html = html.replace(/^\- (.*$)/gim, '<li>$1</li>');

            // Linebreaks
            html = html.replace(/\n/gim, '<br>');
            return html;
        },

        updateStats() {
            const raw = this.content || '';
            this.stats.chars = raw.length;
            const wordsArr = raw.trim().split(/\s+/).filter(w => w.length > 0);
            this.stats.words = wordsArr.length;
            const minutes = Math.max(1, Math.ceil(this.stats.words / 200));
            this.stats.readingTime = this.stats.words === 0 ? '0 min' : `~${minutes} min`;
        },

        insertSyntax(before, after = '', defaultPlaceholder = '') {
            const textarea = this.$refs.editorTextarea;
            if (!textarea) {
                this.content += `\n${before}${defaultPlaceholder}${after}\n`;
                return;
            }

            const start = textarea.selectionStart || 0;
            const end = textarea.selectionEnd || 0;
            const selectedText = this.content.substring(start, end) || defaultPlaceholder;

            const replacement = before + selectedText + after;
            this.content = this.content.substring(0, start) + replacement + this.content.substring(end);

            this.$nextTick(() => {
                textarea.focus();
                const newPos = start + before.length + selectedText.length;
                textarea.setSelectionRange(newPos, newPos);
            });
        },

        insertTableTemplate() {
            const table = `\n| Parameter | Specification | Details |\n| :--- | :--- | :--- |\n| Operating Voltage | 3.3V ~ 5.0V DC | Tolerant |\n| Current Consumption | 15mA (Idle) / 80mA (Max) | Typical |\n| Working Temperature | -40°C ~ +85°C | Industrial |\n\n`;
            this.insertSyntax('', '', table);
        },

        insertGeminiTemplate() {
            const template = `## Product Overview\nHigh-performance embedded component designed for IoT automation, PCB engineering, and precision instrumentation.\n\n### Key Hardware Features\n- **Core Processor:** Ultra-fast 32-bit architecture with low-power sleep modes\n- **Standard Bus Interface:** Hardware SPI, I2C, UART and Native USB connectivity\n- **Protection Circuits:** On-board reverse polarity and transient ESD protection\n- **Breadboard Friendly:** Standard 2.54mm pitch headers for seamless breadboard prototyping\n\n### Technical Specifications\n| Parameter | Specification | Tolerance |\n| :--- | :--- | :--- |\n| **Input Voltage** | 3.3V ~ 5.0V DC | ±5% |\n| **Peak Operating Current** | 80 mA | Max |\n| **Clock Frequency** | 72 MHz | High-speed |\n| **Flash Storage** | 64 KB / 128 KB | Non-volatile |\n| **Operating Range** | -40°C to +85°C | Industrial Grade |\n\n### Pinout & Peripheral Guide\n- **VCC:** 3.3V/5V DC Input Power Supply\n- **GND:** System Ground\n- **TX / RX:** High-speed Hardware UART lines\n- **SDA / SCL:** Hardware I2C Serial Data & Clock lines\n\n> **Pro Tip:** When placing this module on a custom PCB, place a 100nF ceramic decoupling capacitor as close as possible to the power pin for optimal RF and signal stability.\n`;

            if (this.content && this.content.trim().length > 0) {
                if (confirm('Replace current description with the Gemini Hardware Template?')) {
                    this.content = template;
                }
            } else {
                this.content = template;
            }
        },

        cleanPastedText() {
            if (!this.content) return;
            // Clean weird zero-width spaces, windows carriage returns, and excessive newlines
            let cleaned = this.content
                .replace(/\r\n/g, '\n')
                .replace(/[\u200B-\u200D\uFEFF]/g, '')
                .replace(/\n{4,}/g, '\n\n\n')
                .trim();
            this.content = cleaned;
            alert('Formatted and cleaned description whitespace successfully!');
        },

        confirmClear() {
            if (confirm('Are you sure you want to clear the description?')) {
                this.content = '';
            }
        }
    };
}
</script>
