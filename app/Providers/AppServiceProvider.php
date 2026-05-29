<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        Schema::defaultStringLength(191);

        // Injecter l'utilisateur connecté + ses permissions dans toutes les vues
        // afin que les contrôles conditionnels de permissions restent cohérents.
        View::composer('*', function (\Illuminate\View\View $view) {
            /** @var User|null $authUser */
            $authUser = Auth::check() ? Auth::user() : null;

            /** @var string[] $userPermCodes */
            $userPermCodes = $authUser ? $authUser->getPermissionCodes() : [];

            $latestUnreadNotifications = collect();
            $unreadNotificationCount = 0;
            $actionNotificationCount = 0;
            $unreadNotificationCategoryCounts = collect();

            if ($authUser && Schema::hasTable('notifications')) {
                $latestUnreadNotifications = $authUser->unreadNotifications()
                    ->latest()
                    ->limit(8)
                    ->get();

                $unreadNotificationCount = (int) $authUser->unreadNotifications()->count();
                $actionNotificationCount = (int) $authUser->unreadNotifications()
                    ->latest()
                    ->limit(50)
                    ->get()
                    ->filter(function ($notification) {
                        return in_array(data_get($notification->data, 'type'), ['warning', 'danger', 'action_required'], true);
                    })
                    ->count();

                $unreadNotificationCategoryCounts = $authUser->unreadNotifications()
                    ->latest()
                    ->limit(100)
                    ->get()
                    ->map(function ($notification) {
                        return data_get($notification->data, 'category', 'systeme');
                    })
                    ->countBy();
            }

            $view->with('authUser', $authUser);
            $view->with('userPermCodes', $userPermCodes);
            $view->with('latestUnreadNotifications', $latestUnreadNotifications);
            $view->with('unreadNotificationCount', $unreadNotificationCount);
            $view->with('actionNotificationCount', $actionNotificationCount);
            $view->with('unreadNotificationCategoryCounts', $unreadNotificationCategoryCounts);
        });
    }
}
