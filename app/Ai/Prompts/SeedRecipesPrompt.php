<?php

namespace App\Ai\Prompts;

use Stringable;

class SeedRecipesPrompt implements Stringable
{
    public function __construct(
        private readonly string $diet,
        private readonly string $mealType,
        private readonly string $locale,
        private readonly int $count,
        private readonly ?string $request = null,
        private readonly ?int $targetKcal = null,
    ) {}

    public function __toString(): string
    {
        $language = $this->locale === 'de' ? 'German' : 'English';
        $kcal = $this->targetKcal ?? match ($this->mealType) {
            'breakfast' => 550,
            'lunch' => 700,
            'snack' => 280,
            'dinner' => 600,
            default => 500,
        };

        // On-demand single-dish mode: the user asked for a specific dish or named
        // ingredients they have at home. Generate exactly what they asked for.
        if ($this->request !== null) {
            return <<<PROMPT
            Generate exactly 1 appetizing {$this->diet} {$this->mealType} recipe for the {$language}-language market that matches this request from the user: "{$this->request}".

            Interpret the request faithfully:
            - If it names a dish (e.g. "Chili con Carne"), create that dish — an authentic, recognizable version of it.
            - If it lists ingredients the user has at home (e.g. "chicken, rice, broccoli"), create a coherent dish built around those ingredients.

            The recipe must:
            - Target approximately {$kcal} kcal per serving (±15% OK) so it fits the meal it replaces
            - Be high-protein (≥25g) for fitness-focused users
            - Respect the {$this->diet} constraint (never introduce ingredients that violate it)
            - Use canonical English enum values for primary_protein, cuisine, format, hero_veg, allergens, ingredients[].unit (regardless of locale)
            - Use {$language} for human-readable fields (name, description, ingredients[].name, instructions)
            - Include the cascading dietary tags in `tags` (e.g. vegan → vegan + vegetarian + pescatarian + omnivore)
            - Be realistic to cook in a home kitchen with normal ingredients

            Set meal_types = ["{$this->mealType}"].

            Call save_recipes ONCE with the single recipe.
            PROMPT;
        }

        return <<<PROMPT
        Generate {$this->count} appetizing, diverse {$this->diet} {$this->mealType} recipes for the {$language}-language market.

        Each recipe must:
        - Target approximately {$kcal} kcal per serving (±15% OK)
        - Be high-protein (≥25g) for fitness-focused users
        - Use canonical English enum values for primary_protein, cuisine, format, hero_veg, allergens, ingredients[].unit (regardless of locale)
        - Use {$language} for human-readable fields (name, description, ingredients[].name, instructions)
        - Include the cascading dietary tags in `tags`: a {$this->diet} recipe must include the appropriate cascade (e.g. vegan → vegan + vegetarian + pescatarian + omnivore)
        - Vary across cuisines, formats, and primary_protein within the {$this->diet} constraints
        - Be realistic to cook in a home kitchen with normal ingredients

        Set meal_types = ["{$this->mealType}"].

        Call save_recipes ONCE with all {$this->count} recipes.
        PROMPT;
    }
}
