<?php

namespace App\Enums;

enum BodyGoal: string
{
    // Current goals
    case LOSE_WEIGHT = 'lose_weight';
    case BUILD_MUSCLE = 'build_muscle';
    case GET_FIT = 'get_fit';

    // Deprecated — kept for old user data compatibility
    case MUSCLE_GAIN = 'muscle_gain';
    case WEIGHT_LOSS = 'weight_loss';
    case MAINTENANCE = 'maintenance';
    case ENDURANCE = 'endurance';
    case STRENGTH = 'strength';

    public function label(?string $locale = null): string
    {
        return __('enums.bodyGoal.'.$this->resolveCanonical()->value, [], $locale);
    }

    public function description(?string $locale = null): string
    {
        return __('enums.bodyGoal.'.$this->resolveCanonical()->value.'_description', [], $locale);
    }

    public function calorieAdjustment(): int
    {
        return match ($this->resolveCanonical()) {
            self::LOSE_WEIGHT => -500,
            self::BUILD_MUSCLE => 300,
            self::GET_FIT => 0,
        };
    }

    public function actionLabel(?string $locale = null): string
    {
        return __('emails.body_goal_labels.'.$this->resolveCanonical()->value, [], $locale);
    }

    /**
     * Protein intake per kg body weight.
     *
     * @see https://pubmed.ncbi.nlm.nih.gov/28642676/
     */
    public function proteinPerKg(): float
    {
        return match ($this->resolveCanonical()) {
            self::LOSE_WEIGHT => 2.5,
            self::BUILD_MUSCLE => 2.0,
            self::GET_FIT => 1.8,
        };
    }

    /**
     * Map deprecated cases to their canonical replacement.
     */
    public function resolveCanonical(): self
    {
        return match ($this) {
            self::WEIGHT_LOSS => self::LOSE_WEIGHT,
            self::MUSCLE_GAIN, self::STRENGTH => self::BUILD_MUSCLE,
            self::MAINTENANCE, self::ENDURANCE => self::GET_FIT,
            default => $this,
        };
    }

    /**
     * Only the 3 current goals — use for validation and UI.
     *
     * @return list<self>
     */
    public static function current(): array
    {
        return [
            self::LOSE_WEIGHT,
            self::BUILD_MUSCLE,
            self::GET_FIT,
        ];
    }
}
