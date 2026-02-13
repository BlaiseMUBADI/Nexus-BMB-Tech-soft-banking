<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
<<<<<<< HEAD
use Illuminate\Support\Facades\Schema;
=======
>>>>>>> 7bf0eb9c5695044b8a207badf2110ec6967eb389

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
<<<<<<< HEAD
           Schema::defaultStringLength(191);
=======
        //
>>>>>>> 7bf0eb9c5695044b8a207badf2110ec6967eb389
    }
}
