<?php

namespace App\Enums;

/**
 * How often the user wants to cook. Orthogonal to CookingPreference (which
 * controls per-meal effort): a meal-prepper might spend 60 minutes on Sunday
 * but eat ready-made portions the rest of the week.
 */
enum CookingFrequency: string
{
    case DAILY = 'daily';
    case FEW_TIMES = 'few_times';
    case MEAL_PREP = 'meal_prep';

    public function label(): string
    {
        return __('enums.cookingFrequency.'.$this->value);
    }

    /**
     * Prompt-ready coaching hint. Tells the AI what kinds of recipes fit this
     * cadence — single-portion vs hold-friendly vs batch-yield.
     */
    public function promptHint(): string
    {
        return match ($this) {
            self::DAILY => 'User cooks daily — prefer fresh single-portion recipes, leftover viability not required.',
            self::FEW_TIMES => 'User cooks a few times per week — favor recipes that hold 2-3 days well (stews, grain bowls, baked dishes) over things that wilt fast (delicate salads, soft tacos).',
            self::MEAL_PREP => 'User meal-preps — strongly favor batch-friendly recipes that scale to 4+ servings, freeze well, and reheat without quality loss. Avoid recipes that must be eaten fresh.',
        };
    }
}
