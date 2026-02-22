<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Administration\UtilisateurController;

Route::prefix('administration')->group(function () {
    Route::get('/utilisateurs/nouveau', [UtilisateurController::class, 'nouveau'])->name('administration.utilisateurs.nouveau');
    Route::get('/utilisateurs', [UtilisateurController::class, 'liste'])->name('administration.utilisateurs.liste');
    Route::get('/utilisateurs/{id}', [UtilisateurController::class, 'show'])->name('administration.utilisateurs.show');
    Route::get('/utilisateurs/{id}/edit', [UtilisateurController::class, 'edit'])->name('administration.utilisateurs.edit');
    
    
    Route::post('/utilisateurs', [UtilisateurController::class, 'store'])->name('administration.utilisateurs.store');
    // Route AJAX pour infos agent (utilisée dans la création utilisateur)
    Route::get('/utilisateurs/agent-info/{matricule}', [App\Http\Controllers\Administration\UtilisateurController::class, 'agentInfo']);
    // Suppression utilisateur
    Route::delete('/utilisateurs/{id}', [UtilisateurController::class, 'destroy'])->name('administration.utilisateurs.destroy');
    // Modification utilisateur
    Route::put('/utilisateurs/{id}', [UtilisateurController::class, 'update'])->name('administration.utilisateurs.update');
});
