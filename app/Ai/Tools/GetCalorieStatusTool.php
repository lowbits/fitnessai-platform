<?php

namespace App\Ai\Tools;

use App\Ai\Tools\Concerns\InteractsWithPlan;
use App\Ai\Tools\Support\ToolResult;
use App\Models\User;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Tools\Request;
use Stringable;

/**
 * Shows the user's calorie status for today — eaten vs goal and what's left,
 * plus macros. Read-only.
 */
class GetCalorieStatusTool implements Tool
{
    use InteractsWithPlan;

    public function __construct(private readonly User $user) {}

    public function description(): Stringable|string
    {
        return "Shows the user's calorie status for today: how much they have eaten versus their goal, what's left, and their macros. Use it for \"wie viele Kalorien hab ich noch?\", \"hab ich heute zu viel gegessen?\", \"wie stehen meine Makros?\". Read-only.";
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
        $plan = $this->activePlan($this->user);

        if (! $plan) {
            return ToolResult::error('no_active_plan', 'There is no active plan yet.');
        }

        $eaten = $this->user->calorieTrackings()
            ->whereDate('tracked_date', today())
            ->selectRaw('COALESCE(SUM(calories),0) as kcal, COALESCE(SUM(protein_g),0) as protein, COALESCE(SUM(carbs_g),0) as carbs, COALESCE(SUM(fat_g),0) as fat')
            ->first();

        return ToolResult::info('calorie_status', [
            'eaten' => (int) round($eaten->kcal),
            'goal' => (int) $plan->daily_calories,
            'remaining' => (int) round($plan->daily_calories - $eaten->kcal),
            'protein' => ['eaten' => (int) round($eaten->protein), 'target' => (int) $plan->daily_protein_g],
            'carbs' => ['eaten' => (int) round($eaten->carbs), 'target' => (int) $plan->daily_carbs_g],
            'fat' => ['eaten' => (int) round($eaten->fat), 'target' => (int) $plan->daily_fat_g],
        ]);
    }
}
