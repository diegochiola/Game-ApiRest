<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\GameController;
use App\Http\Controllers\PassportController;

// Routes without registration
Route::post('/register', [PassportController::class,'register']);
Route::post('/login', [PassportController::class,'login']);

// Routes with registration
Route::middleware('auth:api')->group(function () {
    Route::put('/players/{id}', [UserController::class, 'updateName']); 
    Route::post('/logout', [PassportController::class, 'logout']);
});

// Routes for players
Route::middleware(['auth:api', 'role:player'])->group(function () { 
    Route::post('/players/{id}/games', [GameController::class, 'createGame']);
    Route::delete('/players/{id}/games', [GameController::class, 'destroy']); 
    Route::get('/players/{id}/games', [GameController::class, 'getGames']); 
});

// Routes for admins
Route::middleware(['auth:api', 'role:admin'])->group(function () {   
    Route::get('/players', [GameController::class, 'allUsersPercentageOfWins']);
    Route::get('/players/ranking', [GameController::class, 'ranking']); 
    Route::get('/players/ranking/winner', [GameController::class, 'winner']); 
    Route::get('/players/ranking/loser', [GameController::class, 'loser']); 
    //agregue para que en los test los admin puedan ver los juegos del jugador:
    Route::get('/players/{id}/games', [GameController::class, 'getGamesForPlayer']);
});
