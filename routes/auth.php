<?php

declare(strict_types=1);

use App\Http\Controllers\Api\Auth\EmailVerificationController;
use App\Http\Controllers\Api\Auth\ForgotPasswordController;
use App\Http\Controllers\Api\Auth\LoginUserController;
use App\Http\Controllers\Api\Auth\LogoutUserController;
use App\Http\Controllers\Api\Auth\RegisterUserController;
use App\Http\Controllers\Api\Auth\ResentEmailVerificationController;
use App\Http\Controllers\Api\Auth\ResetPasswordController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => 'guest'], function () {
    Route::post('/forgot-password', ForgotPasswordController::class)->name('password.forgot');
    Route::post('/reset-password', ResetPasswordController::class)->name('password.reset');
    Route::post('/register', RegisterUserController::class)->name('register');

    Route::post('/register', RegisterUserController::class)
        ->name('register');

    Route::post('/login', LoginUserController::class)
        ->name('login');

    Route::post('/register', RegisterUserController::class)
        ->middleware('guest')
        ->name('register');

    Route::get('/verify-email/{id}/{hash}', EmailVerificationController::class)
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');
});

Route::group(
    ['middleware' => 'auth:sanctum'],
    function () {

        Route::post('/email/verification/resend', ResentEmailVerificationController::class)
            ->middleware(['throttle:6,1'])
            ->name('verification.send');

        Route::post('/logout', LogoutUserController::class)
            ->name('logout');
    }
);
