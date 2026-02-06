<?php
// app/Enums/BodyGoal.php

namespace App\Enums;

/**
 * Body Goal Enum
 *
 * Defines user's fitness goal and associated calorie adjustments.
 * Used in user_profiles.body_goal column.
 */
enum BodyGoal: string
{
    case MUSCLE_GAIN = 'muscle_gain';
    case WEIGHT_LOSS = 'weight_loss';
    case MAINTENANCE = 'maintenance';
    case ENDURANCE = 'endurance';
    case STRENGTH = 'strength';

    /**
     * Get human-readable label for frontend display.
     */
    public function label(?string $locale = null): string
    {
        return __('enums.bodyGoal.' . $this->value, [], $locale);
    }

    /**
     * Get detailed description of this goal.
     */
    public function description(?string $locale = null): string
    {
        return __('enums.bodyGoal.' . $this->value . '_description', [], $locale);
    }

    /**
     * Get calorie adjustment for this goal.
     * Applied to TDEE when calculating daily calorie target.
     *
     * @return int Calorie adjustment (+/- kcal)
     */
    public function calorieAdjustment(): int
    {
        return match ($this) {
            self::MUSCLE_GAIN => 300,   // Caloric surplus for muscle growth
            self::WEIGHT_LOSS => -500,  // Caloric deficit for fat loss
            self::MAINTENANCE => 0,     // Maintain current weight
            self::ENDURANCE => 200,     // Slight surplus for energy demands
            self::STRENGTH => 250,      // Moderate surplus for strength gains
        };
    }


    /**
     * Protein intake per kg body weight for this goal.
     *
     * Based on ISSN Position Stand (2017) + 2024/2025 research:
     * - Muscle gain: 1.6–2.2 g/kg → 2.0 as practical target
     * - Weight loss: 2.3–3.1 g/kg → 2.5 to preserve lean mass in deficit
     * - Strength: similar to muscle gain, slightly higher
     * - Endurance: 1.4–1.8 g/kg
     * - Maintenance: 1.4–2.0 g/kg → 1.8 as middle ground
     *
     * @see https://pubmed.ncbi.nlm.nih.gov/28642676/
     */
    public function proteinPerKg(): float
    {
        return match ($this) {
            self::MUSCLE_GAIN => 2.0,
            self::STRENGTH => 2.2,
            self::WEIGHT_LOSS => 2.5,
            self::ENDURANCE => 1.6,
            self::MAINTENANCE => 1.8,
        };
    }
}
