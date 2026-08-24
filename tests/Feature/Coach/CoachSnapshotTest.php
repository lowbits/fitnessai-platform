<?php

use App\Ai\Agents\MonaCoachAgent;
use App\Ai\Support\CoachSnapshot;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('it summarizes the weight trend since start', function () {
    $user = User::factory()->withProfile()->create();
    $user->profile->update(['weight_kg' => 90]);
    $user->bodyProgress()->create(['weight_kg' => 87, 'recorded_at' => now()]);

    $text = app(CoachSnapshot::class)->forUser($user->fresh());

    expect($text)->toContain('87')
        ->toContain('-3')
        ->toContain('since start');
});

test('it is empty for a brand-new user with no data', function () {
    $user = User::factory()->create();

    expect(app(CoachSnapshot::class)->forUser($user))->toBe('');
});

test('mona instructions carry the progress snapshot', function () {
    $user = User::factory()->withProfile()->create();
    $user->profile->update(['weight_kg' => 82]);
    $user->bodyProgress()->create(['weight_kg' => 82, 'recorded_at' => now()]);

    $instructions = (new MonaCoachAgent($user->fresh()))->instructions();

    expect($instructions)->toContain('WHERE THEY ARE RIGHT NOW')->toContain('82');
});
