@extends('layouts.admin')

@section('title', 'Third-Party API Hub')
@section('page-title', 'Third-Party API Hub & Integration Manager')

@section('content')
<div class="max-w-6xl mx-auto space-y-6" x-data="apiHubManager()">

    <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 border border-slate-200 dark:border-slate-800 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-base font-bold text-slate-900 dark:text-white flex items-center gap-2">
                <i data-lucide="blocks" class="w-5 h-5 text-emerald-500"></i>
                <span>Third-Party API Integrations & Gateways</span>
            </h2>
            <p class="text-xs text-slate-500 mt-1">Configure and connect any Custom SMS Gateway, Bulk SMS Dhaka, Steadfast Courier, bKash, BulkSMS BD, and Pathao.</p>
        </div>
        <div class="flex items-center gap-2 text-xs">
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-emerald-50 dark:bg-emerald-950/50 border border-emerald-200 dark:border-emerald-800 text-emerald-700 dark:text-emerald-300 font-semibold">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                <span>Universal Gateway Engine Ready</span>
            </span>
        </div>
    </div>

    @if(session('success'))
        <div class="p-4 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300 text-xs font-semibold flex items-center gap-2">
            <i data-lucide="check-circle" class="w-4 h-4 text-emerald-600 shrink-0"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if($errors->any())
        <div class="p-4 rounded-xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 text-rose-800 dark:text-rose-300 text-xs font-semibold">
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Universal Custom SMS Gateway Full-Width Card -->
    <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 sm:p-8 border-2 {{ ($apis['custom_sms']->is_active ?? false) ? 'border-sky-500 shadow-lg ring-4 ring-sky-500/10' : 'border-slate-200 dark:border-slate-800 shadow-sm' }} space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-100 dark:border-slate-800 pb-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-sky-50 dark:bg-sky-950/50 border border-sky-200 dark:border-sky-800 flex items-center justify-center text-sky-600 dark:text-sky-400 font-bold">
                    <i data-lucide="radio" class="w-5 h-5"></i>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h3 class="font-black text-base text-slate-900 dark:text-white">Custom Bulk SMS Gateway (Connect Any Provider)</h3>
                        <span class="text-[10px] px-2 py-0.5 rounded-full bg-sky-100 text-sky-700 dark:bg-sky-950 dark:text-sky-300 font-extrabold uppercase">Universal REST API</span>
                    </div>
                    <p class="text-xs text-slate-500 mt-0.5">Plug in any SMS provider in Bangladesh or globally by configuring its HTTP Endpoint and parameter keys.</p>
                </div>
            </div>
            <div>
                <span class="text-xs font-extrabold uppercase px-3 py-1 rounded-full {{ ($apis['custom_sms']->is_active ?? false) ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800' : 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400 border border-slate-200 dark:border-slate-700' }}">
                    {{ ($apis['custom_sms']->is_active ?? false) ? 'Active Gateway' : 'Inactive' }}
                </span>
            </div>
        </div>

        <form id="form-custom-sms" method="POST" action="{{ route('admin.settings.api-hub.update') }}" class="space-y-4">
            @csrf
            @method('PUT')
            <input type="hidden" name="provider" value="custom_sms">
            <input type="hidden" name="type" value="sms">
            <input type="hidden" name="title" value="Custom Bulk SMS Gateway">

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Gateway Provider Name</label>
                    <input type="text" name="gateway_name" id="custom_gateway_name" value="{{ old('gateway_name', $apis['custom_sms']->credentials['gateway_name'] ?? 'My Custom Bulk SMS') }}" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs font-semibold outline-none focus:ring-2 focus:ring-sky-500" placeholder="e.g. Diana Host, GreenWeb, Alpha SMS">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">HTTP Request Method</label>
                    <select name="http_method" id="custom_http_method" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs font-bold outline-none focus:ring-2 focus:ring-sky-500">
                        <option value="GET" {{ ($apis['custom_sms']->credentials['http_method'] ?? 'GET') === 'GET' ? 'selected' : '' }}>GET Request (Standard Query Params)</option>
                        <option value="POST" {{ ($apis['custom_sms']->credentials['http_method'] ?? '') === 'POST' ? 'selected' : '' }}>POST Request (Form Data)</option>
                        <option value="POST_JSON" {{ ($apis['custom_sms']->credentials['http_method'] ?? '') === 'POST_JSON' ? 'selected' : '' }}>POST Request (JSON Payload)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">API Endpoint URL *</label>
                    <input type="url" name="endpoint_url" id="custom_endpoint_url" value="{{ old('endpoint_url', $apis['custom_sms']->credentials['endpoint_url'] ?? '') }}" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs font-mono outline-none focus:ring-2 focus:ring-sky-500" placeholder="https://api.provider.com/sms/send">
                </div>
            </div>

            <!-- Dynamic Parameter Mappings -->
            <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/40 border border-slate-200 dark:border-slate-700 space-y-4">
                <div class="text-xs font-bold text-slate-800 dark:text-slate-200 flex items-center gap-2">
                    <i data-lucide="sliders" class="w-4 h-4 text-sky-500"></i>
                    <span>Dynamic Parameter Key Names & Credentials Mapping</span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                    <!-- API Key Param & Value -->
                    <div class="space-y-1">
                        <label class="block text-[11px] font-bold text-slate-500">API Key Parameter Name</label>
                        <input type="text" name="api_key_param" id="custom_api_key_param" value="{{ old('api_key_param', $apis['custom_sms']->credentials['api_key_param'] ?? 'apikey') }}" class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-mono outline-none" placeholder="e.g. apikey, api_key, token">
                    </div>
                    <div class="space-y-1">
                        <label class="block text-[11px] font-bold text-slate-500">API Key Value</label>
                        <input type="password" name="api_key_value" id="custom_api_key_value" value="{{ old('api_key_value', $apis['custom_sms']->credentials['api_key_value'] ?? '') }}" class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-mono outline-none" placeholder="Enter API Key / Token">
                    </div>

                    <!-- Sender ID Param & Value -->
                    <div class="space-y-1">
                        <label class="block text-[11px] font-bold text-slate-500">Sender / Caller ID Param Name</label>
                        <input type="text" name="sender_id_param" id="custom_sender_id_param" value="{{ old('sender_id_param', $apis['custom_sms']->credentials['sender_id_param'] ?? 'callerID') }}" class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-mono outline-none" placeholder="e.g. callerID, sender_id">
                    </div>
                    <div class="space-y-1">
                        <label class="block text-[11px] font-bold text-slate-500">Sender ID / Masking Value</label>
                        <input type="text" name="sender_id_value" id="custom_sender_id_value" value="{{ old('sender_id_value', $apis['custom_sms']->credentials['sender_id_value'] ?? '1234') }}" class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-mono outline-none" placeholder="e.g. 1234, BrandName">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                    <!-- Phone Param -->
                    <div class="space-y-1">
                        <label class="block text-[11px] font-bold text-slate-500">Recipient Phone Param Name</label>
                        <input type="text" name="phone_param" id="custom_phone_param" value="{{ old('phone_param', $apis['custom_sms']->credentials['phone_param'] ?? 'number') }}" class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-mono outline-none" placeholder="e.g. number, to, phone">
                    </div>

                    <!-- Message Param -->
                    <div class="space-y-1">
                        <label class="block text-[11px] font-bold text-slate-500">Message Content Param Name</label>
                        <input type="text" name="message_param" id="custom_message_param" value="{{ old('message_param', $apis['custom_sms']->credentials['message_param'] ?? 'message') }}" class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-mono outline-none" placeholder="e.g. message, msg, text">
                    </div>

                    <!-- Extra Params -->
                    <div class="space-y-1">
                        <label class="block text-[11px] font-bold text-slate-500">Extra Fixed Params (Optional)</label>
                        <input type="text" name="extra_params" id="custom_extra_params" value="{{ old('extra_params', $apis['custom_sms']->credentials['extra_params'] ?? '') }}" class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-mono outline-none" placeholder="e.g. type=text, format=json">
                    </div>

                    <!-- Success Keyword -->
                    <div class="space-y-1">
                        <label class="block text-[11px] font-bold text-slate-500">Success Indicator Keyword</label>
                        <input type="text" name="success_keyword" id="custom_success_keyword" value="{{ old('success_keyword', $apis['custom_sms']->credentials['success_keyword'] ?? '1000') }}" class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-mono outline-none" placeholder="e.g. 1000, 202, success, true">
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-between pt-2 border-t border-slate-100 dark:border-slate-800">
                <label class="flex items-center gap-2 cursor-pointer text-xs font-bold text-sky-700 dark:text-sky-300">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" value="1" {{ ($apis['custom_sms']->is_active ?? false) ? 'checked' : '' }} class="rounded border-slate-300 text-sky-600 focus:ring-sky-500 w-4 h-4">
                    <span>Set this Custom Gateway as Active Provider</span>
                </label>
            </div>
        </form>

        <!-- Custom SMS Live Test & Save Controls -->
        <div class="space-y-2 pt-2 border-t border-slate-100 dark:border-slate-800">
            <div x-show="testResults.custom_sms" x-transition class="p-3 rounded-xl text-xs font-medium" :class="testResults.custom_sms?.success ? 'bg-emerald-50 text-emerald-800 border border-emerald-200 dark:bg-emerald-950/60 dark:text-emerald-300' : 'bg-rose-50 text-rose-800 border border-rose-200 dark:bg-rose-950/60 dark:text-rose-300'">
                <div class="font-bold flex items-center gap-1.5 mb-0.5">
                    <i data-lucide="terminal" class="w-3.5 h-3.5"></i>
                    <span>Custom Gateway Test Output:</span>
                </div>
                <div x-text="testResults.custom_sms?.message"></div>
                <template x-if="testResults.custom_sms?.raw">
                    <pre class="mt-2 p-2 bg-slate-950 text-emerald-400 rounded-lg text-[10px] font-mono overflow-x-auto whitespace-pre-wrap" x-text="testResults.custom_sms.raw"></pre>
                </template>
            </div>

            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div class="flex items-center gap-2">
                    <input type="text" x-model="testNumber" placeholder="Test Mobile Number (e.g. 01700000000)" class="px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs font-mono outline-none w-56">
                    <button type="button" @click="testCustomGateway()" :disabled="loading.custom_sms" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-800 dark:text-slate-200 rounded-xl text-xs font-bold transition flex items-center gap-1.5">
                        <span x-show="loading.custom_sms" class="w-3 h-3 border-2 border-slate-400 border-t-transparent rounded-full animate-spin"></span>
                        <span>Send Test Ping SMS</span>
                    </button>
                </div>
                <button type="submit" form="form-custom-sms" class="px-6 py-2.5 bg-sky-600 hover:bg-sky-500 text-white rounded-xl text-xs font-bold transition shadow-sm">Save Custom SMS Gateway</button>
            </div>
        </div>
    </div>

    <!-- Integrations Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

        <!-- 1. Bulk SMS Dhaka Gateway (bulksmsdhaka.com / bulksmsdhaka.net) -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 border-2 {{ ($apis['bulksmsdhaka']->is_active ?? false) ? 'border-emerald-500/50 shadow-md' : 'border-slate-200 dark:border-slate-800 shadow-sm' }} space-y-4 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
                    <div class="flex items-center gap-2.5">
                        <span class="w-3.5 h-3.5 rounded-full {{ ($apis['bulksmsdhaka']->is_active ?? false) ? 'bg-emerald-500 shadow-xs shadow-emerald-500/50' : 'bg-slate-300 dark:bg-slate-700' }}"></span>
                        <div>
                            <div class="flex items-center gap-2">
                                <h3 class="font-bold text-sm text-slate-900 dark:text-white">Bulk SMS Dhaka Gateway</h3>
                                <span class="text-[9px] px-1.5 py-0.5 rounded bg-sky-100 text-sky-700 dark:bg-sky-950 dark:text-sky-300 font-bold">bulksmsdhaka.com</span>
                            </div>
                            <div class="text-[11px] text-slate-400">Direct OTP send & transactional SMS via bulksmsdhaka.net</div>
                        </div>
                    </div>
                    <span class="text-[10px] font-extrabold uppercase px-2.5 py-1 rounded-full {{ ($apis['bulksmsdhaka']->is_active ?? false) ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800' : 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400 border border-slate-200 dark:border-slate-700' }}">
                        {{ ($apis['bulksmsdhaka']->is_active ?? false) ? 'Active' : 'Inactive' }}
                    </span>
                </div>

                <form id="form-bulksmsdhaka" method="POST" action="{{ route('admin.settings.api-hub.update') }}" class="space-y-3 mt-4">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="provider" value="bulksmsdhaka">
                    <input type="hidden" name="type" value="sms">
                    <input type="hidden" name="title" value="Bulk SMS Dhaka Gateway (bulksmsdhaka.com)">

                    <div>
                        <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1">API Key (apikey)</label>
                        <input type="password" name="api_key" id="bulksmsdhaka_api_key" value="{{ old('api_key', $apis['bulksmsdhaka']->api_key ?? $apis['bulksmsdhaka']->credentials['api_key'] ?? ($apis['bulksmsdhaka']->credentials['apikey'] ?? '')) }}" class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 text-xs font-mono bg-slate-50 dark:bg-slate-800/80 outline-none focus:border-emerald-500" placeholder="Enter Bulk SMS Dhaka API Key">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1">Caller ID / Sender ID (callerID)</label>
                        <input type="text" name="caller_id" id="bulksmsdhaka_caller_id" value="{{ old('caller_id', $apis['bulksmsdhaka']->caller_id ?? $apis['bulksmsdhaka']->credentials['caller_id'] ?? ($apis['bulksmsdhaka']->credentials['callerID'] ?? ($apis['bulksmsdhaka']->credentials['sender_id'] ?? '1234'))) }}" class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 text-xs font-mono bg-slate-50 dark:bg-slate-800/80 outline-none focus:border-emerald-500" placeholder="e.g. 1234 or your Approved Mask">
                    </div>

                    <div class="pt-2 flex items-center justify-between border-t border-slate-100 dark:border-slate-800">
                        <label class="flex items-center gap-2 cursor-pointer text-xs font-semibold text-emerald-700 dark:text-emerald-400">
                            <input type="hidden" name="is_active" value="0">
                            <input type="checkbox" name="is_active" value="1" {{ ($apis['bulksmsdhaka']->is_active ?? false) ? 'checked' : '' }} class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500 w-4 h-4">
                            <span>Enable Bulk SMS Dhaka Gateway</span>
                        </label>
                    </div>
                </form>
            </div>

            <!-- Test Connection & Save Footer -->
            <div class="space-y-2 pt-2">
                <div x-show="testResults.bulksmsdhaka" x-transition class="p-2.5 rounded-xl text-xs font-medium" :class="testResults.bulksmsdhaka?.success ? 'bg-emerald-50 text-emerald-800 border border-emerald-200 dark:bg-emerald-950/60 dark:text-emerald-300' : 'bg-rose-50 text-rose-800 border border-rose-200 dark:bg-rose-950/60 dark:text-rose-300'">
                    <span x-text="testResults.bulksmsdhaka?.message"></span>
                </div>
                <div class="flex items-center justify-between gap-2">
                    <button type="button" @click="testProvider('bulksmsdhaka')" :disabled="loading.bulksmsdhaka" class="px-3 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-xl text-xs font-semibold transition flex items-center gap-1.5">
                        <span x-show="loading.bulksmsdhaka" class="w-3 h-3 border-2 border-slate-400 border-t-transparent rounded-full animate-spin"></span>
                        <span>Ping & Test Live API</span>
                    </button>
                    <button type="submit" form="form-bulksmsdhaka" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl text-xs font-bold transition shadow-xs">Save Bulk SMS Dhaka</button>
                </div>
            </div>
        </div>

        <!-- 2. Steadfast Courier API -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 border border-slate-200 dark:border-slate-800 shadow-sm space-y-4 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
                    <div class="flex items-center gap-2.5">
                        <span class="w-3.5 h-3.5 rounded-full {{ ($apis['steadfast']->is_active ?? false) ? 'bg-emerald-500 shadow-xs shadow-emerald-500/50' : 'bg-slate-300 dark:bg-slate-700' }}"></span>
                        <div>
                            <h3 class="font-bold text-sm text-slate-900 dark:text-white">Steadfast Courier API</h3>
                            <div class="text-[11px] text-slate-400">Automated order consignment dispatch & tracking</div>
                        </div>
                    </div>
                    <span class="text-[10px] font-extrabold uppercase px-2.5 py-1 rounded-full {{ ($apis['steadfast']->is_active ?? false) ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800' : 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400 border border-slate-200 dark:border-slate-700' }}">
                        {{ ($apis['steadfast']->is_active ?? false) ? 'Active' : 'Inactive' }}
                    </span>
                </div>

                <form id="form-steadfast" method="POST" action="{{ route('admin.settings.api-hub.update') }}" class="space-y-3 mt-4">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="provider" value="steadfast">
                    <input type="hidden" name="type" value="courier">
                    <input type="hidden" name="title" value="Steadfast Courier API">

                    <div>
                        <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1">API Key</label>
                        <input type="password" name="api_key" id="steadfast_api_key" value="{{ old('api_key', $apis['steadfast']->api_key ?? $apis['steadfast']->credentials['api_key'] ?? '') }}" class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 text-xs font-mono bg-slate-50 dark:bg-slate-800/80 outline-none focus:border-emerald-500" placeholder="Enter Steadfast API Key">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1">Secret Key</label>
                        <input type="password" name="secret_key" id="steadfast_secret_key" value="{{ old('secret_key', $apis['steadfast']->secret_key ?? $apis['steadfast']->credentials['secret_key'] ?? '') }}" class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 text-xs font-mono bg-slate-50 dark:bg-slate-800/80 outline-none focus:border-emerald-500" placeholder="Enter Steadfast Secret Key">
                    </div>

                    <div class="pt-2 flex items-center justify-between border-t border-slate-100 dark:border-slate-800">
                        <label class="flex items-center gap-2 cursor-pointer text-xs font-semibold text-slate-700 dark:text-slate-300">
                            <input type="hidden" name="is_active" value="0">
                            <input type="checkbox" name="is_active" value="1" {{ ($apis['steadfast']->is_active ?? false) ? 'checked' : '' }} class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500 w-4 h-4">
                            <span>Enable Steadfast Courier</span>
                        </label>
                    </div>
                </form>
            </div>

            <!-- Test Connection & Save Footer -->
            <div class="space-y-2 pt-2">
                <div x-show="testResults.steadfast" x-transition class="p-2.5 rounded-xl text-xs font-medium" :class="testResults.steadfast?.success ? 'bg-emerald-50 text-emerald-800 border border-emerald-200 dark:bg-emerald-950/60 dark:text-emerald-300' : 'bg-rose-50 text-rose-800 border border-rose-200 dark:bg-rose-950/60 dark:text-rose-300'">
                    <span x-text="testResults.steadfast?.message"></span>
                </div>
                <div class="flex items-center justify-between gap-2">
                    <button type="button" @click="testProvider('steadfast')" :disabled="loading.steadfast" class="px-3 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-xl text-xs font-semibold transition flex items-center gap-1.5">
                        <span x-show="loading.steadfast" class="w-3 h-3 border-2 border-slate-400 border-t-transparent rounded-full animate-spin"></span>
                        <span>Test Connection</span>
                    </button>
                    <button type="submit" form="form-steadfast" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl text-xs font-bold transition shadow-xs">Save Steadfast API</button>
                </div>
            </div>
        </div>

        <!-- 3. bKash Payment Gateway -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 border border-slate-200 dark:border-slate-800 shadow-sm space-y-4 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
                    <div class="flex items-center gap-2.5">
                        <span class="w-3.5 h-3.5 rounded-full {{ ($apis['bkash']->is_active ?? false) ? 'bg-pink-500 shadow-xs shadow-pink-500/50' : 'bg-slate-300 dark:bg-slate-700' }}"></span>
                        <div>
                            <h3 class="font-bold text-sm text-slate-900 dark:text-white">bKash Payment Gateway</h3>
                            <div class="text-[11px] text-slate-400">Direct Merchant PGW Checkout Integration</div>
                        </div>
                    </div>
                    <span class="text-[10px] font-extrabold uppercase px-2.5 py-1 rounded-full {{ ($apis['bkash']->is_active ?? false) ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800' : 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400 border border-slate-200 dark:border-slate-700' }}">
                        {{ ($apis['bkash']->is_active ?? false) ? 'Active' : 'Inactive' }}
                    </span>
                </div>

                <form id="form-bkash" method="POST" action="{{ route('admin.settings.api-hub.update') }}" class="space-y-3 mt-4">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="provider" value="bkash">
                    <input type="hidden" name="type" value="payment">
                    <input type="hidden" name="title" value="bKash Payment Gateway">

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1">App Key</label>
                            <input type="password" name="app_key" id="bkash_app_key" value="{{ old('app_key', $apis['bkash']->app_key ?? $apis['bkash']->credentials['app_key'] ?? '') }}" class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 text-xs font-mono bg-slate-50 dark:bg-slate-800/80 outline-none focus:border-pink-500" placeholder="bKash App Key">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1">App Secret</label>
                            <input type="password" name="app_secret" id="bkash_app_secret" value="{{ old('app_secret', $apis['bkash']->app_secret ?? $apis['bkash']->credentials['app_secret'] ?? '') }}" class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 text-xs font-mono bg-slate-50 dark:bg-slate-800/80 outline-none focus:border-pink-500" placeholder="bKash App Secret">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1">Username (Merchant No/ID)</label>
                            <input type="text" name="username" id="bkash_username" value="{{ old('username', $apis['bkash']->username ?? $apis['bkash']->credentials['username'] ?? '') }}" class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 text-xs font-mono bg-slate-50 dark:bg-slate-800/80 outline-none focus:border-pink-500" placeholder="e.g. 017xxxxxxxx">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1">Password</label>
                            <input type="password" name="password" id="bkash_password" value="{{ old('password', $apis['bkash']->password ?? $apis['bkash']->credentials['password'] ?? '') }}" class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 text-xs font-mono bg-slate-50 dark:bg-slate-800/80 outline-none focus:border-pink-500" placeholder="bKash PGW Password">
                        </div>
                    </div>

                    <div class="pt-2 flex items-center justify-between border-t border-slate-100 dark:border-slate-800 gap-4">
                        <label class="flex items-center gap-2 cursor-pointer text-xs font-semibold text-slate-700 dark:text-slate-300">
                            <input type="hidden" name="is_sandbox" value="0">
                            <input type="checkbox" name="is_sandbox" id="bkash_is_sandbox" value="1" {{ ($apis['bkash']->is_sandbox ?? true) ? 'checked' : '' }} class="rounded border-slate-300 text-slate-700 focus:ring-slate-500 w-4 h-4">
                            <span>Sandbox / Test Mode</span>
                        </label>

                        <label class="flex items-center gap-2 cursor-pointer text-xs font-semibold text-pink-700 dark:text-pink-400">
                            <input type="hidden" name="is_active" value="0">
                            <input type="checkbox" name="is_active" value="1" {{ ($apis['bkash']->is_active ?? false) ? 'checked' : '' }} class="rounded border-slate-300 text-pink-600 focus:ring-pink-500 w-4 h-4">
                            <span>Enable bKash Checkout</span>
                        </label>
                    </div>
                </form>
            </div>

            <!-- Test Connection & Save Footer -->
            <div class="space-y-2 pt-2">
                <div x-show="testResults.bkash" x-transition class="p-2.5 rounded-xl text-xs font-medium" :class="testResults.bkash?.success ? 'bg-emerald-50 text-emerald-800 border border-emerald-200 dark:bg-emerald-950/60 dark:text-emerald-300' : 'bg-rose-50 text-rose-800 border border-rose-200 dark:bg-rose-950/60 dark:text-rose-300'">
                    <span x-text="testResults.bkash?.message"></span>
                </div>
                <div class="flex items-center justify-between gap-2">
                    <button type="button" @click="testProvider('bkash')" :disabled="loading.bkash" class="px-3 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-xl text-xs font-semibold transition flex items-center gap-1.5">
                        <span x-show="loading.bkash" class="w-3 h-3 border-2 border-slate-400 border-t-transparent rounded-full animate-spin"></span>
                        <span>Test Token Grant</span>
                    </button>
                    <button type="submit" form="form-bkash" class="px-4 py-2 bg-pink-600 hover:bg-pink-500 text-white rounded-xl text-xs font-bold transition shadow-xs">Save bKash API</button>
                </div>
            </div>
        </div>

        <!-- 4. BulkSMS BD Gateway -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 border border-slate-200 dark:border-slate-800 shadow-sm space-y-4 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
                    <div class="flex items-center gap-2.5">
                        <span class="w-3.5 h-3.5 rounded-full {{ ($apis['bulksms_bd']->is_active ?? ($apis['bulksms']->is_active ?? false)) ? 'bg-sky-500 shadow-xs shadow-sky-500/50' : 'bg-slate-300 dark:bg-slate-700' }}"></span>
                        <div>
                            <div class="flex items-center gap-2">
                                <h3 class="font-bold text-sm text-slate-900 dark:text-white">BulkSMS BD Gateway</h3>
                                <span class="text-[9px] px-1.5 py-0.5 rounded bg-sky-100 text-sky-700 dark:bg-sky-950 dark:text-sky-300 font-bold">bulksmsbd.net</span>
                            </div>
                            <div class="text-[11px] text-slate-400">Alternative SMS Gateway for bulksmsbd.net</div>
                        </div>
                    </div>
                    <span class="text-[10px] font-extrabold uppercase px-2.5 py-1 rounded-full {{ ($apis['bulksms_bd']->is_active ?? ($apis['bulksms']->is_active ?? false)) ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800' : 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400 border border-slate-200 dark:border-slate-700' }}">
                        {{ ($apis['bulksms_bd']->is_active ?? ($apis['bulksms']->is_active ?? false)) ? 'Active' : 'Inactive' }}
                    </span>
                </div>

                <form id="form-bulksms" method="POST" action="{{ route('admin.settings.api-hub.update') }}" class="space-y-3 mt-4">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="provider" value="bulksms_bd">
                    <input type="hidden" name="type" value="sms">
                    <input type="hidden" name="title" value="BulkSMS BD Gateway (bulksmsbd.net)">

                    <div>
                        <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1">API Key</label>
                        <input type="password" name="api_key" id="bulksms_api_key" value="{{ old('api_key', $apis['bulksms_bd']->api_key ?? $apis['bulksms_bd']->credentials['api_key'] ?? ($apis['bulksms']->api_key ?? ($apis['bulksms']->credentials['api_key'] ?? ''))) }}" class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 text-xs font-mono bg-slate-50 dark:bg-slate-800/80 outline-none focus:border-sky-500" placeholder="Enter BulkSMS BD API Key">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1">Sender ID / Masking (e.g. 8809617618999 or Brand)</label>
                        <input type="text" name="sender_id" id="bulksms_sender_id" value="{{ old('sender_id', $apis['bulksms_bd']->sender_id ?? $apis['bulksms_bd']->credentials['sender_id'] ?? ($apis['bulksms']->sender_id ?? ($apis['bulksms']->credentials['sender_id'] ?? 'DREAMERS'))) }}" class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 text-xs font-mono bg-slate-50 dark:bg-slate-800/80 outline-none focus:border-sky-500" placeholder="Enter Sender ID / Masking">
                    </div>

                    <div class="pt-2 flex items-center justify-between border-t border-slate-100 dark:border-slate-800">
                        <label class="flex items-center gap-2 cursor-pointer text-xs font-semibold text-slate-700 dark:text-slate-300">
                            <input type="hidden" name="is_active" value="0">
                            <input type="checkbox" name="is_active" value="1" {{ ($apis['bulksms_bd']->is_active ?? ($apis['bulksms']->is_active ?? false)) ? 'checked' : '' }} class="rounded border-slate-300 text-sky-600 focus:ring-sky-500 w-4 h-4">
                            <span>Enable BulkSMS BD Gateway</span>
                        </label>
                    </div>
                </form>
            </div>

            <!-- Test Connection & Save Footer -->
            <div class="space-y-2 pt-2">
                <div x-show="testResults.bulksms" x-transition class="p-2.5 rounded-xl text-xs font-medium" :class="testResults.bulksms?.success ? 'bg-emerald-50 text-emerald-800 border border-emerald-200 dark:bg-emerald-950/60 dark:text-emerald-300' : 'bg-rose-50 text-rose-800 border border-rose-200 dark:bg-rose-950/60 dark:text-rose-300'">
                    <span x-text="testResults.bulksms?.message"></span>
                </div>
                <div class="flex items-center justify-between gap-2">
                    <button type="button" @click="testProvider('bulksms')" :disabled="loading.bulksms" class="px-3 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-xl text-xs font-semibold transition flex items-center gap-1.5">
                        <span x-show="loading.bulksms" class="w-3 h-3 border-2 border-slate-400 border-t-transparent rounded-full animate-spin"></span>
                        <span>Check Balance & Ping</span>
                    </button>
                    <button type="submit" form="form-bulksms" class="px-4 py-2 bg-sky-600 hover:bg-sky-500 text-white rounded-xl text-xs font-bold transition shadow-xs">Save BulkSMS BD</button>
                </div>
            </div>
        </div>

        <!-- 5. Pathao Courier API -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 border border-slate-200 dark:border-slate-800 shadow-sm space-y-4 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
                    <div class="flex items-center gap-2.5">
                        <span class="w-3.5 h-3.5 rounded-full {{ ($apis['pathao']->is_active ?? false) ? 'bg-rose-500 shadow-xs shadow-rose-500/50' : 'bg-slate-300 dark:bg-slate-700' }}"></span>
                        <div>
                            <h3 class="font-bold text-sm text-slate-900 dark:text-white">Pathao Logistics API</h3>
                            <div class="text-[11px] text-slate-400">Pathao Merchant Parcel Logistics Integration</div>
                        </div>
                    </div>
                    <span class="text-[10px] font-extrabold uppercase px-2.5 py-1 rounded-full {{ ($apis['pathao']->is_active ?? false) ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800' : 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400 border border-slate-200 dark:border-slate-700' }}">
                        {{ ($apis['pathao']->is_active ?? false) ? 'Active' : 'Inactive' }}
                    </span>
                </div>

                <form id="form-pathao" method="POST" action="{{ route('admin.settings.api-hub.update') }}" class="space-y-3 mt-4">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="provider" value="pathao">
                    <input type="hidden" name="type" value="courier">
                    <input type="hidden" name="title" value="Pathao Logistics API">

                    <div>
                        <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1">Client ID</label>
                        <input type="text" name="client_id" value="{{ old('client_id', $apis['pathao']->client_id ?? $apis['pathao']->credentials['client_id'] ?? '') }}" class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 text-xs font-mono bg-slate-50 dark:bg-slate-800/80 outline-none focus:border-rose-500" placeholder="Enter Pathao Client ID">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1">Client Secret</label>
                        <input type="password" name="client_secret" value="{{ old('client_secret', $apis['pathao']->client_secret ?? $apis['pathao']->credentials['client_secret'] ?? '') }}" class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 text-xs font-mono bg-slate-50 dark:bg-slate-800/80 outline-none focus:border-rose-500" placeholder="Enter Pathao Client Secret">
                    </div>

                    <div class="pt-2 flex items-center justify-between border-t border-slate-100 dark:border-slate-800">
                        <label class="flex items-center gap-2 cursor-pointer text-xs font-semibold text-slate-700 dark:text-slate-300">
                            <input type="hidden" name="is_active" value="0">
                            <input type="checkbox" name="is_active" value="1" {{ ($apis['pathao']->is_active ?? false) ? 'checked' : '' }} class="rounded border-slate-300 text-rose-600 focus:ring-rose-500 w-4 h-4">
                            <span>Enable Pathao Logistics</span>
                        </label>
                    </div>
                </form>
            </div>

            <!-- Save Footer -->
            <div class="pt-2 flex justify-end">
                <button type="submit" form="form-pathao" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 dark:bg-slate-700 dark:hover:bg-slate-600 text-white rounded-xl text-xs font-bold transition shadow-xs">Save Pathao API</button>
            </div>
        </div>

    </div>

</div>

<script>
function apiHubManager() {
    return {
        testNumber: '01700000000',
        loading: {
            custom_sms: false,
            bulksmsdhaka: false,
            steadfast: false,
            bulksms: false,
            bkash: false,
        },
        testResults: {
            custom_sms: null,
            bulksmsdhaka: null,
            steadfast: null,
            bulksms: null,
            bkash: null,
        },
        async testCustomGateway() {
            this.loading.custom_sms = true;
            this.testResults.custom_sms = null;

            const payload = {
                provider: 'custom_sms',
                _token: '{{ csrf_token() }}',
                gateway_name: document.getElementById('custom_gateway_name')?.value || '',
                http_method: document.getElementById('custom_http_method')?.value || 'GET',
                endpoint_url: document.getElementById('custom_endpoint_url')?.value || '',
                api_key_param: document.getElementById('custom_api_key_param')?.value || '',
                api_key_value: document.getElementById('custom_api_key_value')?.value || '',
                sender_id_param: document.getElementById('custom_sender_id_param')?.value || '',
                sender_id_value: document.getElementById('custom_sender_id_value')?.value || '',
                phone_param: document.getElementById('custom_phone_param')?.value || 'number',
                message_param: document.getElementById('custom_message_param')?.value || 'message',
                extra_params: document.getElementById('custom_extra_params')?.value || '',
                success_keyword: document.getElementById('custom_success_keyword')?.value || '',
                test_phone: this.testNumber || '01700000000',
                test_message: 'Custom SMS Gateway connection verification test ping'
            };

            try {
                const response = await fetch('{{ route('admin.settings.api_hub.test') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(payload)
                });

                const data = await response.json();
                this.testResults.custom_sms = data;
            } catch (err) {
                this.testResults.custom_sms = {
                    success: false,
                    message: 'Network error or server unreachable while testing Custom SMS Gateway.'
                };
            } finally {
                this.loading.custom_sms = false;
            }
        },
        async testProvider(provider) {
            this.loading[provider] = true;
            this.testResults[provider] = null;

            let payload = {
                provider: provider,
                _token: '{{ csrf_token() }}'
            };

            if (provider === 'bulksmsdhaka') {
                payload.api_key = document.getElementById('bulksmsdhaka_api_key')?.value || '';
                payload.caller_id = document.getElementById('bulksmsdhaka_caller_id')?.value || '';
            } else if (provider === 'steadfast') {
                payload.api_key = document.getElementById('steadfast_api_key')?.value || '';
                payload.secret_key = document.getElementById('steadfast_secret_key')?.value || '';
            } else if (provider === 'bulksms') {
                payload.api_key = document.getElementById('bulksms_api_key')?.value || '';
                payload.sender_id = document.getElementById('bulksms_sender_id')?.value || '';
            } else if (provider === 'bkash') {
                payload.app_key = document.getElementById('bkash_app_key')?.value || '';
                payload.app_secret = document.getElementById('bkash_app_secret')?.value || '';
                payload.username = document.getElementById('bkash_username')?.value || '';
                payload.password = document.getElementById('bkash_password')?.value || '';
                payload.is_sandbox = document.getElementById('bkash_is_sandbox')?.checked ? 1 : 0;
            }

            try {
                const response = await fetch('{{ route('admin.settings.api_hub.test') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(payload)
                });

                const data = await response.json();
                this.testResults[provider] = data;
            } catch (err) {
                this.testResults[provider] = {
                    success: false,
                    message: 'Network error or server unreachable while testing ' + provider + ' API.'
                };
            } finally {
                this.loading[provider] = false;
            }
        }
    };
}
</script>
@endsection
