<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Clients\ClientController;
use App\Http\Controllers\ComptesClients\CompteController;

Route::prefix('comptes-clients')->group(function () {
    // Gestion des clients
    Route::resource('clients', ClientController::class);
    // Gestion des comptes
    Route::get('comptes/create', [CompteController::class, 'create'])->name('comptes.create');
    Route::post('comptes', [CompteController::class, 'store'])->name('comptes.store');
    // Ajoute ici d'autres routes si besoin
});
