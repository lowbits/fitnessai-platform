<?php

namespace App\Ai\Tools;

use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Tools\Request;
use Stringable;

class CreateWorkoutExercise implements Tool
{
    /**
     * Get the description of the tool's purpose.
     */
    public function description(): Stringable|string
    {
        return 'Creates a workout exercise entry in the workout plan. '
            .'Call this tool once for each exercise after finding it via searchExercises. '
            .'Always provide the exercise_id from search results — never invent IDs. '
            .'Include programming details: sets, reps (or duration for cardio/warmup/cooldown), '
            .'rest period in seconds, tempo if relevant, and optional coaching notes.';
    }

    /**
     * Execute the tool.
     */
    public function handle(Request $request): Stringable|string
    {
        dd('INSIDE CREATE WORKOUT EXERCISE TOOL', $request);
    }

    /**
     * Get the tool's schema definition.
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'exercise_id' => $schema->number()->description('The exercise ID from MeilisearchSimilaritySearch results. Never invent IDs.')->required(),
            'name' => $schema->string()->description('Exercise name exactly as returned from search results.')->required(),
            'type' => $schema->string()->description('One of: warmup, strength, cooldown, cardio.')->required(),
            'sets' => $schema->number()->description('Number of sets.')->required(),
            'reps' => $schema->string()->description('Rep range (e.g. "8-10") or duration (e.g. "5 min") for cardio/warmup.')->required(),
            'rest_seconds' => $schema->number()->description('Rest period between sets in seconds.')->nullable(),
            'tempo' => $schema->string()->description('Tempo notation e.g. "3-1-2-0" (eccentric-pause-concentric-pause).')->nullable(),
            'rpe' => $schema->number()->description('Rate of perceived exertion (1-10).')->nullable(),
            'notes' => $schema->string()->description('Coaching notes or form cues in the user language.')->nullable(),
            'order' => $schema->number()->description('Position in the workout (1 = first exercise).')->required(),
        ];
    }
}
