<?php

use App\Ai\Tools\LogWeightTool;
use App\Ai\Tools\UpdateCheckInTool;
use App\Models\User;
use Laravel\Ai\Tools\Request;

test('logging a weight opens a check-in event linked to the weigh-in', function () {
    $user = User::factory()->withProfile(['weight_kg' => 82])->create();

    (new LogWeightTool($user))->handle(new Request(['weight_kg' => 82.5]));

    $entry = $user->bodyProgress()->sole();
    $checkIn = $user->checkIns()->sole();

    expect($checkIn->body_progress_id)->toBe($entry->id);
    expect($checkIn->checked_in_at->isToday())->toBeTrue();
    expect($checkIn->mood)->toBeNull();
});

test('a second weigh-in the same day reuses the check-in', function () {
    $user = User::factory()->withProfile(['weight_kg' => 82])->create();

    (new LogWeightTool($user))->handle(new Request(['weight_kg' => 82.5]));
    (new LogWeightTool($user))->handle(new Request(['weight_kg' => 82.7]));

    expect($user->checkIns()->count())->toBe(1);
});

test('mood without a weigh-in still records the wellbeing signal', function () {
    $user = User::factory()->withProfile(['weight_kg' => 82])->create();

    $result = json_decode((new UpdateCheckInTool($user))->handle(new Request(['mood' => 3, 'energy' => 2])), true);

    expect($result['widget'])->toBe('check_in_saved');

    $checkIn = $user->checkIns()->sole();
    expect($checkIn->mood)->toBe(3);
    expect($checkIn->body_progress_id)->toBeNull();
});
