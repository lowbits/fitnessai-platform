<?php

use App\Ai\Agents\WorkoutProgrammerAgent;
use App\Jobs\GenerateMealPlanBatch;
use App\Jobs\GenerateUserMealPlan;
use App\Jobs\GenerateUserWorkoutPlan;
use App\Models\MealPlan;
use App\Models\Plan;
use App\Models\User;
use App\Models\WorkoutPlan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;

uses(RefreshDatabase::class);

test('complete plan exits early without calling AI', function () {
    WorkoutProgrammerAgent::fake();

    $user = User::factory()->withProfile()->create();

    $plan = Plan::factory()->create([
        'user_id' => $user->id,
        'duration_days' => 7,
        'workouts_per_week' => 4,
    ]);

    for ($day = 1; $day <= 7; $day++) {
        WorkoutPlan::factory()->create([
            'plan_id' => $plan->id,
            'day_number' => $day,
            'status' => 'generated',
        ]);
    }

    $job = new GenerateUserWorkoutPlan($user, $plan);
    $job->handle();

    expect(WorkoutPlan::where('plan_id', $plan->id)->count())->toBe(7)
        ->and(WorkoutPlan::where('plan_id', $plan->id)->where('day_number', 8)->exists())->toBeFalse();

    WorkoutProgrammerAgent::assertNotPrompted(fn () => true);
});

test('failed middle day is retried when later days are generated', function () {
    WorkoutProgrammerAgent::fake(['Fake workout response']);

    $user = User::factory()->withProfile()->create();

    $plan = Plan::factory()->create([
        'user_id' => $user->id,
        'duration_days' => 7,
        'workouts_per_week' => 7,
    ]);

    // Days 1-4 generated, day 5 failed, days 6-7 generated
    for ($day = 1; $day <= 7; $day++) {
        WorkoutPlan::factory()->create([
            'plan_id' => $plan->id,
            'day_number' => $day,
            'status' => $day === 5 ? 'failed' : 'generated',
        ]);
    }

    $job = new GenerateUserWorkoutPlan($user, $plan);
    $job->handle();

    // Day 5 was attempted (agent was called), not skipped as "already complete"
    WorkoutProgrammerAgent::assertPrompted(fn () => true);

    // Days 6-7 should remain generated (not re-attempted)
    expect(WorkoutPlan::where('plan_id', $plan->id)->where('day_number', 6)->first()->status)->toBe('generated')
        ->and(WorkoutPlan::where('plan_id', $plan->id)->where('day_number', 7)->first()->status)->toBe('generated');
});

test('pending middle day is retried when later days are generated', function () {
    WorkoutProgrammerAgent::fake(['Fake workout response']);

    $user = User::factory()->withProfile()->create();

    $plan = Plan::factory()->create([
        'user_id' => $user->id,
        'duration_days' => 7,
        'workouts_per_week' => 7,
    ]);

    // Days 1-4 generated, day 5 pending, days 6-7 generated
    for ($day = 1; $day <= 7; $day++) {
        WorkoutPlan::factory()->create([
            'plan_id' => $plan->id,
            'day_number' => $day,
            'status' => $day === 5 ? 'pending' : 'generated',
        ]);
    }

    $job = new GenerateUserWorkoutPlan($user, $plan);
    $job->handle();

    // Day 5 was attempted via AI, not left as pending
    WorkoutProgrammerAgent::assertPrompted(fn () => true);

    $day5 = WorkoutPlan::where('plan_id', $plan->id)->where('day_number', 5)->first();
    expect($day5->status)->not->toBe('pending');
});

test('missing day records are detected and generated', function () {
    WorkoutProgrammerAgent::fake(['Fake workout response']);

    $user = User::factory()->withProfile()->create();

    $plan = Plan::factory()->create([
        'user_id' => $user->id,
        'duration_days' => 7,
        'workouts_per_week' => 7,
    ]);

    // Only days 1, 3, 5, 7 generated (gaps at 2, 4, 6)
    foreach ([1, 3, 5, 7] as $day) {
        WorkoutPlan::factory()->create([
            'plan_id' => $plan->id,
            'day_number' => $day,
            'status' => 'generated',
        ]);
    }

    $job = new GenerateUserWorkoutPlan($user, $plan);
    $job->handle();

    // Missing days should have been created
    $totalDays = WorkoutPlan::where('plan_id', $plan->id)->count();
    expect($totalDays)->toBe(7);

    // The AI was called for the missing days
    WorkoutProgrammerAgent::assertPrompted(fn () => true);
});

test('partial generation continues from correct day', function () {
    WorkoutProgrammerAgent::fake(['Fake workout response']);

    $user = User::factory()->withProfile()->create();

    $plan = Plan::factory()->create([
        'user_id' => $user->id,
        'duration_days' => 30,
        'workouts_per_week' => 7,
    ]);

    // Days 1-7 generated
    for ($day = 1; $day <= 7; $day++) {
        WorkoutPlan::factory()->create([
            'plan_id' => $plan->id,
            'day_number' => $day,
            'status' => 'generated',
        ]);
    }

    $job = new GenerateUserWorkoutPlan($user, $plan);
    $job->handle();

    // Days 8-14 should have been created (7 days at a time)
    expect(WorkoutPlan::where('plan_id', $plan->id)->where('day_number', 8)->exists())->toBeTrue()
        ->and(WorkoutPlan::where('plan_id', $plan->id)->where('day_number', 14)->exists())->toBeTrue()
        ->and(WorkoutPlan::where('plan_id', $plan->id)->where('day_number', 15)->exists())->toBeFalse();
});

test('meal plan retry dispatches batches when middle day failed', function () {
    Queue::fake();

    $user = User::factory()->withProfile()->create();

    $plan = Plan::factory()->create([
        'user_id' => $user->id,
        'duration_days' => 7,
    ]);

    // Day 2 failed, all others generated
    for ($day = 1; $day <= 7; $day++) {
        MealPlan::factory()->create([
            'plan_id' => $plan->id,
            'day_number' => $day,
            'status' => $day === 2 ? 'failed' : 'generated',
        ]);
    }

    // Reset failed → pending (like the retry command does)
    MealPlan::where('plan_id', $plan->id)->where('status', 'failed')->update(['status' => 'pending']);

    $job = new GenerateUserMealPlan($user, $plan);
    $job->handle();

    // Should dispatch batch jobs for the incomplete day, not skip as complete
    Queue::assertPushed(GenerateMealPlanBatch::class);
});

test('meal plan skips generation when all days are generated', function () {
    Queue::fake();

    $user = User::factory()->withProfile()->create();

    $plan = Plan::factory()->create([
        'user_id' => $user->id,
        'duration_days' => 7,
    ]);

    for ($day = 1; $day <= 7; $day++) {
        MealPlan::factory()->create([
            'plan_id' => $plan->id,
            'day_number' => $day,
            'status' => 'generated',
        ]);
    }

    $job = new GenerateUserMealPlan($user, $plan);
    $job->handle();

    Queue::assertNotPushed(GenerateMealPlanBatch::class);
});
