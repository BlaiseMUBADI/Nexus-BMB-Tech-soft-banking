<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CaisseController;
use App\Http\Controllers\OperationCaisseController;

Route::middleware('auth')->prefix('caisses')->name('caisses.')->group(function () {

    
    Route::middleware('permission:EBEN-PER10')->group(function () {
        Route::get('ouverture', [CaisseController::class, 'ouverture'])->name('ouverture');

       
        Route::get('operations',                        [OperationCaisseController::class, 'index'])->name('operations.index');
        Route::get('operations/comptes/search',         [OperationCaisseController::class, 'searchCompte'])->name('operations.comptes.search');
        Route::get('operations/journal',      [OperationCaisseController::class, 'journalPage'])->name('journal.page');
        Route::get('operations/journal/data', [OperationCaisseController::class, 'journal'])->name('journal.data');
        Route::get('operations/rapport',      [OperationCaisseController::class, 'rapportFinJournee'])->name('rapport.fin.journee');

      
        Route::get('mobile', [OperationCaisseController::class, 'mobileIndex'])->name('mobile.index');
    });

    
    Route::middleware('permission:EBEN-PER11')->group(function () {
        Route::post('changer-statut/{id}', [CaisseController::class, 'changerStatut'])->name('changerStatut');

       
        Route::get('fermeture/initier/{id}',    [CaisseController::class, 'initierFermeture'])->name('fermeture.initier');
        Route::post('fermeture/confirmer/{id}', [CaisseController::class, 'confirmerFermeture'])->name('fermeture.confirmer');
        Route::get('fermeture/pendante',        [CaisseController::class, 'maCloturePendante'])->name('fermeture.pendante');

        
        Route::post('operations',             [OperationCaisseController::class, 'store'])->name('operations.store');
        Route::post('operations/{id}/annuler',[OperationCaisseController::class, 'annuler'])->name('operations.annuler');
        Route::get('operations/{id}/bordereau',[OperationCaisseController::class, 'bordereau'])->name('operations.bordereau');

        
        Route::post('mobile/depart', [OperationCaisseController::class, 'mobileDepart'])->name('mobile.depart');
        Route::post('mobile/retour', [OperationCaisseController::class, 'mobileRetour'])->name('mobile.retour');
    });

    
    Route::middleware('permission:EBEN-PER10')->group(function () {
        Route::post('demande-appro',    [CaisseController::class, 'demanderApprovisionnement'])->name('demande.appro');
        Route::get('mes-demandes',      [CaisseController::class, 'mesDemandes'])->name('mes.demandes');
    });

    
    Route::middleware('permission:EBEN-PER11')->group(function () {
        Route::post('operations/{id}/demande', [OperationCaisseController::class, 'demanderModification'])->name('operations.demande.modification');
    });

    
    Route::middleware('permission:EBEN-PER44')->group(function () {
        Route::get('demandes-modification',            [OperationCaisseController::class, 'demandesModificationPage'])->name('demandes.modification.page');
        Route::get('demandes-modification/data',       [OperationCaisseController::class, 'demandesModificationJson'])->name('demandes.modification.data');
        Route::get('demandes-modification/count',      [OperationCaisseController::class, 'demandesModificationCount'])->name('demandes.modification.count');
        Route::post('demandes-modification/{id}/approuver', [OperationCaisseController::class, 'approuverModification'])->name('demandes.modification.approuver');
        Route::post('demandes-modification/{id}/rejeter',   [OperationCaisseController::class, 'rejeterModification'])->name('demandes.modification.rejeter');
    });
});
