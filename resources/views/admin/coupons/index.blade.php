@extends('layouts.admin')

@section('title', 'Coupon & Discount Engine')
@section('page-title', 'Coupons & Promo Codes Engine')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-3 gap-6">

    <!-- Create Coupon Form -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl p-5 border border-slate-200 dark:border-slate-800 shadow-sm space-y-4">
        <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
            <i data-lucide="ticket-percent" class="w-4 h-4 text-emerald-500"></i>
            <span>Create Promo Coupon</span>
        </h3>
        
        <form method="POST" action="{{ route('admin.coupons.store') }}" class="space-y-3">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-slate-500 mb-1">Coupon Code *</label>
                <input type="text" name="code" required placeholder="e.g. FLASH2026" class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-mono font-bold uppercase outline-none focus:ring-2 focus:ring-emerald-500">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Discount Type *</label>
                    <select name="discount_type" class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs outline-none">
                        <option value="percentage">Percentage (%)</option>
                        <option value="fixed">Fixed Amount (৳)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Discount Value *</label>
                    <input type="number" step="0.01" name="discount_value" required placeholder="10" class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs code-font outline-none">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Min Order Amount (৳)</label>
                    <input type="number" step="0.01" name="min_order_amount" placeholder="0.00" class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs code-font outline-none">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Max Discount (৳)</label>
                    <input type="number" step="0.01" name="max_discount_amount" placeholder="1000.00" class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs code-font outline-none">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Total Usage Limit</label>
                    <input type="number" name="usage_limit" placeholder="500" class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs outline-none">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Per User Limit</label>
                    <input type="number" name="usage_per_user" value="1" class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs outline-none">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Starts At</label>
                    <input type="date" name="starts_at" class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs outline-none">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Expires At</label>
                    <input type="date" name="expires_at" class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs outline-none">
                </div>
            </div>

            <button type="submit" class="w-full py-2 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl text-xs font-bold shadow-md transition">
                + Generate Coupon
            </button>
        </form>
    </div>

    <!-- Active Coupons List (2 Cols) -->
    <div class="md:col-span-2 bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
        <div class="p-4 border-b border-slate-200 dark:border-slate-800 font-bold text-sm text-slate-900 dark:text-white">
            Available Discount Coupons ({{ $coupons->count() }})
        </div>
        <div class="divide-y divide-slate-100 dark:divide-slate-800">
            @forelse($coupons as $coupon)
            <div class="p-4 flex items-center justify-between hover:bg-slate-50 dark:hover:bg-slate-800/30 transition">
                <div class="space-y-1">
                    <div class="flex items-center gap-2">
                        <span class="font-mono font-black text-sm px-2.5 py-1 rounded-lg bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20">
                            {{ $coupon->code }}
                        </span>
                        <span class="text-xs font-bold text-slate-900 dark:text-white">
                            {{ $coupon->discount_type === 'percentage' ? $coupon->discount_value . '% OFF' : '৳' . number_format($coupon->discount_value, 2) . ' OFF' }}
                        </span>
                    </div>
                    <div class="text-[11px] text-slate-500">
                        Min Order: ৳{{ number_format($coupon->min_order_amount, 2) }}
                        @if($coupon->max_discount_amount) | Max: ৳{{ number_format($coupon->max_discount_amount, 2) }} @endif
                    </div>
                </div>

                <div class="flex items-center gap-4">
                    <div class="text-right text-xs">
                        <span class="text-slate-400">Used:</span>
                        <span class="font-bold text-slate-900 dark:text-white">{{ $coupon->times_used }}</span>
                        @if($coupon->usage_limit) / {{ $coupon->usage_limit }} @endif
                    </div>

                    <form action="{{ route('admin.coupons.destroy', $coupon->id) }}" method="POST" onsubmit="return confirm('Delete coupon?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-xs text-rose-500 hover:underline">Delete</button>
                    </form>
                </div>
            </div>
            @empty
            <div class="p-8 text-center text-slate-400 text-xs">No active coupons created.</div>
            @endforelse
        </div>
    </div>

</div>
@endsection
