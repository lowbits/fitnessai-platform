<?php

namespace App\Ai\Tools;

use App\Actions\Workouts\PopulateWorkoutPlanAction;
use App\Ai\DataTransferObjects\WorkoutPlanResult;
use App\Models\WorkoutPlan;
use Exception;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\Support\Facades\Log;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Tools\Request;
use Stringable;

class SaveWorkoutPlanTool implements Tool
{
    public function __construct(private readonly WorkoutPlan $workoutPlan, private readonly PopulateWorkoutPlanAction $populateWorkoutPlanAction) {}

    /**
     * Get the description of the tool's purpose.
     */
    public function description(): Stringable|string
    {
        return 'Saves a complete workout plan with exercises to the database. You MUST call this tool to submit the final workout plan.';
    }

    /**
     * Execute the tool.
     */
    public function handle(Request $request): Stringable|string
    {

        try {
            $result = WorkoutPlanResult::fromRequest($request);

            Log::debug('[WorkoutGen][SaveTool] Saving workout', [
                'workout_plan_id' => $this->workoutPlan->id,
                'workout_name' => $request['workout_name'],
                'workout_type' => $request['workout_type'],
                'exercises_count' => count($request['exercises'] ?? []),
            ]);

            $this->populateWorkoutPlanAction->execute($this->workoutPlan, $result);

            Log::info('[WorkoutGen][SaveTool] Saved successfully', [
                'workout_plan_id' => $this->workoutPlan->id,
                'workout_name' => $request['workout_name'],
                'workout_type' => $request['workout_type'],
                'exercises_count' => count($request['exercises'] ?? []),
            ]);

            return json_encode([
                'success' => true,
                'message' => "Workout plan '{$request['workout_name']}' saved with ".count($request['exercises'] ?? []).' exercises.',
            ]);
        } catch (Exception $e) {
            Log::error('[WorkoutGen][SaveTool] Failed to save', [
                'workout_plan_id' => $this->workoutPlan->id,
                'workout_name' => $request['workout_name'] ?? null,
                'workout_type' => $request['workout_type'] ?? null,
                'error' => $e->getMessage(),
                'exception' => $e::class,
            ]);

            return json_encode([
                'success' => false,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Get the tool's schema definition.
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'workout_name' => $schema->string()
                ->description('Concise workout name: 2-4 words, muscle groups or training focus. No day numbers, difficulty, or goals.')
                ->required(),

            'workout_type' => $schema->string()
                ->description('Primary type for this session. One of: strength, hypertrophy, endurance, cardio, hiit, mobility.')
                ->required(),

            'description' => $schema->string()
                ->description('Brief overview of the workout purpose and focus (1-2 sentences in user language).')
                ->required(),

            'estimated_duration_minutes' => $schema->number()
                ->description('Total time including warmup and cooldown. Be conservative — round up.')
                ->required(),

            'difficulty' => $schema->string()
                ->description('One of: Beginner, Intermediate, Advanced.')
                ->required(),

            'muscle_groups' => $schema->array()
                ->items($schema->string())
                ->description('Primary muscle groups targeted in this workout.')
                ->required(),

            'exercises' => $schema->array()->items($schema->object([
                'exercise_id' => $schema->number()
                    ->description('The exercise ID from MeilisearchSimilaritySearch results. Never invent IDs.')
                    ->required(),

                'name' => $schema->string()
                    ->description('Exercise name exactly as returned from search results. Do not translate.')
                    ->required(),

                'type' => $schema->string()
                    ->description('Exercise category. One of: warmup, strength, cardio, cooldown, stretch.')
                    ->required(),

                'description' => $schema->string()
                    ->description('What the exercise does and its benefits (1-2 sentences in user language).')
                    ->required(),

                'instructions' => $schema->array()
                    ->items($schema->string())
                    ->description('Step-by-step execution guide in user language. 3-5 clear steps covering positioning, movement, and breathing.')
                    ->required(),

                'form_cues' => $schema->string()
                    ->description('1-2 key safety/technique points in user language. Focus on the most common mistake for this exercise.')
                    ->required(),

                'sets' => $schema->number()
                    ->description('Number of sets. Null for warmup/cooldown/stretches.')
                    ->nullable(),

                'reps' => $schema->number()
                    ->description('Number of reps per set. Vary within protocol range across exercises. Null for time-based exercises.')
                    ->nullable(),

                'duration_seconds' => $schema->number()
                    ->description('Duration in seconds for warmup, cooldown, or stretch exercises. Null for rep-based exercises.')
                    ->nullable(),

                'rest_seconds' => $schema->string()
                    ->description('Rest between sets as range in seconds (e.g. "60-90"). Per goal protocol. Null for warmup/cooldown.')
                    ->nullable(),

                'tempo' => $schema->string()
                    ->description('Only set when a specific non-standard tempo is prescribed (e.g. "3-0-1-0" for slow eccentric). Do NOT set for normal movement speed. Null for warmup/cooldown.')
                    ->nullable(),

                'execution_style' => $schema->string()
                    ->description('One of: controlled, explosive, isometric, tempo_based. Use "tempo_based" ONLY when tempo is set with a non-standard tempo. Use "controlled" for normal pace. Null for warmup/cooldown.')
                    ->nullable(),

                'rpe' => $schema->string()
                    ->description('Target RPE range (e.g. "7-8"). Per goal protocol. Null for warmup/cooldown/stretches.')
                    ->nullable(),

                'weight_recommendation' => $schema->string()
                    ->description('Weight guidance per skill level from the prompt. Include practical context (e.g. "Moderate — complete all reps with 2-3 in reserve"). "Bodyweight" for bodyweight exercises. Null for warmup/cooldown.')
                    ->nullable(),

                'alternatives' => $schema->array()
                    ->items($schema->object([
                        'exercise_id' => $schema->number()
                            ->description('Alternative exercise ID from search results.')
                            ->required(),
                        'name' => $schema->string()
                            ->description('Alternative exercise name from search results.')
                            ->required(),
                    ])->withoutAdditionalProperties())
                    ->description('1-2 alternative exercises looked up via MeilisearchSimilaritySearch. Must differ from alternatives of other exercises.')
                    ->nullable(),

                'order' => $schema->number()
                    ->description('Position in the workout (1 = first exercise).')
                    ->required(),

            ])->withoutAdditionalProperties())
                ->description('Exercises for this workout: warmup, main exercises, and cooldown.')
                ->required(),
        ];
    }
}
