<?php

use App\Http\Controllers\Profil\ProfileController;
use App\Http\Controllers\Notifications\NotificationCenterController;
use App\Http\Controllers\Utility\ClientLogController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\RH\AffectationController;
use App\Http\Controllers\RH\AgentController;
use App\Http\Controllers\Clients\ClientController;

// NOTE : administration.php, tresorerie.php, comptabilite.php, rh.php,
// profile.php, comptes_clients.php, credit.php et caisse.php sont désormais
// chargés depuis bootstrap/app.php (callback then() de withRouting()) — ces
// require_once() ne survivaient pas à `php artisan route:cache`. Voir le
// commentaire dans bootstrap/app.php pour le détail.

// Routes d'authentification (login, register, etc.)
require __DIR__.'/auth.php';

// Module Recouvrement (Auto-Collection) - Réservé aux profils avec EBEN-PER90
Route::middleware(['auth', 'permission:EBEN-PER90'])->group(function () {
    Route::get('/recouvrement', [App\Http\Controllers\RecouvrementController::class, 'index'])->name('recouvrement.index');
    Route::get('/recouvrement/historique', [App\Http\Controllers\RecouvrementController::class, 'historique'])->name('recouvrement.historique');
    Route::get('/recouvrement/historique/print', [App\Http\Controllers\RecouvrementController::class, 'printHistorique'])->name('recouvrement.historique.print');
    Route::post('/recouvrement/run', [App\Http\Controllers\RecouvrementController::class, 'runAutoCollection'])->name('recouvrement.run');
});


// Log erreurs AJAX côté client → storage/logs/laravel.log
Route::post('log/client-error', [ClientLogController::class, 'store'])
    ->middleware('auth')
    ->name('log.clientError');

// Heartbeat : prolonge la session en mettant à jour _last_activity
// (déplacé dans DashboardController — une Closure ici empêchait `route:cache`
// de fonctionner, cf. commentaire dans DashboardController::heartbeat())
Route::post('/session/heartbeat', [DashboardController::class, 'heartbeat'])
    ->middleware('auth')->name('session.heartbeat');

// NOTE : Route::redirect('/', '/dashboard') a été retiré — ce helper génère
// une redirection RELATIVE à la racine du domaine et casse l'application
// quand elle est installée dans un sous-dossier (ex: WAMP, .../public).
// DashboardController::redirectToDashboard() utilise redirect()->route(),
// qui génère une URL absolue correcte, tout en restant une vraie action de
// contrôleur (donc compatible `route:cache`, contrairement à une Closure).
Route::get('/', [DashboardController::class, 'redirectToDashboard']);

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/notifications', [NotificationCenterController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/read-all', [NotificationCenterController::class, 'markAllAsRead'])->name('notifications.read-all');
    Route::post('/notifications/{id}/read', [NotificationCenterController::class, 'markAsRead'])->name('notifications.read');
    Route::get('/notifications/latest', [NotificationCenterController::class, 'latest'])->name('notifications.latest');
});





// Photos (médias protégés — auth + permission de visualisation requises)
// AVANT : gardées par 'auth' seul, donc n'importe quel utilisateur connecté
// de l'application (caissier, comptable, agent crédit...) pouvait récupérer
// la photo de N'IMPORTE QUEL client ou agent en devinant/connaissant le nom
// de fichier, même sans la permission de "voir clients"/"voir agents".
Route::middleware('auth')->group(function () {
    Route::middleware('permission:EBEN-PER15')->get('/clients/photo/{filename}', [ClientController::class, 'photo'])->name('clients.photo');
    Route::middleware('permission:EBEN-PER6')->get('/agents/photo/{filename}',  [AgentController::class, 'photo'])->name('agents.photo');

    // Journal des erreurs JavaScript → storage/logs/laravel.log
    // (déplacé dans DashboardController — voir commentaire dans ce fichier)
    Route::post('/log/frontend-error', [DashboardController::class, 'logFrontendError'])
        ->name('log.frontend.error');
});

