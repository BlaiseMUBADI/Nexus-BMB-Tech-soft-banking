<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CaisseController;

Route::middleware('auth')->prefix('caisses')->name('caisses.')->group(function () {

    // ── Consultation des caisses (EBEN-PER10) ─────────────────────────────
    Route::middleware('permission:EBEN-PER10')->group(function () {
        Route::get('ouverture', [CaisseController::class, 'ouverture'])->name('ouverture');
    });

    // ── Ouverture / fermeture de caisse (EBEN-PER11) ──────────────────────
    Route::middleware('permission:EBEN-PER11')->group(function () {
        Route::post('changer-statut/{id}', [CaisseController::class, 'changerStatut'])->name('changerStatut');

        // ── Arrêté de caisse (billetage + clôture sécurisée) ──────────────
        Route::get('fermeture/initier/{id}',    [CaisseController::class, 'initierFermeture'])->name('fermeture.initier');
        Route::post('fermeture/confirmer/{id}', [CaisseController::class, 'confirmerFermeture'])->name('fermeture.confirmer');
        Route::get('fermeture/pendante',        [CaisseController::class, 'maCloturePendante'])->name('fermeture.pendante');
    });

    // ── Demandes d'approvisionnement (EBEN-PER10) ─────────────────────────
    Route::middleware('permission:EBEN-PER10')->group(function () {
        Route::post('demande-appro',    [CaisseController::class, 'demanderApprovisionnement'])->name('demande.appro');
        Route::get('mes-demandes',      [CaisseController::class, 'mesDemandes'])->name('mes.demandes');
    });
});
