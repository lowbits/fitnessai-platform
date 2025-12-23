<?php

use App\Http\Controllers\Api\V2\EmailVerificationController;
use App\Models\Meal;
use App\Models\MealPlan;
use App\Models\User;
use App\Models\WorkoutPlan;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::group(['prefix' => LaravelLocalization::setLocale(), 'middleware' => [ 'localizationRedirect', 'localeSessionRedirect', 'localeViewPath' ]], function()
{
    /** ADD ALL LOCALIZED ROUTES INSIDE THIS GROUP **/
    Route::get('/', function () {
        return Inertia::render('Welcome', [
            'durationDays' => config('plans.duration_days'),
        ]);
    })->name('home');

    Route::get('/imprint', function () {
        return Inertia::render('Imprint');
    })->name('imprint');
});



// Email verification routes
Route::get('/verify-email/{id}/{hash}', [EmailVerificationController::class, 'verify'])
    ->name('verification.verify-onboarding');

Route::get('/api/v2/plans/{planId}/generation-status', [EmailVerificationController::class, 'status'])
    ->name('plans.generation-status');

Route::get('/api/v2/me', function () {
    $user = User::with(['profile', 'plans'])->latest()->first();

    Log::debug("Returning user with id $user->id...");

    if (!$user) {
        return response()->json(['error' => 'No user found in database'], 404);
    }

    $profile = $user->profile;
    $currentPlan = $user->plans()->where('status', 'active')->first();

    return response()->json([
        'user' => [
            'id' => $user->id,
            'email' => $user->email,
            'name' => $user->name,
            'avatar_url' => null,
            'created_at' => $user->created_at->toIso8601String(),
            'email_verified_at' => $user->email_verified_at?->toIso8601String(),
        ],

        'current_plan' => $currentPlan ? [
            'id' => $currentPlan->id,
            'created_at' => $currentPlan->created_at->toIso8601String(),
            'start_date' => $currentPlan->start_date->format('Y-m-d'),
            'current_day' => $currentPlan->current_day,
            'total_days' => config('plans.duration_days'),
            'goal' => $profile?->body_goal?->value ?? 'maintenance',
            'diet_type' => $profile?->diet_type?->value ?? 'balanced',
            'fitness_level' => $profile?->skill_level?->value ?? 'beginner',
            'nutrition_targets' => [
                'daily_calories' => $currentPlan->daily_calories,
                'protein_g' => $currentPlan->daily_protein_g,
                'carbs_g' => $currentPlan->daily_carbs_g,
                'fat_g' => $currentPlan->daily_fat_g,
            ],
        ] : null,

        'subscription' => [
            'status' => 'free',
            'tier' => 'free',
            'started_at' => null,
            'expires_at' => null,
            'will_renew' => false,
            'features' => [
                'full_plan_access' => false,
                'max_days_accessible' => 4,
                'unlimited_regeneration' => false,
                'meal_alternatives' => false,
                'exercise_alternatives' => false,
                'ai_coach' => false,
            ],
        ],

        'stats' => [
            'days_completed' => $currentPlan ? max(0, $currentPlan->current_day - 1) : 0,
            'workouts_completed' => 0,
            'meals_logged' => 0,
            'streak' => 0,
        ],

        'settings' => [
            'notifications_enabled' => true,
            'workout_reminders' => true,
            'meal_reminders' => true,
            'reminder_time' => '08:00',
            'metric_system' => 'metric',
            'language' => 'de',
            'theme' => 'dark',
        ],

        'onboarding' => [
            'completed' => $profile !== null && $currentPlan !== null,
            'steps' => [
                'questionnaire' => $profile !== null,
                'plan_generated' => $currentPlan !== null,
            ],
        ],
    ]);
});

Route::get('/api/v2/plan/day/{date}', function ($date) {
    Log::info("Request for meal plan with date $date...");

    // Get first user with active plan (TODO: use auth()->user())
    $user = User::with(['profile', 'plans'])->latest()->first();

    if (!$user) {
        return response()->json(['error' => 'No user found'], 404);
    }

    $plan = $user->plans()->where('status', 'active')->first();

    if (!$plan) {
        return response()->json(['error' => 'No active plan found'], 404);
    }

    // Parse the date
    $requestDate = Carbon::parse($date);

    // Calculate day number based on plan start date
    $dayOfPlan = $plan->start_date->diffInDays($requestDate) + 1;

    // Check if locked (free users only get first 4 days)
    $userSubscription = 'free'; // TODO: Get from auth()->user()->subscription
    $maxFreeDays = config('plans.duration_days');
    $isLocked = $dayOfPlan > $maxFreeDays;

    if ($isLocked) {
        return response()->json([
            'locked' => true,
            'day_number' => $dayOfPlan,
            'date' => $requestDate->toDateString(),
            'unlock_required' => [
                'title' => '🔒 Day ' . $dayOfPlan . ' Locked',
                'message' => 'Upgrade to Premium to unlock all ' . config('plans.duration_days') . ' days',
                'price' => '€3.99/month',
                'benefits' => [
                    'Access to all ' . config('plans.duration_days') . ' days',
                    'Unlimited plan regeneration',
                    'Meal alternatives',
                    'Exercise substitutions',
                ],
                'cta' => 'Unlock Premium',
            ],
            'preview' => [
                'workout_name' => 'Upper Body Strength',
                'intensity' => 'High',
            ],
        ], 403);
    }

    // Get meal plan for this day
    $mealPlan = MealPlan::with('meals')
        ->where('plan_id', $plan->id)
        ->where('day_number', $dayOfPlan)
        ->first();

    // If no meal plan found or still pending/failed, show message
    if (!$mealPlan) {
        return response()->json([
            'plan_id' => $plan->id,
            'plan_day' => $dayOfPlan,
            'total_days' => config('plans.duration_days'),
            'date' => $requestDate->toDateString(),
            'day_name' => $requestDate->format('l'),
            'locked' => false,
            'status' => 'not_generated',
            'message' => 'Meal plan for this day has not been generated yet. Please check back soon.',
            'meals' => [],
            'workout' => null,
        ]);
    }

    if ($mealPlan->status === 'pending') {
        return response()->json([
            'plan_id' => $plan->id,
            'plan_day' => $dayOfPlan,
            'total_days' => config('plans.duration_days'),
            'date' => $requestDate->toDateString(),
            'day_name' => $requestDate->format('l'),
            'locked' => false,
            'status' => 'generating',
            'message' => 'Your meal plan is being generated. This may take a few moments.',
            'meals' => [],
            'workout' => null,
        ]);
    }

    if ($mealPlan->status === 'failed') {
        return response()->json([
            'plan_id' => $plan->id,
            'plan_day' => $dayOfPlan,
            'total_days' => config('plans.duration_days'),
            'date' => $requestDate->toDateString(),
            'day_name' => $requestDate->format('l'),
            'locked' => false,
            'status' => 'failed',
            'message' => 'Failed to generate meal plan. Please contact support.',
            'meals' => [],
            'workout' => null,
        ], 500);
    }

    // Format meals from database
    $meals = $mealPlan->meals->map(function ($meal) {
        return [
            'id' => $meal->id,
            'name' => $meal->name,
            'type' => ucfirst($meal->type),
            'image' => $meal->image ?? "{$meal->type}_alt", // Fallback to type if no image
            'calories' => $meal->calories,
            'protein_g' => $meal->protein_g,
            'carbs_g' => $meal->carbs_g,
            'fat_g' => $meal->fat_g,
        ];
    })->values()->all();

    // Get workout plan for this day
    $workoutPlan = WorkoutPlan::with('exercises')
        ->where('plan_id', $plan->id)
        ->where('day_number', $dayOfPlan)
        ->first();

    $workout = null;
    if ($workoutPlan && $workoutPlan->status === 'generated') {
        $workout = [
            'id' => $workoutPlan->id,
            'name' => $workoutPlan->workout_name,
            'type' => $workoutPlan->workout_type,
            'description' => $workoutPlan->description,
            'duration_minutes' => $workoutPlan->estimated_duration_minutes,
            'exercises_count' => $workoutPlan->exercises->count(),
            'exercises' => $workoutPlan->exercises->pluck('name'),
            'difficulty' => $workoutPlan->difficulty,
            'muscle_groups' => $workoutPlan->muscle_groups,
            'status' => $workoutPlan->status,
        ];
    } elseif ($workoutPlan && $workoutPlan->status === 'pending') {
        $workout = [
            'status' => 'generating',
            'message' => 'Workout is being generated...',
        ];
    }

    return response()->json([
        'plan_id' => $plan->id,
        'plan_day' => $dayOfPlan,
        'total_days' => config('plans.duration_days'),
        'date' => $requestDate->toDateString(),
        'day_name' => $requestDate->format('l'),
        'locked' => false,
        'status' => 'generated',

        'meals' => $meals,
        'workout' => $workout,

        'daily_totals' => [
            'calories' => $mealPlan->total_calories,
            'protein_g' => $mealPlan->total_protein_g,
            'carbs_g' => $mealPlan->total_carbs_g,
            'fat_g' => $mealPlan->total_fat_g,
        ],

        'stats' => [
            'days_completed' => max(0, $plan->current_day - 1),
            'workouts_completed' => 0, // TODO: Implement workout tracking
            'meals_logged' => 0, // TODO: Implement meal logging
            'streak' => 0, // TODO: Implement streak tracking
        ],
    ]);
});


Route::get('/api/v2/meals/{mealId}', function ($mealId) {
    // Get meal from database
    $meal = Meal::find($mealId);

    if (!$meal) {
        return response()->json(['error' => 'Meal not found'], 404);
    }

    return response()->json([
        'id' => $meal->id,
        'name' => $meal->name,
        'type' => ucfirst($meal->type),
        'image' => $meal->image ?? $meal->type,
        'description' => $meal->description,

        'nutrition' => [
            'calories' => $meal->calories,
            'protein_g' => $meal->protein_g,
            'carbs_g' => $meal->carbs_g,
            'fat_g' => $meal->fat_g,
            'fiber_g' => $meal->fiber_g,
            'sugar_g' => $meal->sugar_g,
        ],

        'ingredients' => $meal->ingredients ?? [],
        'instructions' => $meal->instructions ?? [],

        'prep_time_minutes' => $meal->prep_time_minutes,
        'cook_time_minutes' => $meal->cook_time_minutes,
        'total_time_minutes' => ($meal->prep_time_minutes ?? 0) + ($meal->cook_time_minutes ?? 0),
        'difficulty' => $meal->difficulty ?? 'Medium',
        'servings' => $meal->servings ?? 1,

        'tags' => $meal->tags ?? [],
        'allergens' => $meal->allergens ?? [],
    ]);
});

Route::get('/api/v2/workouts/{workoutId}', function ($workoutId) {
    // Get workout from database with exercises
    $workout = WorkoutPlan::with('exercises')->find($workoutId);

    if (!$workout) {
        return response()->json(['error' => 'Workout not found'], 404);
    }

    // Format exercises
    $exercises = $workout->exercises->map(function ($exercise) {
        return [
            'id' => $exercise->id,
            'order' => $exercise->order,
            'name' => $exercise->name,
            'type' => $exercise->type,
            'description' => $exercise->description,
            'sets' => $exercise->sets,
            'reps' => $exercise->reps,
            'duration_seconds' => $exercise->duration_seconds,
            'rest_seconds' => $exercise->rest_seconds,
            'tempo' => $exercise->tempo,
            'weight_recommendation' => $exercise->weight_recommendation,
            'muscle_groups' => $exercise->muscle_groups ?? [],
            'equipment' => $exercise->equipment ?? [],
            'form_cues' => $exercise->form_cues,
            'alternatives' => $exercise->alternatives ?? [],
            'difficulty' => $exercise->difficulty,
            'video_url' => $exercise->video_url,
            'image' => $exercise->image,
        ];
    })->values()->all();

    return response()->json([
        'id' => $workout->id,
        'name' => $workout->workout_name,
        'type' => $workout->workout_type,
        'description' => $workout->description,
        'estimated_duration_minutes' => $workout->estimated_duration_minutes,
        'estimated_calories_burned' => $workout->estimated_calories_burned,
        'difficulty' => $workout->difficulty,
        'muscle_groups' => $workout->muscle_groups ?? [],
        'exercises' => $exercises,
        'exercises_count' => count($exercises),
    ]);
});
