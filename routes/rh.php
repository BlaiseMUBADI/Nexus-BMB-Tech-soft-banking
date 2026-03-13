<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RH\AffectationController;
use App\Http\Controllers\RH\AgentController;
use App\Http\Controllers\RH\ServiceController;
use App\Http\Controllers\RH\PosteController;

Route::middleware('auth')->group(function () {

    
    Route::middleware('permission:EBEN-PER7')->group(function () {
        Route::get('rh/agents/create',    [AgentController::class, 'create'])->name('agents.create');
        Route::post('rh/agents',          [AgentController::class, 'store'])->name('agents.store');
    });

   
    Route::middleware('permission:EBEN-PER8')->group(function () {
        Route::get('rh/services/create',  [ServiceController::class, 'create'])->name('services.create');
        Route::post('rh/services',        [ServiceController::class, 'store'])->name('services.store');
    });

  
    Route::middleware('permission:EBEN-PER6')->group(function () {
        Route::get('rh/agents',           [AgentController::class, 'index'])->name('agents.index');
        Route::get('rh/agents/{agent}',   [AgentController::class, 'show'])->name('agents.show');

        Route::get('rh/services',                        [ServiceController::class, 'index'])->name('services.index');
        Route::get('rh/services/{service}',              [ServiceController::class, 'show'])->name('services.show');
        Route::get('rh/services/{service}/postes',       [PosteController::class, 'index'])->name('postes.index');
        Route::get('rh/services/{service}/postes-ajax',  [PosteController::class, 'ajaxListe'])->name('postes.ajaxListe');
    });

   
    Route::middleware('permission:EBEN-PER8')->group(function () {
        Route::get('rh/agents/{agent}/edit',    [AgentController::class, 'edit'])->name('agents.edit');
        Route::put('rh/agents/{agent}',         [AgentController::class, 'update'])->name('agents.update');
        Route::patch('rh/agents/{agent}',       [AgentController::class, 'update']);
        Route::delete('rh/agents/{agent}',      [AgentController::class, 'destroy'])->name('agents.destroy');

        Route::get('rh/services/{service}/edit',         [ServiceController::class, 'edit'])->name('services.edit');
        Route::put('rh/services/{service}',              [ServiceController::class, 'update'])->name('services.update');
        Route::patch('rh/services/{service}',            [ServiceController::class, 'update']);
        Route::delete('rh/services/{service}',           [ServiceController::class, 'destroy'])->name('services.destroy');
        Route::delete('rh/services-ajax/{service}',      [ServiceController::class, 'ajaxDestroy'])->name('services.ajaxDestroy');

        Route::post('rh/services/{service}/postes',      [PosteController::class, 'store'])->name('postes.store');
        Route::post('rh/services/{service}/postes-ajax', [PosteController::class, 'ajaxStore'])->name('postes.ajaxStore');
        Route::delete('rh/services/{service}/postes-ajax/{poste}', [PosteController::class, 'ajaxDestroy'])->name('postes.ajaxDestroy');
    });

   
    Route::middleware('permission:EBEN-PER9')->prefix('rh')->group(function () {
        Route::get('affectations',                       [AffectationController::class, 'index'])->name('affectations.index');
        Route::post('affectations',                      [AffectationController::class, 'store'])->name('affectations.store');
        Route::get('affectations/{affectation}',         [AffectationController::class, 'show'])->name('affectations.show');
        Route::patch('affectations/{affectation}/etat',  [AffectationController::class, 'updateEtat'])->name('affectations.updateEtat');
        Route::delete('affectations/{affectation}',      [AffectationController::class, 'destroy'])->name('affectations.destroy');
    });
});
