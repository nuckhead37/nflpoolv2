<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\HomeController;
use App\Http\Controllers\PickController;
use App\Http\Controllers\AuthController;

Route::get('/', [HomeController::class, 'home']);
Route::get('/picks', [PickController::class, 'picks']);
Route::get('/picks/{id}', [PickController::class, 'picksWeek']);

Route::get('/login', [AuthController::class, 'showLogin'])
    ->name('login');

Route::post('/login', [AuthController::class, 'login'])
    ->name('login.submit');

Route::get('/logout', [AuthController::class, 'logout'])
    ->name('logout');


Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    });
});
