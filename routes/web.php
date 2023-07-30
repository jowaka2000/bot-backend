<?php

use App\Http\Controllers\Auth\LoginsController;
use App\Http\Controllers\HomesController;
use App\Http\Controllers\Setting\SettingsController;
use App\Http\Controllers\TestController;
use Illuminate\Support\Facades\Http;
// use FacebookAds\Api;
use Illuminate\Support\Facades\Route;
use Telegram\Bot\Api;

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

// Route::get('/', function () {

//     return view('welcome');
// });

Route::controller(HomesController::class)->group(function(){
    Route::get('/','index')->name('home.index');
});

Route::get('/test', [TestController::class, 'index']);




Route::controller(LoginsController::class)->group(function(){
    Route::get('login','index')->name('login');
    Route::post('login','store');
});

Route::controller(SettingsController::class)->group(function(){
    Route::post('settings/storeFacebookProfile','storeFacebookProfile')->name('settings.facebook-profile');
});
