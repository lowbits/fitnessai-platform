<?php

namespace App\Ai\Prompts;

use App\Enums\CookingPreference;
use App\Enums\DietaryPreference;
use App\Enums\DietType;
use App\Enums\MealVariety;
use App\Models\UserProfile;
use Carbon\Carbon;
use Stringable;

/**
 * Dynamic user-specific context for each meal plan generation.
 * Goes into messages() as a UserMessage — small token footprint, changes per call.
 */
class CreateMealPlanPrompt implements Stringable
{
    /** @var array<int, array<string, float>> */
    private const MEAL_SPLITS = [
        ['breakfast' => 0.275, 'lunch' => 0.325, 'snack' => 0.125, 'dinner' => 0.275],
        ['breakfast' => 0.25, 'lunch' => 0.35, 'snack' => 0.10, 'dinner' => 0.30],
        ['breakfast' => 0.30, 'lunch' => 0.30, 'snack' => 0.15, 'dinner' => 0.25],
        ['breakfast' => 0.25, 'lunch' => 0.30, 'snack' => 0.15, 'dinner' => 0.30],
    ];

    private const DEFAULT_MEALS = ['breakfast', 'lunch', 'snack', 'dinner'];

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
        $selectedMeals = $this->resolveSelectedMeals();
        $macroTargets = $this->buildMacroTargets($metabolismData, $selectedMeals);
        $mealList = implode(', ', $selectedMeals);

        $parts = [
            "{$gender}, {$this->profile->age}y, {$this->profile->height_cm}cm, {$weight}kg",
            "Goal: {$this->bodyGoal} ({$goalAdjustment} kcal) | Diet: {$preference->value} ({$preference->description()}) | Style: {$dietStyle}",
            $this->buildDislikes(),
            "Activity: {$metabolismData['activity_multiplier']}x daily + {$metabolismData['training_sessions_per_week']}x training/week",
            $this->buildCookingConstraint(),
            $macroTargets,
            $coachingNotes,
            $this->buildVarietyAndPrepHint(),
            $this->buildFavoriteSignals(),
            "Day {$this->dayNumber} ({$dayOfWeek}, {$this->date->format('Y-m-d')}) — generate ".count($selectedMeals)." meals: {$mealList}",
            "Language: {$language} for ALL text fields (names, descriptions, ingredients, instructions, tags, allergens)",
        ];

        return implode("\n\n", array_filter($parts));
    }

    /**
     * @return list<string>
     */
    private function resolveSelectedMeals(): array
    {
        $selected = $this->profile->selected_meals;

        if (empty($selected)) {
            return self::DEFAULT_MEALS;
        }

        // Preserve canonical order
        return array_values(array_intersect(self::DEFAULT_MEALS, $selected));
    }

    /**
     * @param  list<string>  $selectedMeals
     */
    private function buildMacroTargets(array $metabolismData, array $selectedMeals): string
    {
        $calories = $metabolismData['daily_calories'];
        $protein = $metabolismData['protein_g'];
        $carbs = $metabolismData['carbs_g'];
        $fat = $metabolismData['fat_g'];

        $allSplits = self::MEAL_SPLITS[($this->dayNumber - 1) % count(self::MEAL_SPLITS)];

        // Filter to selected meals and redistribute proportionally
        $filtered = array_intersect_key($allSplits, array_flip($selectedMeals));
        $sum = array_sum($filtered);
        $split = array_map(fn (float $pct) => $pct / $sum, $filtered);

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

    private function buildDislikes(): string
    {
        $dislikes = $this->profile->food_dislikes;

        if (empty($dislikes)) {
            return '';
        }

        return 'NEVER use these ingredients: '.implode(', ', $dislikes);
    }

    private function buildCookingConstraint(): string
    {
        $pref = $this->profile->cooking_preference;

        if (! $pref) {
            return '';
        }

        return match ($pref) {
            CookingPreference::QUICK => 'Cooking: max 15min total per meal (prefer quick, no-cook, or minimal-prep recipes)',
            CookingPreference::ELABORATE => 'Cooking: user enjoys cooking — feel free to include more complex recipes (up to 60min)',
            default => '',
        };
    }

    private function buildVarietyAndPrepHint(): string
    {
        $variety = $this->profile->meal_variety;
        $mealPrep = $this->profile->meal_prep_enabled;
        $isPrepDay = $this->date->isSunday() || $this->dayNumber === 1;

        // Low variety + meal prep: cook once, eat 2-3 days
        if ($variety === MealVariety::LOW && $mealPrep) {
            return $isPrepDay
                ? 'Meal style: meal prep — design batch-friendly meals (stews, grain bowls, sheet pan recipes) that store well. User will eat the same meals for 2-3 consecutive days. Create 2-3 unique meals that work as leftovers.'
                : 'Meal style: meal prep — reuse meals from the most recent prep day. Same meal can repeat on consecutive days.';
        }

        // Low variety, no meal prep: familiar rotation but not consecutive
        if ($variety === MealVariety::LOW && ! $mealPrep) {
            return 'Meal style: keep it simple — use 5-7 familiar recipes across the week. Meals can repeat but NOT on consecutive days.';
        }

        // High variety + meal prep: prep diverse components, assemble differently
        if ($variety === MealVariety::HIGH && $mealPrep) {
            return $isPrepDay
                ? 'Meal style: prep diverse components (grains, proteins, sauces) that can be assembled into different meals each day. Every meal should feel unique.'
                : 'Meal style: use pre-prepared components but create a unique meal combination each day. No repeated meals.';
        }

        // High variety, no meal prep: everything unique
        if ($variety === MealVariety::HIGH) {
            return 'Meal style: every meal should be completely unique — no repeated meals across the entire plan.';
        }

        // Medium variety (default) — only add meal prep hint if enabled
        if ($mealPrep) {
            return $isPrepDay
                ? 'Meal prep day: prefer batch-friendly meals that store well for 2-3 days.'
                : 'Leftovers OK: meals can use pre-prepared components from meal prep.';
        }

        return '';
    }

    private function buildFavoriteSignals(): string
    {
        $favorites = $this->profile->user->favoriteRecipes ?? collect();

        if ($favorites->isEmpty()) {
            return '';
        }

        $cuisines = $favorites->pluck('cuisine')->filter()->unique()->values();
        $proteins = $favorites->pluck('primary_protein')->filter()->unique()->values();

        $parts = [];
        if ($cuisines->isNotEmpty()) {
            $parts[] = 'Preferred cuisines: '.$cuisines->implode(', ');
        }
        if ($proteins->isNotEmpty()) {
            $parts[] = 'Preferred proteins: '.$proteins->implode(', ');
        }

        return implode(' | ', $parts);
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
