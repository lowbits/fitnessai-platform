<?php

namespace App\Enums;

/**
 * Gender Enum
 *
 * User's gender for profile and TDEE calculations.
 * Used in user_profiles.gender column.
 */
enum Gender: string
{
    case MALE = 'male';
    case FEMALE = 'female';
    case OTHER = 'other';
    case PREFER_NOT_TO_SAY = 'prefer_not_to_say';

    /**
     * Get human-readable label for frontend display.
     */
    public function label(): string
    {
        return match ($this) {
            self::MALE => 'Male',
            self::FEMALE => 'Female',
            self::OTHER => 'Other',
            self::PREFER_NOT_TO_SAY => 'Prefer not to say',
        };
    }

    /**
     * Determine if this gender uses male BMR formula.
     * For TDEE calculations using Mifflin-St Jeor equation.
     *
     * @return bool True if should use male formula
     */
    public function usesMaleFormula(): bool
    {
        return match ($this) {
            self::MALE => true,
            self::FEMALE => false,
            self::OTHER => true, // Default to male formula
            self::PREFER_NOT_TO_SAY => true, // Default to male formula
        };
    }
}
