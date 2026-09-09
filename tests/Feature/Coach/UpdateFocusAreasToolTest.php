<?php

use App\Ai\Tools\UpdateFocusAreasTool;
use App\Models\User;
use App\Models\UserProfile;
use Laravel\Ai\Tools\Request;

function updateFocusAreas(User $user, array $args): array
{
    return json_decode((new UpdateFocusAreasTool($user))->handle(new Request($args)), true);
}

function userWithFocusAreas(array $areas = []): User
{
    $user = User::factory()->create();
    UserProfile::factory()->create(['user_id' => $user->id, 'focus_areas' => $areas]);

    return $user->refresh();
}

it('adds valid focus areas and ignores anything outside the set', function () {
    $user = userWithFocusAreas();

    $result = updateFocusAreas($user, ['add' => ['Arms', 'glutes', 'biceps']]);

    expect($result['focus_areas'])->toEqualCanonicalizing(['arms', 'glutes']);
    expect($user->profile->refresh()->focus_areas)->toEqualCanonicalizing(['arms', 'glutes']);
});

it('merges with existing focus areas without duplicating', function () {
    $result = updateFocusAreas(userWithFocusAreas(['arms']), ['add' => ['arms', 'back']]);

    expect($result['focus_areas'])->toEqualCanonicalizing(['arms', 'back']);
});

it('removes a focus area', function () {
    $result = updateFocusAreas(userWithFocusAreas(['arms', 'legs']), ['remove' => ['Legs']]);

    expect($result['focus_areas'])->toBe(['arms']);
});

it('reports nothing to change when no valid area is given', function () {
    expect(updateFocusAreas(userWithFocusAreas(), ['add' => ['biceps']])['error'])->toBe('nothing_to_change');
});
