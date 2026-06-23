<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\HomeController;
use App\Http\Controllers\PickController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\HistoryController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\CurrentSeasonController;
use App\Http\Controllers\SeasonController;
use App\Http\Controllers\SettingController;

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

Route::get('/current', [CurrentSeasonController::class, 'current'])->name('current-season');
Route::get('/current/{week?}', [CurrentSeasonController::class, 'currentWeek']);

Route::get('/picks/view/{week?}', [PickController::class, 'viewPicks']);

Route::middleware('auth')->group(function () {
    Route::get('/account', [UserController::class, 'account']);
    Route::get('/admin', [AdminController::Class, 'adminHome'])->name('admin-home');
    Route::get('/enter-results', [ResultController::Class, 'enterResults']);
    Route::get('/update-picks', [PickController::Class, 'adminUpdatePicks']);
    Route::get('/create-season', [SeasonController::Class, 'createSeason']);
    Route::get('/edit-settings', [SettingController::Class, 'editSettings']);
    Route::get('/manage-users', [UserController::Class, 'adminManageUsers']);
    Route::get('/toggle-season-in-action', [SettingController::Class, 'toggleSeasonInAction']);
    Route::get('/picks/{week?}', [PickController::class, 'makePicks']);
});
