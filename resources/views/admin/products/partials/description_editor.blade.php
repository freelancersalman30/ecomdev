<!-- Modern Gemini AI & Rich Description Editor Component -->
<div 
    x-data="geminiDescriptionEditor({
        initialContent: @js(old('description', $description ?? ''))
    })" 
    class="bg-white dark:bg-slate-900 rounded-2xl p-5 sm:p-6 border border-slate-200 dark:border-slate-800 shadow-sm space-y-5">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-100 dark:border-slate-800 pb-4">
        <div>
            <div class="flex items-center gap-2">
                <div class="w-7 h-7 rounded-lg bg-gradient-to-tr from-emerald-500 via-teal-500 to-emerald-400 text-slate-950 flex items-center justify-center font-black shadow-sm">
                    <i data-lucide="sparkles" class="w-4 h-4"></i>
                </div>
                <h3 class="text-sm font-black text-slate-900 dark:text-white flex items-center gap-2">
                    <span>Technical Description & Gemini AI Documentation</span>
                    <span class="text-[10px] px-2 py-0.5 rounded-full bg-emerald-100 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-400 font-bold border border-emerald-500/20">
                        Gemini Smart Paste Active
                    </span>
                </h3>
            </div>
            <p class="text-xs text-slate-500 mt-0.5">
                Copy anywhere from Google Gemini and paste directly (Ctrl+V) &mdash; rich tables, headings, pinouts, and bold lists are automatically preserved.
            </p>
        </div>

        <!-- View Mode Switcher -->
        <div class="flex items-center gap-1 bg-slate-100 dark:bg-slate-800/80 p-1 rounded-xl border border-slate-200 dark:border-slate-700/80 self-start sm:self-auto text-xs">
            <button 
                type="button" 
                @click="switchMode('visual')" 
                :class="viewMode === 'visual' ? 'bg-white dark:bg-slate-900 text-emerald-600 dark:text-emerald-400 font-bold shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white'"
                class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg transition"
                title="Rich Visual Editor: Paste directly like Word/Google Docs">
                <i data-lucide="layout-template" class="w-3.5 h-3.5"></i>
                <span>Visual WYSIWYG</span>
            </button>

            <button 
                type="button" 
                @click="switchMode('markdown')" 
                :class="viewMode === 'markdown' ? 'bg-white dark:bg-slate-900 text-emerald-600 dark:text-emerald-400 font-bold shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white'"
                class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg transition"
                title="Markdown Mode: Raw markdown source with smart paste">
                <i data-lucide="file-code" class="w-3.5 h-3.5"></i>
                <span>Markdown</span>
            </button>

            <button 
                type="button" 
                @click="switchMode('preview')" 
                :class="viewMode === 'preview' ? 'bg-white dark:bg-slate-900 text-emerald-600 dark:text-emerald-400 font-bold shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white'"
                class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg transition"
                title="Preview Mode: Exactly as customer sees on storefront">
                <i data-lucide="eye" class="w-3.5 h-3.5"></i>
                <span>Storefront Preview</span>
            </button>

            <button 
                type="button" 
                @click="switchMode('split')" 
                :class="viewMode === 'split' ? 'bg-white dark:bg-slate-900 text-emerald-600 dark:text-emerald-400 font-bold shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white'"
                class="hidden lg:flex items-center gap-1.5 px-3 py-1.5 rounded-lg transition"
                title="Split Screen: Visual editor on left, live storefront preview on right">
                <i data-lucide="columns-2" class="w-3.5 h-3.5"></i>
                <span>Split Screen</span>
            </button>
        </div>
    </div>

    <!-- Formatting & Smart Gemini Tools Toolbar -->
    <div class="flex flex-wrap items-center justify-between gap-2 bg-slate-50 dark:bg-slate-800/40 p-2.5 rounded-xl border border-slate-200/80 dark:border-slate-800 text-xs">
        
        <!-- Quick Formatting Actions -->
        <div class="flex flex-wrap items-center gap-1">
            <button type="button" @click="formatExec('bold')" class="p-1.5 rounded-lg hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-bold transition" title="Bold (Ctrl+B)">
                <i data-lucide="bold" class="w-4 h-4"></i>
            </button>
            <button type="button" @click="formatExec('italic')" class="p-1.5 rounded-lg hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 italic transition" title="Italic (Ctrl+I)">
                <i data-lucide="italic" class="w-4 h-4"></i>
            </button>
            <div class="h-4 w-px bg-slate-300 dark:bg-slate-700 mx-1"></div>

            <button type="button" @click="formatExec('formatBlock', '<h2>')" class="px-2 py-1 rounded-lg hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-bold font-mono text-[11px] transition" title="Heading 2">
                H2
            </button>
            <button type="button" @click="formatExec('formatBlock', '<h3>')" class="px-2 py-1 rounded-lg hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-bold font-mono text-[11px] transition" title="Heading 3">
                H3
            </button>
            <div class="h-4 w-px bg-slate-300 dark:bg-slate-700 mx-1"></div>

            <button type="button" @click="formatExec('insertUnorderedList')" class="p-1.5 rounded-lg hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 transition" title="Bullet List">
                <i data-lucide="list" class="w-4 h-4"></i>
            </button>
            <button type="button" @click="formatExec('insertOrderedList')" class="p-1.5 rounded-lg hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 transition" title="Numbered List">
                <i data-lucide="list-ordered" class="w-4 h-4"></i>
            </button>
            <button type="button" @click="insertTable()" class="p-1.5 rounded-lg hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 transition" title="Insert Formatted Specs Table">
                <i data-lucide="table" class="w-4 h-4"></i>
            </button>
            <button type="button" @click="insertNoteBlock()" class="p-1.5 rounded-lg hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 transition" title="Insert Highlighted Note Box">
                <i data-lucide="quote" class="w-4 h-4"></i>
            </button>
        </div>

        <!-- Gemini AI Smart Buttons -->
        <div class="flex items-center gap-1.5">
            <!-- 🤖 1-CLICK GEMINI AUTO-GENERATE BUTTON -->
            <button 
                type="button" 
                @click="autoGenerateFromTitle()" 
                :disabled="isGenerating"
                class="px-3 py-1.5 rounded-lg bg-gradient-to-r from-emerald-600 via-teal-600 to-emerald-500 hover:from-emerald-500 hover:to-teal-500 text-white font-black text-[11px] transition flex items-center gap-1.5 shadow-md shadow-emerald-500/20 active:scale-95 disabled:opacity-60 disabled:cursor-not-allowed">
                <template x-if="isGenerating">
                    <div class="flex items-center gap-1.5">
                        <i data-lucide="loader-2" class="w-3.5 h-3.5 animate-spin"></i>
                        <span>Generating with Gemini...</span>
                    </div>
                </template>
                <template x-if="!isGenerating">
                    <div class="flex items-center gap-1.5">
                        <i data-lucide="sparkles" class="w-3.5 h-3.5 text-yellow-300"></i>
                        <span>Auto-Generate with Gemini</span>
                    </div>
                </template>
            </button>

            <button 
                type="button" 
                @click="pasteFromClipboard()" 
                class="px-2.5 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-bold text-[11px] transition flex items-center gap-1.5 border border-slate-200 dark:border-slate-700 shadow-xs"
                title="Paste directly from clipboard with smart Gemini HTML conversion">
                <i data-lucide="clipboard-paste" class="w-3.5 h-3.5 text-emerald-500"></i>
                <span>Paste from Gemini</span>
            </button>

            <button 
                type="button" 
                @click="insertGeminiTemplate()" 
                class="px-2.5 py-1.5 rounded-lg bg-emerald-500/10 hover:bg-emerald-500/20 text-emerald-700 dark:text-emerald-400 font-bold text-[11px] transition flex items-center gap-1.5 border border-emerald-500/30"
                title="Fill with professional electronic hardware demo data">
                <i data-lucide="sparkles" class="w-3.5 h-3.5"></i>
                <span class="hidden sm:inline">Sample Template</span>
            </button>

            <button 
                type="button" 
                @click="confirmClear()" 
                class="p-1.5 rounded-lg hover:bg-rose-100 dark:hover:bg-rose-950/50 text-slate-400 hover:text-rose-600 transition" 
                title="Clear content">
                <i data-lucide="trash-2" class="w-4 h-4"></i>
            </button>
        </div>
    </div>

    <!-- Hidden synced form input for form submission -->
    <textarea 
        name="description" 
        x-model="content"
        class="hidden"></textarea>

    <!-- Generating Banner / Pulse -->
    <div x-show="isGenerating" x-cloak class="p-3.5 rounded-xl bg-gradient-to-r from-emerald-500/15 via-teal-500/15 to-emerald-500/15 border border-emerald-500/30 flex items-center justify-between text-xs text-emerald-800 dark:text-emerald-300 animate-pulse">
        <div class="flex items-center gap-2.5">
            <div class="w-7 h-7 rounded-lg bg-emerald-500 text-slate-950 flex items-center justify-center font-bold shadow-sm">
                <i data-lucide="sparkles" class="w-4 h-4 animate-spin"></i>
            </div>
            <div>
                <p class="font-black text-slate-900 dark:text-white">Gemini AI is crafting your product documentation...</p>
                <p class="text-[11px] text-slate-500 dark:text-slate-400">Analyzing hardware name, building pinout map & technical specifications table.</p>
            </div>
        </div>
        <span class="text-[10px] font-mono px-2 py-1 rounded-md bg-emerald-600 text-white font-bold tracking-wider">AI ACTIVE</span>
    </div>

    <!-- Success Toast -->
    <div x-show="showAiSuccessToast" x-cloak class="p-3 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 flex items-center justify-between text-xs text-emerald-800 dark:text-emerald-300">
        <div class="flex items-center gap-2">
            <i data-lucide="check-circle" class="w-4 h-4 text-emerald-600"></i>
            <span><strong>Success:</strong> Product overview, technical specifications table & pinout guide generated!</span>
        </div>
        <button type="button" @click="showAiSuccessToast = false" class="text-slate-400 hover:text-slate-600">&times;</button>
    </div>

    <!-- Main Workspace Containers -->
    <div class="relative">

        <!-- 1. VISUAL WYSIWYG MODE (Default - Paste from Gemini directly preserves all tables & formatting!) -->
        <div x-show="viewMode === 'visual'" class="space-y-1">
            <div class="flex items-center justify-between text-[11px] text-slate-400 px-1 mb-1">
                <span>Visual Canvas &bull; Click anywhere and press <strong>Ctrl + V</strong> to paste from Gemini</span>
                <span class="text-emerald-500 font-semibold flex items-center gap-1">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 inline-block animate-pulse"></span>
                    Rich Formatting Enabled
                </span>
            </div>

            <div 
                x-ref="visualEditor"
                contenteditable="true"
                @input="syncFromVisual()"
                @paste="handleVisualPaste($event)"
                class="gemini-visual-canvas w-full p-4 sm:p-5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 text-xs sm:text-sm leading-relaxed outline-none focus:ring-2 focus:ring-emerald-500 transition min-h-[350px] max-h-[600px] overflow-y-auto shadow-inner">
            </div>
        </div>

        <!-- 2. MARKDOWN RAW MODE -->
        <div x-show="viewMode === 'markdown'" x-cloak class="space-y-1">
            <div class="flex items-center justify-between text-[11px] text-slate-400 px-1 mb-1">
                <span>Markdown Source Editor &bull; Smart Paste automatically converts Gemini HTML to Markdown tables</span>
                <span class="text-slate-400 font-mono">GFM Syntax</span>
            </div>

            <textarea 
                x-ref="markdownTextarea"
                x-model="content"
                @input="syncFromMarkdown()"
                @paste="handleMarkdownPaste($event)"
                rows="14"
                placeholder="Paste your product description from Google Gemini here...&#10;&#10;When pasting from Gemini, tables and lists will automatically format into clean Markdown!"
                class="w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 text-xs sm:text-sm font-mono leading-relaxed outline-none focus:ring-2 focus:ring-emerald-500 transition resize-y min-h-[350px] max-h-[600px] shadow-inner"></textarea>
        </div>

        <!-- 3. STOREFRONT PREVIEW MODE -->
        <div 
            x-show="viewMode === 'preview'" 
            x-cloak
            class="w-full p-5 sm:p-6 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-950/60 min-h-[350px] max-h-[600px] overflow-y-auto">
            
            <template x-if="!content || content.trim().length === 0">
                <div class="h-64 flex flex-col items-center justify-center text-center text-slate-400">
                    <i data-lucide="file-text" class="w-10 h-10 opacity-30 mb-2"></i>
                    <p class="text-xs font-semibold">No description entered yet.</p>
                    <p class="text-[11px] text-slate-400 mt-0.5">Click "Paste from Gemini" or switch to Visual WYSIWYG to paste your content.</p>
                </div>
            </template>

            <div 
                x-show="content && content.trim().length > 0"
                x-html="previewHtml"
                class="gemini-rendered-preview prose prose-slate max-w-none text-xs sm:text-sm text-slate-800 dark:text-slate-200 leading-relaxed">
            </div>
        </div>

        <!-- 4. SPLIT SCREEN MODE (Side-by-side on desktop) -->
        <div 
            x-show="viewMode === 'split'" 
            x-cloak
            class="grid grid-cols-1 lg:grid-cols-2 gap-4">
            
            <!-- Left: Visual Canvas -->
            <div class="space-y-1">
                <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider flex items-center justify-between px-1">
                    <span>Visual Editor (Paste Here)</span>
                    <span class="text-emerald-500 font-mono">Live Input</span>
                </div>
                <div 
                    x-ref="splitVisualEditor"
                    contenteditable="true"
                    @input="syncFromSplitVisual()"
                    @paste="handleVisualPaste($event)"
                    class="gemini-visual-canvas w-full p-4 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 text-xs leading-relaxed outline-none focus:ring-2 focus:ring-emerald-500 transition min-h-[350px] max-h-[550px] overflow-y-auto">
                </div>
            </div>

            <!-- Right: Real-time Storefront Preview -->
            <div class="space-y-1">
                <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider flex items-center justify-between px-1">
                    <span>Live Storefront Preview</span>
                    <span class="text-slate-400 font-mono">Customer View</span>
                </div>
                <div class="p-4 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-950/60 min-h-[350px] max-h-[550px] overflow-y-auto">
                    <template x-if="!content || content.trim().length === 0">
                        <div class="h-64 flex flex-col items-center justify-center text-center text-slate-400">
                            <i data-lucide="sparkles" class="w-8 h-8 opacity-30 mb-2"></i>
                            <p class="text-xs">Paste on the left to see live rendering here.</p>
                        </div>
                    </template>
                    <div 
                        x-show="content && content.trim().length > 0"
                        x-html="previewHtml"
                        class="gemini-rendered-preview prose prose-slate max-w-none text-xs text-slate-800 dark:text-slate-200 leading-relaxed">
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
            <i data-lucide="shield-check" class="w-3.5 h-3.5"></i>
            <span>Google Gemini Markdown & Tables Ready</span>
        </div>
    </div>

</div>

<!-- Styles for Visual Canvas & Gemini Storefront Preview -->
<style>
/* 1. VISUAL CANVAS STYLING (WYSIWYG) */
.gemini-visual-canvas h1,
.gemini-visual-canvas h2 {
    font-size: 1.15rem;
    font-weight: 800;
    margin-top: 1.25rem;
    margin-bottom: 0.5rem;
    padding-bottom: 0.35rem;
    border-bottom: 1px solid rgba(148, 163, 184, 0.2);
    color: #0f172a;
}
.dark .gemini-visual-canvas h1,
.dark .gemini-visual-canvas h2 {
    color: #f8fafc;
    border-bottom-color: rgba(51, 65, 85, 0.5);
}
.gemini-visual-canvas h3 {
    font-size: 0.95rem;
    font-weight: 700;
    margin-top: 1rem;
    margin-bottom: 0.35rem;
    color: #059669;
}
.dark .gemini-visual-canvas h3 {
    color: #34d399;
}
.gemini-visual-canvas p {
    margin-bottom: 0.75rem;
    line-height: 1.6;
}
.gemini-visual-canvas ul {
    list-style-type: disc;
    padding-left: 1.25rem;
    margin-bottom: 0.75rem;
}
.gemini-visual-canvas ol {
    list-style-type: decimal;
    padding-left: 1.25rem;
    margin-bottom: 0.75rem;
}
.gemini-visual-canvas li {
    margin-bottom: 0.25rem;
}
.gemini-visual-canvas table {
    width: 100%;
    border-collapse: collapse;
    margin: 1rem 0;
    border-radius: 0.75rem;
    overflow: hidden;
    border: 1px solid #cbd5e1;
    font-size: 0.78rem;
}
.dark .gemini-visual-canvas table {
    border-color: #334155;
}
.gemini-visual-canvas th {
    background: #f1f5f9;
    padding: 0.5rem 0.75rem;
    font-weight: 700;
    text-align: left;
    border-bottom: 1px solid #cbd5e1;
    color: #0f172a;
}
.dark .gemini-visual-canvas th {
    background: #1e293b;
    border-bottom-color: #475569;
    color: #f8fafc;
}
.gemini-visual-canvas td {
    padding: 0.5rem 0.75rem;
    border-bottom: 1px solid #f1f5f9;
    border-right: 1px solid #f1f5f9;
}
.dark .gemini-visual-canvas td {
    border-bottom-color: #1e293b;
    border-right-color: #1e293b;
}
.gemini-visual-canvas blockquote {
    border-left: 4px solid #10b981;
    background: rgba(16, 185, 129, 0.08);
    padding: 0.6rem 0.9rem;
    border-radius: 0 0.5rem 0.5rem 0;
    margin: 0.75rem 0;
    font-style: italic;
    font-size: 0.78rem;
}
.gemini-visual-canvas code {
    background: #f1f5f9;
    color: #059669;
    padding: 0.15rem 0.35rem;
    border-radius: 0.25rem;
    font-family: 'JetBrains Mono', monospace;
    font-size: 0.75rem;
}
.dark .gemini-visual-canvas code {
    background: #1e293b;
    color: #34d399;
}

/* 2. PREVIEW RENDERED STYLING */
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
</style>

<!-- Gemini Smart Editor Script -->
<script>
function geminiDescriptionEditor(config = {}) {
    return {
        content: config.initialContent || '',
        viewMode: 'visual', // Default to visual WYSIWYG so pastes preserve all Gemini styles!
        previewHtml: '',
        isGenerating: false,
        showAiSuccessToast: false,
        stats: {
            words: 0,
            chars: 0,
            readingTime: '0 min'
        },
        turndownService: null,

        init() {
            this.initTurndown();
            this.updateStats();

            // Populate initial content into visual editor
            this.$nextTick(() => {
                this.syncContentToVisualCanvas();
                this.updatePreviewHtml();
            });

            this.$watch('content', () => {
                this.updateStats();
                this.updatePreviewHtml();
            });

            // Global event listener for auto-generation from product name button
            window.addEventListener('trigger-ai-generate', () => {
                this.autoGenerateFromTitle();
            });
        },

        initTurndown() {
            if (typeof TurndownService !== 'undefined') {
                this.turndownService = new TurndownService({
                    headingStyle: 'atx',
                    hr: '---',
                    bulletListMarker: '-',
                    codeBlockStyle: 'fenced'
                });
                if (typeof turndownPluginGfm !== 'undefined' && turndownPluginGfm.gfm) {
                    this.turndownService.use(turndownPluginGfm.gfm);
                }
            }
        },

        switchMode(newMode) {
            // Before switching, synchronize state
            if (this.viewMode === 'visual' || this.viewMode === 'split') {
                this.syncFromVisual();
            }

            this.viewMode = newMode;

            this.$nextTick(() => {
                if (newMode === 'visual') {
                    this.syncContentToVisualCanvas();
                } else if (newMode === 'split') {
                    const splitEditor = this.$refs.splitVisualEditor;
                    if (splitEditor) {
                        splitEditor.innerHTML = this.convertMarkdownToHtml(this.content);
                    }
                }
                this.updatePreviewHtml();
                if (typeof lucide !== 'undefined') lucide.createIcons();
            });
        },

        syncContentToVisualCanvas() {
            const visualEditor = this.$refs.visualEditor;
            if (visualEditor) {
                visualEditor.innerHTML = this.convertMarkdownToHtml(this.content);
            }
        },

        convertMarkdownToHtml(text) {
            if (!text || text.trim() === '') return '';
            
            // If already HTML, return directly
            if (/<\/?[a-z][\s\S]*>/i.test(text)) {
                // If it contains markdown headers or tables alongside HTML, parse with marked
                if (typeof marked !== 'undefined' && marked.parse) {
                    try {
                        return marked.parse(text, { gfm: true, breaks: true });
                    } catch (e) {
                        return text;
                    }
                }
                return text;
            }

            if (typeof marked !== 'undefined' && marked.parse) {
                try {
                    return marked.parse(text, { gfm: true, breaks: true });
                } catch (e) {
                    return text.replace(/\n/g, '<br>');
                }
            }
            return text.replace(/\n/g, '<br>');
        },

        convertHtmlToMarkdown(html) {
            if (!html || html.trim() === '') return '';
            if (this.turndownService) {
                try {
                    return this.turndownService.turndown(html);
                } catch (e) {
                    return html;
                }
            }
            return html;
        },

        syncFromVisual() {
            const visualEditor = this.$refs.visualEditor;
            if (visualEditor) {
                const html = visualEditor.innerHTML;
                this.content = this.convertHtmlToMarkdown(html);
            }
        },

        syncFromSplitVisual() {
            const splitEditor = this.$refs.splitVisualEditor;
            if (splitEditor) {
                const html = splitEditor.innerHTML;
                this.content = this.convertHtmlToMarkdown(html);
            }
        },

        syncFromMarkdown() {
            // Already bound via x-model="content"
            this.updatePreviewHtml();
        },

        updatePreviewHtml() {
            this.previewHtml = this.convertMarkdownToHtml(this.content);
        },

        // Smart Paste Handler for Visual Canvas
        handleVisualPaste(e) {
            const clipboardData = e.clipboardData || window.clipboardData;
            if (!clipboardData) return;

            const html = clipboardData.getData('text/html');
            const plainText = clipboardData.getData('text/plain');

            if (html) {
                // We have rich HTML from Gemini!
                e.preventDefault();
                
                // Sanitize / clean Gemini HTML: remove container wrappers but keep headings, lists, tables, bold
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                
                // Remove scripts, styles, meta
                doc.querySelectorAll('script, style, meta, link').forEach(el => el.remove());
                
                // Clean class attributes on tables to use our styling
                doc.querySelectorAll('table').forEach(tbl => {
                    tbl.removeAttribute('style');
                    tbl.removeAttribute('class');
                });

                const cleanHtml = doc.body.innerHTML;
                document.execCommand('insertHTML', false, cleanHtml);

                this.$nextTick(() => {
                    this.syncFromVisual();
                    this.updatePreviewHtml();
                });
            } else if (plainText && (plainText.includes('##') || plainText.includes('|') || plainText.includes('- '))) {
                // Plain text is actually Markdown from Gemini!
                e.preventDefault();
                const convertedHtml = this.convertMarkdownToHtml(plainText);
                document.execCommand('insertHTML', false, convertedHtml);

                this.$nextTick(() => {
                    this.syncFromVisual();
                    this.updatePreviewHtml();
                });
            }
        },

        // Smart Paste Handler for Markdown Textarea
        handleMarkdownPaste(e) {
            const clipboardData = e.clipboardData || window.clipboardData;
            if (!clipboardData) return;

            const html = clipboardData.getData('text/html');
            if (html && this.turndownService) {
                // Rich Gemini HTML copied into Markdown textarea -> Convert directly into pristine Markdown!
                e.preventDefault();
                try {
                    const md = this.turndownService.turndown(html);
                    this.insertAtCursor(md);
                } catch (err) {
                    // Fallback to normal paste
                }
            }
        },

        // Click-to-Paste from Clipboard Button
        async pasteFromClipboard() {
            try {
                if (navigator.clipboard && navigator.clipboard.read) {
                    const items = await navigator.clipboard.read();
                    for (const item of items) {
                        if (item.types.includes('text/html')) {
                            const blob = await item.getType('text/html');
                            const html = await blob.text();
                            
                            if (this.viewMode === 'visual') {
                                const visualEditor = this.$refs.visualEditor;
                                if (visualEditor) {
                                    visualEditor.focus();
                                    document.execCommand('insertHTML', false, html);
                                    this.syncFromVisual();
                                }
                            } else {
                                const md = this.convertHtmlToMarkdown(html);
                                this.insertAtCursor(md);
                            }
                            return;
                        } else if (item.types.includes('text/plain')) {
                            const blob = await item.getType('text/plain');
                            const text = await blob.text();
                            if (this.viewMode === 'visual') {
                                const html = this.convertMarkdownToHtml(text);
                                const visualEditor = this.$refs.visualEditor;
                                if (visualEditor) {
                                    visualEditor.focus();
                                    document.execCommand('insertHTML', false, html);
                                    this.syncFromVisual();
                                }
                            } else {
                                this.insertAtCursor(text);
                            }
                            return;
                        }
                    }
                } else if (navigator.clipboard && navigator.clipboard.readText) {
                    const text = await navigator.clipboard.readText();
                    if (this.viewMode === 'visual') {
                        const html = this.convertMarkdownToHtml(text);
                        const visualEditor = this.$refs.visualEditor;
                        if (visualEditor) {
                            visualEditor.focus();
                            document.execCommand('insertHTML', false, html);
                            this.syncFromVisual();
                        }
                    } else {
                        this.insertAtCursor(text);
                    }
                }
            } catch (err) {
                alert('Please press Ctrl+V directly inside the editor box to paste your Gemini content.');
            }
        },

        insertAtCursor(text) {
            const textarea = this.$refs.markdownTextarea;
            if (textarea && this.viewMode === 'markdown') {
                const start = textarea.selectionStart || 0;
                const end = textarea.selectionEnd || 0;
                this.content = this.content.substring(0, start) + text + this.content.substring(end);
                this.$nextTick(() => {
                    textarea.focus();
                    textarea.setSelectionRange(start + text.length, start + text.length);
                    this.updatePreviewHtml();
                });
            } else {
                this.content += '\n' + text;
                this.syncContentToVisualCanvas();
                this.updatePreviewHtml();
            }
        },

        formatExec(command, value = null) {
            if (this.viewMode === 'visual') {
                const visualEditor = this.$refs.visualEditor;
                if (visualEditor) visualEditor.focus();
                document.execCommand(command, false, value);
                this.syncFromVisual();
            } else {
                if (command === 'bold') this.insertSyntaxMarkdown('**', '**', 'bold text');
                else if (command === 'italic') this.insertSyntaxMarkdown('*', '*', 'italic text');
                else if (command === 'insertUnorderedList') this.insertSyntaxMarkdown('- ', '', 'Bullet item');
                else if (command === 'insertOrderedList') this.insertSyntaxMarkdown('1. ', '', 'List item');
                else if (value === '<h2>') this.insertSyntaxMarkdown('## ', '', 'Section Title');
                else if (value === '<h3>') this.insertSyntaxMarkdown('### ', '', 'Subheading Title');
            }
        },

        insertSyntaxMarkdown(before, after = '', defaultPlaceholder = '') {
            const textarea = this.$refs.markdownTextarea;
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
                this.updatePreviewHtml();
            });
        },

        insertTable() {
            if (this.viewMode === 'visual') {
                const tableHtml = `
                <table>
                    <thead>
                        <tr>
                            <th>Parameter</th>
                            <th>Specification</th>
                            <th>Operating Condition</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><strong>Voltage</strong></td>
                            <td>3.3V ~ 5.0V DC</td>
                            <td>Wide Range</td>
                        </tr>
                        <tr>
                            <td><strong>Current</strong></td>
                            <td>15mA (Idle) / 80mA (Peak)</td>
                            <td>Low Power</td>
                        </tr>
                        <tr>
                            <td><strong>Operating Temp</strong></td>
                            <td>-40°C ~ +85°C</td>
                            <td>Industrial Grade</td>
                        </tr>
                    </tbody>
                </table><p></p>`;
                const visualEditor = this.$refs.visualEditor;
                if (visualEditor) visualEditor.focus();
                document.execCommand('insertHTML', false, tableHtml);
                this.syncFromVisual();
            } else {
                const tableMd = `\n| Parameter | Specification | Operating Condition |\n| :--- | :--- | :--- |\n| **Voltage** | 3.3V ~ 5.0V DC | Wide Range |\n| **Current** | 15mA (Idle) / 80mA (Peak) | Low Power |\n| **Operating Temp** | -40°C ~ +85°C | Industrial Grade |\n\n`;
                this.insertAtCursor(tableMd);
            }
        },

        insertNoteBlock() {
            if (this.viewMode === 'visual') {
                const noteHtml = `<blockquote><strong>Note:</strong> Ensure proper ESD protection and decoupling capacitors are placed near power lines.</blockquote><p></p>`;
                const visualEditor = this.$refs.visualEditor;
                if (visualEditor) visualEditor.focus();
                document.execCommand('insertHTML', false, noteHtml);
                this.syncFromVisual();
            } else {
                this.insertAtCursor(`\n> **Note:** Ensure proper ESD protection and decoupling capacitors are placed near power lines.\n\n`);
            }
        },

        insertGeminiTemplate() {
            const templateHtml = `
                <h2>Product Overview</h2>
                <p>The <strong>High-Performance Embedded Component</strong> is an industrial-grade electronic module designed for IoT prototyping, robotics, PCB engineering, and precision instrumentation.</p>

                <h3>Key Hardware Features</h3>
                <ul>
                    <li><strong>Core Processor:</strong> Ultra-fast 32-bit RISC core with integrated low-power sleep modes</li>
                    <li><strong>Communication Protocols:</strong> Hardware SPI, I2C, UART and Full-Speed USB interface</li>
                    <li><strong>Integrated Circuit Protection:</strong> On-board reverse polarity and transient ESD protection diodes</li>
                    <li><strong>Breadboard Compatibility:</strong> Standard 2.54mm (0.1") pitch pin headers for fast wiring</li>
                </ul>

                <h3>Technical Specifications</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Parameter</th>
                            <th>Specification</th>
                            <th>Details / Notes</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><strong>Operating Voltage</strong></td>
                            <td>3.3V ~ 5.0V DC</td>
                            <td>Standard Logic Tolerant</td>
                        </tr>
                        <tr>
                            <td><strong>Current Consumption</strong></td>
                            <td>15mA (Standby) / 85mA (Peak)</td>
                            <td>Energy-efficient</td>
                        </tr>
                        <tr>
                            <td><strong>Clock Frequency</strong></td>
                            <td>72 MHz Fast Clock</td>
                            <td>High-speed processing</td>
                        </tr>
                        <tr>
                            <td><strong>Operating Temperature</strong></td>
                            <td>-40°C to +85°C</td>
                            <td>Industrial Grade</td>
                        </tr>
                    </tbody>
                </table>

                <h3>Pinout & Peripheral Map</h3>
                <ul>
                    <li><strong>VCC:</strong> 3.3V / 5V DC Main Power Supply Input</li>
                    <li><strong>GND:</strong> Common System Ground</li>
                    <li><strong>TX / RX:</strong> High-Speed Hardware UART Serial Interface</li>
                    <li><strong>SDA / SCL:</strong> Two-wire I2C Bus Data & Clock Lines</li>
                </ul>

                <blockquote><strong>Pro Tip:</strong> For high-noise industrial environments, place a 100nF ceramic capacitor across the VCC and GND pins right next to the header pins for maximum signal integrity.</blockquote>
            `.trim();

            if (this.content && this.content.trim().length > 0) {
                if (!confirm('Replace current description with the Gemini Hardware Template?')) return;
            }

            if (this.viewMode === 'visual') {
                const visualEditor = this.$refs.visualEditor;
                if (visualEditor) {
                    visualEditor.innerHTML = templateHtml;
                    this.syncFromVisual();
                }
            } else {
                this.content = this.convertHtmlToMarkdown(templateHtml);
                this.syncContentToVisualCanvas();
            }
            this.updatePreviewHtml();
        },

        async autoGenerateFromTitle() {
            // 1. Locate product name input
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

            // 2. Extract optional category and chipset/model
            const categorySelect = document.querySelector('select[name="category_id"]');
            const categoryName = categorySelect && categorySelect.selectedIndex > 0 ? categorySelect.options[categorySelect.selectedIndex].text : '';
            const chipsetInput = document.querySelector('input[name="chipset"]');
            const chipset = chipsetInput ? chipsetInput.value.trim() : '';

            this.isGenerating = true;

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
                        name: productName,
                        category: categoryName,
                        chipset: chipset
                    })
                });

                const data = await response.json();

                if (data.success && data.description) {
                    // Populate full technical description
                    this.content = data.description;
                    this.syncContentToVisualCanvas();
                    this.updatePreviewHtml();

                    // Populate short description if input is present
                    const shortDescTextarea = document.querySelector('textarea[name="short_description"]');
                    if (shortDescTextarea && data.short_description) {
                        shortDescTextarea.value = data.short_description;
                        shortDescTextarea.dispatchEvent(new Event('input', { bubbles: true }));
                    }

                    // Switch to visual mode to show immediate rich layout
                    this.viewMode = 'visual';

                    this.showAiSuccessToast = true;
                    setTimeout(() => { this.showAiSuccessToast = false; }, 6000);
                } else {
                    alert(data.message || 'Could not generate description. Please try again.');
                }
            } catch (err) {
                console.error('Gemini AI Generation Error:', err);
                alert('Connection error while contacting AI generator. Please try again.');
            } finally {
                this.isGenerating = false;
                this.$nextTick(() => {
                    if (typeof lucide !== 'undefined') lucide.createIcons();
                });
            }
        },

        confirmClear() {
            if (confirm('Clear the entire description?')) {
                this.content = '';
                const visualEditor = this.$refs.visualEditor;
                if (visualEditor) visualEditor.innerHTML = '';
                const splitEditor = this.$refs.splitVisualEditor;
                if (splitEditor) splitEditor.innerHTML = '';
                this.updatePreviewHtml();
            }
        },

        updateStats() {
            const raw = this.content || '';
            this.stats.chars = raw.length;
            const wordsArr = raw.replace(/<[^>]*>/g, '').trim().split(/\s+/).filter(w => w.length > 0);
            this.stats.words = wordsArr.length;
            const minutes = Math.max(1, Math.ceil(this.stats.words / 200));
            this.stats.readingTime = this.stats.words === 0 ? '0 min' : `~${minutes} min`;
        }
    };
}
</script>
