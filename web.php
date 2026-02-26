
<?php
use App\Http\Controllers\RH\AffectationController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RH\AgentController;
use App\Http\Controllers\ClientController;
// Administration user management routes
require_once __DIR__.'/administration.php';
// Ressources Humaines routes
require_once __DIR__.'/rh.php';

// Retour à la page d'accueil
Route::get('/', function () {
    return view('dashboard');
});

// Ressources Cleints
Route::resource('clients', ClientController::class);



// Ressourses dashboard
Route::get('/dashboard', [App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');

// Route pour afficher la photo du client
Route::get('/clients/photo/{filename}', [ClientController::class, 'photo'])->name('clients.photo');
// Route pour afficher la photo d'un agent
Route::get('/agents/photo/{filename}', [App\Http\Controllers\RH\AgentController::class, 'photo'])->name('agents.photo');

