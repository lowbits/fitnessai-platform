<?php

use App\Ai\Tools\UpdateCheckInTool;
use App\Models\User;
use Laravel\Ai\Tools\Request;

function updateCheckIn(User $user, array $args): array
{
    return json_decode((new UpdateCheckInTool($user))->handle(new Request($args)), true);
}

test('it requires a weighed-in entry today', function () {
    $user = User::factory()->withProfile()->create();

    expect(updateCheckIn($user, ['waist_cm' => 84])['error'])->toBe('no_checkin_entry');
});

test('it writes measurements onto today\'s entry', function () {
    $user = User::factory()->withProfile()->create();
    $entry = $user->bodyProgress()->create(['weight_kg' => 82, 'recorded_at' => now()]);

    $result = updateCheckIn($user, ['waist_cm' => 84.5, 'hip_cm' => 95]);

    expect($result['widget'])->toBe('check_in_saved');
    expect($result['data']['measurements'])->toBe(2);
    expect((float) $entry->fresh()->waist_circumference_cm)->toBe(84.5);
    expect((float) $entry->fresh()->hip_circumference_cm)->toBe(95.0);
});

test('mood and energy are stored as a structured 1-5 signal on the check-in', function () {
    $user = User::factory()->withProfile()->create();
    $entry = $user->bodyProgress()->create(['weight_kg' => 82, 'recorded_at' => now()]);

    $result = updateCheckIn($user, ['mood' => 4, 'energy' => 3, 'note' => 'müde']);

    expect($result['data']['has_mood'])->toBeTrue();

    $checkIn = $user->checkIns()->sole();
    expect($checkIn->mood)->toBe(4);
    expect($checkIn->energy)->toBe(3);
    expect($checkIn->body_progress_id)->toBe($entry->id);
    expect($entry->fresh()->notes)->toContain('müde');
});

test('mood and energy are clamped to 1-5', function () {
    $user = User::factory()->withProfile()->create();
    $user->bodyProgress()->create(['weight_kg' => 82, 'recorded_at' => now()]);

    updateCheckIn($user, ['mood' => 9, 'energy' => 0]);

    $checkIn = $user->checkIns()->sole();
    expect($checkIn->mood)->toBe(5);
    expect($checkIn->energy)->toBe(1);
});

test('mood updates the same check-in the weigh-in opened', function () {
    $user = User::factory()->withProfile()->create();
    $entry = $user->bodyProgress()->create(['weight_kg' => 82, 'recorded_at' => now()]);
    $user->checkIns()->create(['checked_in_at' => today(), 'body_progress_id' => $entry->id]);

    updateCheckIn($user, ['mood' => 5, 'energy' => 4]);

    expect($user->checkIns()->count())->toBe(1);
    expect($user->checkIns()->sole()->mood)->toBe(5);
});

test('a note is appended to the entry', function () {
    $user = User::factory()->withProfile()->create();
    $entry = $user->bodyProgress()->create(['weight_kg' => 82, 'recorded_at' => now(), 'notes' => 'Gewicht ok']);

    updateCheckIn($user, ['note' => 'Energie hoch, gut geschlafen']);

    expect($entry->fresh()->notes)->toContain('Gewicht ok')->toContain('Energie hoch');
});

test('with nothing to save it signals nothing_to_update', function () {
    $user = User::factory()->withProfile()->create();
    $user->bodyProgress()->create(['weight_kg' => 82, 'recorded_at' => now()]);

    expect(updateCheckIn($user, [])['error'])->toBe('nothing_to_update');
});
