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
#[Temperature(0.2)]
#[Timeout(60)]
class MealPhotoAgent implements Agent, HasStructuredOutput
{
    use Promptable;

    public function model(): string
    {
        return config('ai.models.agent');
    }

    public function instructions(): string
    {
        return <<<'INSTRUCTIONS'
        You are a nutrition expert. Look at the photo of a meal and identify each distinct food or drink.
        For each item, estimate a realistic portion in grams and its nutrition for that portion: energy in
        kcal, and protein, carbohydrates and fat in grams. Judge portions from visible cues (plate size,
        utensils). Set confidence between 0 and 1 for how sure you are of the item and its portion. If you
        cannot identify any food, return an empty items array.
        INSTRUCTIONS;
    }

    /**
     * @return array<string, mixed>
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'items' => $schema->array()->items(
                $schema->object([
                    'name' => $schema->string()->required(),
                    'portion_g' => $schema->number()->description('Estimated portion in grams')->required(),
                    'kcal' => $schema->number()->description('kcal for the estimated portion')->required(),
                    'protein_g' => $schema->number(),
                    'carbs_g' => $schema->number(),
                    'fat_g' => $schema->number(),
                    'confidence' => $schema->number()->description('0 to 1'),
                ])
            )->required(),
        ];
    }
}
