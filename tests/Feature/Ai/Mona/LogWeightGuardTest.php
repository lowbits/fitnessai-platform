<?php

use App\Ai\Tools\LogWeightTool;
use App\Models\User;
use Laravel\Ai\Tools\Request;

/**
 * Mona playbook — weight check-in.
 * Each row is a scenario: what the user reports -> the expected tool result.
 */
function logWeight(User $user, array $args): array
{
    return json_decode((new LogWeightTool($user))->handle(new Request($args)), true);
}

dataset('weight check-in scenarios', [
    // label => [reference weight on profile, reported kg, confirmed?, expected outcome]
    'a plausible weigh-in logs silently' => [80.0, 82.4, false, 'logged'],
    'an implausible jump asks to confirm first' => [72.0, 183.0, false, 'weight_needs_confirmation'],
    'a confirmed implausible jump logs' => [72.0, 183.0, true, 'logged'],
    'below the absolute floor is rejected' => [80.0, 12.0, false, 'invalid_weight'],
    'above the absolute ceiling is rejected' => [80.0, 640.0, false, 'invalid_weight'],
]);

test('weight check-in guards implausible values', function (float $reference, float $reported, bool $confirmed, string $expected) {
    $user = User::factory()->withProfile()->create();
    $user->profile->update(['weight_kg' => $reference]);

    $args = ['weight_kg' => $reported];
    if ($confirmed) {
        $args['confirmed'] = true;
    }

    $result = logWeight($user, $args);

    if ($expected === 'logged') {
        expect($result['logged'] ?? false)->toBeTrue();
        expect($user->fresh()->bodyProgress()->count())->toBe(1);
    } else {
        expect($result['error'] ?? null)->toBe($expected);
        expect($user->fresh()->bodyProgress()->count())->toBe(0);
    }
})->with('weight check-in scenarios');
