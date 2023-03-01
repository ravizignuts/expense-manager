<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\AccountUserController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\TransactionController;
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
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::controller(AuthController::class)->prefix('auth')->group(function(){
    Route::post('register','register');
    Route::post('login','login');
    Route::post('logout','logout')->middleware('auth:sanctum');
});

Route::middleware('auth:sanctum')->group(function(){
    Route::controller(AccountController::class)->prefix('account')->group(function(){
        Route::post('add','add');
        Route::put('edit','edit');
        Route::delete('delete/{id}','delete');
        Route::get('list','list');
        Route::get('get/{id}','get');
    });
    Route::controller(AccountUserController::class)->prefix('accountuser')->group(function(){
        Route::post('add','add');
        Route::post('edit/{id}','edit');
        Route::post('delete/{$id}','delete');
        Route::post('list','list');
        Route::get('get/{id}','get');
    });
    Route::controller(TransactionController::class)->prefix('transaction')->group(function(){
        Route::post('add','add');
        Route::post('edit','edit');
        Route::post('delete','delete');
    });
});
