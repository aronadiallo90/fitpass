<?php

use Illuminate\Support\Facades\Route;

Route::prefix('v1')->name('api.')->group(function () {

    // Routes publiques (pas d'auth)
    Route::post('auth/login', [\App\Http\Controllers\Api\Auth\AuthController::class, 'login'])
        ->middleware('throttle:auth')
        ->name('auth.login');

    Route::post('auth/register', [\App\Http\Controllers\Api\Auth\AuthController::class, 'register'])
        ->middleware('throttle:auth')
        ->name('auth.register');

    Route::get('gyms', [\App\Http\Controllers\Api\GymController::class, 'index'])->name('gyms.index');
    Route::get('gyms/geojson', [\App\Http\Controllers\Api\GymController::class, 'geojson'])->name('gyms.geojson');
    Route::get('gyms/{gym}', [\App\Http\Controllers\Api\GymController::class, 'show'])->name('gyms.show');

    // Validation QR code (bornes autonomes — token statique par salle)
    Route::post('checkins/validate', [\App\Http\Controllers\Api\CheckinController::class, 'store'])
        ->middleware('throttle:60,1')
        ->name('checkins.validate');

    // Webhook PayTech (pas d'auth Sanctum, pas de CSRF)
    Route::post('webhooks/paytech', [\App\Http\Controllers\Api\Webhook\PayTechController::class, 'store'])
        ->name('webhooks.paytech');

    // Routes authentifiées (Sanctum)
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('auth/logout', [\App\Http\Controllers\Api\Auth\AuthController::class, 'logout'])->name('auth.logout');
        Route::get('auth/me', [\App\Http\Controllers\Api\Auth\AuthController::class, 'me'])->name('auth.me');

        Route::apiResource('subscriptions', \App\Http\Controllers\Api\SubscriptionController::class)
            ->only(['index', 'store', 'show']);

        Route::get('payments', [\App\Http\Controllers\Api\PaymentController::class, 'index'])->name('payments.index');
        Route::get('checkins', [\App\Http\Controllers\Api\CheckinController::class, 'index'])->name('checkins.index');
    });
});
