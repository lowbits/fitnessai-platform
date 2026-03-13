<?php

namespace App\Ai\Agents;

use App\Enums\Equipment;
use App\Enums\MuscleGroup;
use App\Models\Exercise;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Attributes\MaxTokens;
use Laravel\Ai\Attributes\Model;
use Laravel\Ai\Attributes\Provider;
use Laravel\Ai\Attributes\Temperature;
use Laravel\Ai\Attributes\Timeout;
use Laravel\Ai\Contracts\Agent;
use Laravel\Ai\Contracts\HasStructuredOutput;
use Laravel\Ai\Enums\Lab;
use Laravel\Ai\Promptable;

#[Provider(Lab::OpenAI)]
#[Model('gpt-4.1-mini')]
#[MaxTokens(2048)]
#[Temperature(0.3)]
#[Timeout(60)]
class ExerciseEnrichmentAgent implements Agent, HasStructuredOutput
{
    use Promptable;

    public function __construct(
        private readonly Exercise $exercise,
        private readonly array $fieldsToEnrich = [],
    ) {}

    public function instructions(): string
    {
        $muscleValues = implode(', ', array_map(fn (MuscleGroup $m) => "\"{$m->value}\"", MuscleGroup::cases()));
        $equipmentValues = implode(', ', array_map(fn (Equipment $e) => "\"{$e->value}\"", Equipment::cases()));

        return <<<INSTRUCTIONS
You are an expert exercise scientist and certified personal trainer. Given an exercise name and any known attributes, provide accurate metadata for the requested fields.

Rules:
- movement_pattern: one of "horizontal_push", "horizontal_pull", "vertical_push", "vertical_pull", "hinge", "squat", "lunge", "carry", "rotation", "isolation", "plank", "flexion", "extension", "lateral_raise", "fly", "press"
- mechanic: "compound" or "isolation"
- force_type: "push", "pull", or "static"
- laterality: "bilateral", "unilateral", or "alternating"
- body_region: "upper_body", "lower_body", "core", or "full_body"
- primary_muscles: array of main muscles worked. ONLY use values from this list: {$muscleValues}
- secondary_muscles: array of supporting muscles. ONLY use values from the same list above.
- equipment: array of equipment needed. ONLY use values from this list: {$equipmentValues}
- training_places: array from "gym", "home", "outdoor", "hotel" — where this exercise can realistically be performed
- difficulty: "Beginner", "Intermediate", or "Advanced"
- description: 2-3 concise English sentences describing the exercise and its primary benefits
- instructions: array of 3-6 step-by-step instruction strings for performing the exercise
- form_cues: 1-2 sentence coaching cue focusing on the most important form points
INSTRUCTIONS;
    }

    public function schema(JsonSchema $schema): array
    {
        $fields = [];

        $stringFields = ['movement_pattern', 'mechanic', 'force_type', 'laterality', 'body_region', 'difficulty', 'description', 'form_cues'];
        $arrayFields = ['primary_muscles', 'secondary_muscles', 'equipment', 'training_places', 'instructions'];

        foreach ($this->fieldsToEnrich as $field) {
            if (in_array($field, $stringFields)) {
                $fields[$field] = $schema->string()->required();
            } elseif (in_array($field, $arrayFields)) {
                $fields[$field] = $schema->array()->items($schema->string())->required();
            }
        }

        return $fields;
    }

    public function buildPrompt(): string
    {
        $context = collect([
            'name' => $this->exercise->name,
            'type' => $this->exercise->type,
        ]);

        $existingFields = [
            'movement_pattern', 'mechanic', 'force_type', 'laterality',
            'body_region', 'difficulty', 'description', 'form_cues',
        ];

        foreach ($existingFields as $field) {
            if ($this->exercise->{$field} !== null) {
                $context[$field] = $this->exercise->{$field};
            }
        }

        $arrayFields = ['primary_muscles', 'secondary_muscles', 'equipment', 'training_places', 'instructions'];

        foreach ($arrayFields as $field) {
            if (! empty($this->exercise->{$field})) {
                $context[$field] = $this->exercise->{$field};
            }
        }

        $contextJson = json_encode($context, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        $fieldsNeeded = implode(', ', $this->fieldsToEnrich);

        return "Enrich the following exercise. Only provide values for these fields: {$fieldsNeeded}\n\nExercise context:\n{$contextJson}";
    }
}
