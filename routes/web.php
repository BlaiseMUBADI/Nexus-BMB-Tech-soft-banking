<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/page1', function () {
    return view('page1');
});

Route::get('/page2', function () {
    return view('page2');
});


use App\Http\Controllers\ClientController;
Route::get('/dashboard', [App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');
Route::resource('clients', ClientController::class);
Route::get('/clients/photo/{filename}', [ClientController::class, 'photo'])->name('clients.photo');
