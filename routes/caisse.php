<?php
use Illuminate\Support\Facades\Route;

Route::prefix('caisses')->name('caisses.')->group(function () {

    // Ouverture / Fermeture physique des guichets
    Route::get('ouverture', [\App\Http\Controllers\CaisseController::class, 'ouverture'])->name('ouverture');
    Route::post('changer-statut/{id}', [\App\Http\Controllers\CaisseController::class, 'changerStatut'])->name('changerStatut');

});
