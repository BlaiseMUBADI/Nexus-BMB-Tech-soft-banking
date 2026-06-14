<?php

use App\Http\Controllers\Profil\ProfileController;
use App\Http\Controllers\Notifications\NotificationCenterController;
use App\Http\Controllers\Utility\ClientLogController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\RH\AffectationController;
use App\Http\Controllers\RH\AgentController;
use App\Http\Controllers\Clients\ClientController;

// Administration user management routes
require_once __DIR__.'/administration.php';

// Trésorerie & Coffre Central
require_once __DIR__.'/tresorerie.php';

// Comptabilité OHADA
require_once __DIR__.'/comptabilite.php';

// Ressources Humaines routes
require_once __DIR__.'/rh.php';

// Routes du profil utilisateur
require_once __DIR__.'/profile.php';

// Routes pour la gestion des comptes clients
require __DIR__.'/auth.php';

// Routes pour la gestion des comptes clients
require_once __DIR__.'/comptes_clients.php';

// Module Crédit
require_once __DIR__.'/credit.php';

// Module Caisse
require_once __DIR__.'/caisse.php';

// Module Recouvrement (Auto-Collection)
Route::middleware(['auth'])->group(function () {
    Route::get('/recouvrement', [App\Http\Controllers\RecouvrementController::class, 'index'])->name('recouvrement.index');
    Route::post('/recouvrement/run', [App\Http\Controllers\RecouvrementController::class, 'runAutoCollection'])->name('recouvrement.run');
});


// Log erreurs AJAX côté client → storage/logs/laravel.log
Route::post('log/client-error', [ClientLogController::class, 'store'])
    ->middleware('auth')
    ->name('log.clientError');

// Heartbeat : prolonge la session en mettant à jour _last_activity
Route::post('/session/heartbeat', function () {
    session(['_last_activity' => time()]);
    $remaining = (int) config('session.inactivity_timeout', 600);
    return response()->json(['ok' => true, 'remaining' => $remaining]);
})->middleware('auth')->name('session.heartbeat');

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::get('/dashboard', function () {
    // Compteur pour l'alerte de recouvrement automatique sur le tableau de bord
    $alerteRecouvrementCount = \App\Models\Credit\CreditDemande::where('prelevement_auto_autorise', 1)
        ->whereNotIn('statut_global', ['SOLDE', 'ANNULE'])
        ->whereHas('echeancier.echeances', function ($query) {
            $query->whereIn('statut', ['EN_ATTENTE', 'EN_RETARD'])
                  ->whereDate('date_echeance', '<=', now()->addDays(1)); // Retard ou demain
        })
        ->count();

    return view('dashboard', compact('alerteRecouvrementCount'));
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/notifications', [NotificationCenterController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/read-all', [NotificationCenterController::class, 'markAllAsRead'])->name('notifications.read-all');
    Route::post('/notifications/{id}/read', [NotificationCenterController::class, 'markAsRead'])->name('notifications.read');
    Route::get('/notifications/latest', [NotificationCenterController::class, 'latest'])->name('notifications.latest');
});





// Photos (médias protégés — auth requis)
Route::middleware('auth')->group(function () {
    Route::get('/clients/photo/{filename}', [ClientController::class, 'photo'])->name('clients.photo');
    Route::get('/agents/photo/{filename}',  [AgentController::class, 'photo'])->name('agents.photo');

    // Journal des erreurs JavaScript → storage/logs/laravel.log
    Route::post('/log/frontend-error', function (\Illuminate\Http\Request $req) {
        \Illuminate\Support\Facades\Log::warning('[Frontend JS] ' . ($req->input('message', '?')), [
            'context'     => $req->input('context'),
            'http_status' => $req->input('status'),
            'user_id'     => \Illuminate\Support\Facades\Auth::id(),
            'ip'          => $req->ip(),
        ]);
        return response()->json(['ok' => true]);
    })->name('log.frontend.error');
});

