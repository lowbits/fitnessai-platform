<?php

namespace App\Actions;

use App\Enums\DietaryPreference;
use App\Enums\MealVariety;
use App\Enums\PrimaryProtein;
use App\Models\Meal;
use App\Models\MealPlan;
use App\Models\Plan;
use App\Models\Recipe;
use App\Models\UserProfile;
use App\Services\Recipe\RecipeAffinity;
use App\Services\Recipe\RecipeFinder;
use App\Support\MealSlotBudget;
use Illuminate\Support\Collection;

/**
 * Decides per-slot meal action (new vs repeat) for a single day in a plan.
 *
 * Variety is a budget: each tier has a target number of distinct recipes per
 * slot over a 7-day cycle. Days within budget get a fresh AI-designed meal —
 * the prior slot meals are passed as a forbidden list so the new one differs
 * on at least one of three axes (primary_protein, format, hero_veg) from
 * every prior meal. That kills template-twins (e.g. tofu-broccoli udon and
 * tofu-broccoli soba — same protein/format/veg, blocked). Days past budget
 * reuse the exact prior Meal record (same grams, same macros) — no AI
 * regeneration, which kills the near-duplicate problem at the source.
 */
class PlanMealSlotsForDay
{
    /** @var list<string> */
    public const SLOTS = ['breakfast', 'lunch', 'snack', 'dinner'];

    public function __construct(
        private readonly RecipeFinder $finder,
        private readonly RecipeAffinity $affinity,
    ) {}

    /**
     * @return array<string, array{action: 'new'|'repeat'|'reuse', forbidden_meals?: Collection<int, Meal>, repeat_from?: Meal, recipe?: Recipe}>
     */
    public function handle(Plan $plan, int $dayNumber, UserProfile $profile): array
    {
        $tier = $profile->meal_variety ?? MealVariety::MEDIUM;
        $targets = $tier->perSlotDistinctTargets();
        $selectedSlots = $this->resolveSelectedSlots($profile);
        $slotKcal = MealSlotBudget::mainSlotKcal(
            $selectedSlots,
            (int) $profile->getMetabolismData()['daily_calories'],
            $profile->auto_fill_calories ?? true,
        );
        $priorMeals = $this->fetchPriorWeekMeals($plan, $dayNumber);
        $allWeekForbidden = $priorMeals->unique('name')->values();
        $context = $this->finderContext($profile, $slotKcal);

        $user = $profile->user;
        $cooldownIds = $this->affinity->cooldownIds($user)->all();
        $affinityScores = $this->affinity->scoresFor($user)->all();

        $result = [];

        foreach ($selectedSlots as $slot) {
            $slotMeals = $priorMeals->where('type', $slot);
            $distinctSoFar = $slotMeals->pluck('name')->unique()->count();
            $target = $targets[$slot];

            if ($distinctSoFar >= $target) {
                $result[$slot] = [
                    'action' => 'repeat',
                    'repeat_from' => $this->pickRepeatCandidate($slotMeals, $dayNumber),
                ];

                continue;
            }

            $recipe = $this->finder->findCandidate(
                mealType: $slot,
                targetKcal: $context['slot_kcal'][$slot],
                locale: $context['locale'],
                allowedProteins: $context['allowed_proteins'],
                dislikes: $context['dislikes'],
                forbiddenAxes: $allWeekForbidden,
                excludeIds: $cooldownIds,
                affinityScores: $affinityScores,
            );

            if ($recipe !== null) {
                $result[$slot] = ['action' => 'reuse', 'recipe' => $recipe];

                continue;
            }

            $result[$slot] = [
                'action' => 'new',
                'forbidden_meals' => $allWeekForbidden,
            ];
        }

        return $result;
    }

    /**
     * @param  array<string, int>  $slotKcal  effective per-slot kcal (mains capped, includes flex)
     * @return array{locale: string, allowed_proteins: list<string>, dislikes: list<string>, slot_kcal: array<string, int>}
     */
    private function finderContext(UserProfile $profile, array $slotKcal): array
    {
        $diet = $profile->resolveDietaryPreference();
        $diet = $diet instanceof DietaryPreference ? $diet : DietaryPreference::OMNIVORE;

        $allowed = array_map(fn (PrimaryProtein $p) => $p->value, PrimaryProtein::allowedFor($diet));

        return [
            'locale' => $profile->user->locale ?? 'en',
            'allowed_proteins' => $allowed,
            'dislikes' => array_map(fn (string $d) => mb_strtolower(trim($d)), $profile->food_dislikes ?? []),
            'slot_kcal' => $slotKcal,
        ];
    }

    /**
     * Variety is scoped to a 7-day cycle (days 1-7, 8-14, ...), so week 2
     * gets a fresh distinct quota — the user isn't stuck eating the same
     * 5 lunches for the whole 28-day plan.
     *
     * @return Collection<int, Meal>
     */
    private function fetchPriorWeekMeals(Plan $plan, int $dayNumber): Collection
    {
        $weekStart = (int) (floor(($dayNumber - 1) / 7) * 7) + 1;

        $mealPlanIds = MealPlan::query()
            ->where('plan_id', $plan->id)
            ->where('status', 'generated')
            ->whereBetween('day_number', [$weekStart, $dayNumber - 1])
            ->pluck('id');

        if ($mealPlanIds->isEmpty()) {
            return collect();
        }

        return Meal::query()
            ->whereIn('meal_plan_id', $mealPlanIds)
            ->with('mealPlan:id,day_number')
            ->get();
    }

    /**
     * @return list<string>
     */
    private function resolveSelectedSlots(UserProfile $profile): array
    {
        $selected = $profile->selected_meals;

        if (empty($selected)) {
            return self::SLOTS;
        }

        return array_values(array_intersect(self::SLOTS, $selected));
    }

    /**
     * Picks a prior meal to reuse for the same slot. Hard rule: never reuse
     * yesterday's meal — that creates the "same lunch and dinner two days in
     * a row" monotony users hate. If the only option IS yesterday's, accept
     * it (better than failing the slot).
     *
     * @param  Collection<int, Meal>  $slotMeals
     */
    private function pickRepeatCandidate(Collection $slotMeals, int $todayDayNumber): Meal
    {
        $notYesterday = $slotMeals->filter(fn (Meal $m) => $m->mealPlan->day_number !== $todayDayNumber - 1);
        $pool = $notYesterday->isNotEmpty() ? $notYesterday : $slotMeals;

        $countsByName = $pool->groupBy('name')->map->count();
        $recency = fn (Meal $m) => $todayDayNumber - $m->mealPlan->day_number;
        $effort = fn (Meal $m) => ($m->prep_time_minutes ?? 0) + ($m->cook_time_minutes ?? 0);

        return $pool
            ->sort(fn (Meal $a, Meal $b) => [$countsByName[$a->name], $recency($a), $effort($a)]
                <=> [$countsByName[$b->name], $recency($b), $effort($b)])
            ->first();
    }
}
