<?php

use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Carbon;
use Inertia\Inertia;
use Laravel\Fortify\Features;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canRegister' => Features::enabled(Features::registration()),
    ]);
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/api/v2/me', function () {
    $user = User::with(['profile', 'plans'])->first();

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
            'total_days' => 28,
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
    log::info("Request with date $date...");

    // Parse the date
    $requestDate = Carbon::parse($date);

    // Plan configuration
    $planStartDate = Carbon::parse('2025-12-18');
    $dayOfPlan = $planStartDate->diffInDays($requestDate) + 1;

    // Check if locked (free users only get first 14 days)
    $userSubscription = 'free'; // TODO: Get from auth()->user()
    $maxFreeDays = 4;
    $isLocked = $dayOfPlan > $maxFreeDays;

    if ($isLocked) {
        return response()->json([
            'locked' => true,
            'day_number' => $dayOfPlan,
            'date' => $requestDate->toDateString(),
            'unlock_required' => [
                'title' => '🔒 Day ' . $dayOfPlan . ' Locked',
                'message' => 'Upgrade to Premium to unlock all 28 days',
                'price' => '€3.99/month',
                'benefits' => [
                    'Access to all 28 days',
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

    // Calculate which day of week (0 = Monday, 6 = Sunday)
    $dayOfWeek = $requestDate->dayOfWeekIso - 1; // 0-6

    // Meal rotation (7-day cycle)
    $mealCycle = [
        // Monday
        [
            'breakfast' => ['name' => 'Protein Oatmeal', 'image' => 'breakfast', 'calories' => 520, 'protein' => 35, 'carbs' => 65, 'fat' => 12],
            'lunch' => ['name' => 'Grilled Chicken Bowl', 'image' => 'lunch', 'calories' => 650, 'protein' => 55, 'carbs' => 70, 'fat' => 18],
            'snack' => ['name' => 'Schoko-Mandel-Proteinballs', 'image' => 'snack', 'calories' => 120, 'protein' => 12, 'carbs' => 45, 'fat' => 12],
            'dinner' => ['name' => 'Salmon & Sweet Potato', 'image' => 'dinner', 'calories' => 680, 'protein' => 48, 'carbs' => 75, 'fat' => 22],
        ],
        // Tuesday
        [
            'breakfast' => ['name' => 'Greek Yogurt Parfait', 'image' => 'breakfast', 'calories' => 480, 'protein' => 32, 'carbs' => 58, 'fat' => 14],
            'lunch' => ['name' => 'Turkey Wrap', 'image' => 'lunch', 'calories' => 620, 'protein' => 48, 'carbs' => 68, 'fat' => 16],
            'snack' => ['name' => 'Schoko-Mandel-Proteinballs', 'image' => 'snack', 'calories' => 120, 'protein' => 12, 'carbs' => 45, 'fat' => 12],
            'dinner' => ['name' => 'Beef Stir Fry', 'image' => 'dinner_alt', 'calories' => 720, 'protein' => 52, 'carbs' => 78, 'fat' => 24],
        ],
        // Wednesday
        [
            'breakfast' => ['name' => 'Scrambled Eggs & Toast', 'image' => 'breakfast', 'calories' => 510, 'protein' => 30, 'carbs' => 62, 'fat' => 16],
            'lunch' => ['name' => 'Tuna Salad', 'image' => 'lunch', 'calories' => 580, 'protein' => 45, 'carbs' => 55, 'fat' => 18],
            'snack' => ['name' => 'Schoko-Mandel-Proteinballs', 'image' => 'snack', 'calories' => 120, 'protein' => 12, 'carbs' => 45, 'fat' => 12],
            'dinner' => ['name' => 'Chicken Pasta', 'image' => 'dinner_alt', 'calories' => 740, 'protein' => 50, 'carbs' => 85, 'fat' => 20],
        ],
        // Thursday
        [
            'breakfast' => ['name' => 'Protein Pancakes', 'image' => 'breakfast', 'calories' => 500, 'protein' => 33, 'carbs' => 60, 'fat' => 14],
            'lunch' => ['name' => 'Chicken Caesar Salad', 'image' => 'lunch', 'calories' => 630, 'protein' => 50, 'carbs' => 52, 'fat' => 22],
            'snack' => ['name' => 'Schoko-Mandel-Proteinballs', 'image' => 'snack', 'calories' => 120, 'protein' => 12, 'carbs' => 45, 'fat' => 12],
            'dinner' => ['name' => 'Grilled Fish Tacos', 'image' => 'breakfast', 'calories' => 690, 'protein' => 46, 'carbs' => 72, 'fat' => 20],
        ],
        // Friday
        [
            'breakfast' => ['name' => 'Avocado Toast & Eggs', 'image' => 'breakfast', 'calories' => 530, 'protein' => 28, 'carbs' => 58, 'fat' => 22],
            'lunch' => ['name' => 'Quinoa Bowl', 'image' => 'breakfast', 'calories' => 600, 'protein' => 42, 'carbs' => 72, 'fat' => 16],
            'snack' => ['name' => 'Schoko-Mandel-Proteinballs', 'image' => 'snack', 'calories' => 120, 'protein' => 12, 'carbs' => 45, 'fat' => 12],
            'dinner' => ['name' => 'Steak & Veggies', 'image' => 'dinner', 'calories' => 750, 'protein' => 55, 'carbs' => 68, 'fat' => 26],
        ],
        // Saturday
        [
            'breakfast' => ['name' => 'Breakfast Burrito', 'image' => 'breakfast_alt', 'calories' => 550, 'protein' => 35, 'carbs' => 65, 'fat' => 18],
            'lunch' => ['name' => 'Poke Bowl', 'image' => 'lunch_alt', 'calories' => 620, 'protein' => 48, 'carbs' => 70, 'fat' => 16],
            'snack' => ['name' => 'Schoko-Mandel-Proteinballs', 'image' => 'snack_alt', 'calories' => 120, 'protein' => 12, 'carbs' => 45, 'fat' => 12],
            'dinner' => ['name' => 'BBQ Chicken', 'image' => 'dinner_alt', 'calories' => 700, 'protein' => 52, 'carbs' => 74, 'fat' => 20],
        ],
        // Sunday
        [
            'breakfast' => ['name' => 'Protein French Toast', 'image' => 'breakfast', 'calories' => 520, 'protein' => 32, 'carbs' => 68, 'fat' => 14],
            'lunch' => ['name' => 'Mediterranean Bowl', 'image' => 'lunch', 'calories' => 640, 'protein' => 45, 'carbs' => 75, 'fat' => 18],
            'snack' => ['name' => 'Schoko-Mandel-Proteinballs', 'image' => 'snack', 'calories' => 120, 'protein' => 12, 'carbs' => 45, 'fat' => 12],
            'dinner' => ['name' => 'Salmon Teriyaki', 'image' => 'dinner', 'calories' => 680, 'protein' => 50, 'carbs' => 72, 'fat' => 20],
        ],
    ];

    // Workout split (7-day cycle: Upper/Rest/Lower/Rest/Push/Rest/Pull)
    $workoutSplit = [
        ['name' => 'Upper Body Strength', 'duration' => 45, 'exercises' => 8],
        null, // Rest
        ['name' => 'Lower Body Power', 'duration' => 50, 'exercises' => 7],
        null, // Rest
        ['name' => 'Push Day', 'duration' => 45, 'exercises' => 8],
        null, // Rest
        ['name' => 'Pull Day', 'duration' => 45, 'exercises' => 8],
    ];

    $todayMeals = $mealCycle[$dayOfWeek];

    Log::debug("TODAY MEALS", $todayMeals);
    $todayWorkout = $workoutSplit[$dayOfWeek];

    // Format meals
    $meals = [];
    Log::debug("Day of playn $dayOfPlan...");
    foreach (['breakfast', 'lunch', 'snack', 'dinner'] as $type) {
        $meal = $todayMeals[$type];
        $meals[] = [
            'id' => 'meal_' . $dayOfPlan . '_' . $type,
            'name' => $meal['name'],
            'type' => ucfirst($type),
            'image' => $meal['image'],
            'calories' => $meal['calories'],
            'protein_g' => $meal['protein'],
            'carbs_g' => $meal['carbs'],
            'fat_g' => $meal['fat'],
        ];
    }

    // Format workout
    $workout = null;
    if ($todayWorkout !== null) {
        $workout = [
            'id' => 'workout_' . $dayOfPlan,
            'name' => $todayWorkout['name'],
            'type' => 'strength',
            'duration_minutes' => $todayWorkout['duration'],
            'exercises_count' => $todayWorkout['exercises'],
            'difficulty' => 'intermediate',
        ];
    }

    return response()->json([
        'plan_id' => 'plan_' . uniqid(),
        'plan_day' => $dayOfPlan,
        'total_days' => 28,
        'date' => $requestDate->toDateString(),
        'day_name' => $requestDate->format('l'),
        'locked' => false,

        'meals' => $meals,
        'workout' => $workout,

        'stats' => [
            'days_completed' => min($dayOfPlan - 1, 2),
            'workouts_completed' => 3,
            'meals_logged' => 12,
            'streak' => 2,
        ],
    ]);
});


Route::get('/api/v2/meals/{mealId}', function ($mealId) {
    // Parse meal ID to extract info (format: meal_3_breakfast)
    // For now, return mockup data

    $meals = [
        'meal_3_breakfast' => [
            'id' => 'meal_3_breakfast',
            'name' => 'Scrambled Eggs & Toast',
            'type' => 'Breakfast',
            'image' => 'breakfast',
            'description' => 'High-protein breakfast with whole grain toast, perfect for starting your day with sustained energy.',

            'nutrition' => [
                'calories' => 510,
                'protein_g' => 30,
                'carbs_g' => 62,
                'fat_g' => 16,
                'fiber_g' => 8,
                'sugar_g' => 6,
            ],

            'ingredients' => [
                ['name' => 'Eggs (large)', 'amount' => '3', 'unit' => 'pcs'],
                ['name' => 'Whole grain bread', 'amount' => '2', 'unit' => 'slices'],
                ['name' => 'Butter', 'amount' => '1', 'unit' => 'tbsp'],
                ['name' => 'Milk', 'amount' => '50', 'unit' => 'ml'],
                ['name' => 'Cherry tomatoes', 'amount' => '100', 'unit' => 'g'],
                ['name' => 'Fresh spinach', 'amount' => '50', 'unit' => 'g'],
                ['name' => 'Salt & pepper', 'amount' => 'to taste', 'unit' => ''],
            ],

            'instructions' => [
                'Toast the whole grain bread until golden brown.',
                'Crack eggs into a bowl, add milk, salt, and pepper. Whisk well.',
                'Heat butter in a non-stick pan over medium heat.',
                'Add spinach and cook until wilted, about 1 minute.',
                'Pour in egg mixture and gently scramble for 2-3 minutes.',
                'Serve scrambled eggs on toast with cherry tomatoes on the side.',
            ],

            'prep_time_minutes' => 5,
            'cook_time_minutes' => 10,
            'total_time_minutes' => 15,
            'difficulty' => 'Easy',
            'servings' => 1,

            'tags' => ['High-Protein', 'Quick', 'Vegetarian'],
            'allergens' => ['Eggs', 'Dairy', 'Gluten'],
        ],

        'meal_3_lunch' => [
            'id' => 'meal_3_lunch',
            'name' => 'Tuna Salad',
            'type' => 'Lunch',
            'image' => 'lunch',
            'description' => 'Light and refreshing tuna salad packed with protein and healthy fats.',

            'nutrition' => [
                'calories' => 580,
                'protein_g' => 45,
                'carbs_g' => 55,
                'fat_g' => 18,
                'fiber_g' => 12,
                'sugar_g' => 8,
            ],

            'ingredients' => [
                ['name' => 'Canned tuna (in water)', 'amount' => '200', 'unit' => 'g'],
                ['name' => 'Mixed salad greens', 'amount' => '150', 'unit' => 'g'],
                ['name' => 'Quinoa (cooked)', 'amount' => '100', 'unit' => 'g'],
                ['name' => 'Cherry tomatoes', 'amount' => '100', 'unit' => 'g'],
                ['name' => 'Cucumber', 'amount' => '100', 'unit' => 'g'],
                ['name' => 'Red onion', 'amount' => '30', 'unit' => 'g'],
                ['name' => 'Olive oil', 'amount' => '1', 'unit' => 'tbsp'],
                ['name' => 'Lemon juice', 'amount' => '2', 'unit' => 'tbsp'],
                ['name' => 'Salt & pepper', 'amount' => 'to taste', 'unit' => ''],
            ],

            'instructions' => [
                'Drain the tuna and place in a large bowl.',
                'Add cooked quinoa to the bowl.',
                'Chop cherry tomatoes, cucumber, and red onion.',
                'Add all vegetables to the bowl with salad greens.',
                'Drizzle with olive oil and lemon juice.',
                'Season with salt and pepper, toss well and serve.',
            ],

            'prep_time_minutes' => 10,
            'cook_time_minutes' => 0,
            'total_time_minutes' => 10,
            'difficulty' => 'Easy',
            'servings' => 1,

            'tags' => ['High-Protein', 'Quick', 'Low-Carb', 'Gluten-Free'],
            'allergens' => ['Fish'],
        ],

        'meal_3_dinner' => [
            'id' => 'meal_3_dinner',
            'name' => 'Chicken Pasta',
            'type' => 'Dinner',
            'image' => 'dinner',
            'description' => 'Satisfying pasta dish with grilled chicken breast and a light tomato sauce.',

            'nutrition' => [
                'calories' => 740,
                'protein_g' => 50,
                'carbs_g' => 85,
                'fat_g' => 20,
                'fiber_g' => 10,
                'sugar_g' => 9,
            ],

            'ingredients' => [
                ['name' => 'Chicken breast', 'amount' => '200', 'unit' => 'g'],
                ['name' => 'Whole wheat pasta', 'amount' => '100', 'unit' => 'g'],
                ['name' => 'Crushed tomatoes', 'amount' => '200', 'unit' => 'g'],
                ['name' => 'Garlic cloves', 'amount' => '2', 'unit' => 'pcs'],
                ['name' => 'Fresh basil', 'amount' => '10', 'unit' => 'g'],
                ['name' => 'Olive oil', 'amount' => '1', 'unit' => 'tbsp'],
                ['name' => 'Parmesan cheese', 'amount' => '20', 'unit' => 'g'],
                ['name' => 'Salt & pepper', 'amount' => 'to taste', 'unit' => ''],
            ],

            'instructions' => [
                'Cook pasta according to package instructions. Drain and set aside.',
                'Season chicken breast with salt and pepper.',
                'Heat olive oil in a pan and cook chicken for 6-7 minutes per side.',
                'Remove chicken and let rest. Slice when ready.',
                'In the same pan, sauté minced garlic until fragrant.',
                'Add crushed tomatoes and simmer for 5 minutes.',
                'Add cooked pasta to the sauce and toss well.',
                'Serve pasta topped with sliced chicken, basil, and parmesan.',
            ],

            'prep_time_minutes' => 10,
            'cook_time_minutes' => 20,
            'total_time_minutes' => 30,
            'difficulty' => 'Medium',
            'servings' => 1,

            'tags' => ['High-Protein', 'Comfort-Food'],
            'allergens' => ['Gluten', 'Dairy'],
        ],
    ];

    $meal = $meals[$mealId] ?? $meals['meal_3_breakfast'];

    return response()->json($meal);
});

require __DIR__ . '/settings.php';
