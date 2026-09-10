<?php

use App\Ai\Prompts\CreateWorkoutPrompt;
use App\Models\User;
use App\Models\UserProfile;

function volumeWorkoutPrompt(): string
{
    $user = User::factory()->create();
    $profile = UserProfile::factory()->create(['user_id' => $user->id]);

    return (string) new CreateWorkoutPrompt($profile, 'en', dayNumber: 1, workoutsPerWeek: 4, workoutNumberInCycle: 1);
}

it('tells the model to spread sets across every target muscle', function () {
    expect(volumeWorkoutPrompt())
        ->toContain('Set distribution')
        ->toContain('8 hard working sets per week')
        ->toContain('single token exercise');
});
