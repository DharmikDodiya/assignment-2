<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\AccountUserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\UserController;
use App\Models\AccountUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

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



Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::get('/verifyuser/{token}', [AuthController::class, 'verifyAccount']);



Route::post('/forgetpassword', [AuthController::class, 'forgetPassword']);
Route::get('/resetPassword', [AuthController::class, 'resetPasswordMessage']);
Route::post('/resetPassword', [AuthController::class, 'resetPassword']);


Route::middleware(['auth:sanctum'])->group(function () {

    Route::controller(UserController::class)->group(function(){
        Route::get('/userprofile',  'userProfile');
        Route::post('/changepassword',  'changePassword');
        Route::get('/logout','logout');
        Route::get('/get/{id}','get');
    });

    Route::controller(AccountController::class)->prefix('account')->group(function () {
        Route::get('/list', 'list');
        Route::post('/create', 'create');
        Route::patch('/update/{id}', 'update');
        Route::get('/get/{id}', 'get');
        Route::post('/delete/{id}', 'destory');
    });

    Route::controller(AccountUserController::class)->prefix('accountuser')->group(function () {
        Route::get('/list', 'list');
        Route::post('/create', 'create');
        Route::patch('/update/{id}', 'update');
        Route::get('/get/{id}', 'get');
        Route::post('/delete/{id}', 'destory');
    });


    Route::controller(TransactionController::class)->prefix('transaction')->group(function () {
        Route::get('/list', 'list');
        Route::post('/create', 'create');
        Route::patch('/update/{id}', 'update');
        Route::get('/get/{id}', 'get');
        Route::post('/delete/{id}', 'destory');
    });

   
});
