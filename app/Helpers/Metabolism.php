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

    /**
     * Hard cap: protein may never exceed this fraction of daily calories.
     * Backstop for edge cases where lean-mass estimation over-prescribes.
     */
    public const PROTEIN_MAX_CALORIE_FRACTION = 0.35;

    private const KCAL_PER_GRAM_PROTEIN = 4;

    private const KCAL_PER_GRAM_CARBS = 4;

    private const KCAL_PER_GRAM_FAT = 9;

    /**
     * Calculate Basal Metabolic Rate using Mifflin-St Jeor equation.
     *
     * More accurate than Harris-Benedict for modern populations.
     *
     * @param  int  $age  Years
     * @param  float  $height  Centimeters
     * @param  float  $weight  Kilograms
     * @return int Calories per day
     */
    public static function calculateBMR(
        Gender $gender,
        int $age,
        float $height,
        float $weight
    ): int {
        // Mifflin-St Jeor: (10 × weight) + (6.25 × height) - (5 × age) + s
        // where s = +5 for males, -161 for females
        $bmr = (10 * $weight) + (6.25 * $height) - (5 * $age);

        if ($gender->usesMaleFormula()) {
            $bmr += 5;
        } else {
            $bmr -= 161;
        }

        return (int) round($bmr);
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

        return (int) round($weeklyCalories / 7);
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
        int $bmr,
        ActivityLevel $activityLevel,
        int $sessionsPerWeek,
        float $bodyWeight,
    ): int {
        $baseExpenditure = (int) round($bmr * $activityLevel->tdeeMultiplier());

        return $baseExpenditure + self::trainingCalories($sessionsPerWeek, $bodyWeight);
    }

    /**
     * Calculate daily calorie target based on goal.
     *
     * @param  int  $tdee  Total Daily Energy Expenditure
     * @return int Adjusted calories per day
     */
    public static function calculateDailyCalories(int $tdee, BodyGoal $goal): int
    {
        return $tdee + $goal->calorieAdjustment();
    }

    /*
    |--------------------------------------------------------------------------
    | Body Composition Estimation
    |--------------------------------------------------------------------------
    |
    | Deurenberg formula estimates body fat % from BMI, age, and sex.
    | Rough for muscular individuals but reasonable for the general
    | population, especially weight-loss users (who skew higher BF%).
    |
    | Used to calculate lean body mass for protein targets during cutting,
    | where ISSN recommends 2.3–3.1 g/kg of LEAN mass (not total weight).
    |
    */

    public static function estimateBodyFatPercent(Gender $gender, int $age, float $height, float $weight): float
    {
        $bmi = $weight / (($height / 100) ** 2);
        $sex = $gender->usesMaleFormula() ? 1 : 0;

        // Deurenberg et al. (1991): BF% = 1.2 × BMI + 0.23 × age − 10.8 × sex − 5.4
        $bf = (1.2 * $bmi) + (0.23 * $age) - (10.8 * $sex) - 5.4;

        return max(5.0, min(60.0, round($bf, 1)));
    }

    public static function estimateLeanMass(Gender $gender, int $age, float $height, float $weight): float
    {
        $bf = self::estimateBodyFatPercent($gender, $age, $height, $weight);

        return round($weight * (1 - $bf / 100), 1);
    }

    /*
    |--------------------------------------------------------------------------
    | Macro Distribution
    |--------------------------------------------------------------------------
    |
    | 1. Protein base weight depends on goal:
    |    - Weight loss: uses estimated lean body mass (ISSN: 2.3–3.1 g/kg lean)
    |    - Muscle gain / maintenance: uses total body weight (ISSN: 1.6–2.2 g/kg)
    |    Then capped at 35% of daily calories as a universal backstop.
    |
    | 2. Fat has a floor of 0.8 g/kg to protect hormonal health.
    |    Especially critical for women in a caloric deficit.
    |
    | 3. Remaining calories are split between carbs and fat
    |    based on the diet's carb/fat ratio.
    |
    | 4. When protein exceeds 35% of total calories, the distribution is flagged
    |    as "challenging" so Mona can provide actionable tips.
    |
    */

    public static function calculateMacros(
        int $dailyCalories,
        float $bodyWeight,
        BodyGoal $goal,
        array $carbFatRatio,
        ?Gender $gender = null,
        ?int $age = null,
        ?float $height = null,
    ): MacroDistribution {
        // 1. Protein: base weight depends on goal
        //    Weight loss → lean mass (prevents over-prescribing for higher-BF users)
        //    Other goals → total body weight
        $proteinBaseWeight = $bodyWeight;

        if ($goal->resolveCanonical() === BodyGoal::LOSE_WEIGHT && $gender && $age && $height) {
            $proteinBaseWeight = self::estimateLeanMass($gender, $age, $height, $bodyWeight);
        }

        $proteinGrams = (int) round($proteinBaseWeight * $goal->proteinPerKg());

        // 1b. Hard cap: protein may not exceed 35% of daily calories
        $maxProteinGrams = (int) floor($dailyCalories * self::PROTEIN_MAX_CALORIE_FRACTION / self::KCAL_PER_GRAM_PROTEIN);
        $proteinCapped = $proteinGrams > $maxProteinGrams;

        if ($proteinCapped) {
            $proteinGrams = $maxProteinGrams;
        }

        $proteinCalories = $proteinGrams * self::KCAL_PER_GRAM_PROTEIN;

        $proteinChallenging = $proteinCalories > ($dailyCalories * self::PROTEIN_WARNING_THRESHOLD);

        // 2. Minimum fat floor
        $minimumFatGrams = (int) round($bodyWeight * self::MIN_FAT_GRAMS_PER_KG);

        // 3. Distribute remaining calories by diet ratio
        $remainingCalories = max(0, $dailyCalories - $proteinCalories);

        $carbShare = $carbFatRatio['carbs'] / ($carbFatRatio['carbs'] + $carbFatRatio['fat']);

        $fatGrams = (int) round(($remainingCalories * (1 - $carbShare)) / self::KCAL_PER_GRAM_FAT);
        $carbsGrams = (int) round(($remainingCalories * $carbShare) / self::KCAL_PER_GRAM_CARBS);

        // 4. Enforce minimum fat, redistribute to carbs if needed
        $minimumFatEnforced = false;

        if ($fatGrams < $minimumFatGrams) {
            $minimumFatEnforced = true;
            $fatGrams = $minimumFatGrams;
            $carbsCalories = $remainingCalories - ($fatGrams * self::KCAL_PER_GRAM_FAT);
            $carbsGrams = max(0, (int) round($carbsCalories / self::KCAL_PER_GRAM_CARBS));
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
