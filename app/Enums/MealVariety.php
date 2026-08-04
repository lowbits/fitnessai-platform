<?php

namespace App\Enums;

enum MealVariety: string
{
    case LOW = 'low';
    case MEDIUM = 'medium';
    case HIGH = 'high';

    public function label(): string
    {
        return __('enums.mealVariety.'.$this->value);
    }

    /**
     * Distinct meals per slot across a 7-day week.
     *
     * The variety dial controls only one thing: how many distinct recipes exist
     * per slot. Days beyond the distinct quota become exact repeats of a prior
     * slot meal (same `Meal` record duplicated — same grams, same macros).
     *
     * @return array{breakfast: int, lunch: int, snack: int, dinner: int}
     */
    public function perSlotDistinctTargets(): array
    {
        return match ($this) {
            self::LOW => ['breakfast' => 2, 'lunch' => 2, 'snack' => 2, 'dinner' => 2],
            self::MEDIUM => ['breakfast' => 4, 'lunch' => 5, 'snack' => 5, 'dinner' => 5],
            self::HIGH => ['breakfast' => 7, 'lunch' => 7, 'snack' => 7, 'dinner' => 7],
        };
    }
}
