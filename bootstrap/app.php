<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            // IMPORTANT : les fichiers de routes require_once'és DEPUIS
            // routes/web.php ne survivent pas à `php artisan route:cache`
            // (constaté empiriquement : 263 routes enregistrées sans cache,
            // seulement ~36 après mise en cache — uniquement celles déclarées
            // directement dans web.php). Toutes les routes des modules sont
            // donc enregistrées ici, dans le callback then(), qui lui est
            // correctement pris en compte par la mise en cache.
            Route::middleware('web')->group(function () {
                require base_path('routes/administration.php');
                require base_path('routes/tresorerie.php');
                require base_path('routes/comptabilite.php');
                require base_path('routes/rh.php');
                require base_path('routes/profile.php');
                require base_path('routes/comptes_clients.php');
                require base_path('routes/credit.php');
                require base_path('routes/caisse.php');
            });
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
