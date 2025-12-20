<?php

namespace App\Helpers;

use App\Enums\Gender;
use App\Enums\ActivityLevel;
use App\Enums\BodyGoal;
use App\Enums\DietType;

class Metabolism
{
    /**
     * Calculate Basal Metabolic Rate using Mifflin-St Jeor equation.
     *
     * More accurate than Harris-Benedict for modern populations.
     *
     * @param Gender $gender
     * @param int $age Years
     * @param float $height Centimeters
     * @param float $weight Kilograms
     * @return int Calories per day
     */
    public static function calculateBMR(
        Gender $gender,
        int    $age,
        float  $height,
        float  $weight
    ): int
    {
        // Mifflin-St Jeor: (10 × weight) + (6.25 × height) - (5 × age) + s
        // where s = +5 for males, -161 for females
        $bmr = (10 * $weight) + (6.25 * $height) - (5 * $age);

        if ($gender->usesMaleFormula()) {
            $bmr += 5;
        } else {
            $bmr -= 161;
        }

        return (int)round($bmr);
    }

    /**
     * Calculate Total Daily Energy Expenditure.
     *
     * @param int $bmr Basal Metabolic Rate
     * @param ActivityLevel $activityLevel
     * @return int Calories per day
     */
    public static function calculateTDEE(int $bmr, ActivityLevel $activityLevel): int
    {
        return (int)round($bmr * $activityLevel->tdeeMultiplier());
    }

    /**
     * Calculate daily calorie target based on goal.
     *
     * @param int $tdee Total Daily Energy Expenditure
     * @param BodyGoal $goal
     * @return int Adjusted calories per day
     */
    public static function calculateDailyCalories(int $tdee, BodyGoal $goal): int
    {
        return $tdee + $goal->calorieAdjustment();
    }

    /**
     * Calculate macro split in grams.
     *
     * @param int $dailyCalories Total daily calories
     * @param DietType $dietType
     * @return array{protein_g: int, carbs_g: int, fat_g: int}
     */
    public static function calculateMacros(int $dailyCalories, DietType $dietType): array
    {
        $split = $dietType->macroSplit();

        // Protein: 4 kcal/g
        // Carbs: 4 kcal/g
        // Fat: 9 kcal/g

        return [
            'protein_g' => (int)round(($dailyCalories * ($split['protein'] / 100)) / 4),
            'carbs_g' => (int)round(($dailyCalories * ($split['carbs'] / 100)) / 4),
            'fat_g' => (int)round(($dailyCalories * ($split['fat'] / 100)) / 9),
        ];
    }
}
