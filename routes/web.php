<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

    // Route::get('auth/google/callback',[AuthController::class ,'callBack']);
Route::get('/auth/google/redirect', [AuthController::class, 'redirect']);
