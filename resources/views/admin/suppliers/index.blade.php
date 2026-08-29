@extends('layouts.admin')

@section('title', 'Supplier Directory & Ledger')
@section('page-title', 'Suppliers & Vendor Directory')

@section('content')
<div class="space-y-6">

    <!-- Header Stats & Add Supplier Button -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 border border-slate-200 dark:border-slate-800 shadow-sm flex flex-col sm:flex-row items-center justify-between gap-4">
        <div>
            <h2 class="text-base font-bold text-slate-900 dark:text-white">Vendor Directory</h2>
            <p class="text-xs text-slate-500">Track vendor procurement volume, payments, and payable dues</p>
        </div>

        <div class="flex items-center gap-4">
            <div class="text-right">
                <span class="text-xs text-slate-400">Total Supplier Payable Due:</span>
                <div class="text-xl font-black text-rose-500 code-font">৳{{ number_format($totalPayable, 2) }}</div>
            </div>
            <button onclick="document.getElementById('addSupplierModal').style.display = 'flex'" class="px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs shadow-md transition flex items-center gap-1.5">
                <i data-lucide="user-plus" class="w-4 h-4"></i>
                <span>+ Add Supplier</span>
            </button>
        </div>
    </div>

    <!-- Suppliers Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
        @foreach($suppliers as $supplier)
        <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 border border-slate-200 dark:border-slate-800 shadow-sm flex flex-col justify-between space-y-4 hover:border-emerald-500/50 transition">
            <div class="space-y-2">
                <div class="flex items-start justify-between">
                    <div>
                        <h3 class="font-bold text-sm text-slate-900 dark:text-white">{{ $supplier->name }}</h3>
                        <p class="text-xs text-slate-500">{{ $supplier->company ?? 'Vendor' }}</p>
                    </div>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $supplier->current_due > 0 ? 'bg-rose-500/10 text-rose-500' : 'bg-emerald-500/10 text-emerald-500' }}">
                        {{ $supplier->current_due > 0 ? 'Due' : 'Clear' }}
                    </span>
                </div>

                <div class="text-xs space-y-1 text-slate-600 dark:text-slate-400 pt-2 border-t border-slate-100 dark:border-slate-800">
                    <div class="flex items-center gap-2">
                        <i data-lucide="phone" class="w-3.5 h-3.5 text-slate-400"></i>
                        <span class="font-mono">{{ $supplier->phone }}</span>
                    </div>
                    @if($supplier->email)
                    <div class="flex items-center gap-2">
                        <i data-lucide="mail" class="w-3.5 h-3.5 text-slate-400"></i>
                        <span>{{ $supplier->email }}</span>
                    </div>
                    @endif
                    @if($supplier->address)
                    <div class="flex items-center gap-2">
                        <i data-lucide="map-pin" class="w-3.5 h-3.5 text-slate-400"></i>
                        <span class="truncate">{{ $supplier->address }}</span>
                    </div>
                    @endif
                </div>
            </div>

            <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-800/50 flex items-center justify-between text-xs">
                <div>
                    <span class="text-slate-400">Total Purchases:</span>
                    <div class="font-bold text-slate-900 dark:text-white code-font">৳{{ number_format($supplier->total_purchased, 2) }}</div>
                </div>
                <div class="text-right">
                    <span class="text-slate-400">Current Due:</span>
                    <div class="font-bold text-rose-500 code-font">৳{{ number_format($supplier->current_due, 2) }}</div>
                </div>
            </div>

            <div class="flex items-center justify-between pt-2 border-t border-slate-100 dark:border-slate-800">
                <span class="text-xs text-slate-400">{{ $supplier->purchases_count }} POs Recorded</span>
                <a href="{{ route('admin.suppliers.show', $supplier->id) }}" class="px-3 py-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-white text-xs font-semibold">
                    View Ledger &rarr;
                </a>
            </div>
        </div>
        @endforeach
    </div>

    <!-- ADD SUPPLIER MODAL -->
    <div id="addSupplierModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/70 backdrop-blur-sm" style="display: none;">
        <div class="bg-white dark:bg-slate-900 rounded-2xl max-w-lg w-full p-6 border border-slate-200 dark:border-slate-800 shadow-2xl space-y-4">
            <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-800 pb-3">
                <h3 class="text-sm font-bold text-slate-900 dark:text-white">Create New Supplier Directory Profile</h3>
                <button onclick="document.getElementById('addSupplierModal').style.display = 'none'" class="text-slate-400 hover:text-white">&times;</button>
            </div>
            
            <form method="POST" action="{{ route('admin.suppliers.store') }}" class="space-y-3">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Supplier Name *</label>
                    <input type="text" name="name" required placeholder="e.g. Shenzhen RoboTech Ltd." class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs outline-none focus:ring-2 focus:ring-emerald-500">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 mb-1">Company / Org</label>
                        <input type="text" name="company" placeholder="Industrial Agency" class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 mb-1">Phone Number *</label>
                        <input type="text" name="phone" required placeholder="01711223344" class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-mono outline-none">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 mb-1">Email</label>
                        <input type="email" name="email" placeholder="vendor@example.com" class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 mb-1">Opening Balance Due (৳)</label>
                        <input type="number" step="0.01" name="opening_balance" placeholder="0.00" class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs code-font outline-none">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Physical Address</label>
                    <textarea name="address" rows="2" placeholder="Warehouse address..." class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs outline-none"></textarea>
                </div>
                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" onclick="document.getElementById('addSupplierModal').style.display = 'none'" class="px-4 py-2 rounded-xl bg-slate-200 dark:bg-slate-800 text-slate-700 dark:text-slate-300 text-xs font-semibold">
                        Cancel
                    </button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold shadow-md">
                        Save Supplier
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
