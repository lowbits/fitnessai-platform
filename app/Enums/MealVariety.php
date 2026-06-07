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

    public function maxUniqueRecipesPerWeek(): int
    {
        return match ($this) {
            self::LOW => 7,
            self::MEDIUM => 12,
            self::HIGH => 21,
        };
    }
}
