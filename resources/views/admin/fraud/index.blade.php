@extends('layouts.admin')

@section('title', 'Fraud & Risk Detection')
@section('page-title', 'Fraud & Courier Delivery Success Rate Check')

@section('content')
<div class="space-y-6" x-data="fraudManager()">

    <!-- Top Integration Status Banner -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 border border-slate-200 dark:border-slate-800 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-base font-bold text-slate-900 dark:text-white flex items-center gap-2">
                <i data-lucide="shield-check" class="w-5 h-5 text-sky-500"></i>
                <span>Fraud & Courier Risk Detection Hub</span>
            </h2>
            <p class="text-xs text-slate-500 mt-1">Live customer background analysis via Zachaikori API, Universal Fraud Gateways, and past store delivery analytics.</p>
        </div>
        <div class="flex items-center gap-2 text-xs">
            @if($activeApi)
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-emerald-50 dark:bg-emerald-950/50 border border-emerald-200 dark:border-emerald-800 text-emerald-700 dark:text-emerald-300 font-bold">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span>Active Gateway: {{ $activeApi->title }}</span>
                </span>
            @else
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-amber-50 dark:bg-amber-950/50 border border-amber-200 dark:border-amber-800 text-amber-700 dark:text-amber-300 font-bold">
                    <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                    <span>Mode: Internal Store Analytics (No Live API Connected)</span>
                </span>
            @endif
        </div>
    </div>

    @if(session('success'))
        <div class="p-4 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300 text-xs font-semibold flex items-center gap-2">
            <i data-lucide="check-circle" class="w-4 h-4 text-emerald-600 shrink-0"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <!-- Phone Number Risk Lookup & Blacklist Form -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

        <!-- Phone Lookup Card -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 border border-slate-200 dark:border-slate-800 shadow-sm space-y-4">
            <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                <i data-lucide="shield-search" class="w-4 h-4 text-sky-500"></i>
                <span>Courier Delivery Success Rate & Risk Lookup</span>
            </h3>
            <p class="text-xs text-slate-500">Check customer phone numbers against courier delivery histories, return rate scores, and past COD cancellations.</p>
            
            <form method="POST" action="{{ route('admin.fraud.check') }}" class="flex items-center gap-2">
                @csrf
                <div class="relative flex-1">
                    <i data-lucide="phone" class="w-4 h-4 absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400"></i>
                    <input type="text" name="phone" required placeholder="Enter customer phone (e.g. 01900998877)" class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-mono font-bold outline-none focus:ring-2 focus:ring-sky-500">
                </div>
                <button type="submit" class="px-5 py-2.5 bg-sky-600 hover:bg-sky-500 text-white rounded-xl text-xs font-bold shadow-md transition flex items-center gap-1.5">
                    <i data-lucide="search" class="w-4 h-4"></i>
                    <span>Evaluate Risk</span>
                </button>
            </form>
        </div>

        <!-- Manual Blacklist Form -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 border border-slate-200 dark:border-slate-800 shadow-sm space-y-4">
            <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                <i data-lucide="ban" class="w-4 h-4 text-rose-500"></i>
                <span>Add Number / IP to Blacklist</span>
            </h3>
            <p class="text-xs text-slate-500">Explicitly flag known fraudulent buyers to automatically block new incoming COD orders.</p>
            
            <form method="POST" action="{{ route('admin.fraud.blacklist') }}" class="space-y-3">
                @csrf
                <div class="grid grid-cols-2 gap-3">
                    <input type="text" name="phone" required placeholder="Phone number *" class="w-full px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-mono outline-none focus:ring-2 focus:ring-rose-500">
                    <input type="text" name="notes" placeholder="Reason (e.g. Repeated parcel return)" class="w-full px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs outline-none focus:ring-2 focus:ring-rose-500">
                </div>
                <button type="submit" class="w-full py-2 bg-rose-600 hover:bg-rose-500 text-white rounded-xl text-xs font-bold shadow-md transition">
                    + Blacklist Number
                </button>
            </form>
        </div>

    </div>

    <!-- API Connection Configuration Section -->
    <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 border border-slate-200 dark:border-slate-800 shadow-sm space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-100 dark:border-slate-800 pb-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-indigo-50 dark:bg-indigo-950/50 border border-indigo-200 dark:border-indigo-800 flex items-center justify-center text-indigo-600 dark:text-indigo-400 font-bold">
                    <i data-lucide="radio-tower" class="w-5 h-5"></i>
                </div>
                <div>
                    <h3 class="font-extrabold text-base text-slate-900 dark:text-white">Fraud & Risk API Connection Hub</h3>
                    <p class="text-xs text-slate-500">Configure Zachaikori API or any Universal/Custom Courier Fraud API for automatic order risk analysis.</p>
                </div>
            </div>

            <!-- Provider Selection Tabs -->
            <div class="flex items-center gap-1.5 p-1 bg-slate-100 dark:bg-slate-800 rounded-xl">
                <button type="button" @click="activeApiTab = 'zachaikori'" :class="activeApiTab === 'zachaikori' ? 'bg-white dark:bg-slate-700 text-indigo-600 dark:text-indigo-300 font-bold shadow-xs' : 'text-slate-600 dark:text-slate-400 font-medium'" class="px-3.5 py-1.5 rounded-lg text-xs transition">
                    ⚡ Zachaikori API
                </button>
                <button type="button" @click="activeApiTab = 'universal'" :class="activeApiTab === 'universal' ? 'bg-white dark:bg-slate-700 text-indigo-600 dark:text-indigo-300 font-bold shadow-xs' : 'text-slate-600 dark:text-slate-400 font-medium'" class="px-3.5 py-1.5 rounded-lg text-xs transition">
                    🌐 Universal Custom API
                </button>
            </div>
        </div>

        <!-- Tab 1: Zachaikori API Configuration -->
        <div x-show="activeApiTab === 'zachaikori'" class="space-y-6">
            <div class="flex items-center justify-between">
                <div>
                    <h4 class="text-xs font-bold text-slate-900 dark:text-white uppercase tracking-wider">Zachaikori / Jachaikori Integration</h4>
                    <p class="text-[11px] text-slate-500">Connect to Zachaikori courier fraud detection service across Steadfast, Pathao, RedX, and Paperfly.</p>
                </div>
                <div>
                    <span class="text-xs font-extrabold uppercase px-3 py-1 rounded-full {{ ($apis['zachaikori']->is_active ?? false) ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300 border border-emerald-200' : 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400' }}">
                        {{ ($apis['zachaikori']->is_active ?? false) ? 'Active Gateway' : 'Inactive' }}
                    </span>
                </div>
            </div>

            <form method="POST" action="{{ route('admin.fraud.api_settings') }}" class="space-y-4">
                @csrf
                <input type="hidden" name="provider" value="zachaikori">

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">API Endpoint URL *</label>
                        <input type="url" name="endpoint_url" value="{{ old('endpoint_url', $apis['zachaikori']->credentials['endpoint_url'] ?? 'https://api.zachaikori.com/api/v1/check') }}" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs font-mono outline-none focus:ring-2 focus:ring-indigo-500" placeholder="https://api.zachaikori.com/api/v1/check">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">API Key / Bearer Token *</label>
                        <input type="password" name="api_key" value="{{ old('api_key', $apis['zachaikori']->credentials['api_key'] ?? '') }}" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs font-mono outline-none focus:ring-2 focus:ring-indigo-500" placeholder="Enter Zachaikori API Key">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">HTTP Request Method</label>
                        <select name="http_method" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs font-bold outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="GET" {{ ($apis['zachaikori']->credentials['http_method'] ?? 'GET') === 'GET' ? 'selected' : '' }}>GET Request (?phone=017...)</option>
                            <option value="POST_JSON" {{ ($apis['zachaikori']->credentials['http_method'] ?? '') === 'POST_JSON' ? 'selected' : '' }}>POST Request (JSON Payload)</option>
                            <option value="POST" {{ ($apis['zachaikori']->credentials['http_method'] ?? '') === 'POST' ? 'selected' : '' }}>POST Request (Form Data)</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Phone Parameter Name</label>
                        <input type="text" name="phone_param" value="{{ old('phone_param', $apis['zachaikori']->credentials['phone_param'] ?? 'phone') }}" class="w-full px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs font-mono outline-none" placeholder="phone">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Minimum Safe Delivery Rate Threshold (%)</label>
                        <input type="number" name="min_success_rate" min="10" max="100" value="{{ old('min_success_rate', $apis['zachaikori']->credentials['min_success_rate'] ?? 60) }}" class="w-full px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs font-bold outline-none" placeholder="60">
                        <span class="text-[10px] text-slate-400">Orders from numbers below this delivery rate will be flagged as suspicious.</span>
                    </div>
                </div>

                <div class="flex items-center justify-between pt-2 border-t border-slate-100 dark:border-slate-800">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" {{ ($apis['zachaikori']->is_active ?? false) ? 'checked' : '' }} class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                        <span class="text-xs font-bold text-slate-800 dark:text-slate-200">Activate Zachaikori as Primary Fraud Check Gateway</span>
                    </label>

                    <div class="flex items-center gap-2">
                        <button type="button" @click="testApi('zachaikori')" class="px-4 py-2 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-800 dark:text-slate-200 text-xs font-bold transition flex items-center gap-1.5">
                            <i data-lucide="zap" class="w-4 h-4 text-amber-500"></i>
                            <span>Test Live Ping</span>
                        </button>
                        <button type="submit" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl text-xs font-bold shadow-md transition">
                            Save Zachaikori Settings
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Tab 2: Universal Custom Fraud API Configuration -->
        <div x-show="activeApiTab === 'universal'" class="space-y-6">
            <div class="flex items-center justify-between">
                <div>
                    <h4 class="text-xs font-bold text-slate-900 dark:text-white uppercase tracking-wider">Universal Fraud & Courier Risk API</h4>
                    <p class="text-[11px] text-slate-500">Plug in any third-party fraud checking provider (FraudChecker BD, Jachaikori, custom webhook, etc.) by mapping its parameters and response keys.</p>
                </div>
                <div>
                    <span class="text-xs font-extrabold uppercase px-3 py-1 rounded-full {{ ($apis['universal_fraud']->is_active ?? false) ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300 border border-emerald-200' : 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400' }}">
                        {{ ($apis['universal_fraud']->is_active ?? false) ? 'Active Gateway' : 'Inactive' }}
                    </span>
                </div>
            </div>

            <form method="POST" action="{{ route('admin.fraud.api_settings') }}" class="space-y-4">
                @csrf
                <input type="hidden" name="provider" value="universal_fraud">

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Provider Name</label>
                        <input type="text" name="provider_name" value="{{ old('provider_name', $apis['universal_fraud']->credentials['provider_name'] ?? 'Universal Fraud Checker') }}" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs font-semibold outline-none focus:ring-2 focus:ring-indigo-500" placeholder="e.g. FraudChecker BD">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">HTTP Request Method</label>
                        <select name="http_method" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs font-bold outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="GET" {{ ($apis['universal_fraud']->credentials['http_method'] ?? 'GET') === 'GET' ? 'selected' : '' }}>GET Request (Query Params / {phone} URL)</option>
                            <option value="POST_JSON" {{ ($apis['universal_fraud']->credentials['http_method'] ?? '') === 'POST_JSON' ? 'selected' : '' }}>POST Request (JSON Body)</option>
                            <option value="POST" {{ ($apis['universal_fraud']->credentials['http_method'] ?? '') === 'POST' ? 'selected' : '' }}>POST Request (Form Data)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">API Endpoint URL *</label>
                        <input type="url" name="endpoint_url" value="{{ old('endpoint_url', $apis['universal_fraud']->credentials['endpoint_url'] ?? '') }}" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs font-mono outline-none focus:ring-2 focus:ring-indigo-500" placeholder="https://api.mygateway.com/check">
                    </div>
                </div>

                <!-- Auth & Headers -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Authorization Header / API Key</label>
                        <div class="grid grid-cols-2 gap-2">
                            <input type="text" name="auth_header_name" value="{{ old('auth_header_name', $apis['universal_fraud']->credentials['auth_header_name'] ?? 'Authorization') }}" class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs font-mono outline-none" placeholder="Header name (e.g. Authorization)">
                            <input type="password" name="auth_header_value" value="{{ old('auth_header_value', $apis['universal_fraud']->credentials['auth_header_value'] ?? '') }}" class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs font-mono outline-none" placeholder="Header value (e.g. Bearer token)">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Phone Parameter Name</label>
                        <input type="text" name="phone_param" value="{{ old('phone_param', $apis['universal_fraud']->credentials['phone_param'] ?? 'phone') }}" class="w-full px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs font-mono outline-none" placeholder="e.g. phone, mobile, number">
                    </div>
                </div>

                <!-- Dynamic Response Mapping -->
                <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/40 border border-slate-200 dark:border-slate-700 space-y-3">
                    <div class="text-xs font-bold text-slate-800 dark:text-slate-200 flex items-center gap-2">
                        <i data-lucide="sliders" class="w-4 h-4 text-indigo-500"></i>
                        <span>JSON Response Field Mapping (Supports nested keys like data.success_rate)</span>
                    </div>

                    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
                        <div>
                            <label class="block text-[10px] font-bold text-slate-500 uppercase">Success Rate Key</label>
                            <input type="text" name="success_rate_key" value="{{ old('success_rate_key', $apis['universal_fraud']->credentials['success_rate_key'] ?? 'success_rate') }}" class="w-full px-2.5 py-1.5 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-mono outline-none" placeholder="success_rate">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-500 uppercase">Total Parcels Key</label>
                            <input type="text" name="total_orders_key" value="{{ old('total_orders_key', $apis['universal_fraud']->credentials['total_orders_key'] ?? 'total_parcels') }}" class="w-full px-2.5 py-1.5 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-mono outline-none" placeholder="total_parcels">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-500 uppercase">Delivered Key</label>
                            <input type="text" name="delivered_orders_key" value="{{ old('delivered_orders_key', $apis['universal_fraud']->credentials['delivered_orders_key'] ?? 'delivered_parcels') }}" class="w-full px-2.5 py-1.5 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-mono outline-none" placeholder="delivered_parcels">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-500 uppercase">Cancelled/Return Key</label>
                            <input type="text" name="cancelled_orders_key" value="{{ old('cancelled_orders_key', $apis['universal_fraud']->credentials['cancelled_orders_key'] ?? 'cancelled_parcels') }}" class="w-full px-2.5 py-1.5 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-mono outline-none" placeholder="cancelled_parcels">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-500 uppercase">Risk Level Key</label>
                            <input type="text" name="risk_level_key" value="{{ old('risk_level_key', $apis['universal_fraud']->credentials['risk_level_key'] ?? 'risk_level') }}" class="w-full px-2.5 py-1.5 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-mono outline-none" placeholder="risk_level">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-500 uppercase">Notes/Reason Key</label>
                            <input type="text" name="reason_key" value="{{ old('reason_key', $apis['universal_fraud']->credentials['reason_key'] ?? 'notes') }}" class="w-full px-2.5 py-1.5 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-mono outline-none" placeholder="notes">
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-between pt-2 border-t border-slate-100 dark:border-slate-800">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" {{ ($apis['universal_fraud']->is_active ?? false) ? 'checked' : '' }} class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                        <span class="text-xs font-bold text-slate-800 dark:text-slate-200">Activate Universal API as Primary Fraud Check Gateway</span>
                    </label>

                    <div class="flex items-center gap-2">
                        <button type="button" @click="testApi('universal_fraud')" class="px-4 py-2 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-800 dark:text-slate-200 text-xs font-bold transition flex items-center gap-1.5">
                            <i data-lucide="zap" class="w-4 h-4 text-amber-500"></i>
                            <span>Test Live Ping</span>
                        </button>
                        <button type="submit" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl text-xs font-bold shadow-md transition">
                            Save Universal API Settings
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Live API Tester Result Console -->
        <div x-show="testResult !== null" class="p-4 rounded-2xl bg-slate-950 text-slate-200 border border-slate-800 space-y-2">
            <div class="flex items-center justify-between text-xs font-bold">
                <span class="flex items-center gap-2 text-indigo-400">
                    <i data-lucide="terminal" class="w-4 h-4"></i>
                    <span>Live API Connection Test Console</span>
                </span>
                <span x-text="testResult && testResult.latency_ms ? testResult.latency_ms + ' ms' : ''" class="font-mono text-emerald-400 text-[11px]"></span>
            </div>
            <pre class="text-[11px] font-mono overflow-x-auto p-3 bg-slate-900 rounded-xl max-h-48 text-emerald-300" x-text="JSON.stringify(testResult, null, 2)"></pre>
        </div>

    </div>

    <!-- Suspicious Orders Alert List -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
        <div class="p-4 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <i data-lucide="alert-triangle" class="w-5 h-5 text-rose-500"></i>
                <h3 class="text-sm font-bold text-slate-900 dark:text-white">Flagged Suspicious Orders</h3>
            </div>
            <span class="text-xs text-rose-500 font-bold">{{ $suspiciousOrders->count() }} flagged</span>
        </div>

        <div class="divide-y divide-slate-100 dark:divide-slate-800">
            @forelse($suspiciousOrders as $order)
            <div class="p-4 flex items-center justify-between hover:bg-slate-50 dark:hover:bg-slate-800/30 transition">
                <div class="space-y-1">
                    <div class="flex items-center gap-2">
                        <a href="{{ route('admin.orders.show', $order->id) }}" class="font-bold code-font text-xs text-slate-900 dark:text-white hover:text-emerald-500">
                            {{ $order->order_no }}
                        </a>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-500/10 text-rose-500 uppercase">
                            Risk: {{ $order->fraud_score }}%
                        </span>
                    </div>
                    <div class="text-xs text-slate-700 dark:text-slate-300 font-medium">{{ $order->shipping_name }} ({{ $order->shipping_phone }})</div>
                    <div class="text-[11px] text-rose-600 dark:text-rose-400">{{ $order->fraud_reason }}</div>
                </div>
                <div class="text-right space-y-1">
                    <div class="text-xs font-bold text-slate-900 dark:text-white code-font">৳{{ number_format($order->grand_total, 2) }}</div>
                    <a href="{{ route('admin.orders.show', $order->id) }}" class="text-xs text-emerald-600 dark:text-emerald-400 font-semibold hover:underline">
                        Review Order &rarr;
                    </a>
                </div>
            </div>
            @empty
            <div class="p-6 text-center text-slate-400 text-xs">No suspicious orders flagged at this moment.</div>
            @endforelse
        </div>
    </div>

    <!-- Blacklisted Records Table -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
        <div class="p-4 border-b border-slate-200 dark:border-slate-800 font-bold text-sm text-slate-900 dark:text-white flex items-center justify-between">
            <span>Blacklisted Phone Numbers & IP Records</span>
            <span class="text-xs font-mono text-slate-400">{{ $fraudRecords->total() }} records</span>
        </div>
        <table class="w-full text-left text-xs">
            <thead class="bg-slate-50 dark:bg-slate-800/50 text-slate-500 font-bold uppercase">
                <tr>
                    <th class="p-3">Phone Number</th>
                    <th class="p-3">Risk Level</th>
                    <th class="p-3">Courier Delivery Rate</th>
                    <th class="p-3">Parcels Summary</th>
                    <th class="p-3">Notes & Reasons</th>
                    <th class="p-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                @forelse($fraudRecords as $rec)
                <tr>
                    <td class="p-3 font-mono font-bold text-slate-900 dark:text-white">
                        {{ $rec->phone }}
                        @if($rec->ip_address)
                            <div class="text-[10px] text-slate-400 font-normal">IP: {{ $rec->ip_address }}</div>
                        @endif
                    </td>
                    <td class="p-3">
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase {{ $rec->risk_level === 'critical' ? 'bg-rose-100 text-rose-700 dark:bg-rose-950 dark:text-rose-300' : 'bg-amber-100 text-amber-700 dark:bg-amber-950 dark:text-amber-300' }}">
                            {{ $rec->risk_level }}
                        </span>
                    </td>
                    <td class="p-3 font-bold code-font {{ $rec->courier_success_rate < 50 ? 'text-rose-500' : 'text-emerald-500' }}">
                        {{ $rec->courier_success_rate }}%
                    </td>
                    <td class="p-3 text-[11px] text-slate-600 dark:text-slate-400">
                        @if($rec->total_parcels > 0)
                            <span class="font-bold text-slate-900 dark:text-white">{{ $rec->delivered_parcels }}/{{ $rec->total_parcels }}</span> delivered
                            @if($rec->cancelled_parcels > 0)
                                <span class="text-rose-500">({{ $rec->cancelled_parcels }} returns)</span>
                            @endif
                        @else
                            <span class="text-slate-400">No courier history</span>
                        @endif
                    </td>
                    <td class="p-3 text-slate-500 max-w-xs truncate">{{ $rec->notes }}</td>
                    <td class="p-3 text-right">
                        <form method="POST" action="{{ route('admin.fraud.blacklist.remove', $rec->id) }}" onsubmit="return confirm('Remove {{ $rec->phone }} from blacklist?')" class="inline">
                            @csrf
                            <button type="submit" class="px-2.5 py-1 rounded-lg text-xs font-semibold text-slate-600 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/40 transition">
                                Whitelist
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="p-6 text-center text-slate-400">No blacklist entries recorded.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        @if($fraudRecords->hasPages())
        <div class="p-4 border-t border-slate-200 dark:border-slate-800">
            {{ $fraudRecords->links() }}
        </div>
        @endif
    </div>

</div>

<script>
function fraudManager() {
    return {
        activeApiTab: '{{ ($apis["universal_fraud"]->is_active ?? false) ? "universal" : "zachaikori" }}',
        testResult: null,
        testLoading: false,

        async testApi(provider) {
            this.testLoading = true;
            this.testResult = { message: 'Connecting to ' + provider + '...' };

            const form = event.target.closest('form');
            const formData = new FormData(form);
            formData.append('phone', '01711223344');

            try {
                const response = await fetch('{{ route("admin.fraud.test_api") }}', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                });
                this.testResult = await response.json();
            } catch (err) {
                this.testResult = {
                    success: false,
                    error: err.message || 'Network request failed'
                };
            } finally {
                this.testLoading = false;
            }
        }
    };
}
</script>
@endsection
