<?php

use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use App\Models\WorkoutPlan;
use Carbon\Carbon;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Notification;

test('command runs daily and checks individual user generation days', function () {
    Queue::fake();
    Notification::fake();

    // Set date to Monday
    Carbon::setTestNow(Carbon::parse('2026-01-05')); // Monday

    $user = User::factory()->create();

    // Create subscription
    Subscription::create([
        'user_id' => $user->id,
        'type' => 'beta',
        'status' => 'active',
        'starts_at' => now(),
        'ends_at' => now()->addMonth(),
    ]);

    // Create plan that started on Friday (so generation day = Monday)
    $plan = Plan::create([
        'user_id' => $user->id,
        'plan_name' => 'Test Plan',
        'status' => 'active',
        'start_date' => Carbon::parse('2026-01-03'), // Friday
        'end_date' => now()->addDays(27),
        'duration_days' => 30,
        'daily_calories' => 2000,
        'daily_protein_g' => 150,
        'daily_carbs_g' => 200,
        'daily_fat_g' => 60,
        'workouts_per_week' => 3,
    ]);

    // Command should run successfully (no mid-week check anymore)
    $this->artisan('plans:generate-weekly')
        ->assertSuccessful();

    Carbon::setTestNow();
});

test('command runs with force flag', function () {
    Queue::fake();
    Notification::fake();

    Carbon::setTestNow(Carbon::parse('2026-01-05')); // Monday

    $this->artisan('plans:generate-weekly --force')
        ->assertSuccessful();

    Carbon::setTestNow();
});

test('generates plans for user on their generation day', function () {
    Queue::fake();
    Notification::fake();

    Carbon::setTestNow(Carbon::parse('2026-01-09')); // Thursday

    $user = User::factory()->create();

    // Create subscription
    Subscription::create([
        'user_id' => $user->id,
        'type' => 'beta',
        'status' => 'active',
        'starts_at' => now(),
        'ends_at' => now()->addMonth(),
    ]);

    // Create plan that started Monday (generation day = Thursday)
    $plan = Plan::create([
        'user_id' => $user->id,
        'plan_name' => 'Test Plan',
        'status' => 'active',
        'start_date' => Carbon::parse('2026-01-06'), // Monday
        'end_date' => now()->addDays(27),
        'duration_days' => 30,
        'daily_calories' => 2000,
        'daily_protein_g' => 150,
        'daily_carbs_g' => 200,
        'daily_fat_g' => 60,
        'workouts_per_week' => 3,
    ]);

    // Create existing workout
    WorkoutPlan::create([
        'plan_id' => $plan->id,
        'date' => now()->subDays(2),
        'day_number' => 1,
        'status' => 'generated',
        'workout_name' => 'Day 1',
        'workout_type' => 'strength',
    ]);

    $this->artisan('plans:generate-weekly')
        ->assertSuccessful();

    // Should have queued jobs
    Queue::assertPushed(\App\Jobs\GenerateUserWorkoutPlan::class);
    Queue::assertPushed(\App\Jobs\GenerateUserMealPlan::class);

    Carbon::setTestNow();
});

test('skips user when not their generation day', function () {
    Queue::fake();
    Notification::fake();

    Carbon::setTestNow(Carbon::parse('2026-01-09')); // Thursday

    $user = User::factory()->create();

    Subscription::create([
        'user_id' => $user->id,
        'type' => 'beta',
        'status' => 'active',
        'starts_at' => now(),
        'ends_at' => now()->addMonth(),
    ]);

    // Plan started Friday, so generation day = Monday (not Thursday)
    $plan = Plan::create([
        'user_id' => $user->id,
        'plan_name' => 'Test Plan',
        'status' => 'active',
        'start_date' => Carbon::parse('2026-01-03'), // Friday
        'end_date' => now()->addDays(27),
        'duration_days' => 30,
        'daily_calories' => 2000,
        'daily_protein_g' => 150,
        'daily_carbs_g' => 200,
        'daily_fat_g' => 60,
        'workouts_per_week' => 3,
    ]);

    WorkoutPlan::create([
        'plan_id' => $plan->id,
        'date' => now()->subDays(2),
        'day_number' => 1,
        'status' => 'generated',
        'workout_name' => 'Day 1',
        'workout_type' => 'strength',
    ]);

    $this->artisan('plans:generate-weekly')
        ->expectsOutputToContain('not their generation day')
        ->assertSuccessful();

    // Should NOT have queued jobs
    Queue::assertNothingPushed();

    Carbon::setTestNow();
});

test('skips users without active subscription', function () {
    Queue::fake();
    Notification::fake();

    Carbon::setTestNow(Carbon::parse('2026-01-09')); // Thursday

    $user = User::factory()->create();

    // Create plan but NO subscription
    Plan::create([
        'user_id' => $user->id,
        'plan_name' => 'Test Plan',
        'status' => 'active',
        'start_date' => Carbon::parse('2026-01-06'), // Monday
        'end_date' => now()->addDays(30),
        'duration_days' => 30,
        'daily_calories' => 2000,
        'daily_protein_g' => 150,
        'daily_carbs_g' => 200,
        'daily_fat_g' => 60,
        'workouts_per_week' => 3,
    ]);

    $this->artisan('plans:generate-weekly')
        ->expectsOutput('No users with active subscriptions and plans found.')
        ->assertSuccessful();

    Carbon::setTestNow();
});

test('calculates correct generation day based on plan start date', function () {
    // Test various start dates and their generation days
    $testCases = [
        ['start' => 'Monday', 'expected' => 'Thursday'], // Monday + 3 = Thursday
        ['start' => 'Friday', 'expected' => 'Monday'], // Friday + 3 = Monday (wraps)
        ['start' => 'Wednesday', 'expected' => 'Saturday'], // Wednesday + 3 = Saturday
    ];

    foreach ($testCases as $case) {
        $startDate = Carbon::parse("2026-01-06")->next($case['start']);
        $expectedDay = Carbon::parse("2026-01-06")->next($case['expected']);

        Queue::fake();
        Notification::fake();
        Carbon::setTestNow($expectedDay);

        $user = User::factory()->create();

        Subscription::create([
            'user_id' => $user->id,
            'type' => 'beta',
            'status' => 'active',
            'starts_at' => now(),
            'ends_at' => now()->addMonth(),
        ]);

        $plan = Plan::create([
            'user_id' => $user->id,
            'plan_name' => 'Test Plan',
            'status' => 'active',
            'start_date' => $startDate,
            'end_date' => $startDate->copy()->addDays(30),
            'duration_days' => 30,
            'daily_calories' => 2000,
            'daily_protein_g' => 150,
            'daily_carbs_g' => 200,
            'daily_fat_g' => 60,
            'workouts_per_week' => 3,
        ]);

        WorkoutPlan::create([
            'plan_id' => $plan->id,
            'date' => $startDate,
            'day_number' => 1,
            'status' => 'generated',
            'workout_name' => 'Day 1',
            'workout_type' => 'strength',
        ]);

        $this->artisan('plans:generate-weekly')
            ->assertSuccessful();

        // Should generate on expected day
        Queue::assertPushed(\App\Jobs\GenerateUserWorkoutPlan::class);

        Carbon::setTestNow();
    }
});

test('skips user who already has 7+ days of future plans', function () {
    Queue::fake();
    Notification::fake();

    Carbon::setTestNow(Carbon::parse('2026-01-09')); // Thursday

    $user = User::factory()->create();

    Subscription::create([
        'user_id' => $user->id,
        'type' => 'beta',
        'status' => 'active',
        'starts_at' => now(),
        'ends_at' => now()->addMonth(),
    ]);

    $plan = Plan::create([
        'user_id' => $user->id,
        'plan_name' => 'Test Plan',
        'status' => 'active',
        'start_date' => Carbon::parse('2026-01-06'), // Monday
        'end_date' => now()->addDays(30),
        'duration_days' => 30,
        'daily_calories' => 2000,
        'daily_protein_g' => 150,
        'daily_carbs_g' => 200,
        'daily_fat_g' => 60,
        'workouts_per_week' => 3,
    ]);

    // Create workouts for next 10 days
    for ($i = 0; $i < 10; $i++) {
        WorkoutPlan::create([
            'plan_id' => $plan->id,
            'date' => now()->addDays($i),
            'day_number' => $i + 1,
            'status' => 'generated',
            'workout_name' => "Day {$i}",
            'workout_type' => 'strength',
        ]);
    }

    $this->artisan('plans:generate-weekly')
        ->expectsOutputToContain('already has plans for next week')
        ->assertSuccessful();

    Queue::assertNothingPushed();

    Carbon::setTestNow();
});

test('sends delayed notification when plans are generated', function () {
    Queue::fake();
    Notification::fake();

    Carbon::setTestNow(Carbon::parse('2026-01-09 00:00:00')); // Thursday midnight

    $user = User::factory()->create();

    Subscription::create([
        'user_id' => $user->id,
        'type' => 'beta',
        'status' => 'active',
        'starts_at' => now(),
        'ends_at' => now()->addMonth(),
    ]);

    $plan = Plan::create([
        'user_id' => $user->id,
        'plan_name' => 'Test Plan',
        'status' => 'active',
        'start_date' => Carbon::parse('2026-01-06'), // Monday (gen day = Thursday)
        'end_date' => now()->addDays(27),
        'duration_days' => 30,
        'daily_calories' => 2000,
        'daily_protein_g' => 150,
        'daily_carbs_g' => 200,
        'daily_fat_g' => 60,
        'workouts_per_week' => 3,
    ]);

    WorkoutPlan::create([
        'plan_id' => $plan->id,
        'date' => now()->subDays(2),
        'day_number' => 1,
        'status' => 'generated',
        'workout_name' => 'Day 1',
        'workout_type' => 'strength',
    ]);

    $this->artisan('plans:generate-weekly')
        ->assertSuccessful();

    // Should have sent notification
    Notification::assertSentTo(
        $user,
        \App\Notifications\WeeklyPlansGeneratedNotification::class
    );

    Carbon::setTestNow();
});
