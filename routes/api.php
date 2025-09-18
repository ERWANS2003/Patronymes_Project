<?php

use App\Http\Controllers\Api\PatronymeController;
use App\Http\Controllers\Api\AuthController;
use Illuminate\Support\Facades\Route;

// Routes publiques
Route::post('login', [AuthController::class, 'login']);
Route::post('register', [AuthController::class, 'register']);
Route::get('patronymes/search', [PatronymeController::class, 'search']);
Route::get('patronymes/{patronyme}', [PatronymeController::class, 'show']);
Route::get('regions', [PatronymeController::class, 'regions']);
Route::get('departements', [PatronymeController::class, 'departements']);

// Routes protégées
Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('patronymes', PatronymeController::class)->except(['show']);
    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('user', [AuthController::class, 'user']);
});
