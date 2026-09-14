<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Profil\ProfileController;

Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Photo du PROPRE agent de l'utilisateur connecté — volontairement SANS
    // permission EBEN-PER6 (celle-ci protège agents.photo pour la gestion RH
    // des AUTRES agents). Voir sa propre photo de profil ne doit jamais
    // dépendre d'une permission RH : sinon un caissier/agent sans EBEN-PER6
    // ne pourrait même pas voir sa propre photo sur sa page de profil.
    Route::get('/profile/photo/{filename}', [ProfileController::class, 'photo'])->name('profile.photo');
});
