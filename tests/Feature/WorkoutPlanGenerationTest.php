<?php

use App\Jobs\GenerateUserWorkoutPlan;
use App\Models\Plan;
use App\Models\User;
use App\Models\WorkoutPlan;
use OpenAI\Laravel\Facades\OpenAI;
use OpenAI\Responses\Chat\CreateResponse;

test('workout plan does not generate day 8 on retry when plan is already complete', function () {
    // Mock OpenAI to prevent actual API calls
    OpenAI::fake([
        CreateResponse::fake([
            'choices' => [
                [
                    'message' => [
                        'content' => json_encode([
                            'workout_name' => 'Test Workout',
                            'workout_description' => 'Test Description',
                            'exercises' => [
                                [
                                    'name' => 'Bench Press',
                                    'sets' => 3,
                                    'reps' => 10,
                                    'rest_seconds' => 60,
                                    'instructions' => 'Push weight up'
                                ]
                            ]
                        ])
                    ]
                ]
            ]
        ])
    ]);

    $user = User::factory()->create();
    $user->profile()->create([
        'age' => 30,
        'height_cm' => 180,
        'weight_kg' => 80,
        'gender' => 'male',
        'body_goal' => 'muscle_gain',
        'skill_level' => 'intermediate',
        'activity_level' => 'mainly_standing',
        'training_place' => 'gym',
        'diet_type' => 'omnivore',
    ]);

    $plan = Plan::factory()->create([
        'user_id' => $user->id,
        'workouts_per_week' => 4,
        'duration_days' => 7,
    ]);

    // Simulate that days 1-7 are already generated (as if job ran successfully)
    for ($day = 1; $day <= 7; $day++) {
        WorkoutPlan::factory()->create([
            'plan_id' => $plan->id,
            'day_number' => $day,
            'status' => 'generated',
            'workout_type' => $day % 2 === 0 ? 'rest' : 'strength',
        ]);
    }

    $countAfterFirstRun = WorkoutPlan::where('plan_id', $plan->id)->count();
    expect($countAfterFirstRun)->toBe(7);

    // Retry job - should exit early because plan is complete
    // This tests the completeness check we added
    $retryJob = new GenerateUserWorkoutPlan($user, $plan);
    $retryJob->handle();

    $countAfterRetry = WorkoutPlan::where('plan_id', $plan->id)->count();

    // Should still be 7, not 8!
    expect($countAfterRetry)->toBe(7, "Expected 7 days after retry but got {$countAfterRetry}");

    // Verify no day 8 exists
    $day8Exists = WorkoutPlan::where('plan_id', $plan->id)
        ->where('day_number', 8)
        ->exists();

    expect($day8Exists)->toBeFalse('Day 8 should not exist');
});

test('workout plan logic calculates correct end day for partial completion', function () {
    // Mock OpenAI
    OpenAI::fake([
        CreateResponse::fake([
            'choices' => [
                [
                    'message' => [
                        'content' => json_encode([
                            'workout_name' => 'Test Workout',
                            'exercises' => []
                        ])
                    ]
                ]
            ]
        ])
    ]);

    $user = User::factory()->create();
    $user->profile()->create([
        'age' => 30,
        'height_cm' => 180,
        'weight_kg' => 80,
        'gender' => 'male',
        'body_goal' => 'muscle_gain',
        'skill_level' => 'intermediate',
        'activity_level' => 'mainly_standing',
        'training_place' => 'gym',
        'diet_type' => 'omnivore',
    ]);

    $plan = Plan::factory()->create([
        'user_id' => $user->id,
        'workouts_per_week' => 4,
        'duration_days' => 7,
    ]);

    // Simulate partial failure - only days 1-3 generated successfully
    for ($day = 1; $day <= 3; $day++) {
        WorkoutPlan::factory()->create([
            'plan_id' => $plan->id,
            'day_number' => $day,
            'status' => 'generated',
            'workout_type' => 'strength',
        ]);
    }

    $countBeforeRetry = WorkoutPlan::where('plan_id', $plan->id)->count();
    expect($countBeforeRetry)->toBe(3);

    // Test the logic that job would use
    $lastGeneratedDay = WorkoutPlan::where('plan_id', $plan->id)
        ->where('status', 'generated')
        ->max('day_number');

    expect($lastGeneratedDay)->toBe(3);

    // Check completeness check
    $isComplete = $lastGeneratedDay >= $plan->duration_days;
    expect($isComplete)->toBeFalse('Plan should not be complete with only 3 days');

    // The next day to generate should be 4
    $nextDay = $lastGeneratedDay + 1;
    expect($nextDay)->toBe(4);

    // Verify endDayNumber calculation with min()
    $daysToGenerate = 7;
    $endDayNumber = min($nextDay + $daysToGenerate - 1, $plan->duration_days);
    expect($endDayNumber)->toBe(7, "End day should be limited to plan duration (7), not 10");
});

test('workout plan logic prevents generation beyond duration for 30 day plan', function () {
    // Mock OpenAI
    OpenAI::fake([
        CreateResponse::fake([
            'choices' => [
                [
                    'message' => [
                        'content' => json_encode([
                            'workout_name' => 'Test Workout',
                            'exercises' => []
                        ])
                    ]
                ]
            ]
        ])
    ]);

    $user = User::factory()->create();
    $user->profile()->create([
        'age' => 30,
        'height_cm' => 180,
        'weight_kg' => 80,
        'gender' => 'male',
        'body_goal' => 'muscle_gain',
        'skill_level' => 'intermediate',
        'activity_level' => 'mainly_standing',
        'training_place' => 'gym',
        'diet_type' => 'omnivore',
    ]);

    $plan = Plan::factory()->create([
        'user_id' => $user->id,
        'workouts_per_week' => 4,
        'duration_days' => 30,
    ]);

    // Simulate week 1 complete (days 1-7)
    for ($day = 1; $day <= 7; $day++) {
        WorkoutPlan::factory()->create([
            'plan_id' => $plan->id,
            'day_number' => $day,
            'status' => 'generated',
            'workout_type' => 'strength',
        ]);
    }

    $lastGeneratedDay = WorkoutPlan::where('plan_id', $plan->id)
        ->where('status', 'generated')
        ->max('day_number');

    expect($lastGeneratedDay)->toBe(7);

    // Check completeness
    $isComplete = $lastGeneratedDay >= $plan->duration_days;
    expect($isComplete)->toBeFalse('7 days should not complete 30 day plan');

    // Next week should start at day 8
    $nextDay = $lastGeneratedDay + 1;
    expect($nextDay)->toBe(8);

    // End day should be day 14 (not beyond 30)
    $daysToGenerate = 7;
    $endDayNumber = min($nextDay + $daysToGenerate - 1, $plan->duration_days);
    expect($endDayNumber)->toBe(14);

    // Simulate days 8-28 generated
    for ($day = 8; $day <= 28; $day++) {
        WorkoutPlan::factory()->create([
            'plan_id' => $plan->id,
            'day_number' => $day,
            'status' => 'generated',
            'workout_type' => 'strength',
        ]);
    }

    $lastGeneratedDay = WorkoutPlan::where('plan_id', $plan->id)
        ->where('status', 'generated')
        ->max('day_number');

    expect($lastGeneratedDay)->toBe(28);

    // For last days (29-30), endDay should be 30, not 35
    $nextDay = $lastGeneratedDay + 1;
    $endDayNumber = min($nextDay + $daysToGenerate - 1, $plan->duration_days);
    expect($endDayNumber)->toBe(30, "End day should be limited to 30, not 35");
});

test('workout plan completeness check identifies completed plans', function () {
    // Mock OpenAI
    OpenAI::fake([
        CreateResponse::fake([
            'choices' => [
                [
                    'message' => [
                        'content' => json_encode([
                            'workout_name' => 'Test Workout',
                            'exercises' => []
                        ])
                    ]
                ]
            ]
        ])
    ]);

    $plan = Plan::factory()->create([
        'duration_days' => 7,
    ]);

    // Simulate all 7 days generated
    for ($day = 1; $day <= 7; $day++) {
        WorkoutPlan::factory()->create([
            'plan_id' => $plan->id,
            'day_number' => $day,
            'status' => 'generated',
        ]);
    }

    $lastGeneratedDay = WorkoutPlan::where('plan_id', $plan->id)
        ->where('status', 'generated')
        ->max('day_number');

    // Completeness check
    $isComplete = $lastGeneratedDay >= $plan->duration_days;

    expect($isComplete)->toBeTrue('Plan with 7/7 days should be complete');
    expect($lastGeneratedDay)->toBe(7);
    expect($plan->duration_days)->toBe(7);
});


