<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/login',[AuthController::class,'login']);
Route::post('/register',[AuthController::class,'register']);
Route::get('/verifyuser/{token}', [AuthController::class, 'verifyAccount']); 
Route::get('/logout',[AuthController::class,'logout']);

Route::middleware('auth:sanctum')->controller(AuthController::class)->group(function(){
    Route::get('/userprofile','userProfile');
});

Route::controller(AccountController::class)->group(function(){
    Route::post('/addaccount','addAccount');
    Route::get('/list','show');
    Route::post('/delete/{id}','destory');
    Route::patch('/update/{id}','update');
    Route::get('/getaccount/{id}','index');
});



