<?php

namespace App\Ai\Agents;

use App\Models\Exercise;
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
#[MaxTokens(2048)]
#[Temperature(0.3)]
#[Timeout(60)]
class ExerciseTranslationAgent implements Agent, HasStructuredOutput
{
    use Promptable;

    public function model(): string
    {
        return config('ai.models.simple');
    }

    public function __construct(
        private readonly Exercise $exercise,
        private readonly string $locale,
    ) {}

    public function instructions(): string
    {
        return <<<'INSTRUCTIONS'
You are an expert fitness translator who produces natural, accurate translations of exercise names and instructions.

Rules by locale:
- "de" (German): Use proper German fitness terminology (e.g. "Bankdrücken" not "Bench Drücken", "Kniebeuge" not "Squat"). Aliases should include common German gym terms and any anglicisms actually used in German-speaking gyms.
- "en" (English): The name should match the canonical exercise name. Aliases should include common variations, abbreviations, and alternative names people might search for (important for search quality).
- For all locales: Keep instructions clear, concise, and actionable. Use terminology native speakers would naturally use in a gym setting.
INSTRUCTIONS;
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'name' => $schema->string()->required(),
            'aliases' => $schema->array()->items($schema->string())->required(),
            'description' => $schema->string()->required(),
            'instructions' => $schema->array()->items($schema->string())->required(),
            'form_cues' => $schema->string()->required(),
        ];
    }

    public function buildPrompt(): string
    {
        $context = [
            'name' => $this->exercise->name,
            'type' => $this->exercise->type,
            'description' => $this->exercise->description,
            'instructions' => $this->exercise->instructions,
            'form_cues' => $this->exercise->form_cues,
            'primary_muscles' => $this->exercise->primary_muscles,
            'equipment' => $this->exercise->equipment,
        ];

        $contextJson = json_encode(array_filter($context), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

        return "Translate the following exercise into locale \"{$this->locale}\". Provide 3-5 aliases (alternative names people might search for).\n\nExercise:\n{$contextJson}";
    }
}
