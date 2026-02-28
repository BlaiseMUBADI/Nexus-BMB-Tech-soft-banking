<?php

use App\Http\Controllers\Profil\ProfileController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\RH\AffectationController;
use App\Http\Controllers\RH\AgentController;
use App\Http\Controllers\Clients\ClientController;

// Administration user management routes
require_once __DIR__.'/administration.php';

// Ressources Humaines routes
require_once __DIR__.'/rh.php';

// Routes du profil utilisateur
require_once __DIR__.'/profile.php';

// Routes pour la gestion des comptes clients
require __DIR__.'/auth.php';

// Routes pour la gestion des comptes clients
require_once __DIR__.'/comptes_clients.php';


Route::get('/', function () {
    return view('dashboard');
})->middleware('auth');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');





// Ressources Cleints
Route::resource('clients', ClientController::class);
// Route pour afficher la photo du client
Route::get('/clients/photo/{filename}', [ClientController::class, 'photo'])->name('clients.photo');
// Route pour afficher la photo d'un agent
Route::get('/agents/photo/{filename}', [App\Http\Controllers\RH\AgentController::class, 'photo'])->name('agents.photo');

