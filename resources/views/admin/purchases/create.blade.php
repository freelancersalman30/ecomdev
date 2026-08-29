@extends('layouts.admin')

@section('title', 'New Purchase Order Intake')
@section('page-title', 'Create Purchase Order (PO)')

@section('content')
<div x-data="purchaseForm()" class="max-w-5xl mx-auto space-y-6">

    <form method="POST" action="{{ route('admin.purchases.store') }}" class="space-y-6">
        @csrf

        <!-- Supplier & Meta Info -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 border border-slate-200 dark:border-slate-800 shadow-sm space-y-4">
            <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                <i data-lucide="truck" class="w-4 h-4 text-emerald-500"></i>
                <span>Supplier & Purchase Invoice Details</span>
            </h3>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Select Supplier *</label>
                    <select name="supplier_id" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-semibold outline-none">
                        <option value="">Select Supplier</option>
                        @foreach($suppliers as $supplier)
                        <option value="{{ $supplier->id }}">{{ $supplier->name }} (Due: ৳{{ number_format($supplier->current_due, 2) }})</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Purchase Date *</label>
                    <input type="date" name="purchase_date" value="{{ date('Y-m-d') }}" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs outline-none">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Supplier Invoice Ref</label>
                    <input type="text" name="supplier_invoice_no" placeholder="SZ-EXP-9812" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-mono outline-none">
                </div>
            </div>
        </div>

        <!-- Line Items & Serial/Barcode Tracking -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 border border-slate-200 dark:border-slate-800 shadow-sm space-y-4">
            <div class="flex items-center justify-between">
                <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                    <i data-lucide="package-plus" class="w-4 h-4 text-sky-500"></i>
                    <span>Purchased Items & Inventory Stock Intake</span>
                </h3>
                <button type="button" @click="addItem()" class="px-3.5 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-white text-xs font-semibold transition">
                    + Add Item Line
                </button>
            </div>

            <div class="space-y-3">
                <template x-for="(item, idx) in items" :key="idx">
                    <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-800/40 border border-slate-200 dark:border-slate-700/60 space-y-3">
                        <div class="grid grid-cols-1 sm:grid-cols-5 gap-3 items-end">
                            <div class="sm:col-span-2">
                                <label class="block text-[10px] font-bold text-slate-500 mb-1">Product Component *</label>
                                <select :name="`items[${idx}][product_id]`" x-model="item.product_id" required class="w-full px-2.5 py-2 rounded-lg border text-xs bg-white dark:bg-slate-800 outline-none">
                                    <option value="">Select Product</option>
                                    @foreach($products as $prod)
                                    <option value="{{ $prod->id }}">{{ $prod->name }} (SKU: {{ $prod->sku }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-500 mb-1">Unit Cost Price (৳) *</label>
                                <input type="number" step="0.01" :name="`items[${idx}][unit_cost]`" x-model.number="item.unit_cost" required placeholder="0.00" class="w-full px-2.5 py-2 rounded-lg border text-xs font-bold code-font bg-white dark:bg-slate-800 outline-none">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-500 mb-1">Intake Qty *</label>
                                <input type="number" :name="`items[${idx}][quantity]`" x-model.number="item.quantity" required min="1" placeholder="1" class="w-full px-2.5 py-2 rounded-lg border text-xs font-bold code-font bg-white dark:bg-slate-800 outline-none">
                            </div>
                            <div class="text-right">
                                <div class="text-xs font-bold text-slate-900 dark:text-white code-font mb-1">
                                    ৳<span x-text="((item.unit_cost || 0) * (item.quantity || 0)).toFixed(2)"></span>
                                </div>
                                <button type="button" @click="removeItem(idx)" class="text-[10px] text-rose-500 hover:underline">Remove</button>
                            </div>
                        </div>

                        <!-- Barcode & Serial Tracking -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-2 border-t border-slate-200/60 dark:border-slate-700/40">
                            <div>
                                <label class="block text-[10px] font-semibold text-slate-400 mb-0.5">Batch / Lot Number</label>
                                <input type="text" :name="`items[${idx}][batch_no]`" placeholder="e.g. BAT-202608-A" class="w-full px-2.5 py-1.5 rounded-lg border text-xs bg-white dark:bg-slate-800 outline-none">
                            </div>
                            <div>
                                <label class="block text-[10px] font-semibold text-slate-400 mb-0.5">Serials / Barcodes (Comma Separated)</label>
                                <input type="text" :name="`items[${idx}][serial_numbers]`" placeholder="SN1001, SN1002, SN1003..." class="w-full px-2.5 py-1.5 rounded-lg border text-xs font-mono bg-white dark:bg-slate-800 outline-none">
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <!-- Totals & Payment Settlement -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 border border-slate-200 dark:border-slate-800 shadow-sm space-y-4">
            <h3 class="text-sm font-bold text-slate-900 dark:text-white">Financial Settlement</h3>

            <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Discount (৳)</label>
                    <input type="number" step="0.01" name="discount" x-model.number="discount" placeholder="0.00" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs code-font outline-none">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Shipping / Freight (৳)</label>
                    <input type="number" step="0.01" name="shipping_cost" x-model.number="shipping" placeholder="0.00" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs code-font outline-none">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Initial Paid Amount (৳)</label>
                    <input type="number" step="0.01" name="paid_amount" x-model.number="paidAmount" placeholder="0.00" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-bold text-emerald-600 code-font outline-none">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Payment Method</label>
                    <select name="payment_method" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs outline-none">
                        <option value="bank">Bank Transfer</option>
                        <option value="cash">Cash In Drawer</option>
                        <option value="bkash">bKash</option>
                    </select>
                </div>
            </div>

            <!-- Grand Total Summary Box -->
            <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-800/60 flex items-center justify-between">
                <div>
                    <div class="text-xs text-slate-500">Calculated Purchase Total:</div>
                    <div class="text-xl font-black text-slate-900 dark:text-white code-font">
                        ৳<span x-text="getGrandTotal().toFixed(2)"></span>
                    </div>
                </div>
                <div class="text-right">
                    <div class="text-xs text-slate-500">Supplier Due Remaining:</div>
                    <div class="text-xl font-black text-rose-500 code-font">
                        ৳<span x-text="Math.max(0, getGrandTotal() - (paidAmount || 0)).toFixed(2)"></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('admin.purchases.index') }}" class="px-5 py-2.5 rounded-xl bg-slate-200 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-semibold text-xs transition">
                Cancel
            </a>
            <button type="submit" class="px-6 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-extrabold text-xs shadow-lg transition">
                Save PO & Increment Stock
            </button>
        </div>

    </form>

</div>
@endsection

@push('scripts')
<script>
    function purchaseForm() {
        return {
            items: [
                { product_id: '', unit_cost: '', quantity: 1 }
            ],
            discount: 0,
            shipping: 0,
            paidAmount: 0,

            addItem() {
                this.items.push({ product_id: '', unit_cost: '', quantity: 1 });
            },

            removeItem(index) {
                if (this.items.length > 1) {
                    this.items.splice(index, 1);
                }
            },

            getGrandTotal() {
                const sub = this.items.reduce((sum, item) => sum + ((item.unit_cost || 0) * (item.quantity || 0)), 0);
                return Math.max(0, (sub - (this.discount || 0)) + (this.shipping || 0));
            }
        };
    }
</script>
@endpush
