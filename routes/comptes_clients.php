<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Clients\ClientController;
use App\Http\Controllers\ComptesClients\CompteController;

Route::prefix('comptes-clients')->group(function () {
    // Gestion des clients
    Route::resource('clients', ClientController::class);
    // Gestion des comptes
    Route::get('comptes', [CompteController::class, 'index'])->name('comptes.index');
    Route::get('comptes/create', [CompteController::class, 'create'])->name('comptes.create');
    Route::post('comptes', [CompteController::class, 'store'])->name('comptes.store');
    Route::delete('comptes/{code_compte}', [CompteController::class, 'destroy'])->name('comptes.destroy');
    Route::get('comptes/{code_compte}', [CompteController::class, 'show'])->name('comptes.show');
    Route::get('comptes/{code_compte}/edit', [CompteController::class, 'edit'])->name('comptes.edit');
    // Ajoute ici d'autres routes si besoin
});
