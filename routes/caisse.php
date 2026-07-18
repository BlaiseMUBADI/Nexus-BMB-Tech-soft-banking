<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CaisseController;
use App\Http\Controllers\OperationCaisseController;
use App\Http\Controllers\Credit\CreditController;
use App\Http\Controllers\Caisse\DepenseController;
use App\Http\Controllers\Caisse\RecetteController;
use App\Http\Controllers\Caisse\OperationAdministrativeController;

Route::middleware('auth')->prefix('caisses')->name('caisses.')->group(function () {

    
    Route::middleware('permission:EBEN-PER10')->group(function () {
        Route::get('ouverture', [CaisseController::class, 'ouverture'])->name('ouverture');

       
        Route::get('operations',                        [OperationCaisseController::class, 'index'])->name('operations.index');
        Route::get('operations/comptes/search',         [OperationCaisseController::class, 'searchCompte'])->name('operations.comptes.search');
        Route::get('operations/commission-preview',      [OperationCaisseController::class, 'commissionPreview'])->name('operations.commission.preview');

        // ── Opérations Administratives : Dépenses (Sorties) + Recettes (Entrées) OHADA ──
        Route::get('operations-administratives', [OperationAdministrativeController::class, 'index'])->name('operations-administratives.index');
        Route::get('depenses/{id}/recu', [DepenseController::class, 'recu'])->name('depenses.recu');
        Route::get('recettes/{id}/recu', [RecetteController::class, 'recu'])->name('recettes.recu');
    });

    // Saisie / annulation : permission dédiée EBEN-PER114
    Route::middleware('permission:EBEN-PER114')->group(function () {
        Route::post('depenses', [DepenseController::class, 'store'])->name('depenses.store');
        Route::post('depenses/{id}/annuler', [DepenseController::class, 'annuler'])->name('depenses.annuler');
        Route::post('recettes', [RecetteController::class, 'store'])->name('recettes.store');
        Route::post('recettes/{id}/annuler', [RecetteController::class, 'annuler'])->name('recettes.annuler');
    });

    Route::middleware('permission:EBEN-PER10')->group(function () {
        Route::get('operations/journal',      [OperationCaisseController::class, 'journalPage'])->name('journal.page');
        Route::get('operations/journal/data', [OperationCaisseController::class, 'journal'])->name('journal.data');
        Route::get('operations/journal/print', [OperationCaisseController::class, 'printJournal'])->name('journal.print');
        Route::get('operations/rapport',      [OperationCaisseController::class, 'rapportFinJournee'])->name('rapport.fin.journee');
    });

    
    Route::middleware('permission:EBEN-PER11')->group(function () {
        Route::get('fermeture/initier/{id}',     [CaisseController::class, 'initierFermeture'])->name('fermeture.initier');
        Route::get('fermeture/pendante',         [CaisseController::class, 'maCloturePendante'])->name('fermeture.pendante');
        Route::get('operations/{id}/bordereau',  [OperationCaisseController::class, 'bordereau'])->name('operations.bordereau');
    });

    Route::middleware('permission:EBEN-PER11')->group(function () {
        Route::post('changer-statut/{id}',       [CaisseController::class, 'changerStatut'])->name('changerStatut');
        Route::post('fermeture/confirmer/{id}',  [CaisseController::class, 'confirmerFermeture'])->name('fermeture.confirmer');
    });

    Route::middleware('permission:EBEN-PER11')->group(function () {
        Route::post('operations',      [OperationCaisseController::class, 'store'])->name('operations.store');
        Route::post('mobile/depart',   [OperationCaisseController::class, 'mobileDepart'])->name('mobile.depart');
        Route::post('mobile/retour',   [OperationCaisseController::class, 'mobileRetour'])->name('mobile.retour');
    });

    // Défense en profondeur : permission vérifiée au niveau route ET dans le contrôleur
    // (EBEN-PER25 transactions bancaire - modèle strict)
    Route::middleware('permission:EBEN-PER25')->post('operations/{id}/annuler', [OperationCaisseController::class, 'annuler'])->name('operations.annuler');

    
    Route::middleware('permission:EBEN-PER10')->group(function () {
        Route::get('mes-demandes', [CaisseController::class, 'mesDemandes'])->name('mes.demandes');
    });

    Route::middleware('permission:EBEN-PER10')->group(function () {
        Route::post('demande-appro', [CaisseController::class, 'demanderApprovisionnement'])->name('demande.appro');
    });

    
    Route::middleware('permission:EBEN-PER11')->group(function () {
        Route::get('operations/{id}/demande-statut', [OperationCaisseController::class, 'statutDemandeModification'])->name('operations.demande.statut');
        Route::post('operations/{id}/demande', [OperationCaisseController::class, 'demanderModification'])->name('operations.demande.modification');
    });

    
    Route::middleware('permission:EBEN-PER44')->group(function () {
        Route::get('demandes-modification',       [OperationCaisseController::class, 'demandesModificationPage'])->name('demandes.modification.page');
        Route::get('demandes-modification/data',  [OperationCaisseController::class, 'demandesModificationJson'])->name('demandes.modification.data');
        Route::get('demandes-modification/count', [OperationCaisseController::class, 'demandesModificationCount'])->name('demandes.modification.count');
    });

    Route::middleware('permission:EBEN-PER44')->group(function () {
        Route::post('demandes-modification/{id}/approuver', [OperationCaisseController::class, 'approuverModification'])->name('demandes.modification.approuver');
        Route::post('demandes-modification/{id}/rejeter',   [OperationCaisseController::class, 'rejeterModification'])->name('demandes.modification.rejeter');
    });

    // Remboursements crédit (vue caissier) — permission Caisse EBEN-PER111
    Route::middleware('permission:EBEN-PER111')->get('remboursements', [OperationCaisseController::class, 'remboursementsCredit'])->name('remboursements.liste');

    // Enregistrement remboursement — dans le groupe Caisse
    Route::middleware('permission:EBEN-PER111')->group(function () {
        Route::get('remboursement/{dossier}', [CreditController::class, 'remboursement'])->name('remboursement');
        Route::post('remboursement/{dossier}', [CreditController::class, 'storeRemboursement'])->name('remboursement.store');
    });
});
