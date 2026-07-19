<?php

use App\Http\Controllers\Comptabilite\ComptabiliteController;
use App\Http\Controllers\Comptabilite\CategorieDepenseController;
use App\Http\Controllers\Comptabilite\CategorieRecetteController;
use App\Http\Controllers\Comptabilite\ExerciceComptableController;
use App\Http\Controllers\Comptabilite\VirementController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'permission:EBEN-PER49'])
    ->prefix('comptabilite')
    ->name('comptabilite.')
    ->group(function () {
        Route::get('/', [ComptabiliteController::class, 'dashboard'])->name('dashboard');

        Route::middleware('permission:EBEN-PER51')->group(function () {
            Route::get('/plan-ohada', [ComptabiliteController::class, 'planComptable'])->name('plan');
        });

        Route::middleware('permission:EBEN-PER115')->prefix('categories-depenses')->name('categories-depenses.')->group(function () {
            Route::get('/', [CategorieDepenseController::class, 'index'])->name('index');
            Route::post('/', [CategorieDepenseController::class, 'store'])->name('store');
            Route::put('/{categorieDepense}', [CategorieDepenseController::class, 'update'])->name('update');
            Route::delete('/{categorieDepense}', [CategorieDepenseController::class, 'destroy'])->name('destroy');
        });

        // ── Exercices comptables (clôture annuelle à double validation) ──
        Route::middleware('permission:EBEN-PER49')->prefix('exercices')->name('exercices.')->group(function () {
            Route::get('/', [ExerciceComptableController::class, 'index'])->name('index');
        });
        Route::middleware('permission:EBEN-PER116')->post('exercices/{exercice}/proposer-cloture', [ExerciceComptableController::class, 'proposerCloture'])->name('exercices.proposer-cloture');
        Route::middleware('permission:EBEN-PER117')->group(function () {
            Route::post('exercices/{exercice}/valider-cloture', [ExerciceComptableController::class, 'validerCloture'])->name('exercices.valider-cloture');
            Route::post('exercices/{exercice}/rejeter-cloture', [ExerciceComptableController::class, 'rejeterCloture'])->name('exercices.rejeter-cloture');
        });

        Route::middleware('permission:EBEN-PER115')->prefix('categories-recettes')->name('categories-recettes.')->group(function () {
            Route::get('/', [CategorieRecetteController::class, 'index'])->name('index');
            Route::post('/', [CategorieRecetteController::class, 'store'])->name('store');
            Route::put('/{categorieRecette}', [CategorieRecetteController::class, 'update'])->name('update');
            Route::delete('/{categorieRecette}', [CategorieRecetteController::class, 'destroy'])->name('destroy');
        });

        // ── Virement bancaire entre comptes clients (proposer / valider double-signature) ──
        Route::prefix('virements')->name('virements.')->group(function () {
            Route::middleware('permission:EBEN-PER119')->group(function () {
                Route::get('/creer', [VirementController::class, 'create'])->name('creer');
                Route::post('/', [VirementController::class, 'store'])->name('store');
            });
            Route::middleware('permission:EBEN-PER119|EBEN-PER120')->group(function () {
                Route::get('/', [VirementController::class, 'index'])->name('index');
                Route::get('/rechercher-compte', [VirementController::class, 'searchCompte'])->name('rechercher-compte');
                Route::get('/{id}/recu', [VirementController::class, 'recu'])->name('recu');
            });
            Route::middleware('permission:EBEN-PER120')->group(function () {
                Route::post('/{id}/approuver', [VirementController::class, 'approuver'])->name('approuver');
                Route::post('/{id}/rejeter', [VirementController::class, 'rejeter'])->name('rejeter');
            });
        });

        Route::middleware('permission:EBEN-PER50')->group(function () {
            Route::get('/journal', [ComptabiliteController::class, 'journal'])->name('journal');
            Route::get('/journal/print', [ComptabiliteController::class, 'printJournal'])->name('journal.print');
        });

        Route::middleware('permission:EBEN-PER52')->group(function () {
            Route::get('/grand-livre', [ComptabiliteController::class, 'grandLivre'])->name('grand-livre');
            Route::get('/grand-livre/print', [ComptabiliteController::class, 'printGrandLivre'])->name('grand-livre.print');
            Route::get('/balance', [ComptabiliteController::class, 'balance'])->name('balance');
            Route::get('/balance/print', [ComptabiliteController::class, 'printBalance'])->name('balance.print');
            Route::get('/compte-resultat', [ComptabiliteController::class, 'compteResultat'])->name('compte-resultat');
            Route::get('/compte-resultat/print', [ComptabiliteController::class, 'printCompteResultat'])->name('compte-resultat.print');
            Route::get('/bilan', [ComptabiliteController::class, 'bilan'])->name('bilan');
            Route::get('/bilan/print', [ComptabiliteController::class, 'printBilan'])->name('bilan.print');
        });
    });
