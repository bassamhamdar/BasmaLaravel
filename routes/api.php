<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\AuthController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::group(['prefix' => 'customer'], function(){
        Route::post('/register', [CustomerController::class, 'register']);
});

Route::group(['prefix' => 'admin'], function(){
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::group(['middleware' => ['jwt.admin']], function() {
        Route::get('/customers/count', [CustomerController::class, 'count']);
        Route::post('/customers/fetch', [CustomerController::class, 'fetch']);
        Route::post('/logout', [AuthController::class, 'logout']);
    });
});
