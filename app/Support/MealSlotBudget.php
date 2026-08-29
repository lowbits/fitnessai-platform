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

    /** A single meal is held at or below this so no plate is unrealistically large. */
    public const MAIN_CAP_KCAL = 1000;

    /** Overflow below this stays on the meals rather than adding a tiny flex shake. */
    public const FLEX_MIN_KCAL = 150;

    /** Above this daily target, three meals get unrealistically large, so a snack slot is added. */
    public const SNACK_RECOMMEND_KCAL = 2800;

    /**
     * Per-slot kcal for the day. With autoFill on, meals are capped so no single
     * plate is unrealistic and whatever the capped meals leave short of the daily
     * target becomes an explicit "flex" protein-shake slot the AI fills. On a
     * high-calorie day a snack slot is added first so the load spreads across four
     * meals instead of piling into one oversized shake. With autoFill off, each
     * slot carries its full renormalized share (large meals, no snack, no shake).
     *
     * @param  list<string>  $selectedSlots
     * @return array<string, int>
     */
    public static function slotKcal(array $selectedSlots, int $dailyCalories, bool $autoFill): array
    {
        if ($autoFill && $dailyCalories > self::SNACK_RECOMMEND_KCAL && ! in_array('snack', $selectedSlots, true)) {
            $selectedSlots[] = 'snack';
        }

        $map = [];
        foreach (self::sharesFor($selectedSlots) as $slot => $share) {
            $kcal = (int) round($dailyCalories * $share);
            $map[$slot] = $autoFill ? min($kcal, self::MAIN_CAP_KCAL) : $kcal;
        }

        if ($autoFill) {
            $overflow = $dailyCalories - array_sum($map);
            if ($overflow >= self::FLEX_MIN_KCAL) {
                $map['flex'] = $overflow;
            }
        }

        return $map;
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
