<?php

namespace App\Ai\Tools;

use App\Actions\RescheduleWorkout;
use App\Ai\Tools\Concerns\InteractsWithPlan;
use App\Ai\Tools\Support\ToolResult;
use App\Models\User;
use Carbon\Carbon;
use Carbon\Exceptions\InvalidFormatException;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Tools\Request;
use Stringable;

/**
 * Lets the user move or skip today's workout when they can't train — "ich kann
 * heute nicht trainieren". Skipping rests today; moving copies the session to a
 * later day and rests today. Both change the plan, so Mona only calls this once
 * the user has decided.
 */
class RescheduleWorkoutTool implements Tool
{
    use InteractsWithPlan;

    public function __construct(
        private readonly User $user,
        private readonly RescheduleWorkout $reschedule,
    ) {}

    public function description(): Stringable|string
    {
        return 'Moves or skips today\'s workout when the user can\'t train ("ich kann heute nicht trainieren", "verschieb mein Training auf morgen", "lass uns heute überspringen"). action="skip" rests today and drops the session; action="move" copies it to target_date (YYYY-MM-DD) and rests today. Ask the user whether to skip or move, and to when, before calling. If it returns target_conflict, tell them what is already on that day and call again with confirmed=true only if they agree to replace it.';
    }

    /**
     * @return array<string, mixed>
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'action' => $schema->string()
                ->description('skip or move.')
                ->required(),
            'target_date' => $schema->string()
                ->description('For move: the day to move the workout to, as YYYY-MM-DD (e.g. tomorrow).'),
            'confirmed' => $schema->boolean()
                ->description('True only after the user agreed to replace a workout already on the target day.'),
        ];
    }

    public function handle(Request $request): Stringable|string
    {
        $workout = $this->todaysWorkoutPlan($this->user);

        if (! $workout || $workout->workout_type === 'rest') {
            return ToolResult::error('no_workout_today', 'There is no workout to move today — today is already a rest day.');
        }

        $action = strtolower((string) ($request['action'] ?? ''));

        if ($action === 'skip') {
            $result = $this->reschedule->skip($this->user, $workout);

            return ToolResult::info('workout_rescheduled', $result);
        }

        if ($action !== 'move') {
            return ToolResult::error('need_decision', 'Ask the user whether to skip today or move the workout to another day.');
        }

        $target = $this->parseDate($request['target_date'] ?? null);

        if (! $target) {
            return ToolResult::error('need_target_date', 'Ask the user which day to move the workout to.');
        }

        $result = $this->reschedule->move($this->user, $workout, $target, (bool) ($request['confirmed'] ?? false));

        return match ($result['outcome']) {
            'moved' => ToolResult::info('workout_rescheduled', $result),
            'target_conflict' => ToolResult::error(
                'target_conflict',
                'That day already has a workout — tell the user and ask if they want to replace it.',
                ['conflict' => $result['conflict']],
            ),
            'same_day' => ToolResult::error('same_day', 'That is today — ask for a different day, or skip instead.'),
            'in_past' => ToolResult::error('in_past', 'That day is in the past — ask for an upcoming day.'),
            default => ToolResult::error('outside_plan', 'That day is outside their current plan — ask for a day within it.'),
        };
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
