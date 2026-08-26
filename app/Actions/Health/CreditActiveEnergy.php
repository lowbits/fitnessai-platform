<?php

namespace App\Actions\Health;

/**
 * Converts a day's active energy (kcal burned) into the calories credited back
 * to the user's budget: a conservative fraction of what they burned, rounded
 * down to the nearest 5, and capped. Factor and cap live in config/health.php.
 */
final class CreditActiveEnergy
{
    public function __invoke(int $activeEnergyKcal): int
    {
        if ($activeEnergyKcal <= 0) {
            return 0;
        }

        $factor = (float) config('health.credit_factor');
        $cap = (int) config('health.credit_cap_kcal');

        $raw = (int) floor($activeEnergyKcal * $factor);
        $floored = intdiv($raw, 5) * 5;

        return min($floored, $cap);
    }
}
