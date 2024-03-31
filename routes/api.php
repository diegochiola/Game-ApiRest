<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Laravel\Passport\Passport;
use App\Http\Controllers\UserController;
use App\Http\Controllers\GameController;
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



//agregamos rutas

Route::post('/login', [UserController::class, 'login'])->name('login');
Route::post('/players', [UserController::class, 'register']);
/*
Route::middleware('auth:api')->group(function () {
    Route::post('/logout', [UserController::class, 'logout'])->name('logout');
    Route::middleware('role:admin')->group(function () {
    });
*/
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
//creamos rutas
Route::group(['prefix' => 'v1', 'namespace' => 'App\Http\Controllers'], function(){
    //Route::apiResource('users',UserController::class);
    Route::apiResource('games',GameController::class);
    Route::get('/users/{id}/games', [GameController::class, 'getGames']); //show games
    Route::post('/users/{id}/games', [GameController::class, 'createGame']); //create game
    Route::get('/users', [UserController::class, 'index']); //show users
    Route::get('/users/{id}/percentage-of-wins', [GameController::class, 'percentageOfWins']);
    Route::delete('/users/{id}/games', [GameController::class, 'destroy']); //eliminas todas las partidas del user
    Route::get('/users/percentage-of-wins', [GameController::class, 'allUsersPercentageOfWins']);
    Route::get('/users/totalPercentageOfWin', [GameController::class, 'getTotalPercentageOfWins']);

});
 

Route::middleware('role:player')->group(function () {
    
});
