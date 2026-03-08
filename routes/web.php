<?php

use App\Http\Controllers\Profil\ProfileController;
use App\Http\Controllers\Utility\ClientLogController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\RH\AffectationController;
use App\Http\Controllers\RH\AgentController;
use App\Http\Controllers\Clients\ClientController;

// Administration user management routes
require_once __DIR__.'/administration.php';

// Trésorerie & Coffre Central
require_once __DIR__.'/tresorerie.php';

// Ressources Humaines routes
require_once __DIR__.'/rh.php';

// Routes du profil utilisateur
require_once __DIR__.'/profile.php';

// Routes pour la gestion des comptes clients
require __DIR__.'/auth.php';

// Routes pour la gestion des comptes clients
require_once __DIR__.'/comptes_clients.php';

require_once __DIR__.'/caisse.php';


// Log erreurs AJAX côté client → storage/logs/laravel.log
Route::post('log/client-error', [ClientLogController::class, 'store'])
    ->middleware('auth')
    ->name('log.clientError');

Route::get('/', function () {
    return view('dashboard');
})->middleware('auth');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');





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

