<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Tresorerie\TresorerieController;
use App\Http\Controllers\CaisseController;


Route::middleware(['auth', 'permission:EBEN-PER44'])->prefix('tresorerie')->group(function () {

    
    
    
    Route::post('/coffre/approvisionner', [TresorerieController::class, 'approvisionner'])
        ->middleware('permission:EBEN-PER45|EBEN-PER77')
        ->name('tresorerie.coffre.approvisionner');
    Route::post('/coffre/alimenter',      [TresorerieController::class, 'alimenter'])
        ->middleware('permission:EBEN-PER47|EBEN-PER77')
        ->name('tresorerie.coffre.alimenter');
    Route::get('/coffre/alimentations',   [TresorerieController::class, 'alimentations'])->name('tresorerie.coffre.alimentations');
    Route::get('/coffre/mouvements',      [TresorerieController::class, 'mouvements'])->name('tresorerie.coffre.mouvements');
    Route::get('/coffre/balances',        [TresorerieController::class, 'balances'])->name('tresorerie.coffre.balances');
    Route::get('/coffre/stats',           [TresorerieController::class, 'stats'])->name('tresorerie.coffre.stats');

    
    
    Route::get('/coffre/demandes',                [TresorerieController::class, 'demandesJson'])->name('tresorerie.coffre.demandes');
    Route::post('/coffre/demandes/{id}/approuver',[TresorerieController::class, 'approuverDemande'])
        ->middleware('permission:EBEN-PER46|EBEN-PER78')
        ->name('tresorerie.coffre.demandes.approuver');
    Route::post('/coffre/demandes/{id}/rejeter',  [TresorerieController::class, 'rejeterDemande'])
        ->middleware('permission:EBEN-PER46|EBEN-PER78')
        ->name('tresorerie.coffre.demandes.rejeter');
    Route::get('/coffre/demandes/count',          [TresorerieController::class, 'demandesCount'])->name('tresorerie.coffre.demandes.count');
    
    Route::get('/coffre/clotures',                            [TresorerieController::class, 'cloturesEnVerification'])->name('tresorerie.coffre.clotures');
    Route::get('/coffre/clotures/count',                      [TresorerieController::class, 'cloturesCount'])->name('tresorerie.coffre.clotures.count');
    Route::post('/coffre/clotures/{guichetId}/approuver',     [TresorerieController::class, 'approuverCloture'])
        ->middleware('permission:EBEN-PER46|EBEN-PER78')
        ->name('tresorerie.coffre.clotures.approuver');
    Route::post('/coffre/clotures/{guichetId}/rejeter',       [TresorerieController::class, 'rejeterCloture'])
        ->middleware('permission:EBEN-PER46|EBEN-PER78')
        ->name('tresorerie.coffre.clotures.rejeter');
    
    Route::post('/coffre/clotures/ligne/{clotureId}/approuver',[TresorerieController::class, 'approuverLigneCloture'])
        ->middleware('permission:EBEN-PER46|EBEN-PER78')
        ->name('tresorerie.coffre.clotures.ligne.approuver');
    Route::post('/coffre/clotures/ligne/{clotureId}/rejeter',  [TresorerieController::class, 'rejeterLigneCloture'])
        ->middleware('permission:EBEN-PER46|EBEN-PER78')
        ->name('tresorerie.coffre.clotures.ligne.rejeter');
    
    
    //Route::get('/coffre',                [TresorerieController::class, 'index'])->name('tresorerie.coffre.index');
    Route::get('/etat-coffre',    [TresorerieController::class, 'etat_coffre'])->name('tresorerie.etat-coffre');
    Route::get('/approvisionnement', [TresorerieController::class, 'interfaceApprovisionnement'])->name('tresorerie.approvisionnement');
    Route::get('/intercaisse', [TresorerieController::class, 'interfaceApprovisionnement'])->name('tresorerie.intercaisse');
    
    Route::get('/commissions', [TresorerieController::class, 'commissions'])->name('tresorerie.commissions.index');
    Route::post('/commissions', [TresorerieController::class, 'storeCommission'])
        ->middleware('permission:EBEN-PER77')
        ->name('tresorerie.commissions.store');
    Route::put('/commissions/{commissionRule}', [TresorerieController::class, 'updateCommission'])
        ->middleware('permission:EBEN-PER78')
        ->name('tresorerie.commissions.update');
    Route::patch('/commissions/{commissionRule}/toggle', [TresorerieController::class, 'toggleCommission'])
        ->middleware('permission:EBEN-PER78')
        ->name('tresorerie.commissions.toggle');
});


Route::middleware(['auth', 'permission:EBEN-PER10'])
    ->post('/caisses/demande-approvisionnement', [CaisseController::class, 'demanderApprovisionnement'])
    ->name('caisses.demande.appro');

Route::middleware(['auth', 'permission:EBEN-PER10'])
    ->get('/caisses/mes-demandes', [CaisseController::class, 'mesDemandes'])
    ->name('caisses.mes.demandes');
