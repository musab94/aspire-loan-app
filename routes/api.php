<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\V1\UserController;
use App\Http\Controllers\V1\LoanController;
use App\Http\Controllers\V1\RepaymentController;

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

Route::group([
    "namespace" => "V1",
    "prefix" => "v1",
], function () {
    /*
    |--------------------------------------------------------------------------
    | USERS
    |--------------------------------------------------------------------------
    */
    Route::group(['prefix' => 'user'], function () {
        Route::post('register', [UserController::class, 'register']);
        Route::post('login', [UserController::class, 'login']);

        Route::group(['middleware' => ['auth:api']], function() {
            Route::get('profile', [UserController::class, 'getProfile']);
            Route::post('apply-loan', [LoanController::class, 'applyLoan']);
            Route::post('repay', [RepaymentController::class, 'repay']);
        });
    });

    Route::group(['prefix' => 'admin', 'middleware' => ['auth:api']], function () {
        Route::post('update-loan-status', [LoanController::class, 'updateLoanStatus']);
    });
});
