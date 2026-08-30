@extends('layouts.admin')

@section('title', 'Third-Party API Hub')
@section('page-title', 'Third-Party API Hub & Courier Credentials')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">

    <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 border border-slate-200 dark:border-slate-800 shadow-sm">
        <h2 class="text-base font-bold text-slate-900 dark:text-white flex items-center gap-2">
            <i data-lucide="blocks" class="w-5 h-5 text-emerald-500"></i>
            <span>Integrated Third-Party API Credentials</span>
        </h2>
        <p class="text-xs text-slate-500">Manage Steadfast, Pathao, RedX, BulkSMS, and Payment Gateway API Keys</p>
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

        <!-- 1. Steadfast Courier API -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 border border-slate-200 dark:border-slate-800 shadow-sm space-y-4">
            <div class="flex items-center justify-between border-b pb-3">
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-emerald-500"></span>
                    <h3 class="font-bold text-sm text-slate-900 dark:text-white">Steadfast Courier API</h3>
                </div>
                <span class="text-[10px] font-bold uppercase {{ ($apis['steadfast']->is_active ?? true) ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300' : 'bg-slate-100 text-slate-500' }} px-2 py-0.5 rounded-full">
                    {{ ($apis['steadfast']->is_active ?? true) ? 'Active' : 'Inactive' }}
                </span>
            </div>

            <form method="POST" action="{{ route('admin.settings.api-hub.update') }}" class="space-y-3">
                @csrf
                @method('PUT')
                <input type="hidden" name="provider" value="steadfast">
                <input type="hidden" name="type" value="courier">
                <input type="hidden" name="title" value="Steadfast Courier API">

                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">API Key</label>
                    <input type="password" name="api_key" value="{{ old('api_key', $apis['steadfast']->api_key ?? $apis['steadfast']->credentials['api_key'] ?? 'sf_key_test_98765') }}" class="w-full px-3 py-2 rounded-xl border text-xs font-mono bg-white dark:bg-slate-800 outline-none" placeholder="Enter Steadfast API Key">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Secret Key</label>
                    <input type="password" name="secret_key" value="{{ old('secret_key', $apis['steadfast']->secret_key ?? $apis['steadfast']->credentials['secret_key'] ?? 'sf_secret_test_12345') }}" class="w-full px-3 py-2 rounded-xl border text-xs font-mono bg-white dark:bg-slate-800 outline-none" placeholder="Enter Steadfast Secret Key">
                </div>
                <div class="flex items-center justify-between pt-2">
                    <label class="flex items-center gap-2 cursor-pointer text-xs text-slate-600 dark:text-slate-400">
                        <input type="checkbox" name="is_active" value="1" {{ ($apis['steadfast']->is_active ?? true) ? 'checked' : '' }} class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                        <span>Active</span>
                    </label>
                    <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl text-xs font-bold transition">Save Steadfast API</button>
                </div>
            </form>
        </div>

        <!-- 2. Pathao Courier API -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 border border-slate-200 dark:border-slate-800 shadow-sm space-y-4">
            <div class="flex items-center justify-between border-b pb-3">
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-rose-500"></span>
                    <h3 class="font-bold text-sm text-slate-900 dark:text-white">Pathao Logistics API</h3>
                </div>
                <span class="text-[10px] font-bold uppercase {{ ($apis['pathao']->is_active ?? false) ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300' : 'bg-slate-100 text-slate-700 dark:bg-slate-800' }} px-2 py-0.5 rounded-full">
                    {{ ($apis['pathao']->is_active ?? false) ? 'Active' : 'Ready' }}
                </span>
            </div>

            <form method="POST" action="{{ route('admin.settings.api-hub.update') }}" class="space-y-3">
                @csrf
                @method('PUT')
                <input type="hidden" name="provider" value="pathao">
                <input type="hidden" name="type" value="courier">
                <input type="hidden" name="title" value="Pathao Logistics API">

                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Client ID</label>
                    <input type="text" name="client_id" value="{{ old('client_id', $apis['pathao']->client_id ?? $apis['pathao']->credentials['client_id'] ?? 'pathao_client_id_01') }}" class="w-full px-3 py-2 rounded-xl border text-xs font-mono bg-white dark:bg-slate-800 outline-none" placeholder="Enter Pathao Client ID">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Client Secret</label>
                    <input type="password" name="client_secret" value="{{ old('client_secret', $apis['pathao']->client_secret ?? $apis['pathao']->credentials['client_secret'] ?? 'pathao_sec_991') }}" class="w-full px-3 py-2 rounded-xl border text-xs font-mono bg-white dark:bg-slate-800 outline-none" placeholder="Enter Pathao Client Secret">
                </div>
                <div class="flex items-center justify-between pt-2">
                    <label class="flex items-center gap-2 cursor-pointer text-xs text-slate-600 dark:text-slate-400">
                        <input type="checkbox" name="is_active" value="1" {{ ($apis['pathao']->is_active ?? true) ? 'checked' : '' }} class="rounded border-slate-300 text-slate-800 focus:ring-slate-700">
                        <span>Active</span>
                    </label>
                    <button type="submit" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-white rounded-xl text-xs font-bold transition">Save Pathao API</button>
                </div>
            </form>
        </div>

        <!-- 3. BulkSMS BD Gateway -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 border border-slate-200 dark:border-slate-800 shadow-sm space-y-4">
            <div class="flex items-center justify-between border-b pb-3">
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-sky-500"></span>
                    <h3 class="font-bold text-sm text-slate-900 dark:text-white">BulkSMS BD Gateway</h3>
                </div>
                <span class="text-[10px] font-bold uppercase {{ ($apis['bulksms_bd']->is_active ?? ($apis['bulksms']->is_active ?? false)) ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300' : 'bg-slate-100 text-slate-700 dark:bg-slate-800' }} px-2 py-0.5 rounded-full">
                    {{ ($apis['bulksms_bd']->is_active ?? ($apis['bulksms']->is_active ?? false)) ? 'Live' : 'Ready' }}
                </span>
            </div>

            <form method="POST" action="{{ route('admin.settings.api-hub.update') }}" class="space-y-3">
                @csrf
                @method('PUT')
                <input type="hidden" name="provider" value="bulksms_bd">
                <input type="hidden" name="type" value="sms">
                <input type="hidden" name="title" value="BulkSMS BD Gateway">

                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">API Key</label>
                    <input type="password" name="api_key" value="{{ old('api_key', $apis['bulksms_bd']->api_key ?? $apis['bulksms_bd']->credentials['api_key'] ?? ($apis['bulksms']->api_key ?? ($apis['bulksms']->credentials['api_key'] ?? 'bsms_key_live_8871'))) }}" class="w-full px-3 py-2 rounded-xl border text-xs font-mono bg-white dark:bg-slate-800 outline-none" placeholder="Enter BulkSMS API Key">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Sender ID / Masking</label>
                    <input type="text" name="sender_id" value="{{ old('sender_id', $apis['bulksms_bd']->sender_id ?? $apis['bulksms_bd']->credentials['sender_id'] ?? ($apis['bulksms']->sender_id ?? ($apis['bulksms']->credentials['sender_id'] ?? 'DREAMERS'))) }}" class="w-full px-3 py-2 rounded-xl border text-xs font-mono bg-white dark:bg-slate-800 outline-none" placeholder="Enter Sender ID / Masking">
                </div>
                <div class="flex items-center justify-between pt-2">
                    <label class="flex items-center gap-2 cursor-pointer text-xs text-slate-600 dark:text-slate-400">
                        <input type="checkbox" name="is_active" value="1" {{ ($apis['bulksms_bd']->is_active ?? ($apis['bulksms']->is_active ?? true)) ? 'checked' : '' }} class="rounded border-slate-300 text-sky-600 focus:ring-sky-500">
                        <span>Active</span>
                    </label>
                    <button type="submit" class="px-4 py-2 bg-sky-600 hover:bg-sky-500 text-white rounded-xl text-xs font-bold transition">Save BulkSMS API</button>
                </div>
            </form>
        </div>

        <!-- 4. bKash Merchant API -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 border border-slate-200 dark:border-slate-800 shadow-sm space-y-4">
            <div class="flex items-center justify-between border-b pb-3">
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-pink-500"></span>
                    <h3 class="font-bold text-sm text-slate-900 dark:text-white">bKash Payment Gateway</h3>
                </div>
                <span class="text-[10px] font-bold uppercase {{ ($apis['bkash']->is_active ?? false) ? 'bg-pink-100 text-pink-700 dark:bg-pink-950 dark:text-pink-300' : 'bg-slate-100 text-slate-700 dark:bg-slate-800' }} px-2 py-0.5 rounded-full">
                    {{ ($apis['bkash']->is_active ?? false) ? 'Active' : 'Merchant' }}
                </span>
            </div>

            <form method="POST" action="{{ route('admin.settings.api-hub.update') }}" class="space-y-3">
                @csrf
                @method('PUT')
                <input type="hidden" name="provider" value="bkash">
                <input type="hidden" name="type" value="payment">
                <input type="hidden" name="title" value="bKash Payment Gateway">

                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">App Key</label>
                    <input type="password" name="app_key" value="{{ old('app_key', $apis['bkash']->app_key ?? $apis['bkash']->credentials['app_key'] ?? 'bkash_app_key_998') }}" class="w-full px-3 py-2 rounded-xl border text-xs font-mono bg-white dark:bg-slate-800 outline-none" placeholder="Enter bKash App Key">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">App Secret</label>
                    <input type="password" name="app_secret" value="{{ old('app_secret', $apis['bkash']->app_secret ?? $apis['bkash']->credentials['app_secret'] ?? 'bkash_app_sec_771') }}" class="w-full px-3 py-2 rounded-xl border text-xs font-mono bg-white dark:bg-slate-800 outline-none" placeholder="Enter bKash App Secret">
                </div>
                <div class="flex items-center justify-between pt-2">
                    <label class="flex items-center gap-2 cursor-pointer text-xs text-slate-600 dark:text-slate-400">
                        <input type="checkbox" name="is_active" value="1" {{ ($apis['bkash']->is_active ?? true) ? 'checked' : '' }} class="rounded border-slate-300 text-pink-600 focus:ring-pink-500">
                        <span>Active</span>
                    </label>
                    <button type="submit" class="px-4 py-2 bg-pink-600 hover:bg-pink-500 text-white rounded-xl text-xs font-bold transition">Save bKash API</button>
                </div>
            </form>
        </div>

    </div>

</div>
@endsection
