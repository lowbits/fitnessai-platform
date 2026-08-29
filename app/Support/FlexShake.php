<?php

namespace App\Support;

use App\Enums\MealType;

/**
 * Deterministic protein-shake meal, scaled to a target kcal.
 *
 * When a generated day lands short of the user's calorie target, this is the
 * "flex" slot that fills the gap — a blended drink whose amounts and macros
 * scale linearly from a reference recipe. No AI call, no recipe row: the same
 * shake the AI writes for planned flex slots, built in PHP so it always hits
 * the number exactly.
 */
final class FlexShake
{
    /** Reference recipe the amounts and macros scale from. */
    private const REFERENCE_KCAL = 350;

    /** kcal split across protein/carbs/fat for the scaled macros. */
    private const PROTEIN_SHARE = 0.35;

    private const CARBS_SHARE = 0.40;

    private const FAT_SHARE = 0.25;

    /**
     * Meal attributes for a shake of the given kcal, ready for Meal::create().
     *
     * @return array<string, mixed>
     */
    public static function build(int $kcal, string $locale): array
    {
        $factor = $kcal / self::REFERENCE_KCAL;
        $de = str_starts_with($locale, 'de');

        return [
            'type' => MealType::FLEX->value,
            'recipe_id' => null,
            'name' => $de
                ? 'Proteinshake mit Banane, Haferflocken und Erdnussmus'
                : 'Protein shake with banana, oats and peanut butter',
            'description' => $de
                ? 'Schneller Shake, der deinen Tag auf dein Kalorienziel auffüllt.'
                : 'A quick shake that tops your day up to your calorie target.',
            'calories' => $kcal,
            'protein_g' => (int) round($kcal * self::PROTEIN_SHARE / 4),
            'carbs_g' => (int) round($kcal * self::CARBS_SHARE / 4),
            'fat_g' => (int) round($kcal * self::FAT_SHARE / 9),
            'ingredients' => self::ingredients($factor, $de),
            'instructions' => $de
                ? [
                    'Milch in einen Mixer geben.',
                    'Whey-Protein, Haferflocken, Banane und Erdnussmus hinzufügen.',
                    'Alles 30–45 Sekunden cremig mixen.',
                    'Direkt trinken oder kalt mitnehmen.',
                ]
                : [
                    'Pour the milk into a blender.',
                    'Add the whey protein, oats, banana and peanut butter.',
                    'Blend for 30–45 seconds until creamy.',
                    'Drink right away or take it with you cold.',
                ],
            'allergens' => ['dairy', 'peanuts', 'gluten'],
            'primary_protein' => 'dairy',
            'cuisine' => 'american',
            'format' => 'smoothie',
            'hero_veg' => 'none',
            'difficulty' => 'Easy',
            'servings' => 1,
            'prep_time_minutes' => 5,
            'cook_time_minutes' => 0,
            'status' => 'generated',
        ];
    }

    /**
     * @return list<array{name: string, unit: string, amount: string}>
     */
    private static function ingredients(float $factor, bool $de): array
    {
        $round = fn (float $value, int $step) => (string) max($step, (int) (round($value / $step) * $step));

        return [
            ['name' => $de ? 'Milch' : 'Milk', 'unit' => 'ml', 'amount' => $round(200 * $factor, 10)],
            ['name' => $de ? 'Whey-Protein Vanille' : 'Whey protein (vanilla)', 'unit' => 'g', 'amount' => $round(25 * $factor, 1)],
            ['name' => $de ? 'Haferflocken' : 'Oats', 'unit' => 'g', 'amount' => $round(30 * $factor, 5)],
            ['name' => $de ? 'Banane' : 'Banana', 'unit' => 'g', 'amount' => $round(80 * $factor, 10)],
            ['name' => $de ? 'Erdnussmus' : 'Peanut butter', 'unit' => 'g', 'amount' => $round(12 * $factor, 1)],
        ];
    }
}
