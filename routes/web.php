<?php

use App\Http\Controllers\AboutController;
use App\Http\Controllers\Api\V2\EmailVerificationController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\CalorieCalculatorController;
use App\Http\Controllers\DownloadAppController;
use App\Http\Controllers\GlossaryController;
use App\Http\Controllers\MacroCalculatorController;
use App\Http\Controllers\NewsletterController;
use App\Http\Controllers\PlanRoastController;
use App\Http\Controllers\WorkoutPlanController;
use App\Models\Plan;
use App\Models\User;
use App\Notifications\PlanGenerationComplete;
use App\Services\QrCodeService;
use Barryvdh\DomPDF\Facade\Pdf;
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

    // Macro Calculator (SEO tool page)
    Route::get(LaravelLocalization::transRoute('routes.free_tools_macro_calculator'), [MacroCalculatorController::class, 'index'])
        ->name('macro-calculator');

    // Plan Roast (free SEO tool: rate/roast a training plan)
    Route::get(LaravelLocalization::transRoute('routes.free_tools_plan_roast'), [PlanRoastController::class, 'index'])
        ->name('plan-roast');

    // Fitness Glossary (SEO hub: explains the terms our plans use)
    Route::get(LaravelLocalization::transRoute('routes.glossary'), [GlossaryController::class, 'index'])
        ->name('glossary');

    // Blog
    Route::get(LaravelLocalization::transRoute('routes.blog_index'), [BlogController::class, 'index'])
        ->name('blog.index');

    Route::get(LaravelLocalization::transRoute('routes.blog_article'), [BlogController::class, 'show'])
        ->name('blog.show');

    // Transactional Landing Pages
    Route::get(LaravelLocalization::transRoute('routes.landing_free_workout_meal_plan'), function () {
        if (app()->getLocale() !== 'en') {
            abort(404);
        }

        return Inertia::render('Landing/FreeWorkoutAndMealPlan', [
            'durationDays' => (int) config('plans.duration_days'),
        ]);
    })->name('landing.free-workout-meal-plan');

    Route::get(LaravelLocalization::transRoute('routes.landing_personal_meal_plan'), function () {
        if (app()->getLocale() !== 'de') {
            abort(404);
        }

        return Inertia::render('Landing/PersonalMealPlan', [
            'durationDays' => (int) config('plans.duration_days'),
        ]);
    })->name('landing.personal-meal-plan');

    Route::get(LaravelLocalization::transRoute('routes.landing_ai_workout_plan_generator'), function () {
        if (app()->getLocale() !== 'en') {
            abort(404);
        }

        return Inertia::render('Landing/AiWorkoutPlanGenerator', [
            'durationDays' => (int) config('plans.duration_days'),
        ]);
    })->name('landing.ai-workout-plan-generator');

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

// Newsletter double opt-in confirmation (signed link). Subscribe is POST /api/newsletter/subscribe.
Route::get('/newsletter/confirm/{subscriber}', [NewsletterController::class, 'confirm'])
    ->name('newsletter.confirm');

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

    // Preview the v2 workout PDF for the most recent plan that has workouts.
    Route::get('/dev/pdf/workout-v2', function () {
        app()->setLocale(request()->query('locale', 'de'));

        $plan = Plan::whereHas('workoutPlans')->latest()->firstOrFail();

        return Pdf::loadView('pdf.workout_plan_v2', [
            'user' => $plan->user,
            'plan' => $plan,
            'workoutPlans' => $plan->workoutPlans()->with('exercises.exercise.translations')->orderBy('day_number')->get(),
        ])->stream('Workout_Plan_v2.pdf');
    })->name('dev.pdf.workout-v2');
}
