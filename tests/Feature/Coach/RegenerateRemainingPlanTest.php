<?php

use App\Actions\RegenerateRemainingPlan;
use App\Jobs\GenerateUserMealPlan;
use App\Jobs\GenerateUserWorkoutPlan;
use App\Models\Meal;
use App\Models\MealPlan;
use App\Models\Plan;
use App\Models\User;
use App\Models\UserProfile;
use App\Models\WorkoutPlan;
use App\Models\WorkoutPlanExercise;
use App\Models\WorkoutTracking;
use Illuminate\Support\Facades\Bus;

it('rebuilds future untouched days and preserves past, eaten and tracked days', function () {
    Bus::fake();

    $user = User::factory()->create();
    UserProfile::factory()->create(['user_id' => $user->id, 'weight_kg' => 80, 'height_cm' => 180]);

    // start 2 days ago, duration 28 → today is day 3
    $start = today()->subDays(2);
    $plan = Plan::factory()->create([
        'user_id' => $user->id,
        'status' => 'active',
        'start_date' => $start,
        'duration_days' => 28,
        'daily_calories' => 9999,
        'generation_completed_at' => now(),
    ]);
    $dateFor = fn (int $day) => $start->copy()->addDays($day - 1);

    $pastMeal = MealPlan::factory()->create(['plan_id' => $plan->id, 'day_number' => 1, 'date' => $dateFor(1), 'status' => 'generated']);
    $futurePlain = MealPlan::factory()->create(['plan_id' => $plan->id, 'day_number' => 4, 'date' => $dateFor(4), 'status' => 'generated']);
    $futureEaten = MealPlan::factory()->create(['plan_id' => $plan->id, 'day_number' => 5, 'date' => $dateFor(5), 'status' => 'generated']);
    Meal::factory()->create(['meal_plan_id' => $futurePlain->id, 'completed_at' => null]);
    Meal::factory()->create(['meal_plan_id' => $futureEaten->id, 'completed_at' => now()]);

    $futureWorkout = WorkoutPlan::factory()->create(['plan_id' => $plan->id, 'day_number' => 4, 'date' => $dateFor(4), 'status' => 'generated']);
    WorkoutPlanExercise::factory()->create(['workout_plan_id' => $futureWorkout->id]);
    $trackedWorkout = WorkoutPlan::factory()->create(['plan_id' => $plan->id, 'day_number' => 6, 'date' => $dateFor(6), 'status' => 'generated']);
    WorkoutTracking::factory()->create(['workout_plan_id' => $trackedWorkout->id]);

    $expectedKcal = $user->profile->getMetabolismData()['daily_calories'];

    $summary = app(RegenerateRemainingPlan::class)->execute($user, $plan);

    expect($summary)->toMatchArray(['from_day' => 4, 'meal_days' => 1, 'workout_days' => 1]);

    expect($futurePlain->refresh()->status)->toBe('pending')
        ->and($futurePlain->meals()->count())->toBe(0)
        ->and($futureEaten->refresh()->status)->toBe('generated')
        ->and($pastMeal->refresh()->status)->toBe('generated');

    expect($futureWorkout->refresh()->status)->toBe('pending')
        ->and($futureWorkout->exercises()->count())->toBe(0)
        ->and($trackedWorkout->refresh()->status)->toBe('generated');

    expect((int) $plan->refresh()->daily_calories)->toBe($expectedKcal)
        ->and((int) $plan->daily_calories)->not->toBe(9999)
        ->and($plan->generation_completed_at)->toBeNull();

    // only reset days are regenerated: day 4 was the furthest reset, today is 3 → window of 1
    Bus::assertDispatched(GenerateUserMealPlan::class, fn ($job) => $job->maxDays === 1);
    Bus::assertDispatched(GenerateUserWorkoutPlan::class, fn ($job) => $job->maxDays === 1);
});

it('dispatches nothing when there are no future days to rebuild', function () {
    Bus::fake();

    $user = User::factory()->create();
    UserProfile::factory()->create(['user_id' => $user->id, 'weight_kg' => 80, 'height_cm' => 180]);
    $plan = Plan::factory()->create([
        'user_id' => $user->id, 'status' => 'active',
        'start_date' => today(), 'duration_days' => 28,
    ]);

    $summary = app(RegenerateRemainingPlan::class)->execute($user, $plan);

    expect($summary)->toMatchArray(['meal_days' => 0, 'workout_days' => 0]);
    Bus::assertNotDispatched(GenerateUserMealPlan::class);
    Bus::assertNotDispatched(GenerateUserWorkoutPlan::class);
});
