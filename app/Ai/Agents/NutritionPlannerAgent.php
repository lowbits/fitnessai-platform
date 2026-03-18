<?php

namespace App\Ai\Agents;

use App\Ai\Prompts\MealPlanSystemPrompt;
use App\Ai\Tools\SaveMealPlanTool;
use App\Models\MealPlan;
use Laravel\Ai\Attributes\Provider;
use Laravel\Ai\Attributes\Timeout;
use Laravel\Ai\Contracts\Agent;
use Laravel\Ai\Contracts\Conversational;
use Laravel\Ai\Contracts\HasTools;
use Laravel\Ai\Enums\Lab;
use Laravel\Ai\Messages\AssistantMessage;
use Laravel\Ai\Messages\UserMessage;
use Laravel\Ai\Promptable;

#[Timeout(300)]
#[Provider([Lab::OpenAI, Lab::Mistral])]
class NutritionPlannerAgent implements Agent, Conversational, HasTools
{
    use Promptable;

    public function __construct(private readonly MealPlan $mealPlan) {}

    public function model(): string
    {
        return config('ai.models.agent');
    }

    /**
     * Static nutritionist knowledge — cached by the provider.
     * Same for every user, every generation.
     */
    public function instructions(): string
    {
        return (string) new MealPlanSystemPrompt;
    }

    /**
     * Build conversation history from previously generated days
     * so the model avoids repetition and ensures cross-day variety.
     *
     * Capped to 4 most recent days to bound token count on long plans.
     */
    public function messages(): iterable
    {
        $previousDays = MealPlan::query()
            ->where('plan_id', $this->mealPlan->plan_id)
            ->where('status', 'generated')
            ->where('id', '!=', $this->mealPlan->id)
            ->with('meals:id,meal_plan_id,type,name,ingredients,primary_protein,cuisine')
            ->orderByDesc('day_number')
            ->limit(4)
            ->get()
            ->sortBy('day_number')
            ->values();

        $messages = [];

        foreach ($previousDays as $day) {
            $messages[] = new UserMessage("Generate meal plan for Day {$day->day_number}.");

            $lines = ["Day {$day->day_number} meals saved:"];

            foreach ($day->meals as $meal) {
                $keyIngredients = $this->extractKeyIngredients($meal->ingredients ?? []);
                $ingredientList = $keyIngredients ? ' ('.implode(', ', $keyIngredients).')' : '';

                $meta = array_filter([$meal->primary_protein, $meal->cuisine]);
                $metaSuffix = $meta ? ' ['.implode(', ', $meta).']' : '';

                $lines[] = '- '.ucfirst($meal->type).": \"{$meal->name}\"{$ingredientList}{$metaSuffix}";
            }

            $messages[] = new AssistantMessage(implode("\n", $lines));
        }

        return $messages;
    }

    /**
     * Extract the first 3 ingredient names for diversity signals.
     *
     * @param  array<int, array{name?: string, amount?: string, unit?: string}>  $ingredients
     * @return array<int, string>
     */
    private function extractKeyIngredients(array $ingredients): array
    {
        return collect($ingredients)
            ->take(3)
            ->pluck('name')
            ->filter()
            ->values()
            ->all();
    }

    /**
     * Get the tools available to the agent.
     *
     * @return Tool[]
     */
    public function tools(): iterable
    {
        return [
            app(SaveMealPlanTool::class, ['mealPlan' => $this->mealPlan]),
        ];
    }
}
