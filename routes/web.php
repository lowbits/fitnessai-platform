<?php

use App\Http\Controllers\AboutController;
use App\Http\Controllers\Api\V2\EmailVerificationController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\CalorieCalculatorController;
use App\Http\Controllers\DownloadAppController;
use App\Http\Controllers\WorkoutPlanController;
use App\Models\Plan;
use App\Models\User;
use App\Notifications\PlanGenerationComplete;
use App\Services\QrCodeService;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::group(['prefix' => LaravelLocalization::setLocale(), 'middleware' => ['localizationRedirect', 'localeSessionRedirect', 'localeViewPath']], function () {
    /** ADD ALL LOCALIZED ROUTES INSIDE THIS GROUP **/
    Route::get('/', function () {
        $locale = app()->getLocale();
        $articles = config("blog.{$locale}", []);

        $blogPosts = collect($articles)->take(3)->map(fn ($article, $slug) => [
            'title' => $article['h1'],
            'description' => $article['description'],
            'url' => "/{$locale}/blog/{$slug}",
            'image' => $article['og_image'] ?? null,
            'imageAlt' => $article['og_image_alt'] ?? $article['h1'],
        ])->values()->all();

        return Inertia::render('Welcome', [
            'durationDays' => (int) config('plans.duration_days'),
            'blogPosts' => $blogPosts,
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

    // About
    Route::get(LaravelLocalization::transRoute('routes.about'), AboutController::class)
        ->name('about');

    // Calorie Calculator (SEO tool page)
    Route::get(LaravelLocalization::transRoute('routes.free_tools_calorie_calculator'), CalorieCalculatorController::class)
        ->name('calorie-calculator');

    // Blog
    Route::get(LaravelLocalization::transRoute('routes.blog_index'), [BlogController::class, 'index'])
        ->name('blog.index');

    Route::get(LaravelLocalization::transRoute('routes.blog_article'), [BlogController::class, 'show'])
        ->name('blog.show');

    // Public Workout Plan Pages (SEO-optimized)
    Route::get(LaravelLocalization::transRoute('routes.workout_plans_index'), [WorkoutPlanController::class, 'index'])
        ->name('workout-plan.index');

    Route::get(LaravelLocalization::transRoute('routes.workout_plans_type'), [WorkoutPlanController::class, 'show'])
        ->name('workout-plan.show');
});

// Email verification routes
Route::get('/verify-email', [EmailVerificationController::class, 'verify'])
    ->middleware(['signed'])
    ->name('verification.verify-onboarding');

// Download app landing page (signed URL when user-specific, plain when generic)
Route::get('/{locale}/app', DownloadAppController::class)
    ->name('download-app');

// Set password landing page (for email links + universal links)
Route::get('/set-password', function () {
    $token = request()->query('token', '');
    $email = request()->query('email', '');

    $isMobile = (bool) preg_match('/Mobile|Android|iPhone|iPad|iPod/i', request()->userAgent() ?? '');
    $appStoreUrl = config('app.app_store.ios.url');
    $appStoreQrCode = $isMobile ? null : app(QrCodeService::class)->generate($appStoreUrl);

    return Inertia::render('SetPassword', [
        'iosAppStoreUrl' => $appStoreUrl,
        'iosAppStoreQrCode' => $appStoreQrCode,
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
        $user = User::first();

        if (! $user) {
            return 'No user found in database. Please create a user first.';
        }

        $plan = Plan::where('user_id', $user->id)->first();

        if (! $plan) {
            return 'No plan found for user. Please create a plan first.';
        }

        $notification = new PlanGenerationComplete($plan, 'too');

        return $notification->toMail($user)->render();
    })->name('dev.test-email.plan-ready');
}
