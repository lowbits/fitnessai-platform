<?php

namespace App\Enums;

enum DietaryPreference: string
{
    case OMNIVORE = 'omnivore';
    case VEGETARIAN = 'vegetarian';
    case PESCATARIAN = 'pescatarian';
    case VEGAN = 'vegan';

    public function label(): string
    {
        return __('enums.dietaryPreference.' . $this->value);
    }

    /**
     * Get detailed description of this dietary preference.
     */
    public function description(): string
    {
        return match ($this) {
            self::OMNIVORE => 'Balanced diet with all food groups (meat, dairy, plants)',
            self::VEGETARIAN => 'Plant-based with dairy and eggs, no meat or fish',
            self::PESCATARIAN => 'Vegetarian diet plus fish and seafood',
            self::VEGAN => 'Fully plant-based, no animal products',
        };
    }

    public function carbFatRatio(): array
    {
        return match ($this) {
            self::OMNIVORE,
            self::PESCATARIAN => ['carbs' => 60, 'fat' => 40],

            self::VEGETARIAN,
            self::VEGAN => ['carbs' => 65, 'fat' => 35],
        };
    }

    /**
     * Get macro split percentages (protein/carbs/fat).
     *
     * @return array{protein: int, carbs: int, fat: int}
     */
    public function macroSplit(): array
    {
        return match ($this) {
            self::OMNIVORE, self::PESCATARIAN => [
                'protein' => 30,
                'carbs' => 45,
                'fat' => 25,
            ],
            self::VEGETARIAN, self::VEGAN => [
                'protein' => 25,
                'carbs' => 50,
                'fat' => 25,
            ],
        };
    }

    /**
     * Get list of excluded food groups.
     *
     * @return array<string>
     */
    public function excludedFoods(): array
    {
        return match ($this) {
            self::OMNIVORE => [],
            self::VEGETARIAN => ['meat', 'fish', 'seafood'],
            self::PESCATARIAN => ['meat'],
            self::VEGAN => ['meat', 'fish', 'seafood', 'dairy', 'eggs', 'honey'],
        };
    }
}
