@extends('layouts.admin')

@section('title', 'Risk Evaluation: ' . $phone)
@section('page-title', 'Fraud & Risk Check Result: ' . $phone)

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <div class="flex items-center justify-between gap-3">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.fraud.index') }}" class="p-2 rounded-xl text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-800 transition">
                <i data-lucide="arrow-left" class="w-5 h-5"></i>
            </a>
            <div>
                <h2 class="text-base font-bold text-slate-900 dark:text-white">Fraud Risk Assessment Report</h2>
                <div class="text-xs text-slate-500 font-mono">Target Phone: <span class="font-bold text-slate-800 dark:text-slate-200">{{ $phone }}</span></div>
            </div>
        </div>

        <div>
            @if($fraudRecord && $fraudRecord->is_blacklisted)
                <form method="POST" action="{{ route('admin.fraud.blacklist.remove', $fraudRecord->id) }}">
                    @csrf
                    <button type="submit" class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold transition shadow-sm flex items-center gap-1.5">
                        <i data-lucide="check" class="w-4 h-4"></i>
                        <span>Remove from Blacklist</span>
                    </button>
                </form>
            @else
                <form method="POST" action="{{ route('admin.fraud.blacklist') }}">
                    @csrf
                    <input type="hidden" name="phone" value="{{ $phone }}">
                    <input type="hidden" name="notes" value="Blacklisted via Risk Evaluation Lookup">
                    <button type="submit" class="px-4 py-2 rounded-xl bg-rose-600 hover:bg-rose-500 text-white text-xs font-bold transition shadow-sm flex items-center gap-1.5">
                        <i data-lucide="ban" class="w-4 h-4"></i>
                        <span>Add to Blacklist</span>
                    </button>
                </form>
            @endif
        </div>
    </div>

    <!-- Risk Score Big Card -->
    <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 sm:p-8 border border-slate-200 dark:border-slate-800 shadow-sm space-y-6">
        <div class="flex flex-col sm:flex-row items-center justify-between gap-6 pb-6 border-b border-slate-100 dark:border-slate-800">
            <div class="space-y-2 text-center sm:text-left">
                <div class="flex items-center gap-2">
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Estimated Risk Level:</span>
                    @if(!empty($evaluation['external_data']['provider_title']))
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-indigo-50 dark:bg-indigo-950 text-indigo-600 dark:text-indigo-400 border border-indigo-200 dark:border-indigo-800">
                            Source: {{ $evaluation['external_data']['provider_title'] }}
                        </span>
                    @endif
                </div>
                <div class="text-3xl font-black uppercase {{ $evaluation['risk_level'] === 'critical' || $evaluation['risk_level'] === 'high' ? 'text-rose-500' : 'text-emerald-500' }}">
                    {{ $evaluation['risk_level'] }} Risk Profile
                </div>
                <p class="text-xs text-slate-500 font-medium">{{ $evaluation['reason'] }}</p>
            </div>

            <div class="w-28 h-28 rounded-full border-4 {{ $evaluation['score'] > 40 ? 'border-rose-500 bg-rose-500/10 text-rose-500' : 'border-emerald-500 bg-emerald-500/10 text-emerald-500' }} flex flex-col items-center justify-center shrink-0">
                <span class="text-3xl font-black code-font">{{ $evaluation['score'] }}%</span>
                <span class="text-[10px] uppercase font-bold text-slate-400">Risk Score</span>
            </div>
        </div>

        <!-- 4-Stat Metric Grid -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-xs">
            <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/40 border border-slate-100 dark:border-slate-800">
                <span class="text-slate-400 text-[11px] block">Courier Delivery Rate:</span>
                <div class="text-xl font-black {{ $evaluation['success_rate'] < 50 ? 'text-rose-500' : 'text-emerald-500' }} code-font mt-1">
                    {{ $evaluation['success_rate'] }}%
                </div>
            </div>

            <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/40 border border-slate-100 dark:border-slate-800">
                <span class="text-slate-400 text-[11px] block">Courier Parcels:</span>
                <div class="text-xl font-bold text-slate-900 dark:text-white code-font mt-1">
                    {{ $evaluation['external_data']['total_parcels'] ?? ($fraudRecord->total_parcels ?? 0) }} Parcels
                </div>
                <div class="text-[10px] text-slate-400 mt-0.5">
                    {{ $evaluation['external_data']['delivered_parcels'] ?? ($fraudRecord->delivered_parcels ?? 0) }} delivered / {{ $evaluation['external_data']['cancelled_parcels'] ?? ($fraudRecord->cancelled_parcels ?? 0) }} returns
                </div>
            </div>

            <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/40 border border-slate-100 dark:border-slate-800">
                <span class="text-slate-400 text-[11px] block">Past Store Orders:</span>
                <div class="text-xl font-bold text-slate-900 dark:text-white code-font mt-1">
                    {{ $pastOrders->count() }} Orders
                </div>
                <div class="text-[10px] text-slate-400 mt-0.5">
                    {{ $customer ? $customer->completed_orders_count : 0 }} completed
                </div>
            </div>

            <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/40 border border-slate-100 dark:border-slate-800">
                <span class="text-slate-400 text-[11px] block">Blacklist Status:</span>
                <div class="text-xl font-bold {{ ($fraudRecord && $fraudRecord->is_blacklisted) ? 'text-rose-500' : 'text-emerald-500' }} mt-1">
                    {{ ($fraudRecord && $fraudRecord->is_blacklisted) ? 'Blacklisted' : 'Clean / Safe' }}
                </div>
            </div>
        </div>

        @if(!empty($evaluation['external_data']['raw_response']))
        <div class="p-4 rounded-2xl bg-slate-950 text-slate-300 border border-slate-800 space-y-2">
            <div class="text-xs font-bold text-indigo-400 flex items-center gap-2">
                <i data-lucide="file-json" class="w-4 h-4"></i>
                <span>Courier API Raw Report Payload</span>
            </div>
            <pre class="text-[11px] font-mono text-emerald-400 overflow-x-auto max-h-40 bg-slate-900 p-3 rounded-xl">{{ json_encode($evaluation['external_data']['raw_response'], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
        </div>
        @endif
    </div>

    <!-- Past Store Orders Table -->
    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
        <div class="p-4 border-b border-slate-200 dark:border-slate-800 font-bold text-sm text-slate-900 dark:text-white flex items-center justify-between">
            <span>Store Order History for Phone {{ $phone }}</span>
            <span class="text-xs font-mono text-slate-400">{{ $pastOrders->count() }} orders</span>
        </div>

        <div class="divide-y divide-slate-100 dark:divide-slate-800">
            @forelse($pastOrders as $order)
            <div class="p-4 flex items-center justify-between hover:bg-slate-50 dark:hover:bg-slate-800/30 transition text-xs">
                <div class="space-y-0.5">
                    <div class="flex items-center gap-2">
                        <a href="{{ route('admin.orders.show', $order->id) }}" class="font-bold code-font text-slate-900 dark:text-white hover:text-emerald-500">
                            {{ $order->order_no }}
                        </a>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase {{ $order->status === 'completed' ? 'bg-emerald-100 text-emerald-700' : ($order->status === 'cancelled' ? 'bg-rose-100 text-rose-700' : 'bg-slate-100 text-slate-700') }}">
                            {{ $order->status }}
                        </span>
                    </div>
                    <div class="text-slate-400 text-[11px]">{{ $order->created_at->format('d M Y, h:i A') }} • {{ $order->shipping_address }}</div>
                </div>

                <div class="text-right font-bold code-font text-slate-900 dark:text-white">
                    ৳{{ number_format($order->grand_total, 2) }}
                </div>
            </div>
            @empty
            <div class="p-6 text-center text-slate-400 text-xs">
                No past orders found in this store for {{ $phone }}.
            </div>
            @endforelse
        </div>
    </div>

</div>
@endsection
