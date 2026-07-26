<?php

namespace App\Ai\Agents;

use App\Models\Recipe;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Attributes\MaxTokens;
use Laravel\Ai\Attributes\Provider;
use Laravel\Ai\Attributes\Temperature;
use Laravel\Ai\Attributes\Timeout;
use Laravel\Ai\Contracts\Agent;
use Laravel\Ai\Contracts\HasStructuredOutput;
use Laravel\Ai\Enums\Lab;
use Laravel\Ai\Promptable;

#[Provider(Lab::OpenAI)]
#[MaxTokens(4096)]
#[Temperature(0.3)]
#[Timeout(90)]
class RecipeTranslationAgent implements Agent, HasStructuredOutput
{
    use Promptable;

    public function model(): string
    {
        return config('ai.models.simple');
    }

    public function __construct(
        private readonly Recipe $recipe,
        private readonly string $locale,
    ) {}

    public function instructions(): string
    {
        return <<<'INSTRUCTIONS'
You are an expert culinary translator who produces natural, accurate translations of recipes.

Rules:
- Translate the recipe name, description, instructions, and ingredient names into the target locale.
- Use native culinary terms (e.g. "Hähnchenbrust" not "Chicken Brust" for German).
- Provide 2-4 aliases (alternative names people might search for in that language).
- Instructions must be clear, practical cooking steps.
- Keep the same number of instruction steps as the original.
- For ingredients: return the same array structure with only the "name" field translated. Keep "amount" and "unit" unchanged.
INSTRUCTIONS;
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'name' => $schema->string()->required(),
            'aliases' => $schema->array()->items($schema->string())->required(),
            'description' => $schema->string()->required(),
            'instructions' => $schema->array()->items($schema->string())->required(),
            'ingredients' => $schema->array()->items(
                $schema->object([
                    'name' => $schema->string()->required(),
                    'amount' => $schema->string()->required(),
                    'unit' => $schema->string()->required(),
                ])
            )->required(),
        ];
    }

    public function buildPrompt(): string
    {
        $context = [
            'name' => $this->recipe->name,
            'description' => $this->recipe->description,
            'instructions' => $this->recipe->instructions,
            'ingredients' => $this->recipe->ingredients,
            'cuisine' => $this->recipe->cuisine,
            'meal_types' => $this->recipe->meal_types,
        ];

        $contextJson = json_encode(array_filter($context), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

        return "Translate the following recipe into locale \"{$this->locale}\". Provide 2-4 aliases (alternative names people might search for).\n\nRecipe:\n{$contextJson}";
    }
}
