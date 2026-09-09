<?php

use App\Ai\Tools\UpdateFoodDislikesTool;
use App\Models\User;
use App\Models\UserProfile;
use Laravel\Ai\Tools\Request;

function updateDislikes(User $user, array $args): array
{
    return json_decode((new UpdateFoodDislikesTool($user))->handle(new Request($args)), true);
}

function userWithDislikes(array $dislikes = []): User
{
    $user = User::factory()->create();
    UserProfile::factory()->create(['user_id' => $user->id, 'food_dislikes' => $dislikes]);

    return $user->refresh();
}

it('saves new dislikes, lowercased and deduped', function () {
    $user = userWithDislikes();

    $result = updateDislikes($user, ['add' => ['Tofu', 'tofu', ' Bulgur ']]);

    expect($result['updated'])->toBeTrue()
        ->and($result['dislikes'])->toEqualCanonicalizing(['tofu', 'bulgur']);
    expect($user->profile->refresh()->food_dislikes)->toEqualCanonicalizing(['tofu', 'bulgur']);
});

it('merges with existing dislikes without duplicating', function () {
    $user = userWithDislikes(['soy']);

    $result = updateDislikes($user, ['add' => ['soy', 'nuts']]);

    expect($result['dislikes'])->toEqualCanonicalizing(['soy', 'nuts']);
});

it('removes dislikes the user takes back', function () {
    $user = userWithDislikes(['tofu', 'bulgur']);

    $result = updateDislikes($user, ['remove' => ['Bulgur']]);

    expect($result['dislikes'])->toBe(['tofu'])
        ->and($result['removed'])->toContain('bulgur');
});

it('reports nothing to change when no foods are given', function () {
    $result = updateDislikes(userWithDislikes(), []);

    expect($result['error'])->toBe('nothing_to_change');
});
