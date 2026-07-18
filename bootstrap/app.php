<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            Route::middleware('web')
                ->group(base_path('routes/profile.php'));
        },
    )
    ->withSchedule(function (\Illuminate\Console\Scheduling\Schedule $schedule): void {
        // Notifications proactives : retards crédit, demandes stales, clôtures en attente
        $schedule->command('notifications:proactive')->hourly()->withoutOverlapping();

        // Marquage automatique des retards crédit (chaque jour à 07h00)
        $schedule->command('credit:marquer-retards')
                 ->dailyAt('07:00')
                 ->withoutOverlapping()
                 ->appendOutputTo(storage_path('logs/credit-retards.log'));
    })
    ->withMiddleware(function (Middleware $middleware): void {
        // Alias RBAC dynamique : ->middleware('permission:CODE_PERMISSION')
        $middleware->alias([
            'permission' => \App\Http\Middleware\CheckPermission::class,
        ]);

        // Vérifier l'état utilisateur après le démarrage de la session web.
        $middleware->web(append: [
            \App\Http\Middleware\CheckUserStatus::class,
            \App\Http\Middleware\CheckInactivity::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
