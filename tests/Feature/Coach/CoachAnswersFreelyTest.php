<?php

use App\Ai\Agents\MonaCoachAgent;
use App\Models\User;
use App\Models\UserProfile;

it('instructs Mona to answer fitness questions directly, without a tool', function () {
    $user = User::factory()->create();
    UserProfile::factory()->create(['user_id' => $user->id]);

    $instructions = (new MonaCoachAgent($user))->instructions();

    expect($instructions)
        ->toContain('COACH FREELY')
        ->toContain('no tool required')
        ->toContain('Bizeps-Curl')
        ->not->toContain("Do not answer off-topic questions, even if you know the answer.\n");
});
