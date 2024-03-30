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
/*
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
*/

//agregamos rutas
Route::post('/login', [UserController::class, 'login'])->name('login');
Route::post('/players', [UserController::class, 'register']);

Route::middleware('auth:api')->group(function () {

    Route::post('/logout', [UserController::class, 'logout'])->name('logout');

    Route::middleware('role:admin')->group(function () {

    });

    Route::middleware('role:player')->group(function () {

    });
});