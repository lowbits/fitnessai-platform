<?php

namespace App\Support;

class RecipeIngredientHash
{
    /**
     * @param  array<int, array{name?: string, amount?: string|int|float, unit?: string}>  $ingredients
     */
    public static function compute(array $ingredients, string $locale): string
    {
        $names = collect($ingredients)
            ->pluck('name')
            ->filter()
            ->map(fn (string $n) => mb_strtolower(trim($n)))
            ->sort()
            ->values()
            ->all();

        return sha1(implode(',', $names).'|'.$locale);
    }
}
