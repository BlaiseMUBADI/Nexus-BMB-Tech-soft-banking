<?php

namespace App\Http\Controllers\Notifications;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\View\View;

class NotificationCenterController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        return view('notifications.index', [
            'unreadNotifications' => $user->unreadNotifications()->latest()->paginate(15, ['*'], 'unread_page'),
            'readNotifications' => $user->readNotifications()->latest()->paginate(15, ['*'], 'read_page'),
        ]);
    }

    public function markAsRead(Request $request, string $id): RedirectResponse
    {
        /** @var \App\Models\User $user */
        $user = $request->user();

        /** @var DatabaseNotification|null $notification */
        $notification = $user->notifications()->where('id', $id)->first();

        if ($notification && $notification->read_at === null) {
            $notification->markAsRead();
        }

        return back()->with('success', 'Notification marquée comme lue.');
    }

    public function markAllAsRead(Request $request): RedirectResponse
    {
        /** @var \App\Models\User $user */
        $user = $request->user();
        $user->unreadNotifications()->update(['read_at' => now()]);

        return back()->with('success', 'Toutes les notifications ont été marquées comme lues.');
    }

    public function latest(Request $request): JsonResponse
    {
        /** @var \App\Models\User $user */
        $user = $request->user();

        $latest = $user->unreadNotifications()
            ->latest()
            ->limit(8)
            ->get()
            ->map(function (DatabaseNotification $notification) {
                return [
                    'id' => $notification->id,
                    'title' => data_get($notification->data, 'title', 'Notification'),
                    'message' => data_get($notification->data, 'message', ''),
                    'type' => data_get($notification->data, 'type', 'info'),
                    'icon' => data_get($notification->data, 'icon', 'fas fa-bell'),
                    'action_url' => data_get($notification->data, 'action_url'),
                    'created_at_human' => optional($notification->created_at)->diffForHumans(),
                ];
            });

        return response()->json([
            'count' => $user->unreadNotifications()->count(),
            'items' => $latest,
        ]);
    }
}
