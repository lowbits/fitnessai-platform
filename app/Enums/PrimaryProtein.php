<?php

namespace App\Enums;

enum PrimaryProtein: string
{
    case CHICKEN = 'chicken';
    case TURKEY = 'turkey';
    case BEEF = 'beef';
    case PORK = 'pork';
    case LAMB = 'lamb';
    case FISH = 'fish';
    case SEAFOOD = 'seafood';
    case EGGS = 'eggs';
    case DAIRY = 'dairy';
    case LEGUMES = 'legumes';
    case TOFU = 'tofu';
    case TEMPEH = 'tempeh';
    case SEITAN = 'seitan';
    case MIXED = 'mixed';

    /**
     * Proteins permitted for a given dietary preference. Omnivores exclude the
     * vegan meat substitutes (tofu, tempeh, seitan); legumes stay in.
     *
     * @return list<self>
     */
    public static function allowedFor(DietaryPreference $preference): array
    {
        return match ($preference) {
            DietaryPreference::OMNIVORE => [
                self::CHICKEN, self::TURKEY, self::BEEF, self::PORK, self::LAMB,
                self::FISH, self::SEAFOOD, self::EGGS, self::DAIRY, self::LEGUMES, self::MIXED,
            ],

            DietaryPreference::PESCATARIAN => [
                self::FISH, self::SEAFOOD, self::EGGS, self::DAIRY,
                self::LEGUMES, self::TOFU, self::TEMPEH, self::SEITAN, self::MIXED,
            ],

            DietaryPreference::VEGETARIAN => [
                self::EGGS, self::DAIRY, self::LEGUMES, self::TOFU,
                self::TEMPEH, self::SEITAN, self::MIXED,
            ],

            DietaryPreference::VEGAN => [
                self::LEGUMES, self::TOFU, self::TEMPEH, self::SEITAN, self::MIXED,
            ],
        };
    }
}
