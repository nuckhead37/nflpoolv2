<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\HomeController;
use App\Http\Controllers\PickController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\HistoryController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\CurrentSeasonController;

Route::get('/', [HomeController::class, 'home'])->name('home');

// Route::get('/picks', [PickController::class, 'picks']);
// Route::get('/picks/{id}', [PickController::class, 'picksWeek']);

Route::get('/login', [AuthController::class, 'showLogin'])
    ->name('login');

Route::post('/login', [AuthController::class, 'login'])
    ->name('login.submit');

Route::get('/logout', [AuthController::class, 'logout'])
    ->name('logout');

Route::post('/account', [UserController::class, 'update'])
    ->name('account.update');

Route::get('/history/{year}', [HistoryController::class, 'historyByYear']);
Route::get('/history', [HistoryController::class, 'history']);

Route::get('/current/{week?}', [CurrentSeasonController::class, 'current']);

Route::middleware('auth')->group(function () {
    Route::get('/account', [UserController::class, 'account']);
    Route::get('/admin', [AdminController::Class, 'adminHome']);
    Route::get('/enter-results', [AdminController::Class, 'adminEnterResults']);
    Route::get('/update-picks', [AdminController::Class, 'adminUpdatePicks']);
    Route::get('/create-season', [AdminController::Class, 'adminCreateSeason']);
    Route::get('/edit-settings', [AdminController::Class, 'adminEditSettings']);
    Route::get('/manage-users', [AdminController::Class, 'adminManageUsers']);
    Route::get('/picks/{week?}', [PickController::class, 'makePicks']);
});
