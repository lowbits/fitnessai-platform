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
     * Proteins permitted for a given dietary preference.
     * Used at search/filter time to exclude incompatible recipes.
     *
     * @return list<self>
     */
    public static function allowedFor(DietaryPreference $preference): array
    {
        return match ($preference) {
            DietaryPreference::OMNIVORE => self::cases(),

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
