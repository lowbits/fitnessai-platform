<?php

use App\Http\Controllers\Api\V2\EmailVerificationController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::group(['prefix' => LaravelLocalization::setLocale(), 'middleware' => ['localizationRedirect', 'localeSessionRedirect', 'localeViewPath']], function () {
    /** ADD ALL LOCALIZED ROUTES INSIDE THIS GROUP **/
    Route::get('/', function () {
        return Inertia::render('Welcome', [
            'durationDays' => (int)config('plans.duration_days'),
        ]);
    })->name('home');


    Route::get(LaravelLocalization::transRoute('routes.data_privacy'), function () {
        return Inertia::render('Legal/DataPrivacy');
    })->name('data-privacy');

    Route::get(LaravelLocalization::transRoute('routes.terms'), function () {
        return Inertia::render('Legal/Terms');
    })->name('terms');

    Route::get(LaravelLocalization::transRoute('routes.disclaimer'), function () {
        return Inertia::render('Legal/Disclaimer');
    })->name('disclaimer');

    Route::get(LaravelLocalization::transRoute('routes.imprint'), function () {
        return Inertia::render('Legal/Imprint');
    })->name('imprint');

    // Public Workout Plan Pages (SEO-optimized)
    Route::get(LaravelLocalization::transRoute('routes.workout_plans_index'), [App\Http\Controllers\WorkoutPlanController::class, 'index'])
        ->name('workout-plan.index');

    Route::get(LaravelLocalization::transRoute('routes.workout_plans_type'), [App\Http\Controllers\WorkoutPlanController::class, 'show'])
        ->name('workout-plan.show');
});

// Email verification routes
Route::get('/{locale}/verify-email/{id}/{hash}', [EmailVerificationController::class, 'verify'])
    ->middleware(['signed'])
    ->name('verification.verify-onboarding');


// Set password landing page (for email links + universal links)
Route::get('/set-password', function () {
    $token = request()->query('token', '');
    $email = request()->query('email', '');

    return Inertia::render('SetPassword', [
        'iosAppStoreUrl' => config('app.app_store.ios.url'),
        'token' => $token,
        'email' => $email,
        'utmSource' => request()->query('utm_source'),
        'utmMedium' => request()->query('utm_medium'),
        'utmCampaign' => request()->query('utm_campaign'),
    ]);
})->middleware(['signed'])
    ->name('set-password');


// Dev routes (only available in local environment)
if (app()->environment('local')) {
    Route::get('/dev/test-email/plan-ready', function () {
        $user = \App\Models\User::first();

        if (!$user) {
            return 'No user found in database. Please create a user first.';
        }

        $plan = \App\Models\Plan::where('user_id', $user->id)->first();

        if (!$plan) {
            return 'No plan found for user. Please create a plan first.';
        }

        $notification = new \App\Notifications\PlanGenerationComplete($plan, 'too');

        return $notification->toMail($user)->render();
    })->name('dev.test-email.plan-ready');
}

