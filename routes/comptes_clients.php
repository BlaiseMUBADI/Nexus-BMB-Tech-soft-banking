<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Clients\ClientController;
use App\Http\Controllers\ComptesClients\CompteController;

Route::middleware('auth')->prefix('comptes-clients')->group(function () {

    Route::middleware('permission:EBEN-PER15')->group(function () {
        Route::get('clients',             [ClientController::class, 'index'])->name('clients.index');
        Route::get('clients/create',      [ClientController::class, 'create'])->name('clients.create');
        Route::get('clients/{client}',    [ClientController::class, 'show'])->name('clients.show');
    });

   
    Route::middleware('permission:EBEN-PER16')->group(function () {
        Route::post('clients',            [ClientController::class, 'store'])->name('clients.store');
    });

    
    Route::middleware('permission:EBEN-PER17')->group(function () {
        Route::get('clients/{client}/edit',    [ClientController::class, 'edit'])->name('clients.edit');
        Route::put('clients/{client}',         [ClientController::class, 'update'])->name('clients.update');
        Route::patch('clients/{client}',       [ClientController::class, 'update']);
        Route::delete('clients/{client}',      [ClientController::class, 'destroy'])->name('clients.destroy');
    });

    
    Route::middleware('permission:EBEN-PER18')->group(function () {
        Route::get('comptes',                  [CompteController::class, 'index'])->name('comptes.index');
        Route::get('comptes/create',           [CompteController::class, 'create'])->name('comptes.create');
        Route::get('comptes/{code_compte}',    [CompteController::class, 'show'])->name('comptes.show');
        Route::get('comptes/{code_compte}/edit', [CompteController::class, 'edit'])->name('comptes.edit');
    });

    
    Route::middleware('permission:EBEN-PER19')->group(function () {
        Route::post('comptes',                 [CompteController::class, 'store'])->name('comptes.store');
        Route::delete('comptes/{code_compte}', [CompteController::class, 'destroy'])->name('comptes.destroy');
    });

    
    Route::middleware('permission:EBEN-PER18')->group(function () {
        Route::get('comptes/{code_compte}/rib',        [CompteController::class, 'imprimerRIB'])->name('comptes.rib');
        Route::get('comptes-liste-pdf',                [CompteController::class, 'imprimerListe'])->name('comptes.liste.pdf');
        Route::get('comptes/{code_compte}/releve-pdf', [CompteController::class, 'releveCompte'])->name('comptes.releve.pdf');
        Route::get('comptes/{code_compte}/historique', [CompteController::class, 'historiqueCompte'])->name('comptes.historique');
    });

    
    Route::middleware('permission:EBEN-PER15')->group(function () {
        Route::get('clients/{matricule}/fiche-pdf', [ClientController::class, 'imprimerFiche'])->name('clients.fiche.pdf');
        Route::get('clients-liste-pdf',             [ClientController::class, 'imprimerListe'])->name('clients.liste.pdf');
    });

});
