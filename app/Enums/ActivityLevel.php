<?php
// app/Enums/ActivityLevel.php
namespace App\Enums;

/**
 * Activity Level Enum
 *
 * Represents user's daily activity level outside of planned workouts.
 * Used to calculate TDEE (Total Daily Energy Expenditure) multiplier.
 */
enum ActivityLevel: string
{
    case MAINLY_SITTING = 'mainly_sitting';
    case MAINLY_STANDING = 'mainly_standing';
    case MAINLY_WALKING = 'mainly_walking';
    case HARD_WORKING = 'hard_working';

    /**
     * Get human-readable label for frontend display.
     */
    public function label(): string
    {
        return match ($this) {
            self::MAINLY_SITTING => 'Mainly Sitting',
            self::MAINLY_STANDING => 'Mainly Standing',
            self::MAINLY_WALKING => 'Mainly Walking',
            self::HARD_WORKING => 'Hard Physical Work',
        };
    }

    /**
     * Get detailed description of this activity level.
     */
    public function description(): string
    {
        return match ($this) {
            self::MAINLY_SITTING => 'Desk job, minimal movement throughout the day (office work, driving, studying)',
            self::MAINLY_STANDING => 'Mostly standing with light activity (retail, teaching, lab work)',
            self::MAINLY_WALKING => 'Frequent walking or moving around (waiter, nurse, light delivery)',
            self::HARD_WORKING => 'Heavy physical labor or very active job (construction, farming, manual labor)',
        };
    }

    /**
     * Get TDEE multiplier for BMR calculation.
     * Based on activity level outside of planned workouts.
     *
     * @return float Multiplier to apply to BMR
     */
    public function tdeeMultiplier(): float
    {
        return match ($this) {
            self::MAINLY_SITTING => 1.2,    // Sedentary
            self::MAINLY_STANDING => 1.375, // Lightly active
            self::MAINLY_WALKING => 1.55,   // Moderately active
            self::HARD_WORKING => 1.725,    // Very active
        };
    }


    /**
     * Get recommended daily step goal based on activity level.
     */
    public function dailyStepGoal(): int
    {
        return match ($this) {
            self::MAINLY_SITTING => 6000,   // Need more intentional steps
            self::MAINLY_STANDING => 8000,  // Moderate steps
            self::MAINLY_WALKING => 10000,  // Already hitting good steps
            self::HARD_WORKING => 12000,    // Very high activity
        };
    }
}
