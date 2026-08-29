@extends('layouts.customer')

@section('title', 'My Orders History - DREAMERS PCB')

@section('customer_content')
<div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-sm space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b pb-4">
        <div>
            <h2 class="text-base font-black text-slate-900 flex items-center gap-2">
                <i data-lucide="package" class="w-5 h-5 text-daraz-orange"></i>
                <span>My Order History</span>
            </h2>
            <p class="text-xs text-slate-400">Track and review all your electronics component orders</p>
        </div>

        <!-- Filter Status Pills -->
        <div class="flex items-center gap-1.5 overflow-x-auto pb-1 text-xs">
            <a href="{{ route('customer.orders') }}" class="px-3 py-1.5 rounded-xl font-bold transition {{ $status === 'all' ? 'bg-slate-900 text-white' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
                All ({{ $orders->total() }})
            </a>
            <a href="{{ route('customer.orders', ['status' => 'pending']) }}" class="px-3 py-1.5 rounded-xl font-bold transition {{ $status === 'pending' ? 'bg-slate-900 text-white' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
                Pending
            </a>
            <a href="{{ route('customer.orders', ['status' => 'in_transit']) }}" class="px-3 py-1.5 rounded-xl font-bold transition {{ $status === 'in_transit' ? 'bg-slate-900 text-white' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
                In-Transit
            </a>
            <a href="{{ route('customer.orders', ['status' => 'completed']) }}" class="px-3 py-1.5 rounded-xl font-bold transition {{ $status === 'completed' ? 'bg-slate-900 text-white' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
                Delivered
            </a>
        </div>
    </div>

    <!-- Orders Table -->
    @if($orders->count() > 0)
    <div class="space-y-4">
        @foreach($orders as $order)
        <div class="p-4 sm:p-5 rounded-2xl border border-slate-200 bg-slate-50/50 space-y-3">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-slate-200/80 pb-3 text-xs">
                <div class="flex items-center gap-3">
                    <span class="font-black text-slate-900 code-font text-sm">{{ $order->order_no }}</span>
                    <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase {{ in_array($order->status, ['delivered', 'completed']) ? 'bg-emerald-100 text-emerald-800' : 'bg-daraz-light text-daraz-orange' }}">
                        {{ str_replace('_', ' ', $order->status) }}
                    </span>
                </div>

                <div class="text-slate-400 text-[11px]">
                    Placed on: <strong class="text-slate-700">{{ $order->created_at->format('d M Y, h:i A') }}</strong>
                </div>
            </div>

            <!-- Items summary -->
            <div class="space-y-2 text-xs">
                @foreach($order->items as $item)
                <div class="flex items-center justify-between gap-3">
                    <div class="flex items-center gap-2 truncate">
                        <span class="font-bold text-slate-800">{{ $item->product_name }}</span>
                        @if($item->variant_name)
                        <span class="text-[10px] text-emerald-600 font-semibold">({{ $item->variant_name }})</span>
                        @endif
                        <span class="text-slate-400 text-[11px] font-mono">x{{ $item->quantity }}</span>
                    </div>
                    <span class="font-bold text-slate-900 code-font whitespace-nowrap">৳{{ number_format($item->subtotal, 2) }}</span>
                </div>
                @endforeach
            </div>

            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pt-3 border-t border-slate-200/80">
                <div class="text-xs">
                    <span class="text-slate-500">Total COD Amount:</span>
                    <span class="font-black text-daraz-orange code-font text-sm ml-1">৳{{ number_format($order->grand_total, 2) }}</span>
                    <span class="text-slate-400 text-[10px] ml-1">({{ ucfirst($order->payment_method) }})</span>
                </div>

                <div class="flex items-center gap-2">
                    <a href="{{ route('order.track', ['order_no' => $order->order_no]) }}" class="px-3 py-1.5 rounded-xl bg-slate-200 hover:bg-slate-300 text-slate-800 font-bold text-xs flex items-center gap-1 transition">
                        <i data-lucide="map-pin" class="w-3.5 h-3.5 text-daraz-orange"></i>
                        <span>Track</span>
                    </a>

                    <a href="{{ route('customer.orders.show', $order->order_no) }}" class="px-4 py-1.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs transition">
                        Order Details &rarr;
                    </a>
                </div>
            </div>
        </div>
        @endforeach

        <!-- Pagination -->
        <div class="pt-4 flex justify-center">
            {{ $orders->links() }}
        </div>
    </div>
    @else
    <div class="py-16 text-center text-slate-400 space-y-2">
        <i data-lucide="package-search" class="w-12 h-12 mx-auto text-slate-300"></i>
        <h4 class="text-sm font-bold text-slate-700">No orders found</h4>
        <p class="text-xs text-slate-400">There are no orders matching the selected status filter.</p>
    </div>
    @endif

</div>
@endsection
