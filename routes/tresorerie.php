<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Tresorerie\TresorerieController;
use App\Http\Controllers\CaisseController;


Route::middleware(['auth', 'permission:EBEN-PER44'])->prefix('tresorerie')->group(function () {

    
    
    
    Route::post('/coffre/approvisionner', [TresorerieController::class, 'approvisionner'])->name('tresorerie.coffre.approvisionner');
    Route::post('/coffre/alimenter',      [TresorerieController::class, 'alimenter'])->name('tresorerie.coffre.alimenter');
    Route::get('/coffre/alimentations',   [TresorerieController::class, 'alimentations'])->name('tresorerie.coffre.alimentations');
    Route::get('/coffre/mouvements',      [TresorerieController::class, 'mouvements'])->name('tresorerie.coffre.mouvements');
    Route::get('/coffre/balances',        [TresorerieController::class, 'balances'])->name('tresorerie.coffre.balances');
    Route::get('/coffre/stats',           [TresorerieController::class, 'stats'])->name('tresorerie.coffre.stats');

    
    
    Route::get('/coffre/demandes',                [TresorerieController::class, 'demandesJson'])->name('tresorerie.coffre.demandes');
    Route::post('/coffre/demandes/{id}/approuver',[TresorerieController::class, 'approuverDemande'])->name('tresorerie.coffre.demandes.approuver');
    Route::post('/coffre/demandes/{id}/rejeter',  [TresorerieController::class, 'rejeterDemande'])->name('tresorerie.coffre.demandes.rejeter');
    Route::get('/coffre/demandes/count',          [TresorerieController::class, 'demandesCount'])->name('tresorerie.coffre.demandes.count');
    
    Route::get('/coffre/clotures',                            [TresorerieController::class, 'cloturesEnVerification'])->name('tresorerie.coffre.clotures');
    Route::get('/coffre/clotures/count',                      [TresorerieController::class, 'cloturesCount'])->name('tresorerie.coffre.clotures.count');
    Route::post('/coffre/clotures/{guichetId}/approuver',     [TresorerieController::class, 'approuverCloture'])->name('tresorerie.coffre.clotures.approuver');
    Route::post('/coffre/clotures/{guichetId}/rejeter',       [TresorerieController::class, 'rejeterCloture'])->name('tresorerie.coffre.clotures.rejeter');
    
    Route::post('/coffre/clotures/ligne/{clotureId}/approuver',[TresorerieController::class, 'approuverLigneCloture'])->name('tresorerie.coffre.clotures.ligne.approuver');
    Route::post('/coffre/clotures/ligne/{clotureId}/rejeter',  [TresorerieController::class, 'rejeterLigneCloture'])->name('tresorerie.coffre.clotures.ligne.rejeter');
    
    
    //Route::get('/coffre',                [TresorerieController::class, 'index'])->name('tresorerie.coffre.index');
    Route::get('/etat-coffre',    [TresorerieController::class, 'etat_coffre'])->name('tresorerie.etat-coffre');
    
    Route::get('/agents-mobiles',     [TresorerieController::class, 'agentsMobiles'])->name('tresorerie.agents.mobiles');
    Route::get('/agents-mobiles-pdf', [TresorerieController::class, 'agentsMobilesPdf'])->name('tresorerie.agents.mobiles.pdf');
});


Route::middleware(['auth', 'permission:EBEN-PER10'])
    ->post('/caisses/demande-approvisionnement', [CaisseController::class, 'demanderApprovisionnement'])
    ->name('caisses.demande.appro');

Route::middleware(['auth', 'permission:EBEN-PER10'])
    ->get('/caisses/mes-demandes', [CaisseController::class, 'mesDemandes'])
    ->name('caisses.mes.demandes');
