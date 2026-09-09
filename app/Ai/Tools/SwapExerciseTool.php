<?php

namespace App\Ai\Tools;

use App\Actions\Workouts\ReplaceWorkoutExercise;
use App\Ai\Tools\Concerns\InteractsWithPlan;
use App\Ai\Tools\Support\ToolResult;
use App\Models\Exercise;
use App\Models\User;
use App\Models\WorkoutPlan;
use App\Models\WorkoutPlanExercise;
use Carbon\Carbon;
use Carbon\Exceptions\InvalidFormatException;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\Support\Collection;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Tools\Request;
use Stringable;

/**
 * Swaps a single exercise in a workout for a different one — a variation, or to
 * work around an injury. Lists alternatives first, then replaces on the user's
 * pick, reusing the same ReplaceWorkoutExercise action as the mobile app.
 */
class SwapExerciseTool implements Tool
{
    use InteractsWithPlan;

    private const MAX_ALTERNATIVES = 4;

    public function __construct(
        private readonly User $user,
        private readonly ReplaceWorkoutExercise $replace,
    ) {}

    public function description(): Stringable|string
    {
        return 'Swaps one exercise in a workout for a different one — for variety or to work around an injury ("gib mir eine andere Übung für Bankdrücken", "tausch die Schulterübung"). Pass exercise (the exercise to change, by name) and from_date (YYYY-MM-DD, default today). Without replacement it returns a few alternatives to offer the user; once they choose, call again with the same exercise plus replacement set to their choice. If they mention pain, pick a replacement that spares the affected area.';
    }

    /**
     * @return array<string, mixed>
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'exercise' => $schema->string()
                ->description('The exercise in the workout to swap, by name.')
                ->required(),
            'from_date' => $schema->string()
                ->description('The workout day, YYYY-MM-DD. Defaults to today.'),
            'replacement' => $schema->string()
                ->description('The chosen alternative exercise name to swap in. Omit to just list alternatives.'),
        ];
    }

    public function handle(Request $request): Stringable|string
    {
        $plan = $this->activePlan($this->user);

        if (! $plan) {
            return ToolResult::error('no_active_plan', 'The user has no active plan.');
        }

        $date = $this->parseDate($request['from_date'] ?? null) ?? today();

        $workout = WorkoutPlan::with('exercises.exercise')
            ->where('plan_id', $plan->id)
            ->whereDate('date', $date)
            ->first();

        if (! $workout || $workout->workout_type === 'rest') {
            return ToolResult::error('no_workout', 'There is no workout on that day.');
        }

        $name = mb_strtolower(trim((string) ($request['exercise'] ?? '')));

        $workoutExercise = $workout->exercises->first(
            fn (WorkoutPlanExercise $e) => $name !== '' && str_contains(mb_strtolower((string) $e->exercise?->name), $name)
        );

        if (! $workoutExercise) {
            return ToolResult::error('exercise_not_found', 'That exercise is not in the workout.', [
                'exercises' => $workout->exercises->map(fn (WorkoutPlanExercise $e) => $e->exercise?->name)->filter()->values()->all(),
            ]);
        }

        $candidates = $this->candidatesFor($workoutExercise);

        if ($candidates->isEmpty()) {
            return ToolResult::error('no_alternatives', 'No suitable alternative exercises were found for that one.');
        }

        $replacement = trim((string) ($request['replacement'] ?? ''));

        if ($replacement === '') {
            return ToolResult::data([
                'exercise' => $workoutExercise->exercise?->name,
                'alternatives' => $candidates->map(fn (Exercise $e) => [
                    'name' => $e->name,
                    'muscles' => $e->primary_muscles,
                ])->values()->all(),
            ]);
        }

        $chosen = $candidates->first(
            fn (Exercise $e) => str_contains(mb_strtolower($e->name), mb_strtolower($replacement))
        );

        if (! $chosen) {
            return ToolResult::error('replacement_not_offered', 'That replacement was not one of the offered options — offer the listed alternatives again.');
        }

        $this->replace->execute($workoutExercise, $chosen);

        return ToolResult::data([
            'swapped' => true,
            'from' => $workoutExercise->exercise?->name,
            'to' => $chosen->name,
        ]);
    }

    /**
     * Alternatives stored at generation time, else same-muscle exercises from
     * the catalog for the user's training place.
     *
     * @return Collection<int, Exercise>
     */
    private function candidatesFor(WorkoutPlanExercise $workoutExercise): Collection
    {
        $storedIds = collect($workoutExercise->alternatives ?? [])->pluck('exercise_id')->filter();

        if ($storedIds->isNotEmpty()) {
            $stored = Exercise::whereIn('id', $storedIds)->get();

            if ($stored->isNotEmpty()) {
                return $stored;
            }
        }

        $muscles = $workoutExercise->exercise?->primary_muscles ?? [];
        $place = $this->user->profile?->training_place?->value;

        return Exercise::query()
            ->where('id', '!=', $workoutExercise->exercise_id)
            ->when($muscles !== [], fn ($query) => $query->where(function ($query) use ($muscles) {
                foreach ($muscles as $muscle) {
                    $query->orWhereJsonContains('primary_muscles', $muscle);
                }
            }))
            ->when($place, fn ($query) => $query->forTrainingPlace($place))
            ->limit(self::MAX_ALTERNATIVES)
            ->get();
    }

    private function parseDate(mixed $value): ?Carbon
    {
        if (! is_string($value) || trim($value) === '') {
            return null;
        }

        try {
            return Carbon::parse($value);
        } catch (InvalidFormatException) {
            return null;
        }
    }
}
