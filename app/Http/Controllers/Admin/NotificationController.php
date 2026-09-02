<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class NotificationController extends Controller
{
    /**
     * Display full notifications management hub.
     */
    public function index(Request $request): View
    {
        $user = Auth::guard('web')->user();
        $filter = $request->get('filter', 'all');
        $type = $request->get('type', 'all');

        if (! Schema::hasTable('notifications')) {
            $notifications = new LengthAwarePaginator([], 0, 20);
            $unreadCount = 0;
            $totalCount = 0;

            return view('admin.notifications.index', compact('notifications', 'unreadCount', 'totalCount', 'filter', 'type'));
        }

        $query = $user ? $user->notifications() : DatabaseNotification::query();

        if ($filter === 'unread') {
            $query->whereNull('read_at');
        } elseif ($filter === 'read') {
            $query->whereNotNull('read_at');
        }

        if ($type !== 'all') {
            if ($type === 'orders') {
                $query->whereIn('data->type', ['new_order']);
            } elseif ($type === 'courier') {
                $query->whereIn('data->type', ['courier_assigned', 'in_courier']);
            } elseif ($type === 'delivery') {
                $query->whereIn('data->type', ['delivery_done', 'completed']);
            } elseif ($type === 'alerts') {
                $query->whereIn('data->type', ['order_cancelled', 'order_returned']);
            }
        }

        $notifications = $query->latest()->paginate(20)->withQueryString();
        $unreadCount = $user ? $user->unreadNotifications()->count() : 0;
        $totalCount = $user ? $user->notifications()->count() : 0;

        return view('admin.notifications.index', compact('notifications', 'unreadCount', 'totalCount', 'filter', 'type'));
    }

    /**
     * Real-time JSON endpoint for top-bar bell dropdown & live polling.
     */
    public function latest(Request $request): JsonResponse
    {
        $user = Auth::guard('web')->user();

        if (! $user || ! Schema::hasTable('notifications')) {
            return response()->json([
                'success' => true,
                'unread_count' => 0,
                'notifications' => [],
            ]);
        }

        $unreadCount = $user->unreadNotifications()->count();
        $notifications = $user->notifications()->take(10)->get()->map(function ($n) {
            return [
                'id' => $n->id,
                'read' => $n->read_at !== null,
                'type' => $n->data['type'] ?? 'general',
                'title' => $n->data['title'] ?? 'Notification',
                'message' => $n->data['message'] ?? '',
                'icon' => $n->data['icon'] ?? 'bell',
                'icon_color' => $n->data['icon_color'] ?? 'emerald',
                'action_url' => $n->data['action_url'] ?? route('admin.orders.index'),
                'time_ago' => $n->created_at->diffForHumans(),
                'created_at' => $n->created_at->toIso8601String(),
            ];
        });

        return response()->json([
            'success' => true,
            'unread_count' => $unreadCount,
            'notifications' => $notifications,
        ]);
    }

    /**
     * Mark a specific notification as read.
     */
    public function markAsRead(Request $request, string $id): JsonResponse|RedirectResponse
    {
        $user = Auth::guard('web')->user();
        if ($user) {
            $notification = $user->notifications()->where('id', $id)->first();
            if ($notification) {
                $notification->markAsRead();
            }
        }

        if ($request->wantsJson()) {
            return response()->json(['success' => true]);
        }

        $redirect = $request->input('redirect');
        if ($redirect) {
            return redirect($redirect);
        }

        return redirect()->back()->with('success', 'Notification marked as read.');
    }

    /**
     * Mark all unread notifications as read.
     */
    public function markAllAsRead(Request $request): JsonResponse|RedirectResponse
    {
        $user = Auth::guard('web')->user();
        if ($user) {
            $user->unreadNotifications()->update(['read_at' => now()]);
        }

        if ($request->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()->back()->with('success', 'All notifications marked as read.');
    }

    /**
     * Delete a single notification.
     */
    public function destroy(Request $request, string $id): JsonResponse|RedirectResponse
    {
        $user = Auth::guard('web')->user();
        if ($user) {
            $user->notifications()->where('id', $id)->delete();
        }

        if ($request->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()->back()->with('success', 'Notification removed.');
    }

    /**
     * Clear all read notifications.
     */
    public function clearAll(Request $request): RedirectResponse
    {
        $user = Auth::guard('web')->user();
        if ($user) {
            $user->notifications()->whereNotNull('read_at')->delete();
        }

        return redirect()->back()->with('success', 'All read notifications have been cleared.');
    }
}
