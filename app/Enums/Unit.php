<?php

namespace App\Enums;

/**
 * Canonical cooking units. Stored in English in the DB so a recipe is
 * locale-agnostic and can be reused across users in any language; the
 * view layer translates via lang/{locale}/units.php.
 *
 * The AI's `unit` output is locked to this list via the SaveMealPlanTool
 * schema, so we never see mixed "Stück" / "piece" leak in again.
 */
enum Unit: string
{
    // Weight
    case GRAM = 'g';
    case KILOGRAM = 'kg';

    // Volume
    case MILLILITER = 'ml';
    case LITER = 'l';
    case TABLESPOON = 'tbsp';
    case TEASPOON = 'tsp';
    case CUP = 'cup';

    // Count
    case PIECE = 'piece';
    case SLICE = 'slice';
    case CLOVE = 'clove';
    case LEAF = 'leaf';
    case SPRIG = 'sprig';
    case HEAD = 'head';
    case BUNCH = 'bunch';
    case STICK = 'stick';

    // Small / imprecise
    case PINCH = 'pinch';
    case DASH = 'dash';
    case DROP = 'drop';
    case HANDFUL = 'handful';

    // Packaging
    case CAN = 'can';
    case JAR = 'jar';
    case PACKAGE = 'package';

    // Special
    case TO_TASTE = 'to_taste';
    case WHOLE = 'whole';

    /**
     * Normalize an AI-emitted unit string to the canonical enum value.
     * The JSON Schema `enum` constraint does the heavy lifting; this catches
     * the rare slip-through (e.g. legacy "Stück" / "EL" / "Prise" in DE plans).
     */
    public static function normalize(?string $input): string
    {
        if (! $input) {
            return self::PIECE->value;
        }

        $key = mb_strtolower(trim($input));

        return self::tryFrom($key)?->value
            ?? match ($key) {
                'stück', 'stueck' => self::PIECE->value,
                'prise' => self::PINCH->value,
                'el', 'esslöffel' => self::TABLESPOON->value,
                'tl', 'teelöffel' => self::TEASPOON->value,
                'scheibe' => self::SLICE->value,
                'zehe' => self::CLOVE->value,
                default => self::PIECE->value,
            };
    }
}
