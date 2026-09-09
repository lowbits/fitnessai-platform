<?php

namespace App\Ai\Tools;

use App\Actions\RescheduleWorkout;
use App\Ai\Tools\Concerns\InteractsWithPlan;
use App\Ai\Tools\Support\ToolResult;
use App\Models\User;
use App\Models\WorkoutPlan;
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
        return 'Moves or skips a workout when the user can\'t train as planned — including pulling a future workout to today or pushing today\'s to another day ("ich kann heute nicht trainieren", "verschieb Beintag auf morgen", "zieh das Training von morgen auf heute"). from_date is the day whose workout to act on (YYYY-MM-DD, default today). action="skip" rests that day and drops the session; action="move" copies that day\'s workout to target_date (YYYY-MM-DD) and rests the source day. So moving tomorrow\'s workout to today = from_date tomorrow, target_date today; moving Sunday\'s to today = from_date this week\'s Sunday, target_date today. Resolve any day the user names — a weekday like "Sonntag", "tomorrow", or a date — to the actual YYYY-MM-DD within their plan. Ask the user what to do and for which days before calling. If it returns target_conflict, tell them what is already on the target day and call again with confirmed=true only if they agree to replace it.';
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
            'from_date' => $schema->string()
                ->description('The day whose workout to act on, as YYYY-MM-DD. Defaults to today.'),
            'target_date' => $schema->string()
                ->description('For move: the day to move the workout to, as YYYY-MM-DD (e.g. today, or tomorrow).'),
            'confirmed' => $schema->boolean()
                ->description('True only after the user agreed to replace a workout already on the target day.'),
        ];
    }

    public function handle(Request $request): Stringable|string
    {
        $plan = $this->activePlan($this->user);

        if (! $plan) {
            return ToolResult::error('no_active_plan', 'The user has no active plan.');
        }

        $from = $this->parseDate($request['from_date'] ?? null) ?? today();

        $workout = WorkoutPlan::with('exercises')
            ->where('plan_id', $plan->id)
            ->whereDate('date', $from)
            ->first();

        if (! $workout || $workout->workout_type === 'rest') {
            return ToolResult::error('no_workout_today', 'There is no workout on that day to move or skip.');
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
            'same_day' => ToolResult::error('same_day', 'The workout is already on that day — ask for a different day, or skip instead.'),
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
