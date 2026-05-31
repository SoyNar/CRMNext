<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ContactController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});




///rutas autsh
Route::controller(AuthController::class)->group(function () {
    Route::get('/login', 'showLogin')->name('login');
    Route::get('/register', 'showRegister')->name('register');

    Route::post('/login', 'login');
    Route::post('/register', 'register');
    Route::post('/logout', 'logout');
});
Route::middleware('auth:sanctum')->controller(ClientController::class)->group(function () {
    Route::get('/clients', 'index')->name('clients.index');
});

Route::middleware('auth:sanctum')->controller(ContactController::class)->group(function () {
    Route::get('/clients/{client}/contacts',  'list')->name('clients.contacts');
});
