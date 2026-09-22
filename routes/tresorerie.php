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
    // Montants du coffre (soldes + statistiques) : autorisation dédiée EBEN-PER130
    Route::get('/coffre/balances',        [TresorerieController::class, 'balances'])
        ->middleware('permission:EBEN-PER130')
        ->name('tresorerie.coffre.balances');
    Route::get('/coffre/stats',           [TresorerieController::class, 'stats'])
        ->middleware('permission:EBEN-PER130')
        ->name('tresorerie.coffre.stats');

    
    
    // Approuver les demandes de ravitaillement (alimentation) : EBEN-PER132
    Route::get('/coffre/demandes',                [TresorerieController::class, 'demandesJson'])->name('tresorerie.coffre.demandes');
    Route::post('/coffre/demandes/{id}/approuver',[TresorerieController::class, 'approuverDemande'])
        ->middleware('permission:EBEN-PER132')
        ->name('tresorerie.coffre.demandes.approuver');
    Route::post('/coffre/demandes/{id}/rejeter',  [TresorerieController::class, 'rejeterDemande'])
        ->middleware('permission:EBEN-PER132')
        ->name('tresorerie.coffre.demandes.rejeter');
    Route::get('/coffre/demandes/count',          [TresorerieController::class, 'demandesCount'])->name('tresorerie.coffre.demandes.count');

    // Valider le billettage / clôtures de guichet : EBEN-PER131
    Route::get('/coffre/clotures',                            [TresorerieController::class, 'cloturesEnVerification'])->name('tresorerie.coffre.clotures');
    Route::get('/coffre/clotures/count',                      [TresorerieController::class, 'cloturesCount'])->name('tresorerie.coffre.clotures.count');
    Route::post('/coffre/clotures/{guichetId}/approuver',     [TresorerieController::class, 'approuverCloture'])
        ->middleware('permission:EBEN-PER131')
        ->name('tresorerie.coffre.clotures.approuver');
    Route::post('/coffre/clotures/{guichetId}/rejeter',       [TresorerieController::class, 'rejeterCloture'])
        ->middleware('permission:EBEN-PER131')
        ->name('tresorerie.coffre.clotures.rejeter');

    Route::post('/coffre/clotures/ligne/{clotureId}/approuver',[TresorerieController::class, 'approuverLigneCloture'])
        ->middleware('permission:EBEN-PER131')
        ->name('tresorerie.coffre.clotures.ligne.approuver');
    Route::post('/coffre/clotures/ligne/{clotureId}/rejeter',  [TresorerieController::class, 'rejeterLigneCloture'])
        ->middleware('permission:EBEN-PER131')
        ->name('tresorerie.coffre.clotures.ligne.rejeter');
    
    
    //Route::get('/coffre',                [TresorerieController::class, 'index'])->name('tresorerie.coffre.index');
    // ── Chaque bloc du menu Trésorerie exige désormais SA propre permission,
    // en plus du portail global EBEN-PER44 déjà requis sur tout ce groupe.
    Route::get('/etat-coffre', [TresorerieController::class, 'etat_coffre'])
        ->middleware('permission:EBEN-PER124')
        ->name('tresorerie.etat-coffre');
    Route::get('/approvisionnement', [TresorerieController::class, 'interfaceApprovisionnement'])
        ->middleware('permission:EBEN-PER125')
        ->name('tresorerie.approvisionnement');
    Route::get('/intercaisse', [TresorerieController::class, 'interfaceApprovisionnement'])
        ->middleware('permission:EBEN-PER125')
        ->name('tresorerie.intercaisse');

    Route::get('/commissions', [TresorerieController::class, 'commissions'])
        ->middleware('permission:EBEN-PER126')
        ->name('tresorerie.commissions.index');
    Route::post('/commissions', [TresorerieController::class, 'storeCommission'])
        ->middleware('permission:EBEN-PER77')
        ->name('tresorerie.commissions.store');
    Route::put('/commissions/{commissionRule}', [TresorerieController::class, 'updateCommission'])
        ->middleware('permission:EBEN-PER78')
        ->name('tresorerie.commissions.update');
    Route::patch('/commissions/{commissionRule}/toggle', [TresorerieController::class, 'toggleCommission'])
        ->middleware('permission:EBEN-PER78')
        ->name('tresorerie.commissions.toggle');

    // ── Change de devises au sein du coffre central (ex: convertir du CDF
    // excédentaire en USD pour pouvoir régler des paiements en USD, sans
    // faire sortir/rentrer d'argent réel — uniquement une conversion
    // interne tracée du même coffre). Permission dédiée EBEN-PER129.
    Route::get('/change-devise', [TresorerieController::class, 'changeDevisePage'])
        ->middleware('permission:EBEN-PER129')
        ->name('tresorerie.change-devise');
    Route::post('/change-devise', [TresorerieController::class, 'changeDevise'])
        ->middleware('permission:EBEN-PER129')
        ->name('tresorerie.change-devise.store');
    Route::get('/change-devise/historique', [TresorerieController::class, 'changeDeviseHistorique'])
        ->middleware('permission:EBEN-PER129')
        ->name('tresorerie.change-devise.historique');
});


// NOTE : les routes caisses.demande.appro et caisses.mes.demandes étaient
// dupliquées ici ET dans routes/caisse.php (mêmes noms de route, même
// permission EBEN-PER10). Un nom de route dupliqué fait échouer
// `php artisan route:cache` avec une LogicException — supprimé ici, la
// version de routes/caisse.php (module Caisse) est conservée.
