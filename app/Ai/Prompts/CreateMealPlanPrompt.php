<?php

namespace App\Ai\Prompts;

use App\Enums\CookingPreference;
use App\Enums\DietaryPreference;
use App\Enums\DietType;
use App\Models\Meal;
use App\Models\UserProfile;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Stringable;

/**
 * Dynamic user-specific context for each meal plan generation.
 * Goes into messages() as a UserMessage — small token footprint, changes per call.
 */
class CreateMealPlanPrompt implements Stringable
{
    /**
     * Fixed per-slot share of daily calories.
     *
     * Constant across every day, by design. A varying day-by-day split causes
     * exact-repeat meals (frozen at their original day's macros) to drift off
     * the slot target when reused later — e.g. a lunch sized for a 0.35-share
     * day becoming +153 kcal over when reused on a 0.30-share day. Keeping
     * the split flat lets repeats land on target wherever they appear, and
     * the user-perceived variety (recipes, cuisines, proteins) is unaffected.
     *
     * @var array<string, float>
     */
    private const MEAL_SPLIT = [
        'breakfast' => 0.275,
        'lunch' => 0.325,
        'snack' => 0.125,
        'dinner' => 0.275,
    ];

    private const DEFAULT_MEALS = ['breakfast', 'lunch', 'snack', 'dinner'];

    /**
     * @param  array<string, array{action: 'new'|'repeat', forbidden_meals?: Collection<int, Meal>, repeat_from?: Meal}>  $slotPlan
     */
    public function __construct(
        private UserProfile $profile,
        private string $locale,
        private int $dayNumber,
        private Carbon $date,
        private string $bodyGoal,
        private array $slotPlan,
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
        $newSlots = $this->newSlots();
        $macroTargets = $this->buildMacroTargets($metabolismData, $this->resolveSelectedSlots(), $newSlots);
        $mealList = implode(', ', $newSlots);

        $parts = [
            "{$gender}, {$this->profile->age}y, {$this->profile->height_cm}cm, {$weight}kg",
            "Goal: {$this->bodyGoal} ({$goalAdjustment} kcal) | Diet: {$preference->value} ({$preference->description()}) | Style: {$dietStyle}",
            $this->buildDislikes(),
            "Activity: {$metabolismData['activity_multiplier']}x daily + {$metabolismData['training_sessions_per_week']}x training/week",
            $this->buildCookingConstraint(),
            $macroTargets,
            $coachingNotes,
            $this->buildFavoriteSignals(),
            $this->buildSlotConstraints(),
            "Day {$this->dayNumber} ({$dayOfWeek}, {$this->date->format('Y-m-d')}, ".($this->date->isWeekday() ? 'workday' : 'weekend').') — generate '.count($newSlots)." meals: {$mealList}",
            "Language: {$language} for human-readable fields (name, description, ingredients[].name, instructions). Tags stay in the user's language. EXCEPT primary_protein, cuisine, format, hero_veg, allergens — those MUST be the canonical English enum values from the schema regardless of locale.",
        ];

        return implode("\n\n", array_filter($parts));
    }

    /**
     * Slots the AI must generate today (NEW only — REPEAT slots are filled by PHP after).
     *
     * @return list<string>
     */
    private function newSlots(): array
    {
        return array_values(array_filter(
            array_keys($this->slotPlan),
            fn (string $slot) => $this->slotPlan[$slot]['action'] === 'new',
        ));
    }

    /**
     * Per-slot calorie/macro targets.
     *
     * Renormalization is done over the user's full selected slots — so a user
     * who skipped snack still gets the missing 12.5% redistributed across the
     * remaining 3 slots. But emit only NEW slot lines, because REPEAT slots
     * already have fixed macros from their source meal. If we renormalized
     * only over NEW slots, today's NEW slots would each be told to swell to
     * cover the REPEAT slots' calories too — that's the day-5 5,400 kcal bug.
     *
     * When at least one slot is a REPEAT, the per-slot ranges (scoped to
     * natural shares) won't sum to the full daily total — and the AI sees a
     * math conflict between "Daily total: 2554" and ranges that max at 2346.
     * The "Your budget for the NEW slots" line resolves this: it shows the
     * AI exactly what its slots must sum to, leaving the repeat slot's share
     * out of the equation since PHP will insert it after.
     *
     * @param  list<string>  $userSelectedSlots
     * @param  list<string>  $newSlots
     */
    private function buildMacroTargets(array $metabolismData, array $userSelectedSlots, array $newSlots): string
    {
        $calories = $metabolismData['daily_calories'];
        $protein = $metabolismData['protein_g'];
        $carbs = $metabolismData['carbs_g'];
        $fat = $metabolismData['fat_g'];

        $filtered = array_intersect_key(self::MEAL_SPLIT, array_flip($userSelectedSlots));
        $sum = array_sum($filtered);
        $split = $sum > 0 ? array_map(fn (float $pct) => $pct / $sum, $filtered) : [];

        $newShare = 0.0;
        foreach ($newSlots as $slot) {
            $newShare += $split[$slot] ?? 0.0;
        }

        $lines = ["Daily totals: {$calories} kcal | {$protein}g P | {$carbs}g C | {$fat}g F"];

        if ($newShare < 0.999) {
            $newCal = (int) round($calories * $newShare);
            $newP = (int) round($protein * $newShare);
            $newC = (int) round($carbs * $newShare);
            $newF = (int) round($fat * $newShare);
            $lines[] = "Your budget for the NEW slots you'll generate today: {$newCal} kcal | {$newP}g P | {$newC}g C | {$newF}g F";
            $lines[] = '(The rest is locked in by PHP-inserted repeat slot(s) — do NOT regenerate them.)';
        }

        $lines[] = '';

        foreach ($split as $mealType => $pct) {
            if (! in_array($mealType, $newSlots, true)) {
                continue;
            }

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
     * The user's full selected slots (all 4 by default). Used as the base
     * for macro normalization — distinct from `newSlots()` which is the
     * subset the AI generates *today*.
     *
     * @return list<string>
     */
    private function resolveSelectedSlots(): array
    {
        $selected = $this->profile->selected_meals;

        if (empty($selected)) {
            return self::DEFAULT_MEALS;
        }

        return array_values(array_intersect(self::DEFAULT_MEALS, $selected));
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

    /**
     * Cross-slot template-twin guard. Lists ALL prior meals this week
     * (regardless of slot) with their three variety axes. EVERY new meal
     * generated today — any slot — MUST differ on at least one of
     * {primary_protein, format, hero_veg} from EVERY meal listed.
     *
     * Why cross-slot, not per-slot: Spinat-Ricotta-Lasagne (dinner) and
     * Spinat-Ricotta-Cannelloni (lunch) both classify as dairy/bake/spinach.
     * The per-slot rule never compared them because they're in different
     * slots, and three Italian baked-pasta-spinach dishes shipped in one
     * Thomas week. Plan-wide enforcement blocks the whole class.
     *
     * REPEAT slots are not generated by the AI (PHP duplicates the prior
     * Meal record after this call) — they're omitted from this section.
     */
    private function buildSlotConstraints(): string
    {
        $forbidden = collect();
        foreach ($this->slotPlan as $plan) {
            if ($plan['action'] !== 'new') {
                continue;
            }
            $forbidden = $forbidden->merge($plan['forbidden_meals'] ?? collect());
        }

        $forbidden = $forbidden->unique('name')->values();

        if ($forbidden->isEmpty()) {
            return '';
        }

        $lines = ['Prior meals this week (across ALL slots). Every NEW meal you generate today — regardless of slot — MUST differ on at least ONE of {primary_protein, format, hero_veg} from EVERY meal listed below. Matching all three on any single listed meal is forbidden — that\'s a template-twin:'];

        foreach ($forbidden as $meal) {
            $protein = $meal->primary_protein ?: '?';
            $format = $meal->format ?: '?';
            $heroVeg = $meal->hero_veg ?: '?';
            $slot = $meal->type ?: '?';
            $lines[] = "- \"{$meal->name}\" [slot: {$slot}, protein: {$protein}, format: {$format}, hero_veg: {$heroVeg}]";
        }

        return implode("\n", $lines);
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
