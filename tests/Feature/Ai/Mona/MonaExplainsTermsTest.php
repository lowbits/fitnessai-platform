<?php

use App\Ai\Agents\MonaCoachAgent;
use App\Models\User;

test('Mona is briefed to explain plan notation like RPE and tempo', function () {
    $user = User::factory()->withProfile()->create();

    $instructions = (new MonaCoachAgent($user))->instructions();

    expect($instructions)
        ->toContain('RPE')
        ->toContain('Tempo')
        ->toContain('RIR');
});
