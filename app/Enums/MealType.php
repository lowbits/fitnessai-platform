<?php

namespace App\Enums;

enum MealType: string
{
    case BREAKFAST = 'breakfast';
    case LUNCH = 'lunch';
    case DINNER = 'dinner';
    case SNACK = 'snack';
    case FLEX = 'flex';
    case OTHER = 'other';

    /**
     * Get human-readable label for frontend display.
     */
    public function label(): string
    {
        return __('enums.mealType.'.$this->value);
    }
}
