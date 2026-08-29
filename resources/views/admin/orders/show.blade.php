@extends('layouts.admin')

@section('title', 'Order ' . $order->order_no)
@section('page-title', 'Order Details: ' . $order->order_no)

@section('content')
<div class="space-y-6">

    <!-- Top Action Bar -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl p-4 border border-slate-200 dark:border-slate-800 shadow-sm flex flex-col sm:flex-row items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.orders.index') }}" class="p-2 rounded-xl text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-800 transition">
                <i data-lucide="arrow-left" class="w-5 h-5"></i>
            </a>
            <div>
                <div class="flex items-center gap-2">
                    <h2 class="text-base font-extrabold text-slate-900 dark:text-white code-font">{{ $order->order_no }}</h2>
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold uppercase bg-slate-100 dark:bg-slate-800 text-slate-800 dark:text-slate-300">
                        {{ $order->order_type }}
                    </span>
                </div>
                <div class="text-xs text-slate-500">Placed on {{ $order->created_at->format('d M Y, h:i A') }}</div>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('admin.orders.invoice', $order->id) }}" target="_blank" class="px-3.5 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-white text-xs font-semibold flex items-center gap-1.5 transition">
                <i data-lucide="printer" class="w-4 h-4"></i>
                <span>Print Invoice</span>
            </a>
            <a href="{{ route('admin.orders.packing_slip', $order->id) }}" target="_blank" class="px-3.5 py-2 rounded-xl bg-amber-600 hover:bg-amber-500 text-white text-xs font-semibold flex items-center gap-1.5 transition">
                <i data-lucide="package" class="w-4 h-4"></i>
                <span>Packing Slip</span>
            </a>
        </div>
    </div>

    <!-- Main 2-Column Content -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Left: Items & Financials & Status Timeline (2 Cols) -->
        <div class="lg:col-span-2 space-y-6">

            <!-- Products List Card -->
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
                <div class="p-4 border-b border-slate-200 dark:border-slate-800 font-bold text-sm text-slate-900 dark:text-white flex items-center justify-between">
                    <span>Order Items ({{ $order->items->count() }})</span>
                    <span class="text-xs text-slate-500 font-normal">Pricing & Specifications</span>
                </div>
                <div class="divide-y divide-slate-100 dark:divide-slate-800">
                    @foreach($order->items as $item)
                    <div class="p-4 flex items-center justify-between gap-4">
                        <div class="flex items-center gap-3">
                            <img src="{{ $item->product->thumbnail ?? 'https://images.unsplash.com/photo-1518770660439-4636190af475?w=200' }}" alt="{{ $item->product_name }}" class="w-12 h-12 rounded-xl object-cover border border-slate-200 dark:border-slate-700">
                            <div>
                                <h4 class="text-xs font-bold text-slate-900 dark:text-white">{{ $item->product_name }}</h4>
                                @if($item->variant_name)
                                <div class="text-[11px] text-emerald-600 dark:text-emerald-400 font-medium">{{ $item->variant_name }}</div>
                                @endif
                                <div class="text-[10px] text-slate-400 font-mono">SKU: {{ $item->sku }}</div>
                                @if($item->product && $item->product->pcb_model)
                                <div class="text-[10px] text-slate-500">PCB: {{ $item->product->pcb_model }} | Chip: {{ $item->product->chipset }}</div>
                                @endif
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="text-xs text-slate-500">৳{{ number_format($item->unit_price, 2) }} × {{ $item->quantity }}</div>
                            <div class="text-sm font-bold text-slate-900 dark:text-white code-font">৳{{ number_format($item->subtotal, 2) }}</div>
                        </div>
                    </div>
                    @endforeach
                </div>

                <!-- Financial Calculation Summary -->
                <div class="p-4 bg-slate-50/80 dark:bg-slate-950/60 border-t border-slate-200 dark:border-slate-800 space-y-2 text-xs">
                    <div class="flex justify-between text-slate-500">
                        <span>Items Subtotal:</span>
                        <span class="font-semibold text-slate-800 dark:text-slate-200 code-font">৳{{ number_format($order->subtotal, 2) }}</span>
                    </div>
                    @if($order->discount > 0)
                    <div class="flex justify-between text-emerald-600">
                        <span>Discount @if($order->coupon_code) (Coupon: {{ $order->coupon_code }}) @endif:</span>
                        <span class="font-semibold code-font">-৳{{ number_format($order->discount, 2) }}</span>
                    </div>
                    @endif
                    <div class="flex justify-between text-slate-500">
                        <span>Shipping Delivery Charge:</span>
                        <span class="font-semibold text-slate-800 dark:text-slate-200 code-font">+৳{{ number_format($order->shipping_charge, 2) }}</span>
                    </div>
                    @if($order->tax > 0)
                    <div class="flex justify-between text-slate-500">
                        <span>Tax / VAT:</span>
                        <span class="font-semibold text-slate-800 dark:text-slate-200 code-font">+৳{{ number_format($order->tax, 2) }}</span>
                    </div>
                    @endif
                    <div class="flex justify-between text-base font-black pt-2 border-t border-slate-200 dark:border-slate-800 text-slate-900 dark:text-white">
                        <span>Grand Total:</span>
                        <span class="text-emerald-500 code-font">৳{{ number_format($order->grand_total, 2) }}</span>
                    </div>
                    <div class="flex justify-between text-xs text-slate-500 pt-1">
                        <span>Paid Amount:</span>
                        <span class="font-bold text-slate-800 dark:text-slate-200 code-font">৳{{ number_format($order->paid_amount, 2) }}</span>
                    </div>
                    @if($order->due_amount > 0)
                    <div class="flex justify-between text-xs font-bold text-rose-500">
                        <span>Due Amount (COD):</span>
                        <span class="code-font">৳{{ number_format($order->due_amount, 2) }}</span>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Order Status Pipeline Update Form -->
            <div class="bg-white dark:bg-slate-900 rounded-2xl p-5 border border-slate-200 dark:border-slate-800 shadow-sm space-y-4">
                <h3 class="text-sm font-bold text-slate-900 dark:text-white">Update Status Pipeline</h3>
                
                <form method="POST" action="{{ route('admin.orders.status.update', $order->id) }}" class="space-y-3">
                    @csrf
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 mb-1">Pipeline Status</label>
                            <select name="status" class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-semibold text-slate-900 dark:text-white outline-none">
                                <option value="pending" {{ $order->status === 'pending' ? 'selected' : '' }}>Pending (Awaiting Confirmation)</option>
                                <option value="processing" {{ $order->status === 'processing' ? 'selected' : '' }}>Processing (Packaging & Quality Check)</option>
                                <option value="on_the_way" {{ $order->status === 'on_the_way' ? 'selected' : '' }}>On the Way</option>
                                <option value="in_courier" {{ $order->status === 'in_courier' ? 'selected' : '' }}>In Courier (Dispatched)</option>
                                <option value="completed" {{ $order->status === 'completed' ? 'selected' : '' }}>Completed (Delivered & Paid)</option>
                                <option value="cancelled" {{ $order->status === 'cancelled' ? 'selected' : '' }}>Cancelled (Restore Inventory)</option>
                                <option value="returned" {{ $order->status === 'returned' ? 'selected' : '' }}>Returned (Restore Inventory)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 mb-1">Status Note (Optional)</label>
                            <input type="text" name="note" placeholder="e.g. Verified customer by phone" class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs outline-none">
                        </div>
                    </div>
                    <button type="submit" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs rounded-xl shadow-md transition">
                        Update Status & Log
                    </button>
                </form>
            </div>

            <!-- Status History Timeline -->
            <div class="bg-white dark:bg-slate-900 rounded-2xl p-5 border border-slate-200 dark:border-slate-800 shadow-sm space-y-3">
                <h3 class="text-sm font-bold text-slate-900 dark:text-white">Status Activity Logs</h3>
                <div class="space-y-3 pl-2 border-l-2 border-slate-200 dark:border-slate-800">
                    @forelse($order->statusLogs as $log)
                    <div class="relative pl-4">
                        <div class="w-2.5 h-2.5 rounded-full bg-emerald-500 absolute -left-[1.35rem] top-1"></div>
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-bold text-slate-900 dark:text-white uppercase">{{ str_replace('_', ' ', $log->to_status) }}</span>
                            <span class="text-[10px] text-slate-400 font-mono">{{ $log->created_at->format('d M, h:i A') }}</span>
                        </div>
                        @if($log->note)
                        <div class="text-xs text-slate-500 mt-0.5">{{ $log->note }}</div>
                        @endif
                    </div>
                    @empty
                    <div class="text-xs text-slate-400">No activity recorded yet.</div>
                    @endforelse
                </div>
            </div>

        </div>

        <!-- Right: Customer Info, Fraud Scoring & Courier Booking (1 Col) -->
        <div class="space-y-6">

            <!-- Fraud & Risk Evaluation Score Card -->
            <div class="bg-white dark:bg-slate-900 rounded-2xl p-5 border border-slate-200 dark:border-slate-800 shadow-sm space-y-3">
                <div class="flex items-center justify-between">
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white">Fraud & Risk Check</h3>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold uppercase {{ $order->is_fraud_suspect ? 'bg-rose-500/10 text-rose-500' : 'bg-emerald-500/10 text-emerald-500' }}">
                        {{ $order->is_fraud_suspect ? 'Suspicious' : 'Safe Order' }}
                    </span>
                </div>
                
                <div class="p-3 rounded-xl {{ $order->is_fraud_suspect ? 'bg-rose-50 dark:bg-rose-950/30 border border-rose-200 dark:border-rose-800' : 'bg-slate-50 dark:bg-slate-800/40' }} space-y-1.5 text-xs">
                    <div class="flex justify-between">
                        <span class="text-slate-500">Risk Score:</span>
                        <span class="font-bold code-font {{ $order->fraud_score > 40 ? 'text-rose-500' : 'text-emerald-500' }}">{{ $order->fraud_score }} / 100</span>
                    </div>
                    @if($order->fraud_reason)
                    <div class="text-[11px] text-rose-600 dark:text-rose-400 font-medium">
                        Reason: {{ $order->fraud_reason }}
                    </div>
                    @endif
                    <div class="flex justify-between text-slate-400 text-[10px]">
                        <span>IP Address:</span>
                        <span class="font-mono">{{ $order->ip_address ?? '127.0.0.1' }}</span>
                    </div>
                </div>
            </div>

            <!-- Customer & Shipping Profile -->
            <div class="bg-white dark:bg-slate-900 rounded-2xl p-5 border border-slate-200 dark:border-slate-800 shadow-sm space-y-3">
                <h3 class="text-sm font-bold text-slate-900 dark:text-white">Customer & Shipping Address</h3>
                
                <div class="space-y-2 text-xs">
                    <div>
                        <div class="font-bold text-slate-900 dark:text-white text-sm">{{ $order->shipping_name }}</div>
                        <div class="text-emerald-600 dark:text-emerald-400 font-mono font-bold">{{ $order->shipping_phone }}</div>
                        @if($order->shipping_email)
                        <div class="text-slate-400">{{ $order->shipping_email }}</div>
                        @endif
                    </div>
                    <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-800/40 text-slate-600 dark:text-slate-300">
                        <div class="font-semibold text-slate-800 dark:text-slate-200">Delivery Address:</div>
                        <div>{{ $order->shipping_address }}</div>
                        <div class="font-medium text-emerald-600 dark:text-emerald-400 mt-1">City: {{ $order->shipping_city }}</div>
                    </div>
                </div>

                @if($order->customer)
                <div class="pt-3 border-t border-slate-100 dark:border-slate-800 text-[11px] space-y-1">
                    <div class="font-bold text-slate-700 dark:text-slate-300">CRM History:</div>
                    <div class="flex justify-between text-slate-500">
                        <span>Total Orders:</span>
                        <span class="font-bold text-slate-800 dark:text-slate-200">{{ $order->customer->total_orders_count }}</span>
                    </div>
                    <div class="flex justify-between text-slate-500">
                        <span>Delivery Rate:</span>
                        <span class="font-bold text-emerald-500">{{ $order->customer->delivery_success_rate }}%</span>
                    </div>
                </div>
                @endif
            </div>

            <!-- Auto Courier Consignment Booking Widget -->
            <div class="bg-white dark:bg-slate-900 rounded-2xl p-5 border border-slate-200 dark:border-slate-800 shadow-sm space-y-3">
                <div class="flex items-center justify-between">
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white">Courier API Booking</h3>
                    <i data-lucide="truck" class="w-4 h-4 text-emerald-500"></i>
                </div>

                @if($order->courier_tracking_id)
                <div class="p-3 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-xs space-y-1">
                    <div class="font-bold text-emerald-700 dark:text-emerald-300 flex items-center justify-between">
                        <span>{{ $order->courier_name }}</span>
                        <span class="text-[10px] uppercase font-mono">{{ $order->courier_status ?? 'Booked' }}</span>
                    </div>
                    <div class="text-[11px] text-slate-600 dark:text-slate-400">Tracking Code:</div>
                    <div class="font-mono font-bold text-slate-900 dark:text-white">{{ $order->courier_tracking_id }}</div>
                </div>
                @else
                <form method="POST" action="{{ route('admin.orders.courier.book', $order->id) }}" class="space-y-3">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 mb-1">Select Courier Partner</label>
                        <select name="courier_name" class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-semibold outline-none">
                            <option value="Steadfast">Steadfast Courier API</option>
                            <option value="Pathao">Pathao Courier API</option>
                            <option value="RedX">RedX Express API</option>
                        </select>
                    </div>
                    <button type="submit" class="w-full py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs shadow-md transition flex items-center justify-center gap-1.5">
                        <i data-lucide="send" class="w-4 h-4"></i>
                        <span>Auto Book Consignment</span>
                    </button>
                </form>
                @endif
            </div>

        </div>

    </div>

</div>
@endsection
