<?php
// app/Enums/DietType.php
namespace App\Enums;

/**
 * Diet Type Enum
 *
 * Represents user's dietary preference or restriction.
 * Used to generate appropriate meal plans with correct macro splits.
 */
enum DietType: string
{
    // Lifestyle diets
    case OMNIVORE = 'omnivore';
    case VEGETARIAN = 'vegetarian';
    case PESCATARIAN = 'pescatarian';
    case VEGAN = 'vegan';

    // Performance diets
    case HIGH_PROTEIN = 'high_protein';
    case LOW_CARB = 'low_carb';
    case KETOGENIC = 'ketogenic';
    case PALEO = 'paleo';
    case MEDITERRANEAN = 'mediterranean';

    // Eating patterns
    case INTERMITTENT_FASTING = 'intermittent_fasting';

    /**
     * Get human-readable label for frontend display.
     */
    public function label(): string
    {
        return __('enums.dietType.' . $this->value);
    }

    /**
     * Get detailed description of this diet type.
     */
    public function description(): string
    {
        return match ($this) {
            self::OMNIVORE => 'Balanced diet with all food groups (meat, dairy, plants)',
            self::VEGETARIAN => 'Plant-based with dairy and eggs, no meat or fish',
            self::PESCATARIAN => 'Vegetarian diet plus fish and seafood',
            self::VEGAN => 'Fully plant-based, no animal products',
            self::HIGH_PROTEIN => 'Higher protein intake for muscle building and strength',
            self::LOW_CARB => 'Reduced carbohydrates, higher protein and fats',
            self::KETOGENIC => 'Very low carb, high fat for ketosis',
            self::PALEO => 'Whole foods, no grains, legumes, or processed foods',
            self::MEDITERRANEAN => 'Rich in healthy fats, fish, vegetables, and whole grains',
            self::INTERMITTENT_FASTING => 'Time-restricted eating windows (e.g., 16:8)',
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
            self::OMNIVORE => [
                'protein' => 30,
                'carbs' => 45,
                'fat' => 25,
            ],
            self::VEGETARIAN => [
                'protein' => 25,
                'carbs' => 50,
                'fat' => 25,
            ],
            self::PESCATARIAN => [
                'protein' => 30,
                'carbs' => 45,
                'fat' => 25,
            ],
            self::VEGAN => [
                'protein' => 25,
                'carbs' => 50,
                'fat' => 25,
            ],
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
            self::INTERMITTENT_FASTING => [
                'protein' => 30,
                'carbs' => 45,
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
            self::HIGH_PROTEIN => [],
            self::LOW_CARB => ['bread', 'pasta', 'rice', 'sugar'],
            self::KETOGENIC => ['bread', 'pasta', 'rice', 'sugar', 'fruit', 'grains'],
            self::PALEO => ['grains', 'legumes', 'dairy', 'processed_foods', 'sugar'],
            self::MEDITERRANEAN => ['processed_foods', 'red_meat'],
            self::INTERMITTENT_FASTING => [],
        };
    }

    /**
     * Determine if this diet has eating time restrictions.
     */
    public function hasTimeRestrictions(): bool
    {
        return $this === self::INTERMITTENT_FASTING;
    }

    /**
     * Get recommended eating window for time-restricted diets.
     *
     * @return array{start: string, end: string}|null
     */
    public function eatingWindow(): ?array
    {
        return match ($this) {
            self::INTERMITTENT_FASTING => [
                'start' => '12:00',
                'end' => '20:00',
            ],
            default => null,
        };
    }
}
