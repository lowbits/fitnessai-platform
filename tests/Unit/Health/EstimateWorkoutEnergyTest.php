<?php

use App\Actions\Health\EstimateWorkoutEnergy;

function estimate(?string $type, int $minutes, ?float $weightKg): ?int
{
    return (new EstimateWorkoutEnergy)($type, $minutes, $weightKg);
}

it('returns null without a usable duration or weight', function () {
    expect(estimate('strength', 0, 80.0))->toBeNull()
        ->and(estimate('strength', 45, null))->toBeNull()
        ->and(estimate('strength', 45, 0.0))->toBeNull();
});

it('estimates strength energy from MET, weight and duration', function () {
    // 6.0 MET * 80 kg * 1 h = 480
    expect(estimate('strength', 60, 80.0))->toBe(480)
        ->and(estimate('hypertrophy', 48, 80.0))->toBe(384);
});

it('falls back to a default MET for unknown types', function () {
    // 5.0 MET * 80 * 0.5 h = 200
    expect(estimate('yoga_flow_9000', 30, 80.0))->toBe(200);
});

it('returns null for zero-MET activities like rest', function () {
    expect(estimate('rest', 60, 80.0))->toBeNull();
});
