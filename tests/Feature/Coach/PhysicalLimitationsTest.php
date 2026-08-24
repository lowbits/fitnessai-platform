<?php

use App\Ai\Agents\MonaCoachAgent;
use App\Ai\Prompts\CreateWorkoutPrompt;
use App\Ai\Support\PhysicalLimitations;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('it lists the affected areas and the note', function () {
    $user = User::factory()->withProfile()->create();
    $user->profile->update([
        'physical_limitations' => ['knee', 'back'],
        'physical_limitations_note' => 'ACL surgery 3 months ago',
    ]);

    $text = PhysicalLimitations::forProfile($user->profile->fresh());

    expect($text)->toContain('knee')
        ->toContain('back')
        ->toContain('ACL surgery 3 months ago');
});

test('it is empty when there are no limitations', function () {
    $user = User::factory()->withProfile()->create();
    $user->profile->update(['physical_limitations' => [], 'physical_limitations_note' => null]);

    expect(PhysicalLimitations::forProfile($user->profile->fresh()))->toBe('');
});

test('mona instructions carry the limitations', function () {
    $user = User::factory()->withProfile()->create();
    $user->profile->update(['physical_limitations' => ['shoulder']]);

    expect((new MonaCoachAgent($user->fresh()))->instructions())->toContain('shoulder');
});

test('workout generation prompt carries the limitations', function () {
    $user = User::factory()->withProfile()->create();
    $user->profile->update([
        'physical_limitations' => ['knee'],
        'physical_limitations_note' => 'meniscus',
    ]);

    $prompt = (string) new CreateWorkoutPrompt($user->profile->fresh(), 'en', 1, 3);

    expect($prompt)->toContain('knee')->toContain('meniscus')->toContain('LIMITATIONS');
});
