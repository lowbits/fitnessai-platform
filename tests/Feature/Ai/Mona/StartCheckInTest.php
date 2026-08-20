<?php

use App\Ai\Tools\StartCheckInTool;
use App\Models\User;
use Laravel\Ai\Tools\Request;

test('it opens a check_in card with the last and start weight', function () {
    $user = User::factory()->withProfile()->create();
    $user->profile->update(['weight_kg' => 90]);
    $user->bodyProgress()->create(['weight_kg' => 84.2, 'recorded_at' => now()]);

    $result = json_decode((new StartCheckInTool($user))->handle(new Request([])), true);

    expect($result['widget'])->toBe('check_in');
    expect($result['requires_input'])->toBeTrue();
    expect($result['data']['last_weight'])->toEqual(84.2);
    expect($result['data']['start_weight'])->toEqual(90.0);
});

test('with no history the weights are null', function () {
    $user = User::factory()->create();

    $result = json_decode((new StartCheckInTool($user))->handle(new Request([])), true);

    expect($result['data']['last_weight'])->toBeNull();
    expect($result['data']['start_weight'])->toBeNull();
});
