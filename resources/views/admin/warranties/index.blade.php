@extends('layouts.admin')

@section('title', 'Product Warranty Verification & Management')
@section('page-title', 'Product Warranty Verification Hub')

@section('content')
<div x-data="{ createModalOpen: false, editModalOpen: false, activeWarranty: null }" class="space-y-6">

    <!-- Top KPI Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-5 gap-3.5 sm:gap-4">
        
        <!-- 1. Total Warranties -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl p-4 sm:p-5 border border-slate-200 dark:border-slate-800 shadow-sm relative overflow-hidden">
            <div class="flex items-center justify-between text-slate-400">
                <span class="text-xs font-bold uppercase tracking-wider">Total Issued</span>
                <div class="w-8 h-8 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 flex items-center justify-center">
                    <i data-lucide="shield" class="w-4 h-4"></i>
                </div>
            </div>
            <div class="mt-2.5">
                <div class="text-2xl font-black text-slate-900 dark:text-white code-font">{{ $kpis['total'] }}</div>
                <div class="text-[11px] text-slate-400">Registered hardware warranties</div>
            </div>
            <div class="absolute bottom-0 inset-x-0 h-1 bg-slate-400/40"></div>
        </div>

        <!-- 2. Active Warranties -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl p-4 sm:p-5 border border-slate-200 dark:border-slate-800 shadow-sm relative overflow-hidden">
            <div class="flex items-center justify-between text-slate-400">
                <span class="text-xs font-bold uppercase tracking-wider text-emerald-600 dark:text-emerald-400">Active Covered</span>
                <div class="w-8 h-8 rounded-xl bg-emerald-500/10 text-emerald-500 flex items-center justify-center">
                    <i data-lucide="shield-check" class="w-4 h-4"></i>
                </div>
            </div>
            <div class="mt-2.5">
                <div class="text-2xl font-black text-emerald-600 dark:text-emerald-400 code-font">{{ $kpis['active'] }}</div>
                <div class="text-[11px] text-slate-400">Valid warranty coverage</div>
            </div>
            <div class="absolute bottom-0 inset-x-0 h-1 bg-emerald-500"></div>
        </div>

        <!-- 3. Expiring Soon (< 30 Days) -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl p-4 sm:p-5 border border-slate-200 dark:border-slate-800 shadow-sm relative overflow-hidden">
            <div class="flex items-center justify-between text-slate-400">
                <span class="text-xs font-bold uppercase tracking-wider text-amber-600 dark:text-amber-400">Expiring Soon</span>
                <div class="w-8 h-8 rounded-xl bg-amber-500/10 text-amber-500 flex items-center justify-center">
                    <i data-lucide="clock" class="w-4 h-4"></i>
                </div>
            </div>
            <div class="mt-2.5">
                <div class="text-2xl font-black text-amber-600 dark:text-amber-400 code-font">{{ $kpis['expiring_soon'] }}</div>
                <div class="text-[11px] text-slate-400">&le; 30 days remaining</div>
            </div>
            <div class="absolute bottom-0 inset-x-0 h-1 bg-amber-500"></div>
        </div>

        <!-- 4. Expired Warranties -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl p-4 sm:p-5 border border-slate-200 dark:border-slate-800 shadow-sm relative overflow-hidden">
            <div class="flex items-center justify-between text-slate-400">
                <span class="text-xs font-bold uppercase tracking-wider text-rose-600 dark:text-rose-400">Expired</span>
                <div class="w-8 h-8 rounded-xl bg-rose-500/10 text-rose-500 flex items-center justify-center">
                    <i data-lucide="shield-alert" class="w-4 h-4"></i>
                </div>
            </div>
            <div class="mt-2.5">
                <div class="text-2xl font-black text-rose-600 dark:text-rose-400 code-font">{{ $kpis['expired'] }}</div>
                <div class="text-[11px] text-slate-400">Elapsed validity (0 days)</div>
            </div>
            <div class="absolute bottom-0 inset-x-0 h-1 bg-rose-500"></div>
        </div>

        <!-- 5. Serviced / Claimed -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl p-4 sm:p-5 border border-slate-200 dark:border-slate-800 shadow-sm relative overflow-hidden col-span-2 lg:col-span-1">
            <div class="flex items-center justify-between text-slate-400">
                <span class="text-xs font-bold uppercase tracking-wider text-purple-600 dark:text-purple-400">Serviced / Claims</span>
                <div class="w-8 h-8 rounded-xl bg-purple-500/10 text-purple-500 flex items-center justify-center">
                    <i data-lucide="wrench" class="w-4 h-4"></i>
                </div>
            </div>
            <div class="mt-2.5">
                <div class="text-2xl font-black text-purple-600 dark:text-purple-400 code-font">{{ $kpis['claimed'] }}</div>
                <div class="text-[11px] text-slate-400">Replaced or repaired</div>
            </div>
            <div class="absolute bottom-0 inset-x-0 h-1 bg-purple-500"></div>
        </div>

    </div>

    <!-- Verified Warranty Showcase Card (Shows when search/verification matches) -->
    @if(request()->filled('verify_code'))
    <div class="rounded-2xl p-5 border {{ $verifiedWarranty ? 'bg-gradient-to-r from-slate-900 via-slate-850 to-slate-900 border-emerald-500/50 text-white shadow-xl shadow-emerald-950/30' : 'bg-rose-50 dark:bg-rose-950/20 border-rose-200 dark:border-rose-800' }}">
        @if($verifiedWarranty)
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-slate-700/60 pb-4 mb-4">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-2xl bg-emerald-500/20 text-emerald-400 flex items-center justify-center shadow-md">
                    <i data-lucide="shield-check" class="w-6 h-6"></i>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <span class="px-2 py-0.5 rounded text-[10px] font-black uppercase tracking-wider bg-emerald-500 text-slate-950">
                            Verified Authentic Warranty
                        </span>
                        <span class="font-mono text-xs text-emerald-400 font-bold">{{ $verifiedWarranty->warranty_code }}</span>
                    </div>
                    <h3 class="text-base sm:text-lg font-black text-white mt-1">{{ $verifiedWarranty->product->name }}</h3>
                </div>
            </div>

            <!-- Remaining Days Highlight -->
            <div class="flex items-center gap-3 bg-slate-800/80 px-4 py-2.5 rounded-2xl border border-slate-700">
                <div class="text-right">
                    <div class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Remaining Period</div>
                    <div class="text-lg font-black code-font {{ $verifiedWarranty->remaining_days > 30 ? 'text-emerald-400' : ($verifiedWarranty->remaining_days > 0 ? 'text-amber-400' : 'text-rose-400') }}">
                        {{ $verifiedWarranty->remaining_days }} Days Remaining
                    </div>
                </div>
                <div class="w-10 h-10 rounded-xl {{ $verifiedWarranty->remaining_days > 0 ? 'bg-emerald-500/20 text-emerald-400' : 'bg-rose-500/20 text-rose-400' }} flex items-center justify-center font-black">
                    <i data-lucide="{{ $verifiedWarranty->remaining_days > 0 ? 'check-circle' : 'alert-circle' }}" class="w-5 h-5"></i>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-xs">
            <div>
                <span class="text-[10px] text-slate-400 uppercase font-bold">Serial Number</span>
                <div class="font-mono font-bold text-white mt-0.5">{{ $verifiedWarranty->serial_number ?: 'N/A' }}</div>
            </div>
            <div>
                <span class="text-[10px] text-slate-400 uppercase font-bold">Customer</span>
                <div class="font-semibold text-white mt-0.5">{{ $verifiedWarranty->customer_name }}</div>
                <div class="text-slate-400 font-mono text-[11px]">{{ $verifiedWarranty->customer_phone }}</div>
            </div>
            <div>
                <span class="text-[10px] text-slate-400 uppercase font-bold">Coverage Dates</span>
                <div class="text-slate-200 mt-0.5">{{ $verifiedWarranty->start_date->format('d M Y') }} &rarr; {{ $verifiedWarranty->end_date->format('d M Y') }}</div>
                <div class="text-[10px] text-emerald-400">{{ $verifiedWarranty->warranty_period }}</div>
            </div>
            <div>
                <span class="text-[10px] text-slate-400 uppercase font-bold">Order Reference</span>
                <div class="mt-0.5">
                    @if($verifiedWarranty->order)
                    <a href="{{ route('admin.orders.show', $verifiedWarranty->order_id) }}" class="font-mono text-emerald-400 hover:underline font-bold">
                        {{ $verifiedWarranty->order->order_no }}
                    </a>
                    @else
                    <span class="text-slate-500 font-mono">Walk-in / Direct</span>
                    @endif
                </div>
            </div>
        </div>
        @else
        <div class="flex items-center gap-3 text-rose-700 dark:text-rose-400 text-xs">
            <i data-lucide="alert-circle" class="w-5 h-5 flex-shrink-0"></i>
            <div>
                <span class="font-bold">No warranty found</span> matching code or serial: <strong>{{ request('verify_code') }}</strong>. Please check input digits or register a new warranty.
            </div>
        </div>
        @endif
    </div>
    @endif

    <!-- Verification Search & Action Bar -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl p-4 sm:p-5 border border-slate-200 dark:border-slate-800 shadow-sm flex flex-col sm:flex-row items-center justify-between gap-4">
        
        <!-- Live Instant Warranty Verification Form -->
        <form method="GET" action="{{ route('admin.warranties.index') }}" class="flex flex-col sm:flex-row items-center gap-2.5 w-full sm:w-auto">
            <div class="relative w-full sm:w-96">
                <i data-lucide="search" class="w-4 h-4 absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400"></i>
                <input 
                    type="text" 
                    name="verify_code" 
                    value="{{ request('verify_code', request('search')) }}" 
                    placeholder="Verify Warranty Code, Serial #, or Order #" 
                    class="w-full pl-9 pr-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-800 dark:text-slate-200 outline-none focus:ring-2 focus:ring-emerald-500">
            </div>
            <button type="submit" class="w-full sm:w-auto px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs flex items-center justify-center gap-1.5 transition shadow-sm">
                <i data-lucide="shield-check" class="w-4 h-4"></i>
                <span>Verify Now</span>
            </button>
            @if(request()->filled('verify_code') || request()->filled('status') || request()->filled('search'))
            <a href="{{ route('admin.warranties.index') }}" class="px-3 py-2 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 text-slate-600 dark:text-slate-400 text-xs font-semibold">
                Reset
            </a>
            @endif
        </form>

        <!-- Right: Register New Warranty Button -->
        <button @click="createModalOpen = true" class="w-full sm:w-auto px-4 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 dark:bg-emerald-600 dark:hover:bg-emerald-500 text-white font-bold text-xs flex items-center justify-center gap-2 transition shadow-sm">
            <i data-lucide="plus-circle" class="w-4 h-4"></i>
            <span>Register Warranty</span>
        </button>

    </div>

    <!-- Status Tabs Bar -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl p-2 border border-slate-200 dark:border-slate-800 shadow-sm flex items-center gap-2 overflow-x-auto no-scrollbar">
        @php
            $currentStatus = request('status', '');
            $tabs = [
                '' => ['label' => 'All Warranties', 'count' => $kpis['total']],
                'active' => ['label' => 'Active Coverage', 'count' => $kpis['active']],
                'expiring_soon' => ['label' => 'Expiring Soon (< 30d)', 'count' => $kpis['expiring_soon']],
                'expired' => ['label' => 'Expired (0d)', 'count' => $kpis['expired']],
                'claimed' => ['label' => 'Claimed / Serviced', 'count' => $kpis['claimed']],
            ];
        @endphp

        @foreach($tabs as $tabKey => $tab)
        <a href="{{ route('admin.warranties.index', ['status' => $tabKey, 'verify_code' => request('verify_code')]) }}" 
           class="flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs font-semibold whitespace-nowrap transition {{ $currentStatus === $tabKey ? 'bg-emerald-500 text-slate-950 font-bold shadow-md shadow-emerald-500/20' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
            <span>{{ $tab['label'] }}</span>
            <span class="px-1.5 py-0.5 rounded-full text-[10px] font-bold {{ $currentStatus === $tabKey ? 'bg-slate-950/20 text-slate-950' : 'bg-slate-200 dark:bg-slate-800 text-slate-700 dark:text-slate-300' }}">
                {{ $tab['count'] }}
            </span>
        </a>
        @endforeach
    </div>

    <!-- Warranties Table -->
    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-50 dark:bg-slate-850 border-b border-slate-200 dark:border-slate-800 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                        <th class="px-5 py-3.5">Warranty Code & Serial</th>
                        <th class="px-5 py-3.5">Product Details</th>
                        <th class="px-5 py-3.5">Customer & Contact</th>
                        <th class="px-5 py-3.5">Validity Dates</th>
                        <th class="px-5 py-3.5">Remaining Days</th>
                        <th class="px-5 py-3.5 text-center">Status</th>
                        <th class="px-5 py-3.5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($warranties as $w)
                    <tr class="hover:bg-slate-50/70 dark:hover:bg-slate-800/40 transition">
                        
                        <!-- Warranty Code & Serial -->
                        <td class="px-5 py-4">
                            <div class="font-mono font-bold text-slate-900 dark:text-white">{{ $w->warranty_code }}</div>
                            @if($w->serial_number)
                            <div class="text-[11px] text-slate-400 font-mono">SN: {{ $w->serial_number }}</div>
                            @endif
                            @if($w->order)
                            <a href="{{ route('admin.orders.show', $w->order_id) }}" class="inline-flex items-center gap-1 text-[10px] text-emerald-600 dark:text-emerald-400 hover:underline font-mono">
                                <i data-lucide="package" class="w-3 h-3"></i>
                                <span>{{ $w->order->order_no }}</span>
                            </a>
                            @endif
                        </td>

                        <!-- Product -->
                        <td class="px-5 py-4">
                            <div class="font-semibold text-slate-900 dark:text-white line-clamp-1 max-w-xs">{{ $w->product->name }}</div>
                            <div class="text-[10px] text-slate-400 font-mono">{{ $w->product->sku }} &bull; {{ $w->warranty_period }}</div>
                        </td>

                        <!-- Customer -->
                        <td class="px-5 py-4">
                            <div class="font-medium text-slate-900 dark:text-white">{{ $w->customer_name }}</div>
                            <div class="text-[11px] text-slate-500 font-mono">{{ $w->customer_phone }}</div>
                        </td>

                        <!-- Dates -->
                        <td class="px-5 py-4">
                            <div class="text-slate-700 dark:text-slate-300">{{ $w->start_date->format('d M Y') }} &rarr; {{ $w->end_date->format('d M Y') }}</div>
                            <div class="text-[10px] text-slate-400 font-mono">Duration: {{ $w->warranty_days }} days</div>
                        </td>

                        <!-- Remaining Days with Visual Meter -->
                        <td class="px-5 py-4">
                            <div class="space-y-1.5 w-36">
                                <div class="flex items-center justify-between text-[11px] font-bold">
                                    <span class="{{ $w->remaining_days > 30 ? 'text-emerald-600 dark:text-emerald-400' : ($w->remaining_days > 0 ? 'text-amber-600 dark:text-amber-400' : 'text-rose-600 dark:text-rose-400') }}">
                                        {{ $w->remaining_days }} Days
                                    </span>
                                    <span class="text-[10px] text-slate-400 font-normal">
                                        {{ $w->remaining_percentage }}% left
                                    </span>
                                </div>
                                <!-- Progress Bar -->
                                <div class="w-full bg-slate-100 dark:bg-slate-800 rounded-full h-1.5 overflow-hidden">
                                    <div class="h-1.5 rounded-full {{ $w->remaining_days > 30 ? 'bg-emerald-500' : ($w->remaining_days > 0 ? 'bg-amber-500' : 'bg-rose-500') }}" 
                                         style="width: {{ $w->remaining_percentage }}%"></div>
                                </div>
                            </div>
                        </td>

                        <!-- Status Badge -->
                        <td class="px-5 py-4 text-center">
                            @php
                                $badge = $w->status_badge;
                            @endphp
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider {{ $badge['badge_class'] }}">
                                {{ $badge['label'] }}
                            </span>
                        </td>

                        <!-- Actions -->
                        <td class="px-5 py-4 text-right">
                            <div class="flex items-center justify-end gap-1.5">
                                <button 
                                    @click="activeWarranty = {{ json_encode($w) }}; editModalOpen = true" 
                                    class="p-1.5 rounded-lg text-slate-500 hover:text-emerald-500 hover:bg-slate-100 dark:hover:bg-slate-800 transition" 
                                    title="Edit & Extend Warranty">
                                    <i data-lucide="edit-3" class="w-4 h-4"></i>
                                </button>
                                <form method="POST" action="{{ route('admin.warranties.destroy', $w->id) }}" onsubmit="return confirm('Are you sure you want to delete this warranty record?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1.5 rounded-lg text-slate-400 hover:text-rose-500 hover:bg-rose-500/10 transition" title="Delete Record">
                                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                                    </button>
                                </form>
                            </div>
                        </td>

                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-5 py-12 text-center text-slate-400">
                            <i data-lucide="shield-off" class="w-10 h-10 mx-auto text-slate-300 dark:text-slate-700 mb-2"></i>
                            <div class="font-bold text-slate-600 dark:text-slate-300">No warranty records found</div>
                            <p class="text-xs text-slate-400 mt-1">Try adjusting search parameters or register a new warranty.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($warranties->hasPages())
        <div class="p-4 border-t border-slate-100 dark:border-slate-800">
            {{ $warranties->links() }}
        </div>
        @endif
    </div>

    <!-- MODAL 1: Register / Issue New Warranty -->
    <div x-show="createModalOpen" x-transition.opacity class="fixed inset-0 z-50 bg-slate-950/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div @click.away="createModalOpen = false" class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-2xl max-w-lg w-full p-6 space-y-5">
            <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-xl bg-emerald-500/20 text-emerald-500 flex items-center justify-center">
                        <i data-lucide="shield-plus" class="w-4 h-4"></i>
                    </div>
                    <h3 class="font-bold text-sm text-slate-900 dark:text-white">Register / Issue Product Warranty</h3>
                </div>
                <button @click="createModalOpen = false" class="text-slate-400 hover:text-slate-600">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <form method="POST" action="{{ route('admin.warranties.store') }}" class="space-y-4 text-xs">
                @csrf

                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Select Product *</label>
                    <select name="product_id" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs outline-none focus:ring-2 focus:ring-emerald-500">
                        <option value="">-- Choose Product --</option>
                        @foreach($products as $prod)
                        <option value="{{ $prod->id }}">{{ $prod->name }} ({{ $prod->sku }}) - {{ $prod->warranty ?: 'Standard Warranty' }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Customer Name *</label>
                        <input type="text" name="customer_name" required placeholder="e.g. Salman Chowdhury" class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs outline-none focus:ring-2 focus:ring-emerald-500">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Customer Phone *</label>
                        <input type="text" name="customer_phone" required placeholder="01711223344" class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs outline-none focus:ring-2 focus:ring-emerald-500">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Hardware Serial #</label>
                        <input type="text" name="serial_number" placeholder="SN-PCB-2026-X" class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-mono outline-none focus:ring-2 focus:ring-emerald-500">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Warranty Days *</label>
                        <input type="number" name="warranty_days" value="365" required class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs outline-none focus:ring-2 focus:ring-emerald-500">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Start Date *</label>
                        <input type="date" name="start_date" value="{{ date('Y-m-d') }}" required class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs outline-none focus:ring-2 focus:ring-emerald-500">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Initial Status *</label>
                        <select name="status" required class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs outline-none focus:ring-2 focus:ring-emerald-500">
                            <option value="active">Active (Covered)</option>
                            <option value="claimed">Claimed</option>
                            <option value="voided">Voided</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Admin Notes</label>
                    <textarea name="admin_notes" rows="2" placeholder="Optional notes regarding replacement, inspection or terms..." class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs outline-none focus:ring-2 focus:ring-emerald-500"></textarea>
                </div>

                <div class="pt-2 flex items-center justify-end gap-2 border-t border-slate-100 dark:border-slate-800">
                    <button type="button" @click="createModalOpen = false" class="px-4 py-2 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-semibold">
                        Cancel
                    </button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold transition shadow-sm">
                        Issue Warranty Certificate
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL 2: Edit & Extend Warranty -->
    <div x-show="editModalOpen" x-transition.opacity class="fixed inset-0 z-50 bg-slate-950/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div @click.away="editModalOpen = false" class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-2xl max-w-lg w-full p-6 space-y-5">
            <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-xl bg-emerald-500/20 text-emerald-500 flex items-center justify-center">
                        <i data-lucide="edit-3" class="w-4 h-4"></i>
                    </div>
                    <h3 class="font-bold text-sm text-slate-900 dark:text-white">Edit / Extend Warranty <span class="font-mono text-emerald-500" x-text="activeWarranty?.warranty_code"></span></h3>
                </div>
                <button @click="editModalOpen = false" class="text-slate-400 hover:text-slate-600">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <form :action="'/admin/warranties/' + activeWarranty?.id" method="POST" class="space-y-4 text-xs">
                @csrf
                @method('PUT')

                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Hardware Serial Number</label>
                    <input type="text" name="serial_number" :value="activeWarranty?.serial_number" placeholder="Enter hardware serial / IMEI" class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-mono outline-none focus:ring-2 focus:ring-emerald-500">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Coverage Status *</label>
                        <select name="status" x-model="activeWarranty.status" required class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs outline-none focus:ring-2 focus:ring-emerald-500">
                            <option value="active">Active (Covered)</option>
                            <option value="claimed">Claimed / Replaced</option>
                            <option value="expired">Expired</option>
                            <option value="voided">Voided (Tampered/Liquid)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Expiration End Date *</label>
                        <input type="date" name="end_date" :value="activeWarranty?.end_date ? activeWarranty.end_date.substring(0, 10) : ''" required class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs outline-none focus:ring-2 focus:ring-emerald-500">
                    </div>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Service & Claim Notes</label>
                    <textarea name="claim_notes" rows="2" :value="activeWarranty?.claim_notes" placeholder="Rework details, replacement item reference, inspection status..." class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs outline-none focus:ring-2 focus:ring-emerald-500"></textarea>
                </div>

                <div class="pt-2 flex items-center justify-end gap-2 border-t border-slate-100 dark:border-slate-800">
                    <button type="button" @click="editModalOpen = false" class="px-4 py-2 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-semibold">
                        Cancel
                    </button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold transition shadow-sm">
                        Update Warranty
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
