<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClientController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});



Route::get('/clients', function () {
    return view('clients.dashboard');
})->name('clients');


///rutas autsh
Route::controller(AuthController::class)->group(function () {
    Route::get('/login', 'showLogin')->name('login');
    Route::get('/register', 'showRegister')->name('register');

    Route::post('/login', 'login');
    Route::post('/register', 'register');
    Route::post('/logout', 'logout');
});
Route::middleware('auth')->controller(ClientController::class)->group(function () {
    Route::get('/clients', 'index')->name('clients.index');
});
