<?php

use App\Ai\Agents\MonaCoachAgent;
use App\Models\User;

test('Mona instructions never contain the user name (never sent to the AI)', function () {
    $user = User::factory()->withProfile()->create(['name' => 'Zztopplinger Xavieronimus']);

    $instructions = (new MonaCoachAgent($user))->instructions();

    expect($instructions)
        ->not->toContain('Zztopplinger')
        ->not->toContain('Xavieronimus');
});
