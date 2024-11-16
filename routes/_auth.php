<?php

use App\Http\Controllers\API\Auth\AuthController;
use App\Http\Controllers\API\Auth\EmailVerificationController;
use App\Http\Controllers\API\Auth\ForgotPasswordController;
use App\Http\Controllers\API\Auth\LoginController;
use App\Http\Controllers\API\Auth\RegisterController;
use Illuminate\Support\Facades\Route;

Route::middleware("api")->group(function () {
    Route::post('/register', RegisterController::class);
    Route::post('/login', LoginController::class);

    //EMAIL VARIFY
    Route::post('/email-verification', [EmailVerificationController::class, 'send'])
        ->middleware('throttle:emails');
    Route::post('/email-verify', [EmailVerificationController::class, 'verify']);

    //FORGOT PASSWORD
    Route::post('/forgot-password', [ForgotPasswordController::class, 'send_password_reset_email'])
        ->middleware('throttle:emails');
    Route::post('/forgot-password-verify', [ForgotPasswordController::class, 'verify_reset_token']);
    Route::post('/reset-password', [ForgotPasswordController::class, 'reset_password']);
});

//AUTHENTICATED ROUTES
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/check-token', [AuthController::class, 'checkToken']);
    Route::post('/logout', [AuthController::class, 'logout']);
});
