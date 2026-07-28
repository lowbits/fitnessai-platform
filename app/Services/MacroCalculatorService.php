<?php

namespace App\Services;

use App\Enums\ActivityLevel;
use App\Enums\BodyGoal;
use App\Enums\DietaryPreference;
use App\Enums\Gender;
use App\Helpers\Metabolism;

/**
 * Drives the public macronutrient calculator off the exact same domain logic
 * the app uses to build real plans (App\Helpers\Metabolism). Reusing the
 * product algorithm keeps the marketing tool's numbers consistent with what a
 * user actually receives in the app.
 */
class MacroCalculatorService
{
    /**
     * @return array{
     *     bmr: int, tdee: int, calories: int,
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
        ActivityLevel $activity,
        int $trainingSessions,
        BodyGoal $goal,
        DietaryPreference $diet,
    ): array {
        $bmr = Metabolism::calculateBMR($gender, $age, $heightCm, $weightKg);
        $tdee = Metabolism::calculateTDEE($bmr, $activity, $trainingSessions, $weightKg);
        $calories = Metabolism::calculateDailyCalories($tdee, $goal);

        $macros = Metabolism::calculateMacros(
            dailyCalories: $calories,
            bodyWeight: $weightKg,
            goal: $goal,
            carbFatRatio: $diet->carbFatRatio(),
            gender: $gender,
            age: $age,
            height: $heightCm,
        );

        // Shares are taken against the macro total (not the calorie target) so
        // the three bars fill exactly 100%. The target may differ by a few kcal
        // from the macro sum due to per-gram rounding — that is expected.
        $macroTotal = $macros->totalCalories();

        return [
            'bmr' => $bmr,
            'tdee' => $tdee,
            'calories' => $calories,
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
     * Compute from a validated/typed input array (the 8 public form fields).
     *
     * @param  array{gender: string, age: int|string, height: int|string|float, weight: int|string|float, activity: string, sessions: int|string, goal: string, diet: string}  $data
     * @return array<string, mixed>
     */
    public function calculateFromArray(array $data): array
    {
        return $this->calculate(
            gender: Gender::from($data['gender']),
            age: (int) $data['age'],
            heightCm: (float) $data['height'],
            weightKg: (float) $data['weight'],
            activity: ActivityLevel::from($data['activity']),
            trainingSessions: (int) $data['sessions'],
            goal: $this->resolveGoal($data['goal']),
            diet: DietaryPreference::from($data['diet']),
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
