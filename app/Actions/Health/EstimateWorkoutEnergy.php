<?php

namespace App\Actions\Health;

/**
 * A conservative MET-based estimate of the energy a strength/cardio session
 * burns, used when the workout plan has no stored value. kcal = MET × kg × h.
 * Same "keep the math in PHP so it's tunable" philosophy as the activity credit.
 */
final class EstimateWorkoutEnergy
{
    /**
     * Compendium-of-Physical-Activities MET values, kept deliberately modest.
     *
     * @var array<string, float>
     */
    private const MET = [
        'strength' => 6.0,
        'hypertrophy' => 6.0,
        'cardio' => 7.0,
        'hiit' => 9.0,
        'mobility' => 2.5,
        'rest' => 0.0,
    ];

    private const DEFAULT_MET = 5.0;

    public function __invoke(?string $workoutType, int $durationMinutes, ?float $weightKg): ?int
    {
        if ($durationMinutes <= 0 || $weightKg === null || $weightKg <= 0) {
            return null;
        }

        $met = self::MET[$workoutType] ?? self::DEFAULT_MET;

        if ($met <= 0.0) {
            return null;
        }

        return (int) round($met * $weightKg * ($durationMinutes / 60));
    }
}
