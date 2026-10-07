@extends('layouts.admin')

@section('title', 'Custom PC: ' . $customPcBuild->title)
@section('page-title', 'Custom PC Profile: ' . $customPcBuild->title)

@section('content')
<div class="space-y-6" x-data="{ convertModalOpen: false }">

    <!-- Top Action Bar & Overview Header -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 border border-slate-200 dark:border-slate-800 shadow-sm flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
        <div class="flex items-start sm:items-center gap-4">
            <a href="{{ route('admin.custom-pc-builds.index') }}" class="p-2 rounded-xl text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-800 transition">
                <i data-lucide="arrow-left" class="w-5 h-5"></i>
            </a>
            
            <div class="w-16 h-16 rounded-2xl bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 flex items-center justify-center flex-shrink-0 overflow-hidden">
                @if($customPcBuild->thumbnail)
                    <img src="{{ $customPcBuild->thumbnail }}" alt="{{ $customPcBuild->title }}" class="w-full h-full object-cover">
                @else
                    <i data-lucide="cpu" class="w-8 h-8 text-indigo-400"></i>
                @endif
            </div>

            <div>
                <div class="flex flex-wrap items-center gap-2">
                    <h1 class="text-lg font-black text-slate-900 dark:text-white">{{ $customPcBuild->title }}</h1>
                    @if($customPcBuild->is_featured)
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold uppercase bg-amber-500/20 text-amber-400 flex items-center gap-1">
                            <i data-lucide="sparkles" class="w-3 h-3"></i> Featured
                        </span>
                    @endif
                    @if($customPcBuild->is_published)
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/10 text-emerald-500 flex items-center gap-1">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                            Live on Storefront
                        </span>
                    @else
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-500/10 text-slate-400">
                            Draft / Hidden
                        </span>
                    @endif
                </div>

                <div class="text-xs text-slate-400 font-mono mt-1 flex flex-wrap items-center gap-3">
                    <span>Code: <strong class="text-indigo-400">{{ $customPcBuild->build_code }}</strong></span>
                    <span>•</span>
                    <span>Category: <strong>{{ $customPcBuild->category ?? 'Gaming' }}</strong></span>
                    <span>•</span>
                    <span>Tier: <strong>{{ $customPcBuild->performance_level ?? 'Mid-Range' }}</strong></span>
                    <span>•</span>
                    <span>Stock: <strong class="capitalize">{{ str_replace('_', ' ', $customPcBuild->stock_status) }}</strong></span>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex flex-wrap items-center gap-2 w-full md:w-auto justify-end">
            <!-- Convert to POS Order -->
            <button type="button" 
                    @click="convertModalOpen = true" 
                    class="px-4 py-2.5 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white font-bold text-xs shadow-lg shadow-emerald-500/20 transition flex items-center gap-1.5">
                <i data-lucide="shopping-cart" class="w-4 h-4"></i>
                <span>Convert to Customer Order</span>
            </button>

            <!-- Printable Quotation -->
            <a href="{{ route('admin.custom-pc-builds.quotation', $customPcBuild->id) }}" 
               target="_blank" 
               class="px-3.5 py-2.5 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 font-bold text-xs transition flex items-center gap-1.5">
                <i data-lucide="printer" class="w-4 h-4"></i>
                <span>Print Spec Sheet</span>
            </a>

            <!-- Edit -->
            <a href="{{ route('admin.custom-pc-builds.edit', $customPcBuild->id) }}" 
               class="px-3.5 py-2.5 rounded-xl bg-sky-50 hover:bg-sky-100 text-sky-600 dark:bg-sky-950/40 dark:hover:bg-sky-950/80 dark:text-sky-400 font-bold text-xs transition flex items-center gap-1.5">
                <i data-lucide="pencil" class="w-4 h-4"></i>
                <span>Edit</span>
            </a>

            <!-- Clone / Duplicate -->
            <form method="POST" action="{{ route('admin.custom-pc-builds.duplicate', $customPcBuild->id) }}" class="inline">
                @csrf
                <button type="submit" title="Clone Build" class="p-2.5 rounded-xl bg-purple-50 hover:bg-purple-100 text-purple-600 dark:bg-purple-950/40 dark:hover:bg-purple-950/80 dark:text-purple-400 font-bold text-xs transition flex items-center gap-1">
                    <i data-lucide="copy" class="w-4 h-4"></i>
                </button>
            </form>

            <!-- Delete -->
            <form method="POST" action="{{ route('admin.custom-pc-builds.destroy', $customPcBuild->id) }}" onsubmit="return confirm('Are you sure you want to delete this custom PC build?');" class="inline">
                @csrf
                @method('DELETE')
                <button type="submit" title="Delete Build" class="p-2.5 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-600 dark:bg-rose-950/40 dark:hover:bg-rose-950/80 dark:text-rose-400 font-bold text-xs transition">
                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                </button>
            </form>
        </div>
    </div>

    <!-- 4 Metrics & Highlights Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        
        <div class="p-4 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm">
            <span class="text-xs text-slate-400">Final Package Price</span>
            <div class="text-2xl font-black text-emerald-500 code-font mt-1">
                ৳{{ number_format($customPcBuild->final_price, 2) }}
            </div>
            @if($customPcBuild->regular_price > $customPcBuild->final_price)
            <div class="text-[11px] text-slate-400 line-through code-font mt-0.5">
                Regular: ৳{{ number_format($customPcBuild->regular_price, 2) }}
            </div>
            @endif
        </div>

        <div class="p-4 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm">
            <span class="text-xs text-slate-400">Combo Discount Savings</span>
            <div class="text-2xl font-black text-rose-500 code-font mt-1">
                @php
                    $savings = max(0, $customPcBuild->regular_price - $customPcBuild->final_price);
                @endphp
                ৳{{ number_format($savings, 2) }}
            </div>
            <div class="text-[11px] text-slate-400 mt-0.5">
                {{ $customPcBuild->discount_type === 'percentage' ? $customPcBuild->discount_amount . '% Combo Discount' : ($savings > 0 ? 'Fixed Bundle Discount' : 'No Discount Applied') }}
            </div>
        </div>

        <div class="p-4 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm">
            <span class="text-xs text-slate-400">Estimated Power Load</span>
            <div class="text-2xl font-black text-amber-500 font-mono mt-1 flex items-center gap-1.5">
                <i data-lucide="zap" class="w-5 h-5"></i>
                <span>{{ $customPcBuild->estimated_wattage ?: 450 }}W</span>
            </div>
            <div class="text-[11px] text-slate-400 mt-0.5">
                Rec. PSU: {{ ceil((($customPcBuild->estimated_wattage ?: 450) * 1.3) / 50) * 50 }}W 80+
            </div>
        </div>

        <div class="p-4 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm">
            @php
                $components = is_array($customPcBuild->components) ? $customPcBuild->components : [];
                $configuredSlots = count(array_filter($components, fn($s) => !empty($s['product_id']) || !empty($s['custom_item_name'])));
            @endphp
            <span class="text-xs text-slate-400">Configured Components</span>
            <div class="text-2xl font-black text-indigo-500 font-mono mt-1">
                {{ $configuredSlots }} Parts
            </div>
            <div class="text-[11px] text-slate-400 mt-0.5">
                Total Parts Count
            </div>
        </div>

    </div>

    <!-- Component Breakdown Table -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
        <div class="p-4 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between">
            <h2 class="font-bold text-sm text-slate-900 dark:text-white flex items-center gap-2">
                <i data-lucide="cpu" class="w-4 h-4 text-indigo-500"></i>
                <span>Assembled Components & Technical Specifications</span>
            </h2>
            <span class="text-xs text-slate-400 font-mono">{{ count($components) }} Component Slots</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 dark:bg-slate-800/60 text-slate-500 font-bold uppercase tracking-wider border-b border-slate-200 dark:border-slate-800">
                    <tr>
                        <th class="px-4 py-3.5">Component Slot</th>
                        <th class="px-4 py-3.5">Selected Item / Model</th>
                        <th class="px-4 py-3.5">Part SKU / Code</th>
                        <th class="px-4 py-3.5">Official Warranty</th>
                        <th class="px-4 py-3.5 text-center">Qty</th>
                        <th class="px-4 py-3.5 text-right">Unit Price</th>
                        <th class="px-4 py-3.5 text-right">Subtotal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($components as $comp)
                    @php
                        $hasItem = !empty($comp['product_id']) || !empty($comp['custom_item_name']);
                        $sub = (float)($comp['unit_price'] ?? 0) * (int)($comp['quantity'] ?? 1);
                    @endphp
                    <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/40 transition {{ $hasItem ? '' : 'opacity-40' }}">
                        
                        <!-- Slot Label -->
                        <td class="px-4 py-3.5 font-bold text-slate-700 dark:text-slate-300">
                            <div class="flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full {{ $hasItem ? 'bg-emerald-500' : 'bg-slate-400' }}"></span>
                                <span>{{ $comp['slot_name'] ?? 'Component' }}</span>
                            </div>
                        </td>

                        <!-- Component Name -->
                        <td class="px-4 py-3.5">
                            @if($hasItem)
                                <div class="flex items-center gap-3">
                                    @if(!empty($comp['thumbnail']))
                                        <img src="{{ $comp['thumbnail'] }}" class="w-8 h-8 rounded-lg object-cover bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 flex-shrink-0">
                                    @endif
                                    <div>
                                        <div class="font-bold text-slate-900 dark:text-white">
                                            {{ $comp['custom_item_name'] ?? $comp['product_name'] }}
                                        </div>
                                        @if(!empty($comp['product_id']))
                                            <a href="{{ route('admin.products.edit', $comp['product_id']) }}" target="_blank" class="text-[10px] text-indigo-400 hover:underline">
                                                View in Inventory Product Catalog &rarr;
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            @else
                                <span class="text-slate-400 italic">Not Selected / Optional</span>
                            @endif
                        </td>

                        <!-- SKU -->
                        <td class="px-4 py-3.5 font-mono text-slate-500">
                            {{ !empty($comp['sku']) ? $comp['sku'] : '-' }}
                        </td>

                        <!-- Warranty -->
                        <td class="px-4 py-3.5 text-slate-600 dark:text-slate-300">
                            @if($hasItem && !empty($comp['warranty']))
                                <span class="px-2 py-0.5 rounded bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 text-[11px] font-semibold">
                                    {{ $comp['warranty'] }}
                                </span>
                            @else
                                <span class="text-slate-400">-</span>
                            @endif
                        </td>

                        <!-- Quantity -->
                        <td class="px-4 py-3.5 text-center font-mono font-bold">
                            {{ $hasItem ? ($comp['quantity'] ?? 1) : 0 }}
                        </td>

                        <!-- Unit Price -->
                        <td class="px-4 py-3.5 text-right font-mono code-font">
                            ৳{{ number_format((float)($comp['unit_price'] ?? 0), 2) }}
                        </td>

                        <!-- Subtotal -->
                        <td class="px-4 py-3.5 text-right font-mono font-bold code-font text-emerald-500">
                            ৳{{ number_format($sub, 2) }}
                        </td>

                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="p-8 text-center text-slate-400">No components configured.</td>
                    </tr>
                    @endforelse
                </tbody>
                <tfoot class="bg-slate-50 dark:bg-slate-800/80 font-bold border-t border-slate-200 dark:border-slate-800 text-xs">
                    <tr>
                        <td colspan="6" class="px-4 py-3 text-right text-slate-500 uppercase">Regular Components Total:</td>
                        <td class="px-4 py-3 text-right code-font text-slate-900 dark:text-white font-black">
                            ৳{{ number_format($customPcBuild->regular_price, 2) }}
                        </td>
                    </tr>
                    @if($customPcBuild->regular_price > $customPcBuild->final_price)
                    <tr>
                        <td colspan="6" class="px-4 py-2 text-right text-rose-500 uppercase">Package Bundle Discount:</td>
                        <td class="px-4 py-2 text-right code-font text-rose-500 font-black">
                            - ৳{{ number_format($customPcBuild->regular_price - $customPcBuild->final_price, 2) }}
                        </td>
                    </tr>
                    @endif
                    <tr class="text-sm bg-emerald-500/10 dark:bg-emerald-950/30">
                        <td colspan="6" class="px-4 py-3 text-right text-emerald-600 dark:text-emerald-400 uppercase font-black">
                            Final Assembled PC Price:
                        </td>
                        <td class="px-4 py-3 text-right code-font text-emerald-500 text-base font-black">
                            ৳{{ number_format($customPcBuild->final_price, 2) }}
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <!-- Description & Notes Cards -->
    @if($customPcBuild->description || $customPcBuild->notes)
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        @if($customPcBuild->description)
        <div class="bg-white dark:bg-slate-900 rounded-2xl p-5 border border-slate-200 dark:border-slate-800 shadow-sm space-y-2">
            <h3 class="text-xs font-bold text-slate-500 uppercase tracking-wider">Public Description & Highlights</h3>
            <p class="text-xs text-slate-700 dark:text-slate-300 whitespace-pre-line leading-relaxed">{{ $customPcBuild->description }}</p>
        </div>
        @endif

        @if($customPcBuild->notes)
        <div class="bg-white dark:bg-slate-900 rounded-2xl p-5 border border-slate-200 dark:border-slate-800 shadow-sm space-y-2">
            <h3 class="text-xs font-bold text-slate-500 uppercase tracking-wider">Admin Internal Notes & Assembly Specs</h3>
            <p class="text-xs text-slate-700 dark:text-slate-300 whitespace-pre-line leading-relaxed">{{ $customPcBuild->notes }}</p>
        </div>
        @endif
    </div>
    @endif

    <!-- CONVERT TO CUSTOMER ORDER MODAL -->
    <div x-show="convertModalOpen" 
         x-cloak 
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/75 backdrop-blur-sm"
         style="display: none;">
        <div class="bg-white dark:bg-slate-900 rounded-2xl max-w-xl w-full p-6 border border-slate-200 dark:border-slate-800 shadow-2xl space-y-4 max-h-[90vh] overflow-y-auto">
            
            <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-800 pb-3">
                <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                    <i data-lucide="shopping-cart" class="w-4 h-4 text-emerald-500"></i>
                    <span>Convert PC Build to Customer Order / POS Sale</span>
                </h3>
                <button type="button" @click="convertModalOpen = false" class="text-slate-400 hover:text-white text-lg font-bold">&times;</button>
            </div>

            <form method="POST" action="{{ route('admin.custom-pc-builds.convert-order', $customPcBuild->id) }}" class="space-y-4 text-xs">
                @csrf

                <!-- Package Summary Box -->
                <div class="p-3.5 rounded-xl bg-emerald-50 dark:bg-emerald-950/30 border border-emerald-200 dark:border-emerald-900/50 flex items-center justify-between">
                    <div>
                        <div class="font-bold text-xs text-emerald-800 dark:text-emerald-300">{{ $customPcBuild->title }}</div>
                        <div class="text-[11px] text-emerald-600 dark:text-emerald-400 font-mono">{{ $customPcBuild->build_code }} • {{ $configuredSlots }} Component Items</div>
                    </div>
                    <div class="text-right">
                        <div class="text-base font-black text-emerald-600 dark:text-emerald-400 code-font">
                            ৳{{ number_format($customPcBuild->final_price, 2) }}
                        </div>
                    </div>
                </div>

                <!-- Order Type & Customer Selection -->
                <div class="space-y-3" x-data="{ custType: 'walkin' }">
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block font-semibold text-slate-500 mb-1">Order Channel / Type</label>
                            <select name="order_type" class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 outline-none">
                                <option value="pos">Store Counter POS Sale</option>
                                <option value="online">Online / Delivery Order</option>
                            </select>
                        </div>
                        <div>
                            <label class="block font-semibold text-slate-500 mb-1">Customer Selection</label>
                            <select name="customer_type" x-model="custType" class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 outline-none">
                                <option value="walkin">Walk-in Counter Customer</option>
                                <option value="existing">Select Registered Customer</option>
                                <option value="new">Create New Customer</option>
                            </select>
                        </div>
                    </div>

                    <!-- If Existing Customer -->
                    <div x-show="custType === 'existing'" class="space-y-2">
                        <label class="block font-semibold text-slate-500">Choose Customer CRM Record</label>
                        <select name="customer_id" class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 outline-none">
                            <option value="">-- Choose Customer --</option>
                            @foreach($customers as $c)
                                <option value="{{ $c->id }}">{{ $c->name }} ({{ $c->phone }})</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- If New Customer -->
                    <div x-show="custType === 'new'" class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block font-semibold text-slate-500 mb-1">Customer Full Name *</label>
                            <input type="text" name="customer_name" placeholder="e.g. Arif Hossain" class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 outline-none">
                        </div>
                        <div>
                            <label class="block font-semibold text-slate-500 mb-1">Phone Number *</label>
                            <input type="text" name="customer_phone" placeholder="017XXXXXXXX" class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 font-mono outline-none">
                        </div>
                        <div class="sm:col-span-2">
                            <label class="block font-semibold text-slate-500 mb-1">Delivery / Shipping Address</label>
                            <input type="text" name="customer_address" placeholder="Address..." class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 outline-none">
                        </div>
                    </div>
                </div>

                <!-- Payment Details -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div>
                        <label class="block font-semibold text-slate-500 mb-1">Payment Method *</label>
                        <select name="payment_method" class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 outline-none">
                            <option value="pos_cash">Cash in Hand</option>
                            <option value="bkash">bKash Merchant / Personal</option>
                            <option value="nagad">Nagad</option>
                            <option value="bank_transfer">Bank Transfer / Card</option>
                            <option value="cod">Cash on Delivery (COD)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-500 mb-1">Deposit Account</label>
                        <select name="account_id" class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 outline-none">
                            @foreach($accounts as $acc)
                                <option value="{{ $acc->id }}">{{ $acc->name }} (৳{{ number_format($acc->balance, 0) }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-500 mb-1">Paid Amount (৳)</label>
                        <input type="number" name="paid_amount" value="{{ $customPcBuild->final_price }}" min="0" step="0.01" class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 code-font outline-none">
                    </div>
                </div>

                <div class="pt-3 border-t border-slate-200 dark:border-slate-800 flex justify-end gap-2">
                    <button type="button" @click="convertModalOpen = false" class="px-4 py-2 rounded-xl bg-slate-200 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-semibold">
                        Cancel
                    </button>
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white font-bold shadow-md flex items-center gap-1.5">
                        <i data-lucide="check" class="w-4 h-4"></i>
                        <span>Confirm & Generate Order</span>
                    </button>
                </div>
            </form>

        </div>
    </div>

</div>
@endsection
