<?php

namespace App\Ai\Tools;

use App\Actions\RegenerateRemainingPlan;
use App\Ai\Tools\Concerns\InteractsWithPlan;
use App\Models\User;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\Support\Facades\Cache;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Tools\Request;
use Stringable;

/**
 * Applies a preference the user just changed (goal, calorie target, focus
 * areas, a new limitation) to their existing plan by rebuilding the remaining
 * days. Confirmation-gated so it never fires by accident, and throttled per
 * plan so repeated changes don't spin up costly generation over and over.
 */
class RegeneratePlanTool implements Tool
{
    use InteractsWithPlan;

    public function __construct(
        private readonly User $user,
        private readonly RegenerateRemainingPlan $regenerate,
    ) {}

    public function description(): Stringable|string
    {
        return 'Rebuilds the user\'s upcoming meals and workouts so their existing plan reflects a change they just made (goal, calorie target, focus areas, or a new limitation). Past days and anything already eaten or trained are kept. Because this is a bigger action, ALWAYS confirm first: call it with confirmed=false to preview, then only call it with confirmed=true after the user clearly says yes. Only use it when a plan-affecting setting actually changed this conversation.';
    }

    /**
     * @return array<string, mixed>
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'confirmed' => $schema->boolean()
                ->description('False to preview and ask; true only after the user has explicitly agreed to rebuild the plan.'),
        ];
    }

    public function handle(Request $request): Stringable|string
    {
        $plan = $this->activePlan($this->user);

        if (! $plan) {
            return json_encode(['error' => 'no_active_plan', 'message' => 'The user has no active plan to rebuild.']);
        }

        if (! ($request['confirmed'] ?? false)) {
            return json_encode([
                'requires_confirmation' => true,
                'message' => 'This rebuilds their upcoming meals and workouts to match the new settings. Past days and anything already eaten stay. Ask them to confirm before rebuilding.',
            ]);
        }

        $cooldown = (int) config('plans.regenerate_cooldown_minutes', 30);

        if (! Cache::add("coach:plan_regen:{$plan->id}", true, now()->addMinutes($cooldown))) {
            return json_encode([
                'error' => 'throttled',
                'message' => 'The plan was just rebuilt and is still updating. Tell them to give it a few minutes before changing it again.',
            ]);
        }

        $summary = $this->regenerate->execute($this->user, $plan);

        return json_encode([
            'regenerating' => true,
            ...$summary,
            'message' => 'The plan is rebuilding from tomorrow — it will be ready in a few minutes.',
        ]);
    }
}
