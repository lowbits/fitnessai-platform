<?php

namespace App\Helpers;

use App\Enums\ActivityLevel;
use App\Enums\BodyGoal;
use App\Enums\Gender;
use App\ValueObjects\MacroDistribution;

class Metabolism
{

    /*
    |--------------------------------------------------------------------------
    | Constants
    |--------------------------------------------------------------------------
    */

    /** Net calories burned per kg per training session (MET ~5–6, minus resting). */
    public const NET_KCAL_PER_KG_PER_SESSION = 5.0;

    /** Hard cap for training sessions. */
    public const MAX_SESSIONS_PER_WEEK = 7;

    /** Minimum fat per kg body weight for hormonal health (especially important for women). */
    public const MIN_FAT_GRAMS_PER_KG = 0.8;

    /**
     * Protein calorie threshold above which intake becomes practically challenging.
     * When exceeded, Mona should offer guidance (e.g. supplementation tips for vegans).
     */
    public const PROTEIN_WARNING_THRESHOLD = 0.35;

    private const KCAL_PER_GRAM_PROTEIN = 4;
    private const KCAL_PER_GRAM_CARBS = 4;
    private const KCAL_PER_GRAM_FAT = 9;

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

    /*
    |--------------------------------------------------------------------------
    | Training Calories
    |--------------------------------------------------------------------------
    |
    | Additional daily calories from planned training sessions,
    | averaged over the week. Scales with body weight because a
    | 100kg person burns significantly more per session than a 55kg person.
    |
    */

    public static function trainingCalories(int $sessionsPerWeek, float $bodyWeight): int
    {
        $sessions = min(max($sessionsPerWeek, 0), self::MAX_SESSIONS_PER_WEEK);

        $weeklyCalories = $bodyWeight * self::NET_KCAL_PER_KG_PER_SESSION * $sessions;

        return (int)round($weeklyCalories / 7);
    }

    /*
    |--------------------------------------------------------------------------
    | TDEE — Total Daily Energy Expenditure
    |--------------------------------------------------------------------------
    |
    | Combines two independent factors:
    | 1. Daily activity (job/lifestyle) → BMR × activity multiplier
    | 2. Planned training → additional calories based on sessions & weight
    |
    | This separation allows more precise estimates than the original
    | Mifflin combined activity factors (1.2–1.9).
    |
    */

    public static function calculateTDEE(
        int           $bmr,
        ActivityLevel $activityLevel,
        int           $sessionsPerWeek,
        float         $bodyWeight,
    ): int
    {
        $baseExpenditure = (int)round($bmr * $activityLevel->tdeeMultiplier());

        return $baseExpenditure + self::trainingCalories($sessionsPerWeek, $bodyWeight);
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

    /*
    |--------------------------------------------------------------------------
    | Macro Distribution
    |--------------------------------------------------------------------------
    |
    | 1. Protein is anchored to body weight (ISSN: 1.4–2.5 g/kg depending on goal).
    | This prevents protein from fluctuating with calorie changes.
    |
    | 2. Fat has a floor of 0.8 g/kg to protect hormonal health.
    | Especially critical for women in a caloric deficit.
    |
    | 3. Remaining calories are split between carbs and fat
    | based on the diet's carb/fat ratio.
    |
    | 4. When protein exceeds 35% of total calories, the distribution is flagged
    | as "challenging" so Mona can provide actionable tips
    | (e.g. "Consider adding a plant-based protein shake to hit your target").
    |
    */

    public static function calculateMacros(
        int      $dailyCalories,
        float    $bodyWeight,
        BodyGoal $goal,
        array    $carbFatRatio,
    ): MacroDistribution
    {
        // 1. Protein: body weight × goal-specific multiplier
        $proteinGrams = (int)round($bodyWeight * $goal->proteinPerKg());
        $proteinCalories = $proteinGrams * self::KCAL_PER_GRAM_PROTEIN;

        $proteinChallenging = $proteinCalories > ($dailyCalories * self::PROTEIN_WARNING_THRESHOLD);

        // 2. Minimum fat floor
        $minimumFatGrams = (int)round($bodyWeight * self::MIN_FAT_GRAMS_PER_KG);

        // 3. Distribute remaining calories by diet ratio
        $remainingCalories = max(0, $dailyCalories - $proteinCalories);

        $carbShare = $carbFatRatio['carbs'] / ($carbFatRatio['carbs'] + $carbFatRatio['fat']);

        $fatGrams = (int)round(($remainingCalories * (1 - $carbShare)) / self::KCAL_PER_GRAM_FAT);
        $carbsGrams = (int)round(($remainingCalories * $carbShare) / self::KCAL_PER_GRAM_CARBS);

        // 4. Enforce minimum fat, redistribute to carbs if needed
        $minimumFatEnforced = false;

        if ($fatGrams < $minimumFatGrams) {
            $minimumFatEnforced = true;
            $fatGrams = $minimumFatGrams;
            $carbsCalories = $remainingCalories - ($fatGrams * self::KCAL_PER_GRAM_FAT);
            $carbsGrams = max(0, (int)round($carbsCalories / self::KCAL_PER_GRAM_CARBS));
        }

        return new MacroDistribution(
            proteinGrams: $proteinGrams,
            carbsGrams: $carbsGrams,
            fatGrams: $fatGrams,
            proteinChallenging: $proteinChallenging,
            minimumFatEnforced: $minimumFatEnforced,
        );
    }
}
