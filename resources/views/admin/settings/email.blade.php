@extends('layouts.admin')

@section('title', 'Email Configuration')
@section('page-title', 'SMTP Email & Notification Gateway')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <form method="POST" action="{{ route('admin.settings.email.update') }}" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 border border-slate-200 dark:border-slate-800 shadow-sm space-y-4">
            <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                <i data-lucide="mail" class="w-4 h-4 text-emerald-500"></i>
                <span>SMTP Mail Server Credentials</span>
            </h3>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Mail Driver</label>
                    <input type="text" name="mail_driver" value="{{ $emailSettings['mail_driver'] ?? 'smtp' }}" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-mono outline-none">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">SMTP Host</label>
                    <input type="text" name="mail_host" value="{{ $emailSettings['mail_host'] ?? 'smtp.mailtrap.io' }}" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-mono outline-none">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">SMTP Port</label>
                    <input type="text" name="mail_port" value="{{ $emailSettings['mail_port'] ?? '587' }}" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-mono outline-none">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Encryption</label>
                    <input type="text" name="mail_encryption" value="{{ $emailSettings['mail_encryption'] ?? 'tls' }}" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-mono outline-none">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">SMTP Username</label>
                    <input type="text" name="mail_username" value="{{ $emailSettings['mail_username'] ?? '' }}" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-mono outline-none">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">SMTP Password</label>
                    <input type="password" name="mail_password" value="{{ $emailSettings['mail_password'] ?? '' }}" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-mono outline-none">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">From Email Address</label>
                    <input type="email" name="mail_from_address" value="{{ $emailSettings['mail_from_address'] ?? 'noreply@dreamerspcb.com' }}" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs outline-none">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">From Sender Name</label>
                    <input type="text" name="mail_from_name" value="{{ $emailSettings['mail_from_name'] ?? 'DREAMERS PCB Support' }}" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs outline-none">
                </div>
            </div>
        </div>

        <div class="flex items-center justify-end">
            <button type="submit" class="px-6 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-extrabold text-xs shadow-lg transition">
                Save SMTP Settings
            </button>
        </div>

    </form>

</div>
@endsection
