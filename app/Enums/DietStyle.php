<?php

namespace App\Enums;

enum DietStyle: string
{
    case HIGH_PROTEIN = 'high_protein';
    case LOW_CARB = 'low_carb';
    case KETOGENIC = 'ketogenic';
    case PALEO = 'paleo';
    case MEDITERRANEAN = 'mediterranean';

    public function label(): string
    {
        return __('enums.dietStyle.' . $this->value);
    }

    /**
     * Get detailed description of this diet style.
     */
    public function description(): string
    {
        return match ($this) {
            self::HIGH_PROTEIN => 'Higher protein intake for muscle building and strength',
            self::LOW_CARB => 'Reduced carbohydrates, higher protein and fats',
            self::KETOGENIC => 'Very low carb, high fat for ketosis',
            self::PALEO => 'Whole foods, no grains, legumes, or processed foods',
            self::MEDITERRANEAN => 'Rich in healthy fats, fish, vegetables, and whole grains',
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
            self::HIGH_PROTEIN => [
                'protein' => 40,
                'carbs' => 35,
                'fat' => 25,
            ],
            self::LOW_CARB => [
                'protein' => 35,
                'carbs' => 25,
                'fat' => 40,
            ],
            self::KETOGENIC => [
                'protein' => 25,
                'carbs' => 5,
                'fat' => 70,
            ],
            self::PALEO => [
                'protein' => 35,
                'carbs' => 35,
                'fat' => 30,
            ],
            self::MEDITERRANEAN => [
                'protein' => 25,
                'carbs' => 40,
                'fat' => 35,
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
            self::HIGH_PROTEIN => [],
            self::LOW_CARB => ['bread', 'pasta', 'rice', 'sugar'],
            self::KETOGENIC => ['bread', 'pasta', 'rice', 'sugar', 'fruit', 'grains'],
            self::PALEO => ['grains', 'legumes', 'dairy', 'processed_foods', 'sugar'],
            self::MEDITERRANEAN => ['processed_foods', 'red_meat'],
        };
    }
}
