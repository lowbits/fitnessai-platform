<?php

use App\Ai\Prompts\CreateWorkoutPrompt;
use App\Models\User;
use App\Models\UserProfile;

it('pushes challenging loads for a hypertrophy goal', function () {
    $user = User::factory()->create();
    $profile = UserProfile::factory()->for($user)->buildMuscle()->create();

    $prompt = (string) new CreateWorkoutPrompt($profile, 'en', dayNumber: 1, workoutsPerWeek: 4, workoutNumberInCycle: 1);

    expect($prompt)
        ->toContain('these are hard working sets')
        ->toContain('Challenging');
});

it('keeps a fat-loss goal at sustainable intensity', function () {
    $user = User::factory()->create();
    $profile = UserProfile::factory()->for($user)->loseWeight()->create();

    $prompt = (string) new CreateWorkoutPrompt($profile, 'en', dayNumber: 1, workoutsPerWeek: 4, workoutNumberInCycle: 1);

    expect($prompt)->not->toContain('these are hard working sets');
});
