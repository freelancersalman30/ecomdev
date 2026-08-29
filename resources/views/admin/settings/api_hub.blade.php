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

    <!-- Integrations Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

        <!-- 1. Steadfast Courier API -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 border border-slate-200 dark:border-slate-800 shadow-sm space-y-4">
            <div class="flex items-center justify-between border-b pb-3">
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-emerald-500"></span>
                    <h3 class="font-bold text-sm text-slate-900 dark:text-white">Steadfast Courier API</h3>
                </div>
                <span class="text-[10px] font-bold uppercase bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300 px-2 py-0.5 rounded-full">Active</span>
            </div>

            <form method="POST" action="{{ route('admin.settings.api-hub.update') }}" class="space-y-3">
                @csrf
                @method('PUT')
                <input type="hidden" name="provider" value="steadfast">

                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">API Key</label>
                    <input type="password" name="api_key" value="{{ $apis['steadfast']->api_key ?? 'sf_key_test_98765' }}" class="w-full px-3 py-2 rounded-xl border text-xs font-mono bg-white dark:bg-slate-800 outline-none">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Secret Key</label>
                    <input type="password" name="secret_key" value="{{ $apis['steadfast']->secret_key ?? 'sf_secret_test_12345' }}" class="w-full px-3 py-2 rounded-xl border text-xs font-mono bg-white dark:bg-slate-800 outline-none">
                </div>
                <div class="flex justify-end pt-2">
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
                <span class="text-[10px] font-bold uppercase bg-slate-100 text-slate-700 dark:bg-slate-800 px-2 py-0.5 rounded-full">Ready</span>
            </div>

            <form method="POST" action="{{ route('admin.settings.api-hub.update') }}" class="space-y-3">
                @csrf
                @method('PUT')
                <input type="hidden" name="provider" value="pathao">

                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Client ID</label>
                    <input type="text" name="client_id" value="{{ $apis['pathao']->client_id ?? 'pathao_client_id_01' }}" class="w-full px-3 py-2 rounded-xl border text-xs font-mono bg-white dark:bg-slate-800 outline-none">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Client Secret</label>
                    <input type="password" name="client_secret" value="{{ $apis['pathao']->client_secret ?? 'pathao_sec_991' }}" class="w-full px-3 py-2 rounded-xl border text-xs font-mono bg-white dark:bg-slate-800 outline-none">
                </div>
                <div class="flex justify-end pt-2">
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
                <span class="text-[10px] font-bold uppercase bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300 px-2 py-0.5 rounded-full">Live</span>
            </div>

            <form method="POST" action="{{ route('admin.settings.api-hub.update') }}" class="space-y-3">
                @csrf
                @method('PUT')
                <input type="hidden" name="provider" value="bulksms_bd">

                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">API Key</label>
                    <input type="password" name="api_key" value="{{ $apis['bulksms_bd']->api_key ?? 'bsms_key_live_8871' }}" class="w-full px-3 py-2 rounded-xl border text-xs font-mono bg-white dark:bg-slate-800 outline-none">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Sender ID / Masking</label>
                    <input type="text" name="sender_id" value="{{ $apis['bulksms_bd']->sender_id ?? 'DREAMERS' }}" class="w-full px-3 py-2 rounded-xl border text-xs font-mono bg-white dark:bg-slate-800 outline-none">
                </div>
                <div class="flex justify-end pt-2">
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
                <span class="text-[10px] font-bold uppercase bg-pink-100 text-pink-700 dark:bg-pink-950 dark:text-pink-300 px-2 py-0.5 rounded-full">Merchant</span>
            </div>

            <form method="POST" action="{{ route('admin.settings.api-hub.update') }}" class="space-y-3">
                @csrf
                @method('PUT')
                <input type="hidden" name="provider" value="bkash">

                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">App Key</label>
                    <input type="password" name="app_key" value="{{ $apis['bkash']->app_key ?? 'bkash_app_key_998' }}" class="w-full px-3 py-2 rounded-xl border text-xs font-mono bg-white dark:bg-slate-800 outline-none">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">App Secret</label>
                    <input type="password" name="app_secret" value="{{ $apis['bkash']->app_secret ?? 'bkash_app_sec_771' }}" class="w-full px-3 py-2 rounded-xl border text-xs font-mono bg-white dark:bg-slate-800 outline-none">
                </div>
                <div class="flex justify-end pt-2">
                    <button type="submit" class="px-4 py-2 bg-pink-600 hover:bg-pink-500 text-white rounded-xl text-xs font-bold transition">Save bKash API</button>
                </div>
            </form>
        </div>

    </div>

</div>
@endsection
