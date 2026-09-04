@extends('layouts.admin')

@section('title', 'Delivery & Shipping Management')
@section('page-title', 'Delivery & Logistics Control Hub')

@section('content')
<div class="space-y-6" x-data="{
    createModalOpen: false,
    editModalOpen: false,
    editData: {
        id: '',
        name: '',
        code: '',
        charge: '',
        estimated_days: '',
        min_order_for_free_delivery: '',
        description: '',
        sort_order: 0,
        is_active: true,
        is_default: false
    },
    openEditModal(item) {
        this.editData = {
            id: item.id,
            name: item.name,
            code: item.code,
            charge: item.charge,
            estimated_days: item.estimated_days,
            min_order_for_free_delivery: item.min_order_for_free_delivery || '',
            description: item.description || '',
            sort_order: item.sort_order || 0,
            is_active: Boolean(item.is_active),
            is_default: Boolean(item.is_default)
        };
        this.editModalOpen = true;
    }
}">

    <!-- Top Action Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white dark:bg-slate-900 p-5 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-sm">
        <div class="space-y-1">
            <h2 class="text-xl font-black text-slate-900 dark:text-white flex items-center gap-2">
                <i data-lucide="truck" class="w-6 h-6 text-emerald-500"></i>
                <span>Delivery Zones & Shipping Charges</span>
            </h2>
            <p class="text-xs text-slate-500 dark:text-slate-400">
                Configure delivery rates, coverage areas, free delivery order thresholds, and nationwide courier options.
            </p>
        </div>

        <div class="flex items-center gap-3">
            <button 
                type="button" 
                @click="createModalOpen = true" 
                class="inline-flex items-center gap-2 px-4 py-2.5 rounded-2xl bg-gradient-to-r from-emerald-600 to-teal-500 hover:from-emerald-500 hover:to-teal-400 text-white text-xs font-black shadow-lg shadow-emerald-600/20 transition transform active:scale-95">
                <i data-lucide="plus-circle" class="w-4 h-4"></i>
                <span>Add Delivery Zone</span>
            </button>
        </div>
    </div>

    <!-- Alert Notifications -->
    @if(session('success'))
    <div class="p-4 rounded-2xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-600 dark:text-emerald-400 text-xs font-bold flex items-center justify-between shadow-sm">
        <div class="flex items-center gap-2.5">
            <i data-lucide="check-circle" class="w-5 h-5 flex-shrink-0"></i>
            <span>{{ session('success') }}</span>
        </div>
        <button type="button" onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700 font-bold text-base">&times;</button>
    </div>
    @endif

    @if(session('error'))
    <div class="p-4 rounded-2xl bg-rose-500/10 border border-rose-500/30 text-rose-600 dark:text-rose-400 text-xs font-bold flex items-center justify-between shadow-sm">
        <div class="flex items-center gap-2.5">
            <i data-lucide="alert-triangle" class="w-5 h-5 flex-shrink-0"></i>
            <span>{{ session('error') }}</span>
        </div>
        <button type="button" onclick="this.parentElement.remove()" class="text-rose-500 hover:text-rose-700 font-bold text-base">&times;</button>
    </div>
    @endif

    @if($errors->any())
    <div class="p-4 rounded-2xl bg-rose-500/10 border border-rose-500/30 text-rose-600 dark:text-rose-400 text-xs space-y-1 shadow-sm">
        @foreach($errors->all() as $err)
            <div class="flex items-center gap-2 font-medium">
                <i data-lucide="alert-circle" class="w-4 h-4 text-rose-500 flex-shrink-0"></i>
                <span>{{ $err }}</span>
            </div>
        @endforeach
    </div>
    @endif

    <!-- 4 Summary Metrics Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- 1. Total Zones -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl p-4 border border-slate-200 dark:border-slate-800 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Total Delivery Zones</p>
                <h3 class="text-2xl font-black text-slate-900 dark:text-white mt-1">{{ $totalZones }}</h3>
                <p class="text-[10px] text-slate-400 mt-0.5">{{ $activeZones }} currently active</p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-emerald-500/10 text-emerald-500 flex items-center justify-center">
                <i data-lucide="map-pinned" class="w-6 h-6"></i>
            </div>
        </div>

        <!-- 2. Active Shipping Options -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl p-4 border border-slate-200 dark:border-slate-800 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Active Methods</p>
                <h3 class="text-2xl font-black text-emerald-500 mt-1">{{ $activeZones }}</h3>
                <p class="text-[10px] text-slate-400 mt-0.5">Live on storefront checkout</p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-blue-500/10 text-blue-500 flex items-center justify-center">
                <i data-lucide="check-circle-2" class="w-6 h-6"></i>
            </div>
        </div>

        <!-- 3. Rates Range -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl p-4 border border-slate-200 dark:border-slate-800 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Charge Range</p>
                <h3 class="text-xl font-black text-slate-900 dark:text-white mt-1 code-font">
                    ৳{{ number_format($minCharge, 0) }} - ৳{{ number_format($maxCharge, 0) }}
                </h3>
                <p class="text-[10px] text-slate-400 mt-0.5">Standard zone rates</p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-amber-500/10 text-amber-500 flex items-center justify-center">
                <i data-lucide="badge-dollar-sign" class="w-6 h-6"></i>
            </div>
        </div>

        <!-- 4. Courier Integration Status -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl p-4 border border-slate-200 dark:border-slate-800 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Courier API Hub</p>
                <h3 class="text-sm font-black text-slate-900 dark:text-white mt-1">
                    {{ $courierIntegrations->where('is_active', true)->count() }} Connected
                </h3>
                <p class="text-[10px] text-slate-400 mt-0.5">Steadfast / Pathao / RedX</p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-purple-500/10 text-purple-500 flex items-center justify-center">
                <i data-lucide="send" class="w-6 h-6"></i>
            </div>
        </div>
    </div>

    <!-- Main Delivery Zones List & Table -->
    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
        <div class="p-5 border-b border-slate-200 dark:border-slate-800 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                    <i data-lucide="layers" class="w-4 h-4 text-emerald-500"></i>
                    <span>Configured Delivery Zones & Charges</span>
                </h3>
                <p class="text-xs text-slate-400">These options are dynamically presented to customers at checkout.</p>
            </div>
            <span class="text-xs font-semibold px-3 py-1 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300">
                {{ $deliveryMethods->count() }} Zone Profiles
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 dark:bg-slate-950/60 text-slate-500 dark:text-slate-400 uppercase font-semibold text-[10px] border-b border-slate-200 dark:border-slate-800">
                    <tr>
                        <th class="px-5 py-3.5">Zone & Method Name</th>
                        <th class="px-5 py-3.5">Identifier Code</th>
                        <th class="px-5 py-3.5 text-right">Delivery Charge</th>
                        <th class="px-5 py-3.5">Estimated Time</th>
                        <th class="px-5 py-3.5">Free Shipping Rule</th>
                        <th class="px-5 py-3.5 text-center">Status</th>
                        <th class="px-5 py-3.5 text-center">Default</th>
                        <th class="px-5 py-3.5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800 font-medium">
                    @forelse($deliveryMethods as $method)
                    <tr class="hover:bg-slate-50/70 dark:hover:bg-slate-800/40 transition">
                        
                        <!-- Zone Name & Description -->
                        <td class="px-5 py-4">
                            <div class="flex items-start gap-2.5">
                                <div class="w-8 h-8 rounded-xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-600 dark:text-slate-300 shrink-0 mt-0.5">
                                    <i data-lucide="map-pin" class="w-4 h-4 text-emerald-500"></i>
                                </div>
                                <div>
                                    <div class="font-extrabold text-slate-900 dark:text-white flex items-center gap-1.5">
                                        <span>{{ $method->name }}</span>
                                        @if($method->is_default)
                                        <span class="px-1.5 py-0.5 rounded-md text-[9px] font-black uppercase bg-emerald-500/10 text-emerald-500 border border-emerald-500/20">Default</span>
                                        @endif
                                    </div>
                                    @if($method->description)
                                    <p class="text-[11px] text-slate-400 max-w-xs mt-0.5 line-clamp-1">{{ $method->description }}</p>
                                    @endif
                                </div>
                            </div>
                        </td>

                        <!-- Code -->
                        <td class="px-5 py-4 font-mono text-slate-500 text-[11px]">
                            <span class="px-2 py-1 rounded bg-slate-100 dark:bg-slate-800">{{ $method->code }}</span>
                        </td>

                        <!-- Charge -->
                        <td class="px-5 py-4 text-right">
                            @if((float)$method->charge == 0)
                            <span class="px-2.5 py-1 rounded-full text-xs font-black bg-emerald-500/10 text-emerald-500 border border-emerald-500/20">
                                FREE
                            </span>
                            @else
                            <span class="font-black text-slate-900 dark:text-white text-sm code-font">
                                ৳{{ number_format($method->charge, 2) }}
                            </span>
                            @endif
                        </td>

                        <!-- Estimated Days -->
                        <td class="px-5 py-4">
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-semibold text-[11px]">
                                <i data-lucide="clock" class="w-3.5 h-3.5 text-amber-500"></i>
                                <span>{{ $method->estimated_days }}</span>
                            </span>
                        </td>

                        <!-- Free Shipping Threshold -->
                        <td class="px-5 py-4 text-slate-600 dark:text-slate-300">
                            @if($method->min_order_for_free_delivery)
                            <div class="text-[11px]">
                                <span class="text-emerald-500 font-bold">Free on orders &ge;</span>
                                <span class="font-black font-mono">৳{{ number_format($method->min_order_for_free_delivery, 0) }}</span>
                            </div>
                            @else
                            <span class="text-slate-400 text-[11px]">Standard rate</span>
                            @endif
                        </td>

                        <!-- Active Toggle -->
                        <td class="px-5 py-4 text-center">
                            <form method="POST" action="{{ route('admin.delivery.toggle', $method->id) }}">
                                @csrf
                                <button 
                                    type="submit" 
                                    class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-bold uppercase transition {{ $method->is_active ? 'bg-emerald-500/15 text-emerald-600 dark:text-emerald-400 border border-emerald-500/30' : 'bg-slate-200 dark:bg-slate-800 text-slate-500 border border-slate-300 dark:border-slate-700' }}"
                                    title="Click to toggle status">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $method->is_active ? 'bg-emerald-500' : 'bg-slate-400' }}"></span>
                                    <span>{{ $method->is_active ? 'Active' : 'Inactive' }}</span>
                                </button>
                            </form>
                        </td>

                        <!-- Default Selector -->
                        <td class="px-5 py-4 text-center">
                            @if($method->is_default)
                            <span class="inline-flex items-center gap-1 text-emerald-500 font-bold text-xs" title="Currently Default Zone">
                                <i data-lucide="check" class="w-4 h-4"></i>
                            </span>
                            @else
                            <form method="POST" action="{{ route('admin.delivery.default', $method->id) }}">
                                @csrf
                                <button type="submit" class="text-[11px] text-slate-400 hover:text-emerald-500 hover:underline transition">
                                    Set Default
                                </button>
                            </form>
                            @endif
                        </td>

                        <!-- Actions (Edit & Delete) -->
                        <td class="px-5 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <button 
                                    type="button" 
                                    @click="openEditModal(@js($method))"
                                    class="p-2 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 transition"
                                    title="Edit Delivery Method">
                                    <i data-lucide="edit-3" class="w-3.5 h-3.5"></i>
                                </button>

                                <form method="POST" action="{{ route('admin.delivery.destroy', $method->id) }}" onsubmit="return confirm('Are you sure you want to delete the delivery zone \'{{ $method->name }}\'?')">
                                    @csrf
                                    @method('DELETE')
                                    <button 
                                        type="submit" 
                                        class="p-2 rounded-xl bg-rose-50 dark:bg-rose-500/10 hover:bg-rose-100 dark:hover:bg-rose-500/20 text-rose-600 transition"
                                        title="Delete Delivery Method">
                                        <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                    </button>
                                </form>
                            </div>
                        </td>

                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-12 text-center text-slate-400">
                            <i data-lucide="truck" class="w-10 h-10 mx-auto mb-2 opacity-40"></i>
                            <p class="font-bold text-sm">No delivery zones configured yet.</p>
                            <p class="text-xs mt-1">Click "Add Delivery Zone" above to create your first shipping option.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Global Delivery Rules & Courier Preferences -->
    <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 border border-slate-200 dark:border-slate-800 shadow-sm space-y-5">
        <div class="border-b border-slate-100 dark:border-slate-800 pb-3 flex items-center justify-between">
            <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                <i data-lucide="sliders" class="w-4 h-4 text-emerald-500"></i>
                <span>Global Storewide Delivery Rules & Logistics Settings</span>
            </h3>
            <span class="text-[11px] text-slate-400">Settings DB Sync</span>
        </div>

        <form method="POST" action="{{ route('admin.delivery.global-rules') }}" class="space-y-4">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                
                <!-- Global Free Shipping Threshold -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                        Global Free Delivery Threshold (৳)
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400 font-bold">৳</span>
                        <input 
                            type="number" 
                            step="0.01" 
                            name="free_shipping_threshold" 
                            value="{{ old('free_shipping_threshold', $globalSettings['free_shipping_threshold'] ?? '') }}"
                            placeholder="e.g. 3000.00 (leave empty to disable)" 
                            class="w-full pl-8 pr-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-semibold code-font outline-none focus:ring-2 focus:ring-emerald-500">
                    </div>
                    <p class="text-[10px] text-slate-400 mt-1">If a cart subtotal exceeds this amount, delivery becomes free nationwide.</p>
                </div>

                <!-- Preferred Default Courier -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                        Preferred Default Dispatch Courier Partner
                    </label>
                    <select 
                        name="default_courier_partner" 
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-semibold outline-none focus:ring-2 focus:ring-emerald-500">
                        <option value="steadfast" {{ ($globalSettings['default_courier_partner'] ?? '') === 'steadfast' ? 'selected' : '' }}>Steadfast Courier (Recommended)</option>
                        <option value="pathao" {{ ($globalSettings['default_courier_partner'] ?? '') === 'pathao' ? 'selected' : '' }}>Pathao Logistics</option>
                        <option value="redx" {{ ($globalSettings['default_courier_partner'] ?? '') === 'redx' ? 'selected' : '' }}>RedX Delivery</option>
                        <option value="in_house" {{ ($globalSettings['default_courier_partner'] ?? '') === 'in_house' ? 'selected' : '' }}>In-House Delivery Fleet</option>
                    </select>
                    <p class="text-[10px] text-slate-400 mt-1">Automated booking API provider for order parcel dispatch.</p>
                </div>

            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Verified Courier Partners (Footer Text) -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                        Footer & Checkout Courier Partners Text
                    </label>
                    <input 
                        type="text" 
                        name="footer_courier_partners" 
                        value="{{ old('footer_courier_partners', $globalSettings['footer_courier_partners'] ?? 'Steadfast • Pathao Courier • RedX') }}"
                        placeholder="e.g. Steadfast • Pathao Courier • RedX" 
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-semibold outline-none focus:ring-2 focus:ring-emerald-500">
                </div>

                <!-- Delivery Notice for Checkout -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                        Checkout Delivery Information Notice
                    </label>
                    <input 
                        type="text" 
                        name="delivery_information_notice" 
                        value="{{ old('delivery_information_notice', $globalSettings['delivery_information_notice'] ?? 'Fast nationwide cash on delivery with real-time SMS tracking.') }}"
                        placeholder="e.g. Delivery within 24-48 hours in Dhaka" 
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-semibold outline-none focus:ring-2 focus:ring-emerald-500">
                </div>
            </div>

            <div class="flex justify-end pt-2">
                <button type="submit" class="px-5 py-2.5 bg-slate-900 dark:bg-emerald-600 hover:bg-slate-800 dark:hover:bg-emerald-500 text-white rounded-xl text-xs font-black shadow-md transition flex items-center gap-2">
                    <i data-lucide="save" class="w-4 h-4"></i>
                    <span>Save Global Delivery Rules</span>
                </button>
            </div>
        </form>
    </div>

    <!-- ================= MODAL: CREATE DELIVERY ZONE ================= -->
    <div 
        x-show="createModalOpen" 
        x-cloak 
        class="fixed inset-0 z-50 overflow-y-auto bg-black/60 backdrop-blur-sm flex items-center justify-center p-4">
        
        <div 
            @click.away="createModalOpen = false"
            class="bg-white dark:bg-slate-900 rounded-3xl max-w-lg w-full p-6 border border-slate-200 dark:border-slate-800 shadow-2xl space-y-5"
            x-transition>
            
            <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
                <h3 class="text-sm font-extrabold text-slate-900 dark:text-white flex items-center gap-2">
                    <i data-lucide="plus-circle" class="w-5 h-5 text-emerald-500"></i>
                    <span>Add New Delivery Zone / Method</span>
                </h3>
                <button @click="createModalOpen = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 text-lg font-bold">&times;</button>
            </div>

            <form method="POST" action="{{ route('admin.delivery.store') }}" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Zone / Method Name *</label>
                    <input 
                        type="text" 
                        name="name" 
                        required 
                        placeholder="e.g. Chittagong Express Delivery" 
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-semibold outline-none focus:ring-2 focus:ring-emerald-500">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Identifier Code (Optional)</label>
                        <input 
                            type="text" 
                            name="code" 
                            placeholder="e.g. chittagong_express" 
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-mono font-semibold outline-none focus:ring-2 focus:ring-emerald-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Delivery Charge (৳) *</label>
                        <input 
                            type="number" 
                            step="0.01" 
                            name="charge" 
                            required 
                            placeholder="120.00" 
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-mono font-black outline-none focus:ring-2 focus:ring-emerald-500">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Estimated Timeline *</label>
                        <input 
                            type="text" 
                            name="estimated_days" 
                            required 
                            placeholder="e.g. 1-2 Days" 
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-semibold outline-none focus:ring-2 focus:ring-emerald-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Free on Orders &ge; (৳)</label>
                        <input 
                            type="number" 
                            step="0.01" 
                            name="min_order_for_free_delivery" 
                            placeholder="e.g. 2500.00 (Optional)" 
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-mono outline-none focus:ring-2 focus:ring-emerald-500">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Description / Route Coverage</label>
                    <textarea 
                        name="description" 
                        rows="2" 
                        placeholder="Details on coverage areas and courier specifics..." 
                        class="w-full px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs outline-none focus:ring-2 focus:ring-emerald-500"></textarea>
                </div>

                <div class="grid grid-cols-2 gap-3 pt-1">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Display Sort Order</label>
                        <input 
                            type="number" 
                            name="sort_order" 
                            value="0" 
                            class="w-full px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs outline-none">
                    </div>
                    <div class="space-y-2 pt-4">
                        <label class="flex items-center gap-2 text-xs font-semibold text-slate-700 dark:text-slate-300 cursor-pointer">
                            <input type="checkbox" name="is_active" value="1" checked class="rounded text-emerald-500 focus:ring-emerald-500">
                            <span>Active on Storefront</span>
                        </label>
                        <label class="flex items-center gap-2 text-xs font-semibold text-slate-700 dark:text-slate-300 cursor-pointer">
                            <input type="checkbox" name="is_default" value="1" class="rounded text-emerald-500 focus:ring-emerald-500">
                            <span>Set as Default Zone</span>
                        </label>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100 dark:border-slate-800">
                    <button 
                        type="button" 
                        @click="createModalOpen = false" 
                        class="px-4 py-2 text-xs font-bold rounded-xl text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-800 transition">
                        Cancel
                    </button>
                    <button 
                        type="submit" 
                        class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-black text-xs shadow-md transition">
                        Create Delivery Zone
                    </button>
                </div>
            </form>

        </div>
    </div>

    <!-- ================= MODAL: EDIT DELIVERY ZONE ================= -->
    <div 
        x-show="editModalOpen" 
        x-cloak 
        class="fixed inset-0 z-50 overflow-y-auto bg-black/60 backdrop-blur-sm flex items-center justify-center p-4">
        
        <div 
            @click.away="editModalOpen = false"
            class="bg-white dark:bg-slate-900 rounded-3xl max-w-lg w-full p-6 border border-slate-200 dark:border-slate-800 shadow-2xl space-y-5"
            x-transition>
            
            <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
                <h3 class="text-sm font-extrabold text-slate-900 dark:text-white flex items-center gap-2">
                    <i data-lucide="edit-3" class="w-5 h-5 text-emerald-500"></i>
                    <span>Edit Delivery Zone & Charges</span>
                </h3>
                <button @click="editModalOpen = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 text-lg font-bold">&times;</button>
            </div>

            <form 
                method="POST" 
                :action="'{{ url('/admin/delivery') }}/' + editData.id" 
                class="space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Zone / Method Name *</label>
                    <input 
                        type="text" 
                        name="name" 
                        x-model="editData.name" 
                        required 
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-semibold outline-none focus:ring-2 focus:ring-emerald-500">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Identifier Code *</label>
                        <input 
                            type="text" 
                            name="code" 
                            x-model="editData.code" 
                            required 
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-mono font-semibold outline-none focus:ring-2 focus:ring-emerald-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Delivery Charge (৳) *</label>
                        <input 
                            type="number" 
                            step="0.01" 
                            name="charge" 
                            x-model="editData.charge" 
                            required 
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-mono font-black outline-none focus:ring-2 focus:ring-emerald-500">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Estimated Timeline *</label>
                        <input 
                            type="text" 
                            name="estimated_days" 
                            x-model="editData.estimated_days" 
                            required 
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-semibold outline-none focus:ring-2 focus:ring-emerald-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Free on Orders &ge; (৳)</label>
                        <input 
                            type="number" 
                            step="0.01" 
                            name="min_order_for_free_delivery" 
                            x-model="editData.min_order_for_free_delivery" 
                            placeholder="Optional" 
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-mono outline-none focus:ring-2 focus:ring-emerald-500">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Description / Route Coverage</label>
                    <textarea 
                        name="description" 
                        x-model="editData.description" 
                        rows="2" 
                        class="w-full px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs outline-none focus:ring-2 focus:ring-emerald-500"></textarea>
                </div>

                <div class="grid grid-cols-2 gap-3 pt-1">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Display Sort Order</label>
                        <input 
                            type="number" 
                            name="sort_order" 
                            x-model="editData.sort_order" 
                            class="w-full px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs outline-none">
                    </div>
                    <div class="space-y-2 pt-4">
                        <label class="flex items-center gap-2 text-xs font-semibold text-slate-700 dark:text-slate-300 cursor-pointer">
                            <input type="checkbox" name="is_active" value="1" x-model="editData.is_active" class="rounded text-emerald-500 focus:ring-emerald-500">
                            <span>Active on Storefront</span>
                        </label>
                        <label class="flex items-center gap-2 text-xs font-semibold text-slate-700 dark:text-slate-300 cursor-pointer">
                            <input type="checkbox" name="is_default" value="1" x-model="editData.is_default" class="rounded text-emerald-500 focus:ring-emerald-500">
                            <span>Set as Default Zone</span>
                        </label>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100 dark:border-slate-800">
                    <button 
                        type="button" 
                        @click="editModalOpen = false" 
                        class="px-4 py-2 text-xs font-bold rounded-xl text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-800 transition">
                        Cancel
                    </button>
                    <button 
                        type="submit" 
                        class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-black text-xs shadow-md transition">
                        Update Delivery Zone
                    </button>
                </div>
            </form>

        </div>
    </div>

</div>
@endsection
