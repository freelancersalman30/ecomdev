@extends('layouts.admin')

@section('title', 'Risk Evaluation: ' . $phone)
@section('page-title', 'Fraud & Risk Check Result: ' . $phone)

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <div class="flex items-center gap-3">
        <a href="{{ route('admin.fraud.index') }}" class="p-2 rounded-xl text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-800 transition">
            <i data-lucide="arrow-left" class="w-5 h-5"></i>
        </a>
        <div>
            <h2 class="text-base font-bold text-slate-900 dark:text-white">Fraud Risk Assessment Report</h2>
            <div class="text-xs text-slate-500 font-mono">Target Phone: {{ $phone }}</div>
        </div>
    </div>

    <!-- Risk Score Big Card -->
    <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 sm:p-8 border border-slate-200 dark:border-slate-800 shadow-sm space-y-6">
        <div class="flex flex-col sm:flex-row items-center justify-between gap-6 pb-6 border-b border-slate-100 dark:border-slate-800">
            <div class="space-y-2 text-center sm:text-left">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Estimated Risk Level:</span>
                <div class="text-3xl font-black uppercase {{ $evaluation['risk_level'] === 'critical' || $evaluation['risk_level'] === 'high' ? 'text-rose-500' : 'text-emerald-500' }}">
                    {{ $evaluation['risk_level'] }} Risk Profile
                </div>
                <p class="text-xs text-slate-500">{{ $evaluation['reason'] }}</p>
            </div>

            <div class="w-28 h-28 rounded-full border-4 {{ $evaluation['score'] > 40 ? 'border-rose-500 bg-rose-500/10 text-rose-500' : 'border-emerald-500 bg-emerald-500/10 text-emerald-500' }} flex flex-col items-center justify-center">
                <span class="text-3xl font-black code-font">{{ $evaluation['score'] }}%</span>
                <span class="text-[10px] uppercase font-bold text-slate-400">Risk Score</span>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs">
            <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-800/40">
                <span class="text-slate-400">Delivery Success Rate:</span>
                <div class="text-lg font-bold text-slate-900 dark:text-white code-font mt-1">{{ $evaluation['success_rate'] }}%</div>
            </div>
            <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-800/40">
                <span class="text-slate-400">Past Orders in Store:</span>
                <div class="text-lg font-bold text-slate-900 dark:text-white code-font mt-1">{{ $pastOrders->count() }} Orders</div>
            </div>
            <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-800/40">
                <span class="text-slate-400">Customer Flag Status:</span>
                <div class="text-lg font-bold {{ $customer && $customer->is_flagged_fraud ? 'text-rose-500' : 'text-emerald-500' }} mt-1">
                    {{ $customer && $customer->is_flagged_fraud ? 'Flagged Buyer' : 'Good Standing' }}
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
