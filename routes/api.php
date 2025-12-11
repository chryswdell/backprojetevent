<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\JudicialEventController;
use App\Http\Controllers\Api\UserController;

// Routes publiques (login)
Route::prefix('v1')->group(function () {
    
    // Login public
    Route::post('/login', [AuthController::class, 'login']);

    // Routes protégées par Sanctum
    Route::middleware('auth:sanctum')->group(function () {

        // Logout + utilisateur connecté
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me', [AuthController::class, 'me']);

        // Toutes les routes des événements judiciaires
        Route::apiResource('judicial-events', JudicialEventController::class);

        // Gestion utilisateurs (admin uniquement, vérifié dans UserController)
        Route::apiResource('users', UserController::class);
    });

});
