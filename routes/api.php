<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ZekrController;
use App\Http\Controllers\HijriController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\PrayerTimeController;
use App\Http\Controllers\UserZekrLogController;
use App\Http\Controllers\ZekrCategoryController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');



Route::controller(AuthController::class)->group(function () {
    Route::post('/login', 'login');
    Route::post('/register', 'registerNormal');
    Route::post('/refresh', 'refresh');
    Route::post('/logout', 'logout');
    Route::get('/check-session', 'checkSession');
    // Route::get('auth/google/redirect', 'redirect');
    Route::get('auth/google/callback', 'callBack');
    Route::post('auth/google/logout', 'logout_google');
});

Route::get('/get-city', [LocationController::class, 'getCity'])->middleware('auth:api');
Route::get('/get/nearestMosque', [LocationController::class, 'nearestMosque'])->middleware('auth:api');

Route::get('/getPrayerTimes', [PrayerTimeController::class, 'getPrayerTimes'])->middleware('auth:api');
Route::get('/getQiblaDirection', [PrayerTimeController::class, 'getQiblaDirection'])->middleware('auth:api');
Route::get('/getNextPrayer', [PrayerTimeController::class, 'getNextPrayer'])->middleware('auth:api');


Route::get('/categories', [ZekrCategoryController::class, 'getCategories']);
Route::get('/by-category', [ZekrController::class, 'getAzkarByCategory']);

Route::middleware('auth:api')->group(function () {
    Route::post('/favorites/toggle', [FavoriteController::class, 'toggleFavorite']);
    Route::get('/favorites', [FavoriteController::class, 'getFavorites']);
});

Route::middleware('auth:api')->group(function () {
    Route::post('/profile/update', [AuthController::class, 'updateProfile']);
});

Route::get('/hijri/convert', [HijriController::class, 'convertToHijri']);
Route::get('/hijri/calendar', [HijriController::class, 'calendar']);

Route::middleware('auth:api')->group(function () {
Route::patch('/markZekrAsRead', [UserZekrLogController::class, 'markZekrAsRead']);
Route::get('/get_staticsit_zekr', [UserZekrLogController::class, 'get_staticsit_zekr']);
Route::get('/get_category_read', [UserZekrLogController::class, 'get_category_read']);

});
//
