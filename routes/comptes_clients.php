<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Clients\ClientController;
use App\Http\Controllers\ComptesClients\CompteController;

Route::middleware('auth')->prefix('comptes-clients')->group(function () {

    // ── Clients : consultation (EBEN-PER15) ───────────────────────────────
    Route::middleware('permission:EBEN-PER15')->group(function () {
        Route::get('clients',             [ClientController::class, 'index'])->name('clients.index');
        Route::get('clients/create',      [ClientController::class, 'create'])->name('clients.create');
        Route::get('clients/{client}',    [ClientController::class, 'show'])->name('clients.show');
    });

    // ── Clients : création (EBEN-PER16) ──────────────────────────────────
    Route::middleware('permission:EBEN-PER16')->group(function () {
        Route::post('clients',            [ClientController::class, 'store'])->name('clients.store');
    });

    // ── Clients : modification / suppression (EBEN-PER17) ────────────────
    Route::middleware('permission:EBEN-PER17')->group(function () {
        Route::get('clients/{client}/edit',    [ClientController::class, 'edit'])->name('clients.edit');
        Route::put('clients/{client}',         [ClientController::class, 'update'])->name('clients.update');
        Route::patch('clients/{client}',       [ClientController::class, 'update']);
        Route::delete('clients/{client}',      [ClientController::class, 'destroy'])->name('clients.destroy');
    });

    // ── Comptes : consultation (EBEN-PER18) ───────────────────────────────
    Route::middleware('permission:EBEN-PER18')->group(function () {
        Route::get('comptes',                  [CompteController::class, 'index'])->name('comptes.index');
        Route::get('comptes/create',           [CompteController::class, 'create'])->name('comptes.create');
        Route::get('comptes/{code_compte}',    [CompteController::class, 'show'])->name('comptes.show');
        Route::get('comptes/{code_compte}/edit', [CompteController::class, 'edit'])->name('comptes.edit');
    });

    // ── Comptes : gestion (EBEN-PER19) ────────────────────────────────────
    Route::middleware('permission:EBEN-PER19')->group(function () {
        Route::post('comptes',                 [CompteController::class, 'store'])->name('comptes.store');
        Route::delete('comptes/{code_compte}', [CompteController::class, 'destroy'])->name('comptes.destroy');
    });
});
