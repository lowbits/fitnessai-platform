<?php

namespace App\Support;

/**
 * Single source of truth for how the daily kcal/macro target is split across
 * meal slots and scaled to a partial share.
 */
final class MealSlotBudget
{
    public const SPLIT = [
        'breakfast' => 0.275,
        'lunch' => 0.325,
        'snack' => 0.125,
        'dinner' => 0.275,
    ];

    /** Cooked mains are guided to stay at or below this so no single meal is unrealistic. */
    public const MAIN_CAP_KCAL = 800;

    /** A fill budget below this is left on the meals rather than prompting a booster. */
    public const FILL_MIN_KCAL = 150;

    /** @var list<string> */
    private const MAIN_SLOTS = ['breakfast', 'lunch', 'dinner'];

    /**
     * Per-slot kcal for the user's selected slots. When autoFill is on, cooked
     * mains are held at MAIN_CAP_KCAL so no single meal is unrealistically large;
     * the remainder becomes the fill budget the AI tops up with shakes/snacks.
     * When off, slots carry their full renormalized share.
     *
     * @param  list<string>  $selectedSlots
     * @return array<string, int>
     */
    public static function mainSlotKcal(array $selectedSlots, int $dailyCalories, bool $autoFill): array
    {
        $map = [];
        foreach (self::sharesFor($selectedSlots) as $slot => $share) {
            $kcal = (int) round($dailyCalories * $share);
            $map[$slot] = $autoFill && in_array($slot, self::MAIN_SLOTS, true)
                ? min($kcal, self::MAIN_CAP_KCAL)
                : $kcal;
        }

        return $map;
    }

    /**
     * Calories left once the (capped) meals are placed — the amount the AI fills
     * with flex protein shakes (preferred) or a snack. Zero when autoFill is off.
     *
     * @param  list<string>  $selectedSlots
     */
    public static function fillBudget(array $selectedSlots, int $dailyCalories, bool $autoFill): int
    {
        if (! $autoFill) {
            return 0;
        }

        return max(0, $dailyCalories - array_sum(self::mainSlotKcal($selectedSlots, $dailyCalories, true)));
    }

    /**
     * Slot shares renormalized to sum to 1.0 across the user's selected slots.
     * A user without snack gets the snack share redistributed across the other 3.
     *
     * @param  list<string>  $selectedSlots
     * @return array<string, float>
     */
    public static function sharesFor(array $selectedSlots): array
    {
        $filtered = array_intersect_key(self::SPLIT, array_flip($selectedSlots));
        $sum = array_sum($filtered);

        return $sum > 0 ? array_map(fn (float $pct) => $pct / $sum, $filtered) : [];
    }

    /**
     * Metabolism macros scaled by a share (single slot, sum of NEW slots, or the whole day).
     *
     * @param  array{daily_calories:int|float, protein_g:int|float, carbs_g:int|float, fat_g:int|float}  $metabolism
     * @return array{calories:int, protein_g:int, carbs_g:int, fat_g:int}
     */
    public static function applyShare(array $metabolism, float $share): array
    {
        return [
            'calories' => (int) round($metabolism['daily_calories'] * $share),
            'protein_g' => (int) round($metabolism['protein_g'] * $share),
            'carbs_g' => (int) round($metabolism['carbs_g'] * $share),
            'fat_g' => (int) round($metabolism['fat_g'] * $share),
        ];
    }
}
