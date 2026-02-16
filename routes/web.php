
<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('dashboard');
});

use App\Http\Controllers\ClientController;
Route::get('/dashboard', [App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');
Route::resource('clients', ClientController::class);
Route::get('/clients/photo/{filename}', [ClientController::class, 'photo'])->name('clients.photo');



Route::get('/test', function () {
    return view('test');
})->name('test');