<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CaisseController;
use App\Http\Controllers\OperationCaisseController;

Route::middleware('auth')->prefix('caisses')->name('caisses.')->group(function () {

    // ── Consultation des caisses (EBEN-PER10) ─────────────────────────────
    Route::middleware('permission:EBEN-PER10')->group(function () {
        Route::get('ouverture', [CaisseController::class, 'ouverture'])->name('ouverture');

        // ── Opérations de caisse ──────────────────────────────────────────
        Route::get('operations',                        [OperationCaisseController::class, 'index'])->name('operations.index');
        Route::get('operations/comptes/search',         [OperationCaisseController::class, 'searchCompte'])->name('operations.comptes.search');
        Route::get('operations/journal',      [OperationCaisseController::class, 'journalPage'])->name('journal.page');
        Route::get('operations/journal/data', [OperationCaisseController::class, 'journal'])->name('journal.data');
        Route::get('operations/rapport',      [OperationCaisseController::class, 'rapportFinJournee'])->name('rapport.fin.journee');

        // ── Gestion Mobile (départ/retour) ────────────────────────────────
        Route::get('mobile', [OperationCaisseController::class, 'mobileIndex'])->name('mobile.index');
    });

    // ── Ouverture / fermeture de caisse (EBEN-PER11) ──────────────────────
    Route::middleware('permission:EBEN-PER11')->group(function () {
        Route::post('changer-statut/{id}', [CaisseController::class, 'changerStatut'])->name('changerStatut');

        // ── Arrêté de caisse (billetage + clôture sécurisée) ──────────────
        Route::get('fermeture/initier/{id}',    [CaisseController::class, 'initierFermeture'])->name('fermeture.initier');
        Route::post('fermeture/confirmer/{id}', [CaisseController::class, 'confirmerFermeture'])->name('fermeture.confirmer');
        Route::get('fermeture/pendante',        [CaisseController::class, 'maCloturePendante'])->name('fermeture.pendante');

        // ── Saisie des opérations (EBEN-PER11) ───────────────────────────
        Route::post('operations',             [OperationCaisseController::class, 'store'])->name('operations.store');
        Route::post('operations/{id}/annuler',[OperationCaisseController::class, 'annuler'])->name('operations.annuler');

        // ── Mobile : dotation et reversement ────────────────────────────
        Route::post('mobile/depart', [OperationCaisseController::class, 'mobileDepart'])->name('mobile.depart');
        Route::post('mobile/retour', [OperationCaisseController::class, 'mobileRetour'])->name('mobile.retour');
    });

    // ── Demandes d'approvisionnement (EBEN-PER10) ─────────────────────────
    Route::middleware('permission:EBEN-PER10')->group(function () {
        Route::post('demande-appro',    [CaisseController::class, 'demanderApprovisionnement'])->name('demande.appro');
        Route::get('mes-demandes',      [CaisseController::class, 'mesDemandes'])->name('mes.demandes');
    });
});
