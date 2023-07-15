<?php

use App\Http\Controllers\Api\AuthsController;
use App\Http\Controllers\Api\PostSchedulersController;
use App\Http\Controllers\Api\TelegramApisController;
use App\Http\Controllers\Api\UserAppsController;
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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::middleware('auth:sanctum')->group(function () {

    Route::get('/user', function (Request $request) {
        $user = $request->user();

        $isAdmin = $user->admin($user);

        return response(compact('user','isAdmin'));
    });

    Route::get('/logout', [AuthsController::class, 'logout']);



    Route::controller(UserAppsController::class)->group(function(){
        Route::post('/user-apps/create','store');
        Route::get('/user-apps/index','index');
        Route::post('/user-apps/update-access-token','updateAccessToken');
        Route::post('/user-apps/update-telegram-username-token','updateTelegramAccessTokenAndUsername');
        Route::get('/user-apps/{id}','show');
        Route::post('/user-apps/{id}/update','update');
        Route::post('/user-apps/{id}/update-app-approve','appApprove');
    });

    Route::controller(PostSchedulersController::class)->group(function(){
        Route::get('/schedule-post/posts/{app_id}','index');
        Route::post('/schedule-post/create','store');
        Route::delete('/schedule-post/{id}/delete','destroy');
    });


    Route::controller(TelegramApisController::class)->group(function(){
        Route::get('/telegram/get-channels/{app_id} ','getChannels');
        Route::post('/telegram/set-chat-id/{app_id}','setChatId');
    });
});


Route::controller(AuthsController::class)->group(function () {
    Route::post('/register', 'register');
    Route::post('/login', 'login');
});
