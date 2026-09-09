<?php

namespace App\Services\Recipe;

use App\Enums\Allergen;
use App\Enums\PrimaryProtein;

/**
 * Turns a dislike / allergen list into Meilisearch exclusion clauses, matching
 * across primary_protein, allergens (EU-14) and ingredient_names so a category
 * like "soy" also excludes tofu.
 */
class DislikeFilter
{
    public function __construct(private readonly FoodTermTranslator $translator) {}

    /**
     * @param  list<string>  $dislikes  free-text dislikes / allergen keys (any language)
     * @return list<string> Meilisearch filter expressions to AND together
     */
    public function clauses(array $dislikes): array
    {
        $proteins = array_column(PrimaryProtein::cases(), 'value');
        $allergens = array_column(Allergen::cases(), 'value');
        $known = array_flip([...$proteins, ...$allergens, ...array_keys(self::SYNONYMS)]);

        $clauses = [];

        foreach ($dislikes as $dislike) {
            $dislike = mb_strtolower(trim($dislike));

            if ($dislike === '') {
                continue;
            }

            $english = isset($known[$dislike]) ? $dislike : $this->translator->toEnglish($dislike);

            foreach (self::SYNONYMS[$english] ?? [$english] as $term) {
                $quoted = json_encode($term);

                if (in_array($term, $proteins, true)) {
                    $clauses[] = "primary_protein != {$quoted}";
                }

                if (in_array($term, $allergens, true)) {
                    $clauses[] = "allergens != {$quoted}";
                }

                $clauses[] = "ingredient_names != {$quoted}";
            }
        }

        return array_values(array_unique($clauses));
    }

    /**
     * Canonical-English dislike -> foods it excludes ("nuts" covers peanuts by design).
     *
     * @var array<string, list<string>>
     */
    private const SYNONYMS = [
        'soy' => ['soy', 'soya', 'tofu', 'tempeh', 'edamame'],
        'soya' => ['soy', 'soya', 'tofu', 'tempeh', 'edamame'],
        'tofu' => ['tofu', 'soy', 'soya'],
        'tempeh' => ['tempeh', 'soy'],

        'nuts' => ['nuts', 'peanuts', 'peanut'],
        'nut' => ['nuts', 'peanuts', 'peanut'],
        'tree nuts' => ['nuts', 'peanuts', 'peanut'],
        'peanut' => ['peanut', 'peanuts', 'nuts'],
        'peanuts' => ['peanuts', 'peanut', 'nuts'],

        'crustaceans' => ['crustaceans', 'shellfish', 'shrimp', 'prawns', 'crab'],
        'shellfish' => ['shellfish', 'crustaceans', 'shrimp', 'prawns', 'crab'],
        'shrimp' => ['shrimp', 'prawns', 'crustaceans'],
        'prawns' => ['prawns', 'shrimp', 'crustaceans'],
        'crab' => ['crab', 'crustaceans'],
        'lobster' => ['lobster', 'crustaceans'],
        'molluscs' => ['molluscs', 'mollusks', 'mussels', 'squid', 'clams'],
        'mollusks' => ['molluscs', 'mollusks', 'mussels', 'squid', 'clams'],
        'mussels' => ['mussels', 'molluscs'],
        'squid' => ['squid', 'molluscs'],
        'clams' => ['clams', 'molluscs'],
        'oysters' => ['oysters', 'molluscs'],
        'seafood' => ['seafood', 'shellfish', 'crustaceans', 'molluscs', 'shrimp'],
        'fish' => ['fish', 'salmon', 'cod', 'tuna'],
        'edamame' => ['edamame', 'soy'],

        'egg' => ['egg', 'eggs'],
        'eggs' => ['eggs', 'egg'],
        'dairy' => ['dairy', 'lactose', 'milk'],
        'lactose' => ['lactose', 'dairy', 'milk'],
        'milk' => ['milk', 'dairy', 'lactose'],
        'gluten' => ['gluten', 'wheat'],
        'wheat' => ['wheat', 'gluten'],

        'sulphites' => ['sulphites', 'sulfites'],
        'sulfites' => ['sulphites', 'sulfites'],
    ];
}
