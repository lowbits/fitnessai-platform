<?php

namespace App\Ai\Prompts;

use App\Enums\CookingPreference;
use App\Enums\DietaryPreference;
use App\Enums\DietType;
use App\Models\Meal;
use App\Models\UserProfile;
use App\Support\MealSlotBudget;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Stringable;

/**
 * Dynamic user-specific context for each meal plan generation.
 * Goes into messages() as a UserMessage — small token footprint, changes per call.
 */
class CreateMealPlanPrompt implements Stringable
{
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
            $this->buildCookingFrequency(),
            $macroTargets,
            $coachingNotes,
            $this->buildFavoriteSignals(),
            $this->buildSlotConstraints(),
            "Day {$this->dayNumber} ({$dayOfWeek}, {$this->date->format('Y-m-d')}, ".($this->date->isWeekday() ? 'workday' : 'weekend').') — generate '.count($newSlots)." meals: {$mealList}",
            "Language: {$language} for human-readable fields (name, description, ingredients[].name, instructions). Tags stay in the user's language. EXCEPT primary_protein, cuisine, format, hero_veg, allergens, ingredients[].unit — those MUST be the canonical English enum values from the schema regardless of locale (e.g. use \"piece\" not \"Stück\", \"tbsp\" not \"EL\", \"pinch\" not \"Prise\").",
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
     * Two presentation rules cover both common cases:
     *
     * 1. Renormalization runs across the user's *full* selected slots, so a
     *    user who skipped snack still gets the missing share redistributed
     *    across the remaining 3 (their daily target stays whole).
     *
     * 2. Per-slot ranges are emitted only for NEW slots — REPEAT slots have
     *    fixed macros from their source Meal and PHP inserts them after the
     *    AI call. When that subset doesn't span the whole day, we add a
     *    "Your budget" line so the AI's per-slot ranges actually sum to the
     *    budget it's told to hit. Without it, the AI sees a math conflict
     *    between "Daily total: 2554" and ranges that max at 2346 — and
     *    Thomas lands at -303 kcal trying to honor the wrong target.
     *
     * @param  list<string>  $userSelectedSlots
     * @param  list<string>  $newSlots
     */
    private function buildMacroTargets(array $metabolismData, array $userSelectedSlots, array $newSlots): string
    {
        $split = $this->slotShares($userSelectedSlots);
        $hasRepeatSlots = count($newSlots) < count($userSelectedSlots);
        $newSlotsShare = array_sum(array_intersect_key($split, array_flip($newSlots)));

        $lines = [$this->macroLine('Daily totals', $metabolismData, 1.0)];

        if ($hasRepeatSlots) {
            $lines[] = $this->macroLine("Your budget for the NEW slots you'll generate today", $metabolismData, $newSlotsShare);
            $lines[] = '(The rest is locked in by PHP-inserted repeat slot(s) — do NOT regenerate them.)';
        }

        $lines[] = '';

        foreach ($split as $slot => $pct) {
            if (in_array($slot, $newSlots, true)) {
                $lines[] = $this->macroRange(ucfirst($slot), $metabolismData, $pct);
            }
        }

        $lines[] = '';
        $lines[] = 'HARD MACRO RULES (per-slot min/max above are boundaries, not suggestions):';
        $lines[] = '- Every "Pg (min N)" must be met or exceeded — no protein undershoot per meal.';
        $lines[] = '- Every "kcal (min N / max N)" must land INSIDE the band — do NOT undershoot the min, do NOT exceed the max. Hitting the calorie target matters as much as the protein floor; a meal that comes in well under its kcal min is wrong.';
        $lines[] = '- "Cg (max N)" must not be exceeded — cut carbs before touching protein when trimming down to the kcal max.';
        $lines[] = '- Every "Fg (min N)" must be met or exceeded.';
        $lines[] = '- If a dish naturally lands short on protein, add a lean source (whey, skyr, quark, cottage cheese, eggs, tofu, chicken breast, edamame). This is authentic across German, Mediterranean, Middle-Eastern and American cuisines — it does NOT break culinary coherence.';
        $lines[] = '- If a dish lands under its kcal min, scale up the portion or add a calorie-dense component that fits the dish (nuts, seeds, olive oil, avocado, whole grains, cheese) until it reaches the band — never leave the day short of its calorie target.';

        return implode("\n", $lines);
    }

    /**
     * Renormalize MEAL_SPLIT against the user's selected slots so the shares
     * always sum to 1.0 (a 3-slot user gets the snack share spread).
     *
     * @param  list<string>  $userSelectedSlots
     * @return array<string, float>
     */
    private function slotShares(array $userSelectedSlots): array
    {
        return MealSlotBudget::sharesFor($userSelectedSlots);
    }

    /**
     * "Label: X kcal | Yg P | Zg C | Wg F" for a single share of the day's macros.
     */
    private function macroLine(string $label, array $metabolism, float $share): string
    {
        $m = MealSlotBudget::applyShare($metabolism, $share);

        return "{$label}: {$m['calories']} kcal | {$m['protein_g']}g P | {$m['carbs_g']}g C | {$m['fat_g']}g F";
    }

    /**
     * "Label: {target} kcal (min/max) | {target}g P (min) | ..." — directional per macro.
     * kcal is a two-sided ±5% band (undershoot AND overshoot bad); protein/fat show
     * floors, carbs a ceiling.
     */
    private function macroRange(string $label, array $metabolism, float $share): string
    {
        $target = MealSlotBudget::applyShare($metabolism, $share);
        $low = MealSlotBudget::applyShare($metabolism, $share * 0.95);
        $high = MealSlotBudget::applyShare($metabolism, $share * 1.05);

        return sprintf(
            '%s: %d kcal (min %d / max %d) | %dg P (min %d) | %dg C (max %d) | %dg F (min %d)',
            $label,
            $target['calories'], $low['calories'], $high['calories'],
            $target['protein_g'], $low['protein_g'],
            $target['carbs_g'], $high['carbs_g'],
            $target['fat_g'], $low['fat_g'],
        );
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

        $list = implode(', ', $dislikes);

        return "HARD CONSTRAINT — disliked ingredients: {$list}. "
            .'NEVER include an ingredient whose name contains any of these strings (case-insensitive). '
            ."This includes compound words: 'apfel' blocks 'Apfelmus', 'Apfelsaft'; 'haselnuss' blocks 'Haselnusscreme', 'Haselnussmus'. "
            .'If you cannot make the dish without these ingredients, pick a different recipe entirely.';
    }

    private function buildCookingConstraint(): string
    {
        $pref = $this->profile->cooking_preference;

        if (! $pref) {
            return '';
        }

        $isWorkday = $this->date->isWeekday();

        return match ($pref) {
            CookingPreference::QUICK => $isWorkday
                ? 'Cooking: WORKDAY — max 12min total prep+cook per meal. User is busy. Prefer no-cook, sheet-pan, or one-pan recipes.'
                : 'Cooking: WEEKEND — max 25min total prep+cook per meal. User still wants speed but has slightly more time.',

            CookingPreference::NORMAL => $isWorkday
                ? 'Cooking: WORKDAY — max 20min total prep+cook per meal. User can cook but is busy.'
                : 'Cooking: WEEKEND — up to 45min total prep+cook OK. Feel free to include one slightly more elaborate "anchor" meal (Sunday roast, weekend brunch).',

            CookingPreference::ELABORATE => $isWorkday
                ? 'Cooking: WORKDAY — max 30min total prep+cook per meal. User enjoys cooking but works during the week.'
                : 'Cooking: WEEKEND — up to 60min total prep+cook OK. User enjoys cooking — include at least one "anchor" recipe (Sunday roast, slow-braised dish, fresh pasta).',
        };
    }

    private function buildCookingFrequency(): string
    {
        return $this->profile->cooking_frequency?->promptHint() ?? '';
    }

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

        $lines = ['Prior meals this week (across ALL slots). Every NEW meal you generate today MUST differ on at least ONE of {primary_protein, format} from EVERY meal listed below. Same vegetable across multiple meals is fine and encouraged for shopping efficiency:'];

        foreach ($forbidden as $meal) {
            $protein = $meal->primary_protein ?: '?';
            $format = $meal->format ?: '?';
            $slot = $meal->type ?: '?';
            $lines[] = "- \"{$meal->name}\" [slot: {$slot}, protein: {$protein}, format: {$format}]";
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

        if ($this->date->isMonday()) {
            $notes[] = 'Leftover hint: today is Monday after the weekend. Lunch CAN be framed as leftovers from a hearty weekend dinner (wrap, bowl, salad with cold protein). This is a nice-to-have, not a requirement — only do it if it fits naturally.';
        }

        return $notes ? 'Notes: '.implode(' ', $notes) : '';
    }
}
