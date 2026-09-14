<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Clients\ClientController;
use App\Http\Controllers\Clients\ClientCarteController;
use App\Http\Controllers\ComptesClients\CompteController;
use App\Http\Controllers\Tresorerie\TresorerieController;

Route::middleware('auth')->prefix('comptes-clients')->group(function () {

    Route::middleware('permission:EBEN-PER76')->group(function () {
        Route::get('clients/agents-terrain', [TresorerieController::class, 'agentsMobiles'])->name('clients.agents-terrain');
        Route::get('clients/agents-terrain-pdf', [TresorerieController::class, 'agentsMobilesPdf'])->name('clients.agents-terrain.pdf');
    });

    // 1. Liste des clients (index)
    Route::middleware('permission:EBEN-PER15')->group(function () {
        Route::get('clients', [ClientController::class, 'index'])->name('clients.index');
    });

    // 2. Formulaire et soumission de création d'un client (AVANT clients/{client})
    Route::middleware('permission:EBEN-PER16')->group(function () {
        Route::get('clients/create', [ClientController::class, 'create'])->name('clients.create');
        Route::post('clients', [ClientController::class, 'store'])->name('clients.store');
    });

    // 3. Consultation d'un client spécifique (reçoit le paramètre dynamique {client})
    Route::middleware('permission:EBEN-PER15')->group(function () {
        Route::get('clients/{client}', [ClientController::class, 'show'])->name('clients.show');
    });

    Route::middleware('permission:EBEN-PER17')->group(function () {
        Route::get('clients/{client}/edit', [ClientController::class, 'edit'])->name('clients.edit');
        Route::put('clients/{client}', [ClientController::class, 'update'])->name('clients.update');
        Route::patch('clients/{client}', [ClientController::class, 'update']);
    });

    Route::middleware('permission:EBEN-PER107')->group(function () {
        Route::delete('clients/{client}', [ClientController::class, 'destroy'])->name('clients.destroy');
    });

    // 4. Liste des comptes (index)
    Route::middleware('permission:EBEN-PER18')->group(function () {
        Route::get('comptes', [CompteController::class, 'index'])->name('comptes.index');
    });

    // 5. Formulaire et soumission de création d'un compte (AVANT comptes/{code_compte})
    Route::middleware('permission:EBEN-PER19')->group(function () {
        Route::get('comptes/create', [CompteController::class, 'create'])->name('comptes.create');
        Route::post('comptes', [CompteController::class, 'store'])->name('comptes.store');
    });

    // 6. Consultation d'un compte spécifique (reçoit le paramètre dynamique {code_compte})
    Route::middleware('permission:EBEN-PER18')->group(function () {
        Route::get('comptes/{code_compte}', [CompteController::class, 'show'])->name('comptes.show');
    });

    Route::middleware('permission:EBEN-PER121')->group(function () {
        Route::get('comptes/{code_compte}/edit', [CompteController::class, 'edit'])->name('comptes.edit');
        Route::put('comptes/{code_compte}', [CompteController::class, 'update'])->name('comptes.update');
        Route::patch('comptes/{code_compte}', [CompteController::class, 'update']);
    });

    Route::middleware('permission:EBEN-PER108')->group(function () {
        Route::delete('comptes/{code_compte}', [CompteController::class, 'destroy'])->name('comptes.destroy');
    });

    Route::middleware('permission:EBEN-PER18')->group(function () {
        Route::get('comptes/{code_compte}/rib', [CompteController::class, 'imprimerRIB'])->name('comptes.rib');
        Route::get('comptes-liste-pdf', [CompteController::class, 'imprimerListe'])->name('comptes.liste.pdf');
        Route::get('comptes/{code_compte}/releve-pdf', [CompteController::class, 'releveCompte'])->name('comptes.releve.pdf');
        Route::get('comptes/{code_compte}/historique', [CompteController::class, 'historiqueCompte'])->name('comptes.historique');
    });

    Route::middleware('permission:EBEN-PER15')->group(function () {
        Route::get('clients/{matricule}/fiche-pdf', [ClientController::class, 'imprimerFiche'])->name('clients.fiche.pdf');
        Route::get('clients-liste-pdf', [ClientController::class, 'imprimerListe'])->name('clients.liste.pdf');
    });

    // ── Carte membre ──────────────────────────────────────────────
    Route::middleware('permission:EBEN-PER123')->group(function () {
        Route::get('clients/{client}/carte-membre/apercu', [ClientCarteController::class, 'apercu'])->name('clients.carte-membre.apercu');
        Route::get('clients/{client}/carte-membre/imprimer', [ClientCarteController::class, 'imprimer'])->name('clients.carte-membre.imprimer');
    });

    // Vérification via QR — accessible à tout agent authentifié
    Route::get('carte-membre/verifier/{token}', [ClientCarteController::class, 'verifier'])->name('clients.carte-membre.verifier');
});