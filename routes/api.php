<?php

use Illuminate\Support\Facades\Route;

// Common Auth Controller
use App\Http\Controllers\API\Common\AuthController;
// Customer Loan Controller
use App\Http\Controllers\API\Customer\LoanController;
// Customer Loan Controller
use App\Http\Controllers\API\Admin\LoanController as AdminLoanController;

//Common routes
Route::post('customer/register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

Route::group(['middleware' => ['auth:api']], function () {
    Route::get('details', [AuthController::class, 'userDetailsById']);
    Route::get('logout', [AuthController::class, 'logout']);
});


// Customer and Admin both can access this routes
Route::group(['prefix' => 'customer'], function () {

    // customer loan add and listing belong to him
    Route::group(['middleware' => ['auth:api']], function () {
        Route::group(['prefix' => 'loan'], function () {
            Route::get('/list', [LoanController::class, 'list']);
            Route::post('/add', [LoanController::class, 'add']);
        });

        // customer loan repayment add
        Route::group(['prefix' => 'loan-repayment'], function () {
            Route::post('/add/{loan_repayment_id}', [LoanController::class, 'addRepayment']);
        });
    });
});

// Only admin can access this routes
Route::group(['prefix' => 'admin', 'middleware' => ['auth:api', AdminCheck::class]], function () {
    // all loan listing and loan approve
    Route::group(['prefix' => 'loan'], function () {
        Route::get('/list', [AdminLoanController::class, 'list']);
        Route::get('/approve/{loan_id}', [AdminLoanController::class, 'approveLoan']);
    });
});
