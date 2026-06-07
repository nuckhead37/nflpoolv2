<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\HomeController;
use App\Http\Controllers\PickController;

// Route::get('/', function () {
//     return view('welcome');
// });


Route::get('/', [HomeController::class, 'home']);
Route::get('picks', [PickController::class, 'picks']);
Route::get('picks/{id}', [PickController::class, 'picksWeek']);
