
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RH\AgentController;
use App\Http\Controllers\ClientController;


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

