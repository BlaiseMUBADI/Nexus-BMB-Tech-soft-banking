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

        // Injecter l'utilisateur connecté (typé) dans la navbar
        View::composer('layouts.navbar', function (\Illuminate\View\View $view) {
            /** @var User|null $authUser */
            $authUser = Auth::check() ? Auth::user() : null;
            $view->with('authUser', $authUser);
        });
    }
}
