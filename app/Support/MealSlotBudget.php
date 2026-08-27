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

    /** Cooked mains are capped at this many kcal so no single meal is unrealistic. */
    public const MAIN_CAP_KCAL = 800;

    /** A single curated booster (shake/bar/no-cook snack) carries at most this much. */
    public const BOOSTER_MAX_KCAL = 500;

    /** Overflow below this is left on the mains rather than spawning a tiny booster. */
    public const BOOSTER_MIN_KCAL = 200;

    /** @var list<string> */
    private const MAIN_SLOTS = ['breakfast', 'lunch', 'dinner'];

    /**
     * Compose the day's eating occasions from the user's selected slots and target.
     *
     * Mains are capped at MAIN_CAP_KCAL so no single cooked meal is unrealistically
     * large; the overflow is absorbed by curated booster snacks (a shake/bar/no-cook
     * snack) so the day still hits its calorie target. Boosters are added only when
     * autoFill is on and the mains would otherwise exceed the cap by a meaningful
     * amount — a low-calorie user keeps their plain 3 meals untouched.
     *
     * @param  list<string>  $selectedSlots
     * @return list<array{type: string, kcal: int, booster: bool}>
     */
    public static function compose(array $selectedSlots, int $dailyCalories, bool $autoFill): array
    {
        $shares = self::sharesFor($selectedSlots);

        if ($shares === []) {
            return [];
        }

        $occasions = [];
        foreach ($shares as $slot => $share) {
            $occasions[] = ['type' => $slot, 'kcal' => (int) round($dailyCalories * $share), 'booster' => false];
        }

        if (! $autoFill) {
            return $occasions;
        }

        $overflow = 0;
        foreach ($occasions as $occasion) {
            if (in_array($occasion['type'], self::MAIN_SLOTS, true)) {
                $overflow += max(0, $occasion['kcal'] - self::MAIN_CAP_KCAL);
            }
        }

        if ($overflow < self::BOOSTER_MIN_KCAL) {
            return $occasions;
        }

        foreach ($occasions as $i => $occasion) {
            if (in_array($occasion['type'], self::MAIN_SLOTS, true)) {
                $occasions[$i]['kcal'] = min($occasion['kcal'], self::MAIN_CAP_KCAL);
            }
        }

        $boosterCount = (int) ceil($overflow / self::BOOSTER_MAX_KCAL);
        $remaining = $overflow;
        for ($n = 0; $n < $boosterCount; $n++) {
            $kcal = $n === $boosterCount - 1 ? $remaining : (int) round($overflow / $boosterCount);
            $remaining -= $kcal;
            $occasions[] = ['type' => 'snack', 'kcal' => $kcal, 'booster' => true];
        }

        return $occasions;
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
