<?php

namespace App\Testing;

use App\Enums\ActivityLevel;
use App\Enums\BodyGoal;
use App\Enums\CookingPreference;
use App\Enums\DietaryPreference;
use App\Enums\Gender;
use App\Enums\MealVariety;
use App\Enums\SkillLevel;
use App\Enums\TrainingPlace;

/**
 * The three product personas fytrr is built for, in code form.
 * Narratives + audit checks live in docs/personas/*.md; the technical
 * UserProfile config lives here so the test:personas command and any
 * factory can consume one source of truth.
 */
class PersonaDefinitions
{
    /**
     * @return array<string, array<string, mixed>>
     */
    public static function all(): array
    {
        return [
            'lukas' => [
                'birthdate' => now()->subYears(23)->subMonths(6)->toDateString(), 'gender' => Gender::MALE, 'weight_kg' => 95.0, 'height_cm' => 178,
                'body_goal' => BodyGoal::BUILD_MUSCLE, 'skill_level' => SkillLevel::BEGINNER,
                'activity_level' => ActivityLevel::MAINLY_SITTING, 'training_place' => TrainingPlace::GYM,
                'training_sessions_per_week' => 3, 'dietary_preference' => DietaryPreference::OMNIVORE,
                'cooking_preference' => CookingPreference::QUICK, 'meal_variety' => MealVariety::LOW,
                'meal_prep_enabled' => true, 'selected_meals' => ['breakfast', 'lunch', 'snack', 'dinner'],
                'food_dislikes' => ['pilze', 'rosenkohl', 'leber'], 'locale' => 'de',
            ],
            'anna' => [
                'birthdate' => now()->subYears(30)->subMonths(6)->toDateString(), 'gender' => Gender::FEMALE, 'weight_kg' => 76.5, 'height_cm' => 178,
                'body_goal' => BodyGoal::LOSE_WEIGHT, 'skill_level' => SkillLevel::BEGINNER,
                'activity_level' => ActivityLevel::MAINLY_WALKING, 'training_place' => TrainingPlace::HOME,
                'training_sessions_per_week' => 3, 'dietary_preference' => DietaryPreference::PESCATARIAN,
                'cooking_preference' => CookingPreference::NORMAL, 'meal_variety' => MealVariety::MEDIUM,
                'meal_prep_enabled' => false, 'selected_meals' => ['breakfast', 'lunch', 'dinner'],
                'food_dislikes' => ['tofu', 'kichererbsen'], 'locale' => 'de',
            ],
            'thomas' => [
                'birthdate' => now()->subYears(38)->subMonths(6)->toDateString(), 'gender' => Gender::MALE, 'weight_kg' => 78.0, 'height_cm' => 183,
                'body_goal' => BodyGoal::BUILD_MUSCLE, 'skill_level' => SkillLevel::BEGINNER,
                'activity_level' => ActivityLevel::MAINLY_SITTING, 'training_place' => TrainingPlace::GYM,
                'training_sessions_per_week' => 3, 'dietary_preference' => DietaryPreference::VEGETARIAN,
                'cooking_preference' => CookingPreference::ELABORATE, 'meal_variety' => MealVariety::HIGH,
                'meal_prep_enabled' => false, 'selected_meals' => ['breakfast', 'lunch', 'snack', 'dinner'],
                'food_dislikes' => ['sellerie', 'fenchel', 'aubergine'], 'locale' => 'de',
            ],
        ];
    }
}
