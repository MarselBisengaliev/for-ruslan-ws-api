<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\GameController;
use App\Http\Controllers\GameScoreController;
use App\Http\Controllers\GameVersionController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::prefix('v1')->group(function() {
    Route::post('/auth/signup', [AuthController::class, 'signup']);
    Route::post('/auth/signin', [AuthController::class, 'signin']);
    Route::get('/games', [GameController::class, 'index']);
    Route::get('/games/{slug}', [GameController::class, 'show']);
    Route::get('/games/{slug}/{version}', [GameVersionController::class, 'show'])->whereNumber('version');
    Route::get('/users/{username}', [UserController::class, 'show']);
    Route::get('/games/{slug}/scores', [GameScoreController::class, 'index']);

    Route::middleware(['auth:sanctum', 'not-blocked'])->group(function() {
        Route::post('/auth/signout', [AuthController::class, 'signout']);
        Route::post('/games', [GameController::class, 'store']);
        Route::post('/games/{slug}/upload', [GameVersionController::class, 'store']);
        Route::put('/games/{slug}', [GameController::class, 'update']);
        Route::delete('/games/{slug}', [GameController::class, 'destroy']);
        Route::post('/games/{slug}/scores', [GameScoreController::class, 'store']);
    });
});
