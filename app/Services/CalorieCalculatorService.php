<?php

namespace App\Services;

use App\Enums\BodyGoal;
use App\Enums\DietaryPreference;
use App\Enums\Gender;
use App\Helpers\Metabolism;

class CalorieCalculatorService
{
    /**
     * Physical Activity Level factor per public activity option.
     *
     * @var array<string, float>
     */
    private const ACTIVITY_FACTORS = [
        'sedentary' => 1.2,
        'light' => 1.375,
        'moderate' => 1.55,
        'active' => 1.725,
        'veryActive' => 1.9,
    ];

    /**
     * @return array{
     *     bmr: int, tdee: int, calories: int, goalDelta: int,
     *     protein: array{grams: int, kcal: int, share: float},
     *     carbs: array{grams: int, kcal: int, share: float},
     *     fat: array{grams: int, kcal: int, share: float},
     * }
     */
    public function calculate(
        Gender $gender,
        int $age,
        float $heightCm,
        float $weightKg,
        string $activity,
        BodyGoal $goal,
    ): array {
        $factor = self::ACTIVITY_FACTORS[$activity] ?? self::ACTIVITY_FACTORS['moderate'];

        $bmr = Metabolism::calculateBMR($gender, $age, $heightCm, $weightKg);
        $tdee = (int) round($bmr * $factor);
        $calories = Metabolism::calculateDailyCalories($tdee, $goal);

        $macros = Metabolism::calculateMacros(
            dailyCalories: $calories,
            bodyWeight: $weightKg,
            goal: $goal,
            carbFatRatio: DietaryPreference::OMNIVORE->carbFatRatio(),
            gender: $gender,
            age: $age,
            height: $heightCm,
        );

        $macroTotal = $macros->totalCalories();

        return [
            'bmr' => $bmr,
            'tdee' => $tdee,
            'calories' => $calories,
            'goalDelta' => $goal->calorieAdjustment(),
            'protein' => $this->portion($macros->proteinGrams, 4, $macroTotal),
            'carbs' => $this->portion($macros->carbsGrams, 4, $macroTotal),
            'fat' => $this->portion($macros->fatGrams, 9, $macroTotal),
        ];
    }

    /**
     * @return array{grams: int, kcal: int, share: float}
     */
    private function portion(int $grams, int $kcalPerGram, int $macroTotal): array
    {
        $kcal = $grams * $kcalPerGram;

        return [
            'grams' => $grams,
            'kcal' => $kcal,
            'share' => $macroTotal > 0 ? round($kcal / $macroTotal, 4) : 0.0,
        ];
    }

    /**
     * Compute from a validated input array (the six public form fields).
     *
     * @param  array{gender: string, age: int|string, height: int|string|float, weight: int|string|float, activity: string, goal: string}  $data
     * @return array<string, mixed>
     */
    public function calculateFromArray(array $data): array
    {
        return $this->calculate(
            gender: Gender::from($data['gender']),
            age: (int) $data['age'],
            heightCm: (float) $data['height'],
            weightKg: (float) $data['weight'],
            activity: $data['activity'],
            goal: $this->resolveGoal($data['goal']),
        );
    }

    /**
     * Map the calculator's public goal values onto the product's BodyGoal enum.
     */
    public function resolveGoal(string $goal): BodyGoal
    {
        return match ($goal) {
            'lose' => BodyGoal::LOSE_WEIGHT,
            'gain' => BodyGoal::BUILD_MUSCLE,
            default => BodyGoal::GET_FIT,
        };
    }
}
