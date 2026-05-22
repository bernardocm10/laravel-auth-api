<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\EmailVerificationController;
use App\Http\Controllers\PasswordController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::middleware('throttle:10,1')->group(function () {
        Route::post('register', [AuthController::class, 'register']);
        Route::post('login', [AuthController::class, 'login']);
    });

    Route::middleware('throttle:5,1')->group(function () {
        Route::post('forgot-password', [PasswordController::class, 'forgot']);
        Route::post('reset-password', [PasswordController::class, 'reset']);
    });

    Route::get('email/verify/{id}/{hash}', [EmailVerificationController::class, 'verify'])
        ->name('verification.verify')
        ->middleware('signed');

    // Grupo autenticado — ValidateTokenVersion garante que JWTs emitidos antes
    // de uma troca de senha sejam rejeitados, mesmo ainda dentro do TTL.
    Route::middleware(['auth:api', 'token.version'])->group(function () {

        // Rotas de sessão — não exigem e-mail verificado (usuário precisa
        // conseguir sair e reenviar verificação mesmo sem ter verificado).
        Route::middleware('throttle:10,1')->group(function () {
            Route::post('refresh', [AuthController::class, 'refresh']);
        });
        Route::post('logout', [AuthController::class, 'logout'])->middleware('throttle:60,1');
        Route::post('email/resend', [EmailVerificationController::class, 'resend'])->middleware('throttle:6,1');

        // Rotas protegidas — exigem e-mail verificado para evitar acesso com
        // contas não confirmadas (endereço falso ou de terceiros).
        Route::middleware('email.verified')->group(function () {
            Route::middleware('throttle:60,1')->group(function () {
                Route::get('me', [AuthController::class, 'me']);
                Route::put('profile', [ProfileController::class, 'update']);
            });
            Route::put('password', [PasswordController::class, 'update'])->middleware('throttle:10,1');
        });
    });
});
