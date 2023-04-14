<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\BlockedUserController;
use App\Http\Controllers\DeletedGameController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::middleware(['auth'])->group(function () {
  Route::get('/', [AdminController::class, 'home'])->name('home');
  Route::get('/users/{username}', [AdminController::class, 'manageUser'])->name('user');
  Route::post('/users/{username}/block', [BlockedUserController::class, 'store'])->name('user.block');
  Route::get('/users/{username}/block', [BlockedUserController::class, 'destroy'])->name('user.unblock');
  Route::get('/games/{slug}', [AdminController::class, 'manageGame'])->name('game');
  Route::get('/games/{slug}/delete', [DeletedGameController::class, 'store'])->name('game.delete');
  Route::get('/games/{slug}/restore', [DeletedGameController::class, 'destroy'])->name('game.restore');
  Route::get('/scores/{scoreId}', [AdminController::class, 'deleteScore'])->name('score.delete');
  Route::get('/games/{slug}/scores/{userId}/all-user-scores', [AdminController::class, 'deleteAllUserScores'])->name('score.delete-all-user-scores');
  Route::get('/games/{slug}/scores/all', [AdminController::class, 'resetAllHighScores'])->name('score.delete-all');

  Route::get('/signout', [AdminController::class, 'signout'])->name('signout');
});

Route::get('/admin', [AdminController::class, 'index'])->name('login');
Route::post('/sign-in', [AdminController::class, 'signin'])->name('signin');
