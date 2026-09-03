@extends('layouts.admin')

@section('title', 'Custom SMS Marketing')
@section('page-title', 'SMS Marketing & Notification Gateway')

@section('content')
<div x-data="smsApp()" class="space-y-6">

    <!-- Top KPI Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        
        <div class="bg-white dark:bg-slate-900 rounded-2xl p-5 border border-slate-200 dark:border-slate-800 shadow-sm flex flex-col justify-between">
            <div>
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Active SMS Gateway</span>
                <div class="text-base font-black text-emerald-600 dark:text-emerald-400 mt-2 truncate">{{ $gatewayName }}</div>
                <div class="text-xs text-slate-400 mt-1 flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    <span>Direct API Connection Live</span>
                </div>
            </div>
            <div class="mt-3 pt-2 border-t border-slate-100 dark:border-slate-800">
                <a href="{{ route('admin.settings.api_hub') }}" class="text-[11px] font-bold text-sky-600 hover:text-sky-500 flex items-center gap-1">
                    <span>Manage Gateway API Keys</span>
                    <i data-lucide="arrow-right" class="w-3 h-3"></i>
                </a>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-900 rounded-2xl p-5 border border-slate-200 dark:border-slate-800 shadow-sm">
            <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Sent Messages</span>
            <div class="text-2xl font-black text-slate-900 dark:text-white code-font mt-2">{{ number_format($totalSent) }}</div>
            <div class="text-xs text-slate-400 mt-1">{{ $totalFailed }} failed attempts</div>
        </div>

        <div class="bg-white dark:bg-slate-900 rounded-2xl p-5 border border-slate-200 dark:border-slate-800 shadow-sm">
            <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Audience Reach</span>
            <div class="text-2xl font-black text-sky-500 code-font mt-2">{{ number_format($customersCount) }} Contacts</div>
            <div class="text-xs text-slate-400 mt-1">Active customer CRM phone numbers</div>
        </div>

    </div>

    @if(session('success'))
        <div class="p-4 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300 text-xs font-semibold flex items-center gap-2">
            <i data-lucide="check-circle" class="w-4 h-4 text-emerald-600 shrink-0"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if(session('warning'))
        <div class="p-4 rounded-xl bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-800 text-amber-800 dark:text-amber-300 text-xs font-semibold flex items-center gap-2">
            <i data-lucide="alert-triangle" class="w-4 h-4 text-amber-600 shrink-0"></i>
            <span>{{ session('warning') }}</span>
        </div>
    @endif

    <!-- 2 Columns: Bulk SMS Sender & Template Tokens Helper -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- SMS Composer (2 Cols) -->
        <div class="lg:col-span-2 bg-white dark:bg-slate-900 rounded-2xl p-6 border border-slate-200 dark:border-slate-800 shadow-sm space-y-4">
            <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                <i data-lucide="send" class="w-4 h-4 text-sky-500"></i>
                <span>Broadcast Dynamic SMS Campaign</span>
            </h3>

            <form method="POST" action="{{ route('admin.sms.send') }}" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Target Recipients *</label>
                    <select name="recipient_type" x-model="recipientType" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-semibold outline-none">
                        <option value="all_customers">All Active Customers ({{ $customersCount }} numbers)</option>
                        <option value="custom_numbers">Custom Comma-Separated Phone Numbers</option>
                    </select>
                </div>

                <div x-show="recipientType === 'custom_numbers'" class="space-y-1">
                    <label class="block text-xs font-semibold text-slate-500">Enter Mobile Numbers (e.g. 01711223344, 01899887766...)</label>
                    <textarea name="custom_numbers" rows="2" placeholder="01711223344, 01899887766, 01900112233..." class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-mono outline-none"></textarea>
                </div>

                <div>
                    <div class="flex items-center justify-between mb-1">
                        <label class="text-xs font-semibold text-slate-500">Message Content & Template *</label>
                        <div class="text-[11px] font-mono text-slate-400">
                            <span x-text="message.length"></span> chars | <span x-text="Math.ceil(message.length / 160) || 1"></span> SMS Part(s)
                        </div>
                    </div>
                    <textarea 
                        name="message" 
                        x-model="message" 
                        rows="4" 
                        required 
                        placeholder="Dear {customer_name}, new STM32 & ESP32-S3 boards are now in stock at DREAMERS PCB! Order: {tracking_link}" 
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs outline-none focus:ring-2 focus:ring-sky-500"></textarea>
                </div>

                <button type="submit" class="px-6 py-2.5 bg-sky-600 hover:bg-sky-500 text-white rounded-xl text-xs font-bold shadow-md shadow-sky-600/20 transition flex items-center gap-2">
                    <i data-lucide="send" class="w-4 h-4"></i>
                    <span>Send SMS Broadcast</span>
                </button>
            </form>
        </div>

        <!-- Dynamic Template Tags Helper (1 Col) -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 border border-slate-200 dark:border-slate-800 shadow-sm space-y-4">
            <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                <i data-lucide="code" class="w-4 h-4 text-emerald-500"></i>
                <span>Available Dynamic Tags</span>
            </h3>
            <p class="text-xs text-slate-500">Click a tag below to automatically append it to your SMS template:</p>

            <div class="space-y-2">
                <button type="button" @click="appendTag('{customer_name}')" class="w-full p-2.5 rounded-xl bg-slate-50 dark:bg-slate-800/50 hover:bg-emerald-500/10 border border-slate-200 dark:border-slate-700/60 text-left transition flex items-center justify-between">
                    <span class="font-mono text-xs font-bold text-emerald-600 dark:text-emerald-400">{customer_name}</span>
                    <span class="text-[10px] text-slate-400">Customer Full Name</span>
                </button>

                <button type="button" @click="appendTag('{order_id}')" class="w-full p-2.5 rounded-xl bg-slate-50 dark:bg-slate-800/50 hover:bg-emerald-500/10 border border-slate-200 dark:border-slate-700/60 text-left transition flex items-center justify-between">
                    <span class="font-mono text-xs font-bold text-emerald-600 dark:text-emerald-400">{order_id}</span>
                    <span class="text-[10px] text-slate-400">Order Invoice ID</span>
                </button>

                <button type="button" @click="appendTag('{tracking_link}')" class="w-full p-2.5 rounded-xl bg-slate-50 dark:bg-slate-800/50 hover:bg-emerald-500/10 border border-slate-200 dark:border-slate-700/60 text-left transition flex items-center justify-between">
                    <span class="font-mono text-xs font-bold text-emerald-600 dark:text-emerald-400">{tracking_link}</span>
                    <span class="text-[10px] text-slate-400">Courier Tracking URL</span>
                </button>
            </div>
        </div>

    </div>

    <!-- SMS Dispatch Logs Table -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
        <div class="p-4 border-b border-slate-200 dark:border-slate-800 font-bold text-sm text-slate-900 dark:text-white flex items-center justify-between">
            <span>Recent Outbound SMS Logs</span>
            <span class="text-xs text-slate-400 font-normal">Page {{ $logs->currentPage() }} of {{ $logs->lastPage() }}</span>
        </div>
        <table class="w-full text-left text-xs">
            <thead class="bg-slate-50 dark:bg-slate-800/50 text-slate-500 font-bold uppercase">
                <tr>
                    <th class="p-3">Gateway</th>
                    <th class="p-3">Phone Number</th>
                    <th class="p-3">Message Content</th>
                    <th class="p-3 text-center">Msg ID</th>
                    <th class="p-3">Status</th>
                    <th class="p-3">Date & Time</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                @forelse($logs as $log)
                <tr>
                    <td class="p-3 font-semibold text-slate-800 dark:text-slate-200">{{ $log->gateway }}</td>
                    <td class="p-3 font-mono font-bold text-slate-900 dark:text-white">{{ $log->phone }}</td>
                    <td class="p-3 max-w-md truncate text-slate-600 dark:text-slate-400" title="{{ $log->message }}">{{ $log->message }}</td>
                    <td class="p-3 text-center font-mono text-slate-500">{{ $log->response_id ?? '-' }}</td>
                    <td class="p-3">
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase {{ $log->status === 'sent' ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300' : 'bg-rose-100 text-rose-700 dark:bg-rose-950 dark:text-rose-300' }}">
                            {{ $log->status }}
                        </span>
                    </td>
                    <td class="p-3 text-slate-400">{{ $log->created_at->format('d M, h:i A') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="p-6 text-center text-slate-400">No SMS logs recorded.</td>
                </tr>
                @endforelse
            </tbody>
        </table>

        @if($logs->hasPages())
        <div class="p-4 border-t border-slate-100 dark:border-slate-800">
            {{ $logs->links() }}
        </div>
        @endif
    </div>

</div>
@endsection

@push('scripts')
<script>
    function smsApp() {
        return {
            recipientType: 'all_customers',
            message: '',
            appendTag(tag) {
                this.message += (this.message.length > 0 ? ' ' : '') + tag;
            }
        };
    }
</script>
@endpush
