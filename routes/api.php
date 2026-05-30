<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClientController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->controller(ClientController::class)->group(function () {
    Route::get('/clients', 'list')->name('clients.list');
//    Route::get('/clients/{client}', 'show')->name('clients.show');
    Route::post('/clients', 'store')->name('clients.store');
    Route::put('/clients/{id}', 'update')->name('clients.update');
    Route::get('/clients/{id}/contacts', 'getContacts')->name('clients.contacts');
});
