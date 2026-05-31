<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ContactController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->controller(ClientController::class)->group(function () {
    Route::get('/clients', 'list')->name('clients.list');
    Route::get('/clients/{client}', 'getById')->name('clients.show');
    Route::post('/clients', 'store')->name('clients.store');
    Route::put('/clients/{id}', 'update')->name('clients.update');
    Route::delete('/clients/{id}', 'delete')->name('clients.delete');
    Route::get('/clients/{id}/contacts', 'getContacts')->name('clients.contacts');
});


Route::middleware('auth:sanctum')->controller(ContactController::class)->group(function () {
    Route::get('contacts/{clientId}/client', 'all')->name('contacts.all');
    Route::post('/contacts/{clientId}/', 'create')->name('contacts.create');
    Route::put('/contacts/{clientId}/client/{id}', 'update')->name('clients.update');
    Route::delete('/contacts/{clientId}/client/{id}', 'delete')->name('clients.delete');
});

