@extends('layouts.admin')

@section('title', 'Notifications Center')
@section('page-title', 'Notifications & Activity Hub')

@section('content')
<div class="space-y-6">

    <!-- Top Action Bar & Header Stats -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white dark:bg-slate-900 p-5 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm">
        <div class="flex items-center gap-3">
            <div class="w-12 h-12 rounded-2xl bg-emerald-500/10 text-emerald-500 flex items-center justify-center flex-shrink-0">
                <i data-lucide="bell-ring" class="w-6 h-6"></i>
            </div>
            <div>
                <h2 class="text-base sm:text-lg font-bold text-slate-900 dark:text-white flex items-center gap-2">
                    <span>Notifications Center</span>
                    @if($unreadCount > 0)
                        <span class="px-2 py-0.5 rounded-full bg-rose-500 text-white text-xs font-bold">{{ $unreadCount }} Unread</span>
                    @endif
                </h2>
                <p class="text-xs text-slate-500">Live operational events for new orders, courier handovers, deliveries, and cancellations</p>
            </div>
        </div>

        <div class="flex items-center gap-2.5 flex-wrap">
            @if($unreadCount > 0)
            <form method="POST" action="{{ route('admin.notifications.mark_all_read') }}">
                @csrf
                <button type="submit" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-emerald-500/10 hover:bg-emerald-500 text-emerald-600 hover:text-slate-950 font-semibold text-xs transition">
                    <i data-lucide="check-check" class="w-4 h-4"></i>
                    <span>Mark All as Read</span>
                </button>
            </form>
            @endif

            <form method="POST" action="{{ route('admin.notifications.clear_all') }}" onsubmit="return confirm('Clear all read notification history?');">
                @csrf
                <button type="submit" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-rose-500/10 text-slate-600 dark:text-slate-300 hover:text-rose-500 font-semibold text-xs border border-slate-200 dark:border-slate-700 transition">
                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                    <span>Clear Read History</span>
                </button>
            </form>
        </div>
    </div>

    <!-- Category Filter Tabs -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl p-2 border border-slate-200 dark:border-slate-800 shadow-sm flex items-center justify-between gap-2 overflow-x-auto">
        <div class="flex items-center gap-2">
            @php
                $types = [
                    'all' => ['label' => 'All Updates', 'icon' => 'layers'],
                    'orders' => ['label' => 'New Orders', 'icon' => 'shopping-bag'],
                    'courier' => ['label' => 'Courier & In-Transit', 'icon' => 'truck'],
                    'delivery' => ['label' => 'Delivered Done', 'icon' => 'check-circle'],
                    'alerts' => ['label' => 'Cancelled / Returned', 'icon' => 'alert-triangle'],
                ];
            @endphp

            @foreach($types as $key => $meta)
            <a href="{{ route('admin.notifications.index', ['type' => $key, 'filter' => $filter]) }}" 
               class="flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs font-semibold whitespace-nowrap transition {{ $type === $key ? 'bg-emerald-500 text-slate-950 font-bold shadow-md shadow-emerald-500/20' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                <i data-lucide="{{ $meta['icon'] }}" class="w-4 h-4"></i>
                <span>{{ $meta['label'] }}</span>
            </a>
            @endforeach
        </div>

        <!-- Read / Unread Status Filter -->
        <div class="flex items-center gap-1 pl-4 border-l border-slate-200 dark:border-slate-800">
            <a href="{{ route('admin.notifications.index', ['type' => $type, 'filter' => 'all']) }}" 
               class="px-2.5 py-1.5 rounded-lg text-xs font-semibold transition {{ $filter === 'all' ? 'bg-slate-200 dark:bg-slate-700 text-slate-900 dark:text-white' : 'text-slate-500 hover:text-slate-800 dark:hover:text-white' }}">
                All
            </a>
            <a href="{{ route('admin.notifications.index', ['type' => $type, 'filter' => 'unread']) }}" 
               class="px-2.5 py-1.5 rounded-lg text-xs font-semibold transition {{ $filter === 'unread' ? 'bg-rose-500 text-white' : 'text-slate-500 hover:text-slate-800 dark:hover:text-white' }}">
                Unread
            </a>
            <a href="{{ route('admin.notifications.index', ['type' => $type, 'filter' => 'read']) }}" 
               class="px-2.5 py-1.5 rounded-lg text-xs font-semibold transition {{ $filter === 'read' ? 'bg-slate-200 dark:bg-slate-700 text-slate-900 dark:text-white' : 'text-slate-500 hover:text-slate-800 dark:hover:text-white' }}">
                Read
            </a>
        </div>
    </div>

    <!-- Notifications List -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
        <div class="divide-y divide-slate-100 dark:divide-slate-800">
            @forelse($notifications as $item)
                @php
                    $isUnread = is_null($item->read_at);
                    $data = $item->data;
                    $typeKey = $data['type'] ?? 'general';
                    $icon = $data['icon'] ?? 'bell';

                    $colorMap = [
                        'new_order' => ['bg' => 'bg-emerald-500/10 dark:bg-emerald-500/20', 'text' => 'text-emerald-500', 'badge' => 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400', 'label' => 'New Order'],
                        'courier_assigned' => ['bg' => 'bg-sky-500/10 dark:bg-sky-500/20', 'text' => 'text-sky-500', 'badge' => 'bg-sky-500/10 text-sky-600 dark:text-sky-400', 'label' => 'Courier Handover'],
                        'in_courier' => ['bg' => 'bg-sky-500/10 dark:bg-sky-500/20', 'text' => 'text-sky-500', 'badge' => 'bg-sky-500/10 text-sky-600 dark:text-sky-400', 'label' => 'In Courier'],
                        'delivery_done' => ['bg' => 'bg-teal-500/10 dark:bg-teal-500/20', 'text' => 'text-teal-500', 'badge' => 'bg-teal-500/10 text-teal-600 dark:text-teal-400', 'label' => 'Delivery Done'],
                        'order_cancelled' => ['bg' => 'bg-rose-500/10 dark:bg-rose-500/20', 'text' => 'text-rose-500', 'badge' => 'bg-rose-500/10 text-rose-600 dark:text-rose-400', 'label' => 'Cancelled'],
                        'order_returned' => ['bg' => 'bg-amber-500/10 dark:bg-amber-500/20', 'text' => 'text-amber-500', 'badge' => 'bg-amber-500/10 text-amber-600 dark:text-amber-400', 'label' => 'Returned'],
                    ];
                    $theme = $colorMap[$typeKey] ?? ['bg' => 'bg-slate-500/10', 'text' => 'text-slate-500', 'badge' => 'bg-slate-100 text-slate-600', 'label' => 'Update'];
                @endphp

                <div class="p-4 sm:p-5 flex items-start justify-between gap-4 transition hover:bg-slate-50/70 dark:hover:bg-slate-800/40 {{ $isUnread ? 'bg-emerald-50/30 dark:bg-emerald-950/10' : '' }}">
                    <div class="flex items-start gap-3.5 min-w-0">
                        <!-- Type Icon -->
                        <div class="w-10 h-10 rounded-xl {{ $theme['bg'] }} {{ $theme['text'] }} flex items-center justify-center flex-shrink-0 mt-0.5">
                            <i data-lucide="{{ $icon }}" class="w-5 h-5"></i>
                        </div>

                        <!-- Details -->
                        <div class="space-y-1 min-w-0">
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="px-2 py-0.5 rounded-md text-[10px] font-bold uppercase tracking-wider {{ $theme['badge'] }}">
                                    {{ $theme['label'] }}
                                </span>
                                <h3 class="text-sm font-bold text-slate-900 dark:text-white truncate">
                                    {{ $data['title'] ?? 'Operational Update' }}
                                </h3>
                                @if($isUnread)
                                    <span class="inline-block w-2 h-2 rounded-full bg-emerald-500"></span>
                                @endif
                            </div>

                            <p class="text-xs text-slate-600 dark:text-slate-300">
                                {{ $data['message'] ?? '' }}
                            </p>

                            <div class="flex items-center gap-3 text-[11px] text-slate-400 pt-1">
                                <span class="flex items-center gap-1 font-mono">
                                    <i data-lucide="clock" class="w-3.5 h-3.5"></i>
                                    {{ $item->created_at->diffForHumans() }}
                                </span>
                                <span>&bull;</span>
                                <span>{{ $item->created_at->format('M d, Y - h:i A') }}</span>
                                @if(!empty($data['amount']))
                                    <span>&bull;</span>
                                    <span class="font-bold text-emerald-600 dark:text-emerald-400 code-font">৳{{ number_format($data['amount'], 2) }}</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center gap-2 flex-shrink-0">
                        @if(!empty($data['action_url']))
                            <form method="POST" action="{{ route('admin.notifications.read', $item->id) }}" class="inline">
                                @csrf
                                <input type="hidden" name="redirect" value="{{ $data['action_url'] }}">
                                <button type="submit" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-emerald-500 hover:text-slate-950 text-slate-700 dark:text-slate-200 text-xs font-semibold transition shadow-sm">
                                    <span>View Details</span>
                                    <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                                </button>
                            </form>
                        @endif

                        @if($isUnread)
                            <form method="POST" action="{{ route('admin.notifications.read', $item->id) }}">
                                @csrf
                                <button type="submit" title="Mark as read" class="p-1.5 rounded-lg text-slate-400 hover:text-emerald-500 hover:bg-emerald-500/10 transition">
                                    <i data-lucide="check" class="w-4 h-4"></i>
                                </button>
                            </form>
                        @endif

                        <form method="POST" action="{{ route('admin.notifications.destroy', $item->id) }}">
                            @csrf
                            @method('DELETE')
                            <button type="submit" title="Delete notification" class="p-1.5 rounded-lg text-slate-400 hover:text-rose-500 hover:bg-rose-500/10 transition">
                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="py-16 text-center">
                    <div class="w-16 h-16 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-400 mx-auto flex items-center justify-center mb-3">
                        <i data-lucide="bell-off" class="w-8 h-8"></i>
                    </div>
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white">No notifications found</h3>
                    <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">You're all caught up! New order updates, courier handovers, and delivery status changes will show up here in real time.</p>
                </div>
            @endforelse
        </div>

        @if($notifications->hasPages())
        <div class="p-4 border-t border-slate-200 dark:border-slate-800">
            {{ $notifications->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
