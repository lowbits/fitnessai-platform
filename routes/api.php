<?php

use App\Http\Controllers\Api\V2\AuthController;
use App\Http\Controllers\Api\V2\MealController;
use App\Http\Controllers\Api\V2\OnboardingController;
use App\Http\Controllers\Api\V2\PlanController;
use App\Http\Controllers\Api\V2\SetPasswordRequestTokenController;
use App\Http\Controllers\Api\V2\WorkoutController;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Http\Controllers\NewPasswordController;


Route::prefix('v2')->group(function () {
    Route::post('/onboarding', [OnboardingController::class, 'store'])
        ->middleware('throttle:3,1');

    Route::prefix('auth')->group(function () {
        Route::post('/login', [AuthController::class, 'login'])
            ->middleware('throttle:5,1');

        // Your existing set-password routes
        Route::post('/set-password', [NewPasswordController::class, 'store'])
            ->middleware('throttle:5,1')->name('set-password.request');
        Route::post('/set-password/request', [SetPasswordRequestTokenController::class, 'store'])
            ->middleware('throttle:5,1')->name('set-password.request');

        Route::middleware('auth:sanctum')->group(function () {
            Route::get('/me', [AuthController::class, 'me']);
            Route::post('/logout', [AuthController::class, 'logout']);
            Route::post('/logout-all', [AuthController::class, 'logoutAll']);
            Route::post('/refresh-token', [AuthController::class, 'refreshToken']);
        });
    });

    // Protected routes requiring authentication
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/plan/day/{date}', [PlanController::class, 'getDayPlan']);
        Route::get('/meals/{mealId}', [MealController::class, 'show']);
        Route::get('/workouts/{workoutId}', [WorkoutController::class, 'show']);
    });
});
