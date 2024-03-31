<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
//use Laravel\Passport\Passport;
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

//user register
//Route::post('/register', [PassportController::class, 'register']);
//user login
//Route::post('/login', [PassportController::class, 'login'])->name('login');

Route::group(['prefix' => 'v1', 'namespace' => 'App\Http\Controllers'], function(){
  
    Route::apiResource('users',UserController::class);
    Route::apiResource('games', GameController::class);
    
    //show all users registered
    Route::get('/users', [UserController::class, 'index']); //show users
    //show games from user
    Route::get('/users/{id}/games', [GameController::class, 'getGames']); 
});

    Route::post('/players', [PassportController::class,'register']);
    //sesion actions
    Route::post('/login', [PassportController::class,'login']);
    Route::post('/logout', [PassportController::class, 'logout']);

    //create game for user
    Route::post('/users/{id}/games', [GameController::class, 'createGame']);
    Route::post('/players/{id}/games', [GameController::class, 'createGame']);

    //deleteall  user`s games 
    Route::delete('/users/{id}/games', [GameController::class, 'destroy']); 
    Route::delete('/players/{id}/games', [GameController::class, 'destroy']);

    //edit user´s name
    Route::put('/users/{id}', [UserController::class, 'updateName']); 
    Route::put('/players/{id}', [UserController::class, 'updateName']); 

    //show all games
    Route::get('/users/{id}/games', [GameController::class, 'getGames']); 
    Route::get('/players/{id}/games', [GameController::class, 'getGames']); 

    //percentage-of-wins of a user
    Route::get('/users/{id}/percentage-of-wins', [GameController::class, 'percentageOfWins']);
    
    //percentage-of-wins of all users
    Route::get('/users/percentage-of-wins', [GameController::class, 'allUsersPercentageOfWins']);
    Route::get('/players', [GameController::class, 'allUsersPercentageOfWins']);
    
    //get total percentage-of-wins of all users
    Route::get('/users/total-percentage-of-wins', [GameController::class, 'getTotalPercentageOfWins']);


//Route::middleware('role:player')->group(function () { });
