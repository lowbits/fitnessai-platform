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
    public function label(): string
    {
        return __('enums.bodyGoal.' . $this->value);
    }

    /**
     * Get detailed description of this goal.
     */
    public function description(): string
    {
        return match($this) {
            self::MUSCLE_GAIN => 'Build lean muscle mass and increase size',
            self::WEIGHT_LOSS => 'Lose body fat while maintaining muscle',
            self::MAINTENANCE => 'Maintain current weight and body composition',
            self::ENDURANCE => 'Build cardiovascular endurance and stamina',
            self::STRENGTH => 'Increase maximum strength and power',
        };
    }

    /**
     * Get calorie adjustment for this goal.
     * Applied to TDEE when calculating daily calorie target.
     *
     * @return int Calorie adjustment (+/- kcal)
     */
    public function calorieAdjustment(): int
    {
        return match($this) {
            self::MUSCLE_GAIN => 300,   // Caloric surplus for muscle growth
            self::WEIGHT_LOSS => -500,  // Caloric deficit for fat loss
            self::MAINTENANCE => 0,     // Maintain current weight
            self::ENDURANCE => 200,     // Slight surplus for energy demands
            self::STRENGTH => 250,      // Moderate surplus for strength gains
        };
    }
}
