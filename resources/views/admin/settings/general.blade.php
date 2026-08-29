@extends('layouts.admin')

@section('title', 'General Settings & Integrations')
@section('page-title', 'Store Profile & General Settings')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <form method="POST" action="{{ route('admin.settings.general.update') }}" class="space-y-6">
        @csrf
        @method('PUT')

        <!-- Store Identity -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 border border-slate-200 dark:border-slate-800 shadow-sm space-y-4">
            <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                <i data-lucide="store" class="w-4 h-4 text-emerald-500"></i>
                <span>Store Identity & Contact Details</span>
            </h3>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Company / Store Name *</label>
                    <input type="text" name="company_name" value="{{ $settings['company_name'] ?? 'DREAMERS PCB' }}" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-bold outline-none focus:ring-2 focus:ring-emerald-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Support Phone Hotline *</label>
                    <input type="text" name="phone" value="{{ $settings['phone'] ?? '+880 1700-112233' }}" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-mono outline-none">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Support Email *</label>
                    <input type="email" name="email" value="{{ $settings['email'] ?? 'support@dreamerspcb.com' }}" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs outline-none">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Currency Symbol</label>
                    <input type="text" name="currency_symbol" value="{{ $settings['currency_symbol'] ?? '৳' }}" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-bold outline-none">
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Physical Warehouse / Office Address</label>
                    <textarea name="address" rows="2" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs outline-none">{{ $settings['address'] ?? 'Multiplan Center, Level 9, Elephant Road, Dhaka, Bangladesh' }}</textarea>
                </div>
            </div>
        </div>

        <!-- Default Shipping & POS Configuration -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 border border-slate-200 dark:border-slate-800 shadow-sm space-y-4">
            <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                <i data-lucide="truck" class="w-4 h-4 text-sky-500"></i>
                <span>Shipping Charges & Automation</span>
            </h3>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Inside Dhaka Delivery Charge (৳)</label>
                    <input type="number" step="0.01" name="inside_dhaka_charge" value="{{ $settings['inside_dhaka_charge'] ?? 70 }}" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-bold code-font outline-none">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Outside Dhaka Delivery Charge (৳)</label>
                    <input type="number" step="0.01" name="outside_dhaka_charge" value="{{ $settings['outside_dhaka_charge'] ?? 130 }}" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-bold code-font outline-none">
                </div>
            </div>
        </div>

        <div class="flex items-center justify-end">
            <button type="submit" class="px-6 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-extrabold text-xs shadow-lg transition">
                Save General Settings
            </button>
        </div>

    </form>

</div>
@endsection
