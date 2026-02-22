<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RH\AffectationController;
use App\Http\Controllers\RH\AgentController;
use App\Http\Controllers\RH\ServiceController;
use App\Http\Controllers\RH\PosteController;

// Ressources Humaines - Agents
Route::resource('rh/agents', AgentController::class);
// Ressources Services RH
Route::resource('rh/services', ServiceController::class);
// Postes dépendant d'un service
Route::get('rh/services/{service}/postes', [PosteController::class, 'index'])->name('postes.index');
Route::post('rh/services/{service}/postes', [PosteController::class, 'store'])->name('postes.store');
// AJAX: supprimer un poste (JSON)
Route::delete('rh/services/{service}/postes-ajax/{poste}', [PosteController::class, 'ajaxDestroy'])->name('postes.ajaxDestroy');
// AJAX: supprimer un service (JSON)
Route::delete('rh/services-ajax/{service}', [ServiceController::class, 'ajaxDestroy'])->name('services.ajaxDestroy');
// AJAX: afficher les postes d'un service (HTML partiel)
Route::get('rh/services/{service}/postes-ajax', [PosteController::class, 'ajaxListe'])->name('postes.ajaxListe');
// AJAX: ajouter un poste (JSON)
Route::post('rh/services/{service}/postes-ajax', [PosteController::class, 'ajaxStore'])->name('postes.ajaxStore');
// Affectation agents/postes (RH)
Route::prefix('rh')->group(function () {
    Route::get('affectations', [AffectationController::class, 'index'])->name('affectations.index');
    Route::post('affectations', [AffectationController::class, 'store'])->name('affectations.store');
});
