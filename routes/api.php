<?php

use App\Http\Controllers\Api\V2\AuthController;
use App\Http\Controllers\Api\V2\BodyProgressController;
use App\Http\Controllers\Api\V2\CalorieTrackingController;
use App\Http\Controllers\Api\V2\MealController;
use App\Http\Controllers\Api\V2\OnboardingController;
use App\Http\Controllers\Api\V2\PlanController;
use App\Http\Controllers\Api\V2\PushNotificationController;
use App\Http\Controllers\Api\V2\SetPasswordRequestTokenController;
use App\Http\Controllers\Api\V2\WorkoutController;
use App\Http\Controllers\Api\V2\WorkoutTrackingController;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Http\Controllers\NewPasswordController;


Route::prefix('v2')->group(function () {
    Route::post('/onboarding', [OnboardingController::class, 'store'])
        ->middleware('throttle:3,1');

    Route::prefix('auth')->group(function () {
        Route::post('/login', [AuthController::class, 'login'])
            ->middleware('throttle:5,1');

        Route::post('/set-password', [NewPasswordController::class, 'store'])
            ->middleware('throttle:5,1')->name('api.set-password');
        Route::post('/set-password/request', [SetPasswordRequestTokenController::class, 'store'])
            ->middleware('throttle:5,1')->name('api.set-password.request');

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

        // Push notification routes
        Route::prefix('notifications')->group(function () {
            Route::post('/register-token', [PushNotificationController::class, 'registerToken']);
            Route::delete('/remove-token', [PushNotificationController::class, 'removeToken']);
            Route::get('/token-status', [PushNotificationController::class, 'getTokenStatus']);

            // Test notification - only in local/dev environment
            if (app()->environment(['local', 'development'])) {
                Route::post('/test', [PushNotificationController::class, 'sendTestNotification']);
            }
        });

        // Workout tracking routes
        Route::prefix('track')->group(function () {
            Route::get('/workouts', [WorkoutTrackingController::class, 'index']);
            Route::post('/workouts', [WorkoutTrackingController::class, 'store']);
            Route::get('/workouts/{workoutTracking}', [WorkoutTrackingController::class, 'show']);
            Route::put('/workouts/{workoutTracking}', [WorkoutTrackingController::class, 'update']);
            Route::delete('/workouts/{workoutTracking}', [WorkoutTrackingController::class, 'destroy']);

            // Calorie tracking routes
            Route::get('/calories', [CalorieTrackingController::class, 'index']);
            Route::post('/calories', [CalorieTrackingController::class, 'store']);
            Route::get('/calories/{calorieTracking}', [CalorieTrackingController::class, 'show']);
            Route::put('/calories/{calorieTracking}', [CalorieTrackingController::class, 'update']);
            Route::delete('/calories/{calorieTracking}', [CalorieTrackingController::class, 'destroy']);

            // Body progress tracking routes
            Route::get('/body-progress', [BodyProgressController::class, 'index']);
            Route::get('/body-progress/latest', [BodyProgressController::class, 'latest']);
            Route::post('/body-progress', [BodyProgressController::class, 'store']);
            Route::put('/body-progress/{id}', [BodyProgressController::class, 'update']);
            Route::delete('/body-progress/{id}', [BodyProgressController::class, 'destroy']);
        });
    });
});
