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
            <p class="text-xs text-slate-500 mt-1">Configure credentials, toggle Active / Inactive states, and test live server API connectivity for Bulk SMS Dhaka, Steadfast, bKash, BulkSMS BD, and Pathao.</p>
        </div>
        <div class="flex items-center gap-2 text-xs">
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-emerald-50 dark:bg-emerald-950/50 border border-emerald-200 dark:border-emerald-800 text-emerald-700 dark:text-emerald-300 font-semibold">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                <span>Live Multi-Gateway Active</span>
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
        loading: {
            bulksmsdhaka: false,
            steadfast: false,
            bulksms: false,
            bkash: false,
        },
        testResults: {
            bulksmsdhaka: null,
            steadfast: null,
            bulksms: null,
            bkash: null,
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
