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

            $view->with('authUser', $authUser);
            $view->with('userPermCodes', $userPermCodes);
        });
    }
}
