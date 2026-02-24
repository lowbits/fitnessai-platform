<?php

namespace App\Ai\Prompts;

use App\Enums\DietaryPreference;
use App\Enums\DietType;
use App\Models\UserProfile;
use Carbon\Carbon;
use Stringable;

/**
 * Dynamic user-specific context for each meal plan generation.
 * Goes into messages() as a UserMessage — small token footprint, changes per call.
 */
class CreateMealPlanPrompt implements Stringable
{
    /** @var array<int, array{breakfast: float, lunch: float, snack: float, dinner: float}> */
    private const MEAL_SPLITS = [
        ['breakfast' => 0.275, 'lunch' => 0.325, 'snack' => 0.125, 'dinner' => 0.275],
        ['breakfast' => 0.25, 'lunch' => 0.35, 'snack' => 0.10, 'dinner' => 0.30],
        ['breakfast' => 0.30, 'lunch' => 0.30, 'snack' => 0.15, 'dinner' => 0.25],
        ['breakfast' => 0.25, 'lunch' => 0.30, 'snack' => 0.15, 'dinner' => 0.30],
    ];

    public function __construct(
        private UserProfile $profile,
        private string $locale,
        private int $dayNumber,
        private Carbon $date,
        private string $bodyGoal,
    ) {}

    public function __toString(): string
    {
        $metabolismData = $this->profile->getMetabolismData();
        $language = $this->locale === 'de' ? 'German' : 'English';
        $preference = $this->profile->resolveDietaryPreference();
        $dietStyle = $this->profile->diet_style?->value ?? 'balanced';
        $gender = $this->profile->gender?->value;
        $weight = $this->profile->user->getCurrentWeight();
        $coachingNotes = $this->buildCoachingNotes($metabolismData, $preference);
        $goalAdjustment = sprintf('%+d', $metabolismData['goal_adjustment']);
        $dayOfWeek = $this->date->format('l');
        $macroTargets = $this->buildMacroTargets($metabolismData);

        $parts = [
            "{$gender}, {$this->profile->age}y, {$this->profile->height}cm, {$weight}kg",
            "Goal: {$this->bodyGoal} ({$goalAdjustment} kcal) | Diet: {$preference->value} ({$preference->description()}) | Style: {$dietStyle}",
            "Activity: {$metabolismData['activity_multiplier']}x daily + {$metabolismData['training_sessions_per_week']}x training/week",
            $macroTargets,
            $coachingNotes,
            "Day {$this->dayNumber} ({$dayOfWeek}, {$this->date->format('Y-m-d')}) — generate 4 meals: breakfast, lunch, snack, dinner",
            "Language: {$language} for ALL text fields (names, descriptions, ingredients, instructions, tags, allergens)",
        ];

        return implode("\n\n", array_filter($parts));
    }

    private function buildMacroTargets(array $metabolismData): string
    {
        $calories = $metabolismData['daily_calories'];
        $protein = $metabolismData['protein_g'];
        $carbs = $metabolismData['carbs_g'];
        $fat = $metabolismData['fat_g'];

        $split = self::MEAL_SPLITS[($this->dayNumber - 1) % count(self::MEAL_SPLITS)];

        $lines = ["Daily totals: {$calories} kcal | {$protein}g P | {$carbs}g C | {$fat}g F", ''];

        foreach ($split as $mealType => $pct) {
            $label = ucfirst($mealType);
            $calMin = (int) round($calories * $pct * 0.95);
            $calMax = (int) round($calories * $pct * 1.05);
            $pMin = (int) round($protein * $pct * 0.95);
            $pMax = (int) round($protein * $pct * 1.05);
            $cMin = (int) round($carbs * $pct * 0.95);
            $cMax = (int) round($carbs * $pct * 1.05);
            $fMin = (int) round($fat * $pct * 0.95);
            $fMax = (int) round($fat * $pct * 1.05);

            $lines[] = "{$label}: {$calMin}-{$calMax} kcal | {$pMin}-{$pMax}g P | {$cMin}-{$cMax}g C | {$fMin}-{$fMax}g F";
        }

        return implode("\n", $lines);
    }

    /**
     * @param  array<string, mixed>  $metabolism
     */
    private function buildCoachingNotes(array $metabolism, DietaryPreference|DietType $preference): string
    {
        $notes = [];

        if ($metabolism['protein_challenging']) {
            $notes[] = match (true) {
                $preference === DietaryPreference::VEGAN,
                $preference === DietType::VEGAN => 'Protein target is ambitious for a vegan diet. Prioritize seitan, tempeh, edamame, lentils, and consider a plant-based protein shake in the snack.',
                $preference === DietaryPreference::VEGETARIAN,
                $preference === DietType::VEGETARIAN => 'Protein target is high for vegetarian. Leverage eggs, Greek yogurt, cottage cheese, and legumes generously.',
                default => 'Protein target is high relative to total calories. Prioritize lean protein sources across all meals.',
            };
        }

        if ($metabolism['minimum_fat_enforced']) {
            $notes[] = 'Minimum fat intake enforced for hormonal health. Do NOT reduce fat below target.';
        }

        return $notes ? 'Notes: '.implode(' ', $notes) : '';
    }
}
