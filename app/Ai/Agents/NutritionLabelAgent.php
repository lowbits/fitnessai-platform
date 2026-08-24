<?php

namespace App\Ai\Agents;

use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Attributes\Provider;
use Laravel\Ai\Attributes\Temperature;
use Laravel\Ai\Attributes\Timeout;
use Laravel\Ai\Contracts\Agent;
use Laravel\Ai\Contracts\HasStructuredOutput;
use Laravel\Ai\Enums\Lab;
use Laravel\Ai\Promptable;

#[Provider(Lab::OpenAI)]
#[Temperature(0.1)]
#[Timeout(45)]
class NutritionLabelAgent implements Agent, HasStructuredOutput
{
    use Promptable;

    public function model(): string
    {
        return config('ai.models.simple');
    }

    public function instructions(): string
    {
        return <<<'INSTRUCTIONS'
        You read a photo of a food product's nutrition table (Nährwerttabelle / nutrition facts).
        Extract the values PER 100 g or 100 ml — not per serving. If the table only lists per-serving,
        convert to per 100 using the stated serving size. Report energy in kcal, and protein,
        carbohydrates and fat in grams. Use a dot as the decimal separator. If a value is not clearly
        readable, omit it rather than guessing. Also report the serving size and unit if shown.
        INSTRUCTIONS;
    }

    /**
     * @return array<string, mixed>
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'kcal' => $schema->number()->description('Energy in kcal per 100 g/ml')->required(),
            'protein_g' => $schema->number()->description('Protein grams per 100 g/ml'),
            'carbs_g' => $schema->number()->description('Carbohydrate grams per 100 g/ml'),
            'fat_g' => $schema->number()->description('Fat grams per 100 g/ml'),
            'serving_size' => $schema->number()->description('Serving size amount, if shown'),
            'serving_unit' => $schema->string()->description('Serving unit, e.g. g or ml'),
        ];
    }
}
