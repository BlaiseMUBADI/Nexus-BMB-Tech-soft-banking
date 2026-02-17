
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RH\AgentController;
use App\Http\Controllers\ClientController;

// Ressources Services RH
Route::resource('rh/services', App\Http\Controllers\RH\ServiceController::class);


// Postes dépendant d'un service
Route::get('rh/services/{service}/postes', [App\Http\Controllers\RH\PosteController::class, 'index'])->name('postes.index');
Route::post('rh/services/{service}/postes', [App\Http\Controllers\RH\PosteController::class, 'store'])->name('postes.store');

// AJAX: afficher les postes d'un service (HTML partiel)
Route::get('rh/services/{service}/postes-ajax', [App\Http\Controllers\RH\PosteController::class, 'ajaxListe'])->name('postes.ajaxListe');
// AJAX: ajouter un poste (JSON)
Route::post('rh/services/{service}/postes-ajax', [App\Http\Controllers\RH\PosteController::class, 'ajaxStore'])->name('postes.ajaxStore');


// Ressources Humaines - Agents
Route::resource('rh/agents', AgentController::class);

// Ressources Cleints
Route::resource('clients', ClientController::class);

// Retour à la page d'accueil
Route::get('/', function () {
    return view('dashboard');
});

// Ressourses dashboard
Route::get('/dashboard', [App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');

// Route pour afficher la photo du client
Route::get('/clients/photo/{filename}', [ClientController::class, 'photo'])->name('clients.photo');
// Route pour afficher la photo d'un agent
Route::get('/agents/photo/{filename}', [App\Http\Controllers\RH\AgentController::class, 'photo'])->name('agents.photo');

