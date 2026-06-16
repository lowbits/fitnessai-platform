<?php

namespace App\Actions;

use App\Enums\MealVariety;
use App\Models\Meal;
use App\Models\MealPlan;
use App\Models\Plan;
use App\Models\UserProfile;
use Illuminate\Support\Collection;

/**
 * Decides per-slot meal action (new vs repeat) for a single day in a plan.
 *
 * Variety is a budget: each tier has a target number of distinct recipes per
 * slot over a 7-day cycle. Days within budget get a fresh AI-designed meal —
 * the prior slot meals are passed as a forbidden list so the new one differs
 * meaningfully (different primary protein AND different cuisine). Days past
 * budget reuse the exact prior Meal record (same grams, same macros) — no AI
 * regeneration, which kills the near-duplicate problem at the source.
 */
class PlanMealSlotsForDay
{
    /** @var list<string> */
    public const SLOTS = ['breakfast', 'lunch', 'snack', 'dinner'];

    /**
     * @return array<string, array{action: 'new'|'repeat', forbidden_meals?: Collection<int, Meal>, repeat_from?: Meal}>
     */
    public function handle(Plan $plan, int $dayNumber, UserProfile $profile): array
    {
        $tier = $profile->meal_variety ?? MealVariety::MEDIUM;
        $targets = $tier->perSlotDistinctTargets();
        $selectedSlots = $this->resolveSelectedSlots($profile);
        $priorMeals = $this->fetchPriorWeekMeals($plan, $dayNumber);

        $result = [];

        foreach ($selectedSlots as $slot) {
            $slotMeals = $priorMeals->where('type', $slot);
            $distinctSoFar = $slotMeals->pluck('name')->unique()->count();
            $target = $targets[$slot];

            if ($distinctSoFar < $target) {
                $result[$slot] = [
                    'action' => 'new',
                    'forbidden_meals' => $slotMeals->unique('name')->values(),
                ];

                continue;
            }

            $result[$slot] = [
                'action' => 'repeat',
                'repeat_from' => $this->pickRepeatCandidate($slotMeals),
            ];
        }

        return $result;
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
     * Pick the prior slot meal that has been used the fewest times this week
     * (least-used spreads repeats evenly). Ties broken by earliest day_number.
     *
     * @param  Collection<int, Meal>  $slotMeals
     */
    private function pickRepeatCandidate(Collection $slotMeals): Meal
    {
        $countsByName = $slotMeals->groupBy('name')->map->count();

        return $slotMeals
            ->sort(fn (Meal $a, Meal $b) => [$countsByName[$a->name], $a->mealPlan->day_number]
                <=> [$countsByName[$b->name], $b->mealPlan->day_number])
            ->first();
    }
}
