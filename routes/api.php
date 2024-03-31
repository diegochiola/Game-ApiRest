<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Laravel\Passport\Passport;
use App\Http\Controllers\UserController;
use App\Http\Controllers\GameController;
use App\Http\Controllers\PassportController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

//agregamos rutas
//Route::post('/login', [UserController::class, 'login'])->name('login');
Route::post('/players', [PassportController::class, 'register']);


Route::group(['prefix' => 'v1', 'namespace' => 'App\Http\Controllers'], function(){
    //Route::apiResource('users',UserController::class);
    Route::apiResource('games',GameController::class);
    Route::get('/users', [UserController::class, 'index']); //show users
    //CRUD
    Route::get('/users/{id}/games', [GameController::class, 'getGames']); //show games
    Route::post('/users/{id}/games', [GameController::class, 'createGame']); //create game
    Route::delete('/users/{id}/games', [GameController::class, 'destroy']); //eliminas todas las partidas del user
    //filter
    Route::get('/users/{id}/percentage-of-wins', [GameController::class, 'percentageOfWins']);
    Route::get('/users/percentage-of-wins', [GameController::class, 'allUsersPercentageOfWins']);
    Route::get('/users/totalPercentageOfWin', [GameController::class, 'getTotalPercentageOfWins']);

});
 

Route::middleware('role:player')->group(function () {
    
});
