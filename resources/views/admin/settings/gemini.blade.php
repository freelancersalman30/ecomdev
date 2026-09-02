@extends('layouts.admin')

@section('title', 'Google Gemini AI Settings')
@section('page-title', 'Google Gemini AI Settings & Prompt Engine')

@section('content')
<div class="max-w-4xl mx-auto space-y-6" x-data="geminiSettingsPage()">

    <!-- Header Status Alert -->
    <div class="bg-gradient-to-r from-slate-900 via-slate-800 to-slate-900 rounded-2xl p-6 border border-slate-700/60 shadow-lg text-white flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="space-y-1">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-xl bg-gradient-to-tr from-emerald-500 to-teal-400 text-slate-950 flex items-center justify-center font-black shadow-md shadow-emerald-500/20">
                    <i data-lucide="sparkles" class="w-5 h-5 text-slate-950"></i>
                </div>
                <h2 class="text-base font-black tracking-tight">Google Gemini AI Engine</h2>
                <span class="text-[10px] px-2.5 py-0.5 rounded-full font-bold uppercase tracking-wider {{ !empty($apiKey) ? 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/30' : 'bg-amber-500/20 text-amber-400 border border-amber-500/30' }}">
                    {{ !empty($apiKey) ? 'Google API Connected' : 'Offline Hardware Engine Active' }}
                </span>
            </div>
            <p class="text-xs text-slate-400 max-w-xl">
                Powers 1-click product description generation, technical hardware specifications, pinout mapping, and Google SEO metadata for your products.
            </p>
        </div>

        <a href="https://aistudio.google.com/app/apikey" target="_blank" rel="noopener noreferrer" class="px-4 py-2 rounded-xl bg-white/10 hover:bg-white/20 border border-white/15 text-white font-bold text-xs flex items-center gap-2 transition flex-shrink-0">
            <i data-lucide="external-link" class="w-3.5 h-3.5 text-emerald-400"></i>
            <span>Get Free Gemini API Key</span>
        </a>
    </div>

    <!-- Main Settings Form -->
    <form method="POST" action="{{ route('admin.settings.gemini.update') }}" class="space-y-6">
        @csrf
        @method('PUT')

        <!-- 1. API Credentials Card -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 border border-slate-200 dark:border-slate-800 shadow-sm space-y-4">
            <div class="flex items-center justify-between">
                <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                    <i data-lucide="key" class="w-4 h-4 text-emerald-500"></i>
                    <span>Gemini API Authentication</span>
                </h3>
                <span class="text-[11px] text-slate-400 font-mono">Google AI Studio</span>
            </div>

            <div class="space-y-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Google Gemini API Key</label>
                    <div class="relative flex items-center">
                        <input 
                            :type="showApiKey ? 'text' : 'password'" 
                            name="gemini_api_key" 
                            x-model="apiKey"
                            placeholder="AIzaSy..." 
                            class="w-full pl-3.5 pr-24 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-mono outline-none focus:ring-2 focus:ring-emerald-500">
                        
                        <div class="absolute right-2 flex items-center gap-1">
                            <button 
                                type="button" 
                                @click="showApiKey = !showApiKey" 
                                class="p-1 rounded-lg text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 text-xs" 
                                :title="showApiKey ? 'Hide' : 'Show'">
                                <i :data-lucide="showApiKey ? 'eye-off' : 'eye'" class="w-4 h-4"></i>
                            </button>
                            <button 
                                type="button" 
                                @click="testApiConnection()" 
                                :disabled="isTesting"
                                class="px-2.5 py-1 rounded-lg bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 hover:bg-emerald-100 font-bold text-[11px] transition flex items-center gap-1 border border-emerald-500/20 disabled:opacity-50">
                                <template x-if="isTesting">
                                    <i data-lucide="loader-2" class="w-3 h-3 animate-spin"></i>
                                </template>
                                <span>Test Key</span>
                            </button>
                        </div>
                    </div>
                    <p class="text-[11px] text-slate-400 mt-1">
                        Leave blank to use the built-in Smart Hardware Heuristics Engine. Both work seamlessly without code modifications.
                    </p>
                </div>

                <!-- Test Connection Status Result -->
                <div x-show="testResult" x-cloak class="p-3 rounded-xl text-xs font-medium transition" :class="testSuccess ? 'bg-emerald-50 dark:bg-emerald-950/40 text-emerald-800 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800' : 'bg-rose-50 dark:bg-rose-950/40 text-rose-800 dark:text-rose-300 border border-rose-200 dark:border-rose-800'">
                    <div class="flex items-start gap-2">
                        <i :data-lucide="testSuccess ? 'check-circle' : 'alert-circle'" class="w-4 h-4 flex-shrink-0 mt-0.5"></i>
                        <span x-text="testResult"></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. AI Model & Generation Parameters -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 border border-slate-200 dark:border-slate-800 shadow-sm space-y-5">
            <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                <i data-lucide="cpu" class="w-4 h-4 text-sky-500"></i>
                <span>Model Architecture & Output Controls</span>
            </h3>

            <!-- Model Selection Cards -->
            <div class="space-y-2">
                <label class="block text-xs font-semibold text-slate-500">Preferred AI Model</label>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                    @foreach($availableModels as $mKey => $mInfo)
                    <label class="relative p-4 rounded-xl border-2 cursor-pointer transition flex flex-col justify-between"
                           :class="selectedModel === '{{ $mKey }}' ? 'border-emerald-500 bg-emerald-50/30 dark:bg-emerald-950/20' : 'border-slate-200 dark:border-slate-800 hover:border-slate-300'">
                        <input type="radio" name="gemini_model" value="{{ $mKey }}" x-model="selectedModel" class="sr-only">
                        <div>
                            <div class="flex items-center justify-between mb-1.5">
                                <span class="font-extrabold text-xs text-slate-900 dark:text-white">{{ $mKey }}</span>
                                <span class="text-[9px] font-bold px-1.5 py-0.5 rounded bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300">
                                    {{ $mInfo['tag'] }}
                                </span>
                            </div>
                            <p class="text-[11px] text-slate-500 leading-relaxed">{{ $mInfo['description'] }}</p>
                        </div>
                        <div class="mt-3 flex items-center text-[11px] font-bold" :class="selectedModel === '{{ $mKey }}' ? 'text-emerald-600' : 'text-slate-400'">
                            <span x-text="selectedModel === '{{ $mKey }}' ? '✓ Selected' : 'Select Model'"></span>
                        </div>
                    </label>
                    @endforeach
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 pt-2 border-t border-slate-100 dark:border-slate-800">
                <!-- Temperature -->
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <label class="block text-xs font-semibold text-slate-500">Creativity / Temperature</label>
                        <span class="text-xs font-mono font-bold text-emerald-600" x-text="temperature"></span>
                    </div>
                    <input 
                        type="range" 
                        name="gemini_temperature" 
                        min="0.1" 
                        max="1.0" 
                        step="0.05" 
                        x-model="temperature" 
                        class="w-full accent-emerald-600 cursor-pointer">
                    <div class="flex items-center justify-between text-[10px] text-slate-400 mt-1">
                        <span>0.1 (Strict & Factual)</span>
                        <span>0.4 (Recommended)</span>
                        <span>1.0 (Creative)</span>
                    </div>
                </div>

                <!-- Auto SEO Toggle -->
                <div class="flex flex-col justify-center">
                    <label class="flex items-start gap-3 cursor-pointer">
                        <input 
                            type="checkbox" 
                            name="gemini_auto_seo" 
                            value="1" 
                            {{ $autoSeo == '1' ? 'checked' : '' }} 
                            class="rounded text-emerald-600 focus:ring-emerald-500 w-4 h-4 mt-0.5">
                        <div>
                            <span class="text-xs font-bold text-slate-800 dark:text-slate-200 block">
                                Automatic SEO Metadata Generation
                            </span>
                            <span class="text-[11px] text-slate-500 block leading-tight">
                                Automatically craft Google SERP Meta Title, Meta Description (160 chars), and target keywords whenever generating a product description.
                            </span>
                        </div>
                    </label>
                </div>
            </div>
        </div>

        <!-- 3. Live Test Generator Box -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 border border-slate-200 dark:border-slate-800 shadow-sm space-y-4">
            <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                <i data-lucide="play-circle" class="w-4 h-4 text-emerald-500"></i>
                <span>Test Live Generation (Sandbox)</span>
            </h3>

            <div class="flex flex-col sm:flex-row items-center gap-2">
                <input 
                    type="text" 
                    x-model="testPromptTitle" 
                    placeholder="Enter sample product name, e.g. ESP32-CAM WiFi Bluetooth Camera Module" 
                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs outline-none">
                <button 
                    type="button" 
                    @click="runSampleGeneration()" 
                    :disabled="isGeneratingSample"
                    class="w-full sm:w-auto px-4 py-2.5 rounded-xl bg-slate-900 dark:bg-slate-800 text-white font-bold text-xs whitespace-nowrap hover:bg-slate-800 transition flex items-center justify-center gap-1.5 disabled:opacity-50">
                    <template x-if="isGeneratingSample">
                        <i data-lucide="loader-2" class="w-3.5 h-3.5 animate-spin"></i>
                    </template>
                    <span>Test Prompt</span>
                </button>
            </div>

            <!-- Sandbox Output Preview -->
            <div x-show="sampleOutput" x-cloak class="mt-3 p-4 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-950/60 max-h-72 overflow-y-auto text-xs font-mono leading-relaxed text-slate-800 dark:text-slate-200 whitespace-pre-wrap" x-text="sampleOutput">
            </div>
        </div>

        <!-- Save Button -->
        <div class="flex items-center justify-end gap-3">
            <button type="submit" class="px-7 py-3 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-extrabold text-xs shadow-lg shadow-emerald-500/20 transition flex items-center gap-2">
                <i data-lucide="save" class="w-4 h-4"></i>
                <span>Save Gemini AI Settings</span>
            </button>
        </div>

    </form>

</div>
@endsection

@push('scripts')
<script>
function geminiSettingsPage() {
    return {
        apiKey: @js($apiKey ?? ''),
        selectedModel: @js($model ?? 'gemini-1.5-flash'),
        temperature: @js($temperature ?? '0.4'),
        showApiKey: false,
        isTesting: false,
        testResult: '',
        testSuccess: false,

        testPromptTitle: 'ESP32-CAM WiFi + Bluetooth Camera Module OV2640',
        isGeneratingSample: false,
        sampleOutput: '',

        async testApiConnection() {
            this.isTesting = true;
            this.testResult = 'Testing connection to Google AI Studio servers...';
            this.testSuccess = false;

            try {
                const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                const response = await fetch("{{ route('admin.settings.gemini.test') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrf || ''
                    },
                    body: JSON.stringify({
                        api_key: this.apiKey,
                        model: this.selectedModel
                    })
                });

                const data = await response.json();
                this.testSuccess = data.success;
                this.testResult = data.message;
            } catch (e) {
                this.testSuccess = false;
                this.testResult = 'Network error while attempting to test connection.';
            } finally {
                this.isTesting = false;
                this.$nextTick(() => { if (typeof lucide !== 'undefined') lucide.createIcons(); });
            }
        },

        async runSampleGeneration() {
            if (!this.testPromptTitle.trim()) {
                alert('Please enter a sample product title');
                return;
            }

            this.isGeneratingSample = true;
            this.sampleOutput = 'Contacting Gemini AI and compiling technical specifications table...';

            try {
                const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                const response = await fetch("{{ route('admin.products.ai.generate') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrf || ''
                    },
                    body: JSON.stringify({
                        name: this.testPromptTitle
                    })
                });

                const data = await response.json();
                if (data.success) {
                    this.sampleOutput = "=== SEO META TITLE ===\n" + (data.meta_title || 'N/A') + "\n\n"
                        + "=== SEO META DESCRIPTION ===\n" + (data.meta_description || 'N/A') + "\n\n"
                        + "=== SEO KEYWORDS ===\n" + (data.meta_keywords || 'N/A') + "\n\n"
                        + "=== TECHNICAL SPECIFICATIONS & PINOUTS ===\n" + data.description;
                } else {
                    this.sampleOutput = 'Error: ' + (data.message || 'Generation failed.');
                }
            } catch (e) {
                this.sampleOutput = 'Error: Failed to connect to local generation endpoint.';
            } finally {
                this.isGeneratingSample = false;
            }
        }
    };
}
</script>
@endpush
