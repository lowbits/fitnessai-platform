<?php

use App\Http\Controllers\Api\V2\AuthController;
use App\Http\Controllers\Api\V2\BodyProgressController;
use App\Http\Controllers\Api\V2\CalorieTrackingController;
use App\Http\Controllers\Api\V2\GetMealAlternativesController;
use App\Http\Controllers\Api\V2\MealController;
use App\Http\Controllers\Api\V2\NewPasswordController;
use App\Http\Controllers\Api\V2\OnboardingController;
use App\Http\Controllers\Api\V2\PlanController;
use App\Http\Controllers\Api\V2\PushNotificationController;
use App\Http\Controllers\Api\V2\ReplaceMealController;
use App\Http\Controllers\Api\V2\RescheduleWorkoutController;
use App\Http\Controllers\Api\V2\ResendVerificationEmailController;
use App\Http\Controllers\Api\V2\SetPasswordRequestTokenController;
use App\Http\Controllers\Api\V2\SkipWorkoutController;
use App\Http\Controllers\Api\V2\WorkoutController;
use App\Http\Controllers\Api\V2\Workouts\AddWorkoutExerciseController;
use App\Http\Controllers\Api\V2\Workouts\RemoveWorkoutExerciseController;
use App\Http\Controllers\Api\V2\Workouts\ReorderWorkoutExercisesController;
use App\Http\Controllers\Api\V2\Workouts\ReplaceWorkoutExerciseController;
use App\Http\Controllers\Api\V2\Workouts\UpdateWorkoutExerciseController;
use App\Http\Controllers\Api\V2\WorkoutTrackingController;
use App\Http\Controllers\Api\V3\AddMealController;
use App\Http\Controllers\Api\V3\Auth\LoginController;
use App\Http\Controllers\Api\V3\Auth\SignupController;
use App\Http\Controllers\Api\V3\AuthController as V3AuthController;
use App\Http\Controllers\Api\V3\CoachController;
use App\Http\Controllers\Api\V3\CoachGreetingController;
use App\Http\Controllers\Api\V3\FoodController;
use App\Http\Controllers\Api\V3\VisionController;
use App\Http\Controllers\Api\V3\MealAlternativesController;
use App\Http\Controllers\Api\V3\MobileOnboardingController;
use App\Http\Controllers\Api\V3\PlanDayController;
use App\Http\Controllers\Api\V3\PlanWeekController;
use App\Http\Controllers\Api\V3\ProfileController;
use App\Http\Controllers\Api\V3\RecipeFavoriteController;
use App\Http\Controllers\Api\V3\RecipeSuggestionsController;
use App\Http\Controllers\Api\V3\StatsController;
use Illuminate\Support\Facades\Route;

Route::prefix('v3')->group(function () {
    Route::post('/onboarding', [MobileOnboardingController::class, 'store'])
        ->middleware('throttle:3,1');

    Route::prefix('auth')->group(function () {
        Route::post('/signup', SignupController::class)->middleware('throttle:3,1');
        Route::post('/login', LoginController::class)->middleware('throttle:5,1');

        Route::middleware('auth:sanctum')->group(function () {
            Route::get('/me', [V3AuthController::class, 'me']);
        });
    });

    Route::get('/recipes/suggestions', RecipeSuggestionsController::class)
        ->middleware('throttle:30,1');

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/stats', StatsController::class);
        Route::get('/plan/day/{date}', PlanDayController::class);
        Route::get('/plan/week/{date?}', PlanWeekController::class);
        Route::patch('/profile', [ProfileController::class, 'update']);
        Route::get('/recipes/favorites', [RecipeFavoriteController::class, 'index']);
        Route::post('/recipes/{recipe:id}/favorite', [RecipeFavoriteController::class, 'store']);
        Route::delete('/recipes/{recipe:id}/favorite', [RecipeFavoriteController::class, 'destroy']);
        Route::post('/meals', AddMealController::class)
            ->name('v3.meals.add');
        Route::post('/meals/{meal}/alternatives', MealAlternativesController::class)
            ->name('v3.meals.alternatives');
        Route::post('/coach/messages', CoachController::class)
            ->name('v3.coach.messages');
        Route::get('/coach/greeting', CoachGreetingController::class)
            ->name('v3.coach.greeting');
        Route::post('/foods', [FoodController::class, 'store'])
            ->name('v3.foods.store');
        Route::get('/foods/recent', [FoodController::class, 'recent'])
            ->name('v3.foods.recent');
        Route::get('/foods/{barcode}', [FoodController::class, 'show'])
            ->whereNumber('barcode')
            ->middleware('throttle:30,1')
            ->name('v3.foods.show');
        Route::post('/nutrition-label', [VisionController::class, 'label'])
            ->middleware('throttle:20,1')->name('v3.vision.label');
        Route::post('/meal-photos', [VisionController::class, 'meal'])
            ->middleware('throttle:20,1')->name('v3.vision.meal');
    });
});

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
            Route::post('/resend-verification-email', ResendVerificationEmailController::class)
                ->middleware('throttle:3,1')
                ->name('api.verification.send');
        });
    });

    // Protected routes requiring authentication
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/plan/day/{date}', [PlanController::class, 'getDayPlan']);
        Route::get('/meals/{meal}', [MealController::class, 'show']);
        Route::post('/meals/{meal}/alternatives', GetMealAlternativesController::class)->name('meals.alternatives');
        Route::post('/meals/{meal}/replace', ReplaceMealController::class)->name('meals.replace');
        Route::delete('/meals/{meal}', [MealController::class, 'destroy'])->name('meals.destroy');
        Route::get('/workouts/{workoutId}', [WorkoutController::class, 'show']);
        Route::post('/workouts/{workout}/reschedule', RescheduleWorkoutController::class);
        Route::post('/workouts/{workoutPlan}/skip', SkipWorkoutController::class)->name('workouts.skip');
        Route::post('/workouts/{workout}/exercises', AddWorkoutExerciseController::class)->name('workouts.exercises.add');
        Route::put('/workouts/{workout}/exercises/reorder', ReorderWorkoutExercisesController::class)->name('workouts.exercises.reorder');
        Route::put('/workouts/{workout}/exercises/{exerciseId}', UpdateWorkoutExerciseController::class)->name('workouts.exercises.update');
        Route::put('/workouts/{workout}/exercises/{exerciseId}/replace', ReplaceWorkoutExerciseController::class)->name('workouts.exercises.replace');
        Route::delete('/workouts/{workout}/exercises/{exerciseId}', RemoveWorkoutExerciseController::class)->name('workouts.exercises.remove');

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
