<?php

use App\Http\Controllers\Comptabilite\ComptabiliteController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'permission:EBEN-PER49'])
    ->prefix('comptabilite')
    ->name('comptabilite.')
    ->group(function () {
        Route::get('/', [ComptabiliteController::class, 'dashboard'])->name('dashboard');

        Route::middleware('permission:EBEN-PER51')->group(function () {
            Route::get('/plan-ohada', [ComptabiliteController::class, 'planComptable'])->name('plan');
        });

        Route::middleware('permission:EBEN-PER50')->group(function () {
            Route::get('/journal', [ComptabiliteController::class, 'journal'])->name('journal');
        });

        Route::middleware('permission:EBEN-PER52')->group(function () {
            Route::get('/grand-livre', [ComptabiliteController::class, 'grandLivre'])->name('grand-livre');
        });
    });
