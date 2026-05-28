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
});
