<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\AccountUserController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\UserController;
use App\Models\Account;
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
|->middleware('verified');
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::controller(AuthController::class)->prefix('auth')->group(function(){
    Route::post('register','register');
    Route::post('login','login');
    Route::post('logout',[AuthController::class,'logout'])->middleware('auth:sanctum');
    Route::patch('verifyuser/{token}','verifyuser');
    Route::prefix('password')->group(function(){
        Route::patch('change','change')->middleware('auth:sanctum');
        Route::patch('forgot-password-mail','sendForgotpasswordMail');
        Route::patch('verifytoken','verifyPasswordResetToken');
        Route::patch('reset','reset');
    });
});

Route::middleware('auth:sanctum')->group(function(){
    Route::controller(UserController::class)->prefix('user')->group(function(){
        Route::get('profile','profile');
        Route::get('list','list');
    });
    Route::controller(AccountController::class)->prefix('account')->group(function(){
        Route::get('list','list');
        Route::post('add','add');
        Route::put('edit','edit');
        Route::delete('delete/{id}','delete');
        Route::get('get/{id}','get');
        Route::get('restore/{id}','restore');
    });
    Route::controller(AccountUserController::class)->prefix('accountuser')->group(function(){
        Route::get('list','list');
        Route::post('add','add');
        Route::put('edit/{id}','edit');
        Route::delete('delete/{id}','delete');
        Route::get('get/{id}','get');
        Route::get('restore/{id}','restore');
    });
    Route::controller(TransactionController::class)->prefix('transaction')->group(function(){
        Route::get('list','list');
        Route::post('add','add');
        Route::put('edit/{id}','edit');
        Route::delete('delete/{id}','delete');
        Route::get('get/{id}','get');
        Route::get('restore/{id}','restore');
    });
});
