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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/login',[AuthController::class,'login']);
Route::post('/register',[AuthController::class,'register']);
Route::get('/verifyuser/{token}', [AuthController::class, 'verifyAccount']); 
Route::get('/logout',[AuthController::class,'logout']);


Route::post('/forgetpassword',[AuthController::class,'forgetPassword']);
Route::get('/resetPassword',[AuthController::class,'resetPasswordMessage']);
Route::post('/resetPassword',[AuthController::class,'resetPassword']);



//Route::get('/userprofile',[AuthController::class,'userProfile'])->middleware('auth:sanctum');
//Route::post('/changepassword',[AuthController::class,'changePassword'])->middleware('auth:sanctum');


// Route::controller(AccountController::class)->group(function(){
//     Route::post('/addaccount','addAccount');
//     Route::get('/list','show');
//     Route::post('/delete/{id}','destory');
//     Route::patch('/update/{id}','update');
//     Route::get('/getaccount/{id}','index');
// });


// Route::controller(AccountUserController::class)->group(function(){
//     Route::post('/addaccountuser','addAccountUser');
//     Route::get('/listaccountuser','accountUserShow');
//     Route::post('/accountuserdelete/{id}','destory');
//     Route::patch('/accountuserupdate/{id}','update');
//     Route::get('/getaccountuser/{id}','index');
// });


// Route::controller(TransactionController::class)->group(function(){
//     Route::post('/addtransaction','addTransaction');
//     Route::get('/listtransaction','listTransaction');
//     Route::post('/transactiondelete/{id}','destory');
//     Route::patch('/transactionupdate/{id}','update');
//     Route::get('/gettransaction/{id}','index');
// });



Route::middleware(['auth:sanctum'])->group(function(){
    
    Route::get('/userprofile',[AuthController::class,'userProfile']);

    Route::post('/changepassword',[AuthController::class,'changePassword']);

    
    Route::prefix('account')->group(function(){
        Route::controller(AccountController::class)->group(function(){
                Route::post('/addaccount','addAccount');
                Route::get('/list','show');
                Route::post('/delete/{id}','destory');
                Route::patch('/update/{id}','update');
                Route::get('/getaccount/{id}','index');
            });
        });
    
    Route::prefix('accountuser')->group(function(){
        Route::controller(AccountUserController::class)->group(function(){
            Route::post('/addaccountuser','addAccountUser');
            Route::get('/listaccountuser','accountUserShow');
            Route::post('/accountuserdelete/{id}','destory');
            Route::patch('/accountuserupdate/{id}','update');
            Route::get('/getaccountuser/{id}','index');
        });
    });

    Route::prefix('transaction')->group(function(){
        Route::controller(TransactionController::class)->group(function(){
            Route::post('/addtransaction','addTransaction');
            Route::get('/listtransaction','listTransaction');
            Route::post('/transactiondelete/{id}','destory');
            Route::patch('/transactionupdate/{id}','update');
            Route::get('/gettransaction/{id}','index');
        });
    });
    
});


