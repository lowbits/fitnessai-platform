<?php

use App\Ai\Prompts\CreateWorkoutPrompt;
use App\Models\User;
use App\Models\UserProfile;

function workoutPromptFor(array $focusAreas): string
{
    $user = User::factory()->create();
    $profile = UserProfile::factory()->create(['user_id' => $user->id, 'focus_areas' => $focusAreas]);

    return (string) new CreateWorkoutPrompt($profile, 'en', dayNumber: 1, workoutsPerWeek: 3);
}

it('injects saved focus areas into the live workout prompt', function () {
    expect(workoutPromptFor(['arms', 'glutes']))
        ->toContain('FOCUS AREAS')
        ->toContain('arms, glutes');
});

it('omits the focus section when none are set', function () {
    expect(workoutPromptFor([]))->not->toContain('FOCUS AREAS');
});
