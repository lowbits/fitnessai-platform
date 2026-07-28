<?php

use App\Models\CalorieTracking;
use App\Models\Meal;
use App\Models\MealPlan;
use App\Models\Plan;
use App\Models\User;
use App\Models\WorkoutTracking;
use App\Services\StreakService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function streakUser(): User
{
    return User::factory()->create();
}

function activePlan(User $user, int $goal = 2000): Plan
{
    return Plan::factory()->create([
        'user_id' => $user->id,
        'status' => 'active',
        'daily_calories' => $goal,
        'start_date' => today()->subDays(60)->toDateString(),
    ]);
}

function logFoodOn(User $user, string $date, int $kcal = 100): void
{
    CalorieTracking::factory()->create([
        'user_id' => $user->id,
        'meal_id' => null,
        'tracked_date' => $date,
        'calories' => $kcal,
    ]);
}

function completeWorkoutOn(User $user, string $date): void
{
    WorkoutTracking::factory()->create(['user_id' => $user->id, 'completed_at' => $date]);
}

/**
 * Plan `$count` recommended meals for a day and track `$tracked` of them.
 */
function planAndTrackMeals(User $user, Plan $plan, string $date, int $count, int $tracked, int $kcalEach = 400): void
{
    $mealPlan = MealPlan::factory()->create(['plan_id' => $plan->id, 'date' => $date]);
    $meals = Meal::factory()->count($count)->create(['meal_plan_id' => $mealPlan->id, 'calories' => $kcalEach]);

    $meals->take($tracked)->each(fn (Meal $meal) => CalorieTracking::factory()->create([
        'user_id' => $user->id,
        'meal_id' => $meal->id,
        'tracked_date' => $date,
        'calories' => $meal->calories,
    ]));
}

function daysAgo(int $n): string
{
    return today()->subDays($n)->toDateString();
}

function streak(User $user): array
{
    return app(StreakService::class)->for($user);
}

it('returns a zero streak and no tier without activity', function () {
    expect(streak(streakUser()))->toMatchArray(['current' => 0, 'longest' => 0, 'tier' => 'none', 'score' => 0]);
});

it('earns the spark shimmer from the first active day', function () {
    $user = streakUser();
    logFoodOn($user, daysAgo(0));

    expect(streak($user))->toMatchArray(['current' => 1, 'score' => 1, 'tier' => 'spark']);
});

it('reaches bronze at a three-day streak', function () {
    $user = streakUser();
    logFoodOn($user, daysAgo(0));
    logFoodOn($user, daysAgo(1));
    logFoodOn($user, daysAgo(2));

    expect(streak($user))->toMatchArray(['score' => 3, 'tier' => 'bronze']);
});

it('counts consecutive active days ending today', function () {
    $user = streakUser();
    logFoodOn($user, daysAgo(0));
    logFoodOn($user, daysAgo(1));
    logFoodOn($user, daysAgo(2));

    expect(streak($user)['current'])->toBe(3);
});

it('keeps the streak alive when today has no activity yet (grace)', function () {
    $user = streakUser();
    logFoodOn($user, daysAgo(1));
    logFoodOn($user, daysAgo(2));

    expect(streak($user)['current'])->toBe(2);
});

it('breaks the current streak on a missed day', function () {
    $user = streakUser();
    logFoodOn($user, daysAgo(0));
    logFoodOn($user, daysAgo(2));
    logFoodOn($user, daysAgo(3));

    expect(streak($user)['current'])->toBe(1);
});

it('counts a day active by a workout alone', function () {
    $user = streakUser();
    completeWorkoutOn($user, daysAgo(0));
    completeWorkoutOn($user, daysAgo(1));

    expect(streak($user)['current'])->toBe(2);
});

it('reports the longest run across history', function () {
    $user = streakUser();
    foreach ([10, 11, 12, 13] as $day) {
        logFoodOn($user, daysAgo($day));
    }
    logFoodOn($user, daysAgo(0));
    logFoodOn($user, daysAgo(1));

    expect(streak($user))->toMatchArray(['current' => 2, 'longest' => 4]);
});

it('marks a day perfect when the calorie goal is met on a rest day', function () {
    $user = streakUser();
    activePlan($user, goal: 2000);
    logFoodOn($user, daysAgo(0), kcal: 2000); // goal met, no planned workout → perfect
    logFoodOn($user, daysAgo(1), kcal: 500);  // active but goal missed → not perfect

    expect(streak($user))->toMatchArray([
        'current' => 2,
        'perfect_days' => 1,
        'score' => 4,        // 2 active + 1 perfect * 2
        'tier' => 'bronze',  // score >= 3
    ]);
});

it('weights perfect days so a quality streak outranks a bare one', function () {
    $bare = streakUser();
    $quality = streakUser();
    activePlan($quality, goal: 2000);
    foreach (range(0, 4) as $day) {
        logFoodOn($bare, daysAgo($day), kcal: 500);       // 5 active, 0 perfect → score 5
        logFoodOn($quality, daysAgo($day), kcal: 2000);   // 5 active, 5 perfect → score 15
    }

    expect(streak($bare)['score'])->toBe(5)
        ->and(streak($quality)['score'])->toBe(15)
        ->and(streak($quality)['tier'])->toBe('silver');
});

it('marks a day perfect when every recommended meal is tracked despite calories under goal', function () {
    $user = streakUser();
    $plan = activePlan($user, goal: 2000);
    // 3 planned meals totalling 1200 kcal — well under the 2000 goal, but all tracked.
    planAndTrackMeals($user, $plan, daysAgo(0), count: 3, tracked: 3, kcalEach: 400);

    expect(streak($user))->toMatchArray(['current' => 1, 'perfect_days' => 1]);
});

it('does not mark a day perfect when a recommended meal is missing and calories fall short', function () {
    $user = streakUser();
    $plan = activePlan($user, goal: 2000);
    planAndTrackMeals($user, $plan, daysAgo(0), count: 3, tracked: 2, kcalEach: 400);

    expect(streak($user))->toMatchArray(['current' => 1, 'perfect_days' => 0]);
});

it('does not complete a day when meals are skipped and calories fall short', function () {
    $user = streakUser();
    $plan = activePlan($user, goal: 2000);
    $mealPlan = MealPlan::factory()->create(['plan_id' => $plan->id, 'date' => daysAgo(0)]);

    $breakfast = Meal::factory()->create(['meal_plan_id' => $mealPlan->id, 'type' => 'breakfast', 'calories' => 400]);
    $lunch = Meal::factory()->create(['meal_plan_id' => $mealPlan->id, 'type' => 'lunch', 'calories' => 800]);
    $dinner = Meal::factory()->create(['meal_plan_id' => $mealPlan->id, 'type' => 'dinner', 'calories' => 800]);

    // Eat breakfast, then skip (soft-delete) lunch + dinner.
    CalorieTracking::factory()->create([
        'user_id' => $user->id,
        'meal_id' => $breakfast->id,
        'tracked_date' => daysAgo(0),
        'calories' => 400,
    ]);
    $lunch->delete();
    $dinner->delete();

    // Skipping the other slots must not let a 400-kcal day count as perfect.
    expect(streak($user))->toMatchArray(['perfect_days' => 0]);
});

it('marks a day perfect when custom calories land within tolerance below goal', function () {
    $user = streakUser();
    activePlan($user, goal: 2000);
    logFoodOn($user, daysAgo(0), kcal: 1950); // 2.5% under goal → within tolerance

    expect(streak($user))->toMatchArray(['current' => 1, 'perfect_days' => 1]);
});

it('does not mark a day perfect when custom calories fall outside tolerance', function () {
    $user = streakUser();
    activePlan($user, goal: 2000);
    logFoodOn($user, daysAgo(0), kcal: 1800); // 10% under goal → outside 5% tolerance

    expect(streak($user))->toMatchArray(['current' => 1, 'perfect_days' => 0]);
});

it('returns the last seven days with calorie progress and perfect flags', function () {
    $user = streakUser();
    activePlan($user, goal: 2000);
    logFoodOn($user, daysAgo(0), kcal: 2000); // 100% + perfect
    logFoodOn($user, daysAgo(1), kcal: 1000); // 50%

    $week = streak($user)['week'];

    expect($week)->toHaveCount(7)
        ->and($week[6])->toMatchArray(['date' => daysAgo(0), 'progress' => 1.0, 'complete' => true])
        ->and($week[5])->toMatchArray(['date' => daysAgo(1), 'progress' => 0.5, 'complete' => false]);
});
