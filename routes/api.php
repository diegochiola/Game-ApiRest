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

//ROUTES WITHOUT REGISTER
Route::post('/players', [PassportController::class,'register']);
Route::post('/login', [PassportController::class,'login']);

//ROUTES WITH REGISTER
Route::middleware('auth:api')->group(function () {
    Route::put('/players/{id}', [UserController::class, 'updateName']); 
    Route::post('/logout', [PassportController::class, 'logout']);
});

//ROUTES FOR PLAYERS
Route::middleware(['auth:api', 'role:player'])->group(function () { 
    //create game
    Route::post('/players/{id}/games', [GameController::class, 'createGame']);
    //delete all games 
    Route::delete('/players/{id}/games', [GameController::class, 'destroy']); 
    //show all games
    Route::get('/players/{id}/games', [GameController::class, 'getGames']); 
});

//ROUTES FOR ADMINS
Route::middleware(['auth:api', 'role:admin'])->group(function () {   
    //percentage-of-wins of all users
    Route::get('/players', [GameController::class, 'allUsersPercentageOfWins']);
    //ranking all players
    Route::get('/players/ranking', [GameController::class, 'ranking']); 
    //winners
    Route::get('/players/ranking/winner', [GameController::class, 'winner']); 
    //losers
    Route::get('/players/ranking/loser', [GameController::class, 'loser']); 
});


/*
Route::group(['prefix' => 'v1', 'namespace' => 'App\Http\Controllers'], function(){
  
    Route::apiResource('users',UserController::class);
    Route::apiResource('games', GameController::class);

    //show all users registered
    Route::get('/users', [UserController::class, 'index']); //show users
    //show games from user
    Route::get('/users/{id}/games', [GameController::class, 'getGames']); 
});

    //create game for user
    Route::post('/users/{id}/games', [GameController::class, 'createGame']);
    Route::post('/players/{id}/games', [GameController::class, 'createGame']);

    //deleteall  user`s games 
    Route::delete('/users/{id}/games', [GameController::class, 'destroy']); 
    Route::delete('/players/{id}/games', [GameController::class, 'destroy']);

    //update user´s name
    Route::put('/users/{id}', [UserController::class, 'updateName']); 
    

    //Ranking
    Route::get('/players/ranking', [GameController::class, 'ranking']);

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
    Route::get('/players/ranking', [GameController::class, 'ranking']);

//Route::middleware('role:player')->group(function () { });
*/