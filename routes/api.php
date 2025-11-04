<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\PrayerTimeController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');



Route::controller(AuthController::class)->group(function () {
    Route::post('/login', 'login');
    Route::post('/register', 'registerNormal');
    Route::post('/refresh', 'refresh');
    Route::post('/logout', 'logout');
    Route::get('/check-session', 'checkSession');
    Route::get('auth/google/redirect', 'redirect');
    Route::get('auth/google/callback', 'callBack');
    Route::post('auth/google/logout', 'logout_google');
});

Route::get('/get-city', [LocationController::class, 'getCity'])->middleware('auth:api');
Route::get('/getPrayerTimes', [PrayerTimeController::class, 'getPrayerTimes'])->middleware('auth:api');
