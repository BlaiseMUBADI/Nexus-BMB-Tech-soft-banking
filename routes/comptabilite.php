<?php

use App\Http\Controllers\Comptabilite\ComptabiliteController;
use App\Http\Controllers\Comptabilite\CategorieDepenseController;
use App\Http\Controllers\Comptabilite\CategorieRecetteController;
use App\Http\Controllers\Comptabilite\ExerciceComptableController;
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
