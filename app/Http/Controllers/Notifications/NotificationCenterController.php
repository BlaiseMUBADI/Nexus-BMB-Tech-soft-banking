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
    private function applyCategoryFilter($query, ?string $category)
    {
        $category = is_string($category) ? trim($category) : null;
        if (!$category || $category === 'tous') {
            return $query;
        }

        return $query->where('data', 'like', '%"category":"' . $category . '"%');
    }

    public function index(Request $request): View
    {
        $user = $request->user();
        $selectedCategory = (string) $request->input('category', 'tous');

        $unreadNotifications = $this->applyCategoryFilter($user->unreadNotifications()->latest(), $selectedCategory)
            ->paginate(15, ['*'], 'unread_page')
            ->appends(['category' => $selectedCategory]);

        $readNotifications = $this->applyCategoryFilter($user->readNotifications()->latest(), $selectedCategory)
            ->paginate(15, ['*'], 'read_page')
            ->appends(['category' => $selectedCategory]);

        $categoryCounts = $user->notifications()
            ->latest()
            ->limit(200)
            ->get()
            ->map(function (DatabaseNotification $notification) {
                return data_get($notification->data, 'category', 'systeme');
            })
            ->countBy();

        return view('notifications.index', [
            'unreadNotifications' => $unreadNotifications,
            'readNotifications' => $readNotifications,
            'notificationCategoryCounts' => $categoryCounts,
            'selectedNotificationCategory' => $selectedCategory,
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
                    'category' => data_get($notification->data, 'category', 'systeme'),
                    'icon' => data_get($notification->data, 'icon', 'fas fa-bell'),
                    'action_url' => data_get($notification->data, 'action_url'),
                    'created_at_human' => optional($notification->created_at)->diffForHumans(),
                ];
            });

        $categoryCounts = $user->unreadNotifications()
            ->latest()
            ->limit(100)
            ->get()
            ->map(function (DatabaseNotification $notification) {
                return data_get($notification->data, 'category', 'systeme');
            })
            ->countBy();

        return response()->json([
            'count' => $user->unreadNotifications()->count(),
            'category_counts' => $categoryCounts,
            'items' => $latest,
        ]);
    }
}
