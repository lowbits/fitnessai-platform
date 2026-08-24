<?php

use App\Ai\Tools\LogWeightTool;
use App\Models\User;
use Laravel\Ai\Tools\Request;

test('a shared feeling is stored as the entry note', function () {
    $user = User::factory()->withProfile(['weight_kg' => 82])->create();

    (new LogWeightTool($user))->handle(new Request([
        'weight_kg' => 82.5,
        'note' => 'Energie top, aber schlecht geschlafen.',
    ]));

    expect($user->bodyProgress()->latest('recorded_at')->value('notes'))
        ->toBe('Energie top, aber schlecht geschlafen.');
});

test('without a feeling the note stays null', function () {
    $user = User::factory()->withProfile(['weight_kg' => 82])->create();

    (new LogWeightTool($user))->handle(new Request(['weight_kg' => 82.5]));

    expect($user->bodyProgress()->latest('recorded_at')->value('notes'))->toBeNull();
});
