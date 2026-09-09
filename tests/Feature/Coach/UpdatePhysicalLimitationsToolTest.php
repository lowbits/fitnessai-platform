<?php

use App\Ai\Tools\UpdatePhysicalLimitationsTool;
use App\Models\User;
use App\Models\UserProfile;
use Laravel\Ai\Tools\Request;

function updateLimitations(User $user, array $args): array
{
    return json_decode((new UpdatePhysicalLimitationsTool($user))->handle(new Request($args)), true);
}

function userWithLimitations(array $areas = [], ?string $note = null): User
{
    $user = User::factory()->create();
    UserProfile::factory()->create([
        'user_id' => $user->id,
        'physical_limitations' => $areas,
        'physical_limitations_note' => $note,
    ]);

    return $user->refresh();
}

it('adds valid areas and a note, ignoring anything not in the fixed set', function () {
    $user = userWithLimitations();

    $result = updateLimitations($user, [
        'add_areas' => ['Shoulder', 'finger'],
        'note' => 'trigger finger OP 5 weeks ago — easy on grip',
    ]);

    expect($result['areas'])->toBe(['shoulder'])
        ->and($result['note'])->toContain('trigger finger');
    expect($user->profile->refresh()->physical_limitations)->toBe(['shoulder']);
});

it('removes an area the user has recovered from', function () {
    $user = userWithLimitations(['shoulder', 'knee']);

    $result = updateLimitations($user, ['remove_areas' => ['knee']]);

    expect($result['areas'])->toBe(['shoulder']);
});

it('keeps the note when omitted and clears it on empty string', function () {
    $user = userWithLimitations(['back'], 'existing note');

    $kept = updateLimitations($user, ['add_areas' => ['neck']]);
    expect($kept['note'])->toBe('existing note');

    $cleared = updateLimitations($user->refresh(), ['note' => '']);
    expect($cleared['note'])->toBeNull();
});

it('reports nothing to change when no input is given', function () {
    expect(updateLimitations(userWithLimitations(), [])['error'])->toBe('nothing_to_change');
});

it('is a no-op when removing an area that is not set and no note changes', function () {
    $result = updateLimitations(userWithLimitations(['shoulder']), ['remove_areas' => ['knee']]);

    expect($result['updated'])->toBeFalse()
        ->and($result['areas'])->toBe(['shoulder']);
});
