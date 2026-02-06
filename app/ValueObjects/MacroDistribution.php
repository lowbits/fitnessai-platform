<?php

namespace App\ValueObjects;

/**
 * Immutable value object representing a daily macro distribution.
 *
 * Returned by Metabolism::calculateMacros() and used by Mona
 * to display nutrition targets and provide contextual coaching tips.
 */
final readonly class MacroDistribution
{
    public function __construct(
        public int  $proteinGrams,
        public int  $carbsGrams,
        public int  $fatGrams,
        public bool $proteinChallenging = false,
        public bool $minimumFatEnforced = false,
    ) {}

    /**
     * Total calories from all macronutrients.
     * May differ from target by a few kcal due to rounding.
     */
    public function totalCalories(): int
    {
        return ($this->proteinGrams * 4)
            + ($this->carbsGrams * 4)
            + ($this->fatGrams * 9);
    }

    /**
     * Protein as percentage of total calories.
     * Useful for Mona to decide when to show dietary tips.
     */
    public function proteinPercentage(): float
    {
        $total = $this->totalCalories();

        return $total > 0
            ? round(($this->proteinGrams * 4) / $total * 100, 1)
            : 0;
    }

    public function toArray(): array
    {
        return [
            'protein_g' => $this->proteinGrams,
            'carbs_g' => $this->carbsGrams,
            'fat_g' => $this->fatGrams,
            'protein_challenging' => $this->proteinChallenging,
            'minimum_fat_enforced' => $this->minimumFatEnforced,
        ];
    }
}
