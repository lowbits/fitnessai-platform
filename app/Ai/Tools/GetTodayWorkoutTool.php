<?php

namespace App\Ai\Tools;

use App\Ai\Tools\Concerns\InteractsWithPlan;
use App\Ai\Tools\Support\ToolResult;
use App\Models\User;
use App\Models\WorkoutPlan;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Tools\Request;
use Stringable;

/**
 * Shows the user's workout for today — or tells them it's a rest day and when
 * their next session is. Read-only: it never changes the plan.
 */
class GetTodayWorkoutTool implements Tool
{
    use InteractsWithPlan;

    public function __construct(private readonly User $user) {}

    public function description(): Stringable|string
    {
        return "Shows the user's workout for today — the session name, exercises and duration, or that today is a rest day plus when their next workout is. Use it for \"what's my workout today?\", \"wann ist mein Training?\", \"wo ist mein Trainingsplan?\". Read-only — it cannot change or reschedule anything.";
    }

    /**
     * @return array<string, mixed>
     */
    public function schema(JsonSchema $schema): array
    {
        return [];
    }

    public function handle(Request $request): Stringable|string
    {
        if (! $this->activePlan($this->user)) {
            return ToolResult::error('no_active_plan', 'There is no active plan yet.');
        }

        $workout = $this->todaysWorkoutPlan($this->user);

        if (! $workout || in_array($workout->status, ['pending', 'generating'], true)) {
            return ToolResult::error('workout_not_generated', "Today's workout is still being generated.");
        }

        if ($workout->status === 'failed') {
            return ToolResult::error('workout_generation_failed', "Today's workout could not be generated.");
        }

        $next = $this->nextWorkoutPlan($this->user);
        $nextCard = $next ? $this->summarize($next) : null;

        if ($workout->workout_type === 'rest') {
            return ToolResult::info('workout_today', [
                'is_rest_day' => true,
                'name' => $workout->workout_name,
                'date' => $workout->date?->format('Y-m-d'),
                'description' => $workout->description,
                'thumbnail_url' => $workout->thumbnailUrl(),
                'next_workout' => $nextCard,
            ]);
        }

        return ToolResult::info('workout_today', [
            'is_rest_day' => false,
            'next_workout' => $nextCard,
        ] + $this->summarize($workout));
    }

    /**
     * @return array<string, mixed>
     */
    private function summarize(WorkoutPlan $workout): array
    {
        return [
            'workout_id' => $workout->id,
            'name' => $workout->workout_name,
            'type' => $workout->workout_type,
            'date' => $workout->date?->format('Y-m-d'),
            'duration_minutes' => $workout->estimated_duration_minutes,
            'calories' => $workout->estimated_calories_burned,
            'difficulty' => $workout->difficulty,
            'muscle_groups' => $workout->muscle_groups ?? [],
            'thumbnail_url' => $workout->thumbnailUrl(),
            'exercises' => $workout->exercises->map(fn ($e) => [
                'name' => $e->exercise?->localizedName() ?? '',
                'sets' => $e->sets,
                'reps' => $e->reps,
                'muscle_groups' => $e->exercise?->primary_muscles ?? [],
            ])->values()->all(),
            'exercises_count' => $workout->exercises->count(),
        ];
    }
}
