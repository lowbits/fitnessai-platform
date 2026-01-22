<?php

namespace App\Jobs;

use App\Models\Meal;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use OpenAI;

/**
 * Job that replaces a meal with an alternative
 * Can work with or without a user hint
 */
class ReplaceMealJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Meal $meal,
        public ?string $hint = null
    ) {
        $this->onQueue('nutrition');
    }

    public function handle(): void
    {
        $mealPlan = $this->meal->mealPlan;
        $plan = $mealPlan->plan;
        $user = $plan->user;
        $profile = $user->profile;

        if (!$profile) {
            Log::error('User profile not found for meal replacement', [
                'user_id' => $user->id,
                'meal_id' => $this->meal->id,
            ]);
            return;
        }

        Log::info('Starting meal replacement', [
            'user_id' => $user->id,
            'meal_id' => $this->meal->id,
            'meal_type' => $this->meal->type,
            'meal_name' => $this->meal->name,
            'with_hint' => !empty($this->hint),
        ]);

        try {
            $client = OpenAI::client(config('services.openai.api_key'));
            $instructions = $this->buildSystemPrompt($profile, $user);
            $contextMessage = $this->buildContextMessage();

            $startTime = microtime(true);

            $response = $client->responses()->create([
                'model' => 'gpt-5-mini',
                'instructions' => $instructions,
                'input' => $contextMessage,
                'tools' => [
                    [
                        'type' => 'function',
                        'name' => 'replace_meal',
                        'description' => 'Creates a replacement meal',
                        'parameters' => [
                            'type' => 'object',
                            'properties' => [
                                'type' => ['type' => 'string', 'enum' => ['breakfast', 'lunch', 'snack', 'dinner']],
                                'name' => ['type' => 'string'],
                                'description' => ['type' => 'string'],
                                'calories' => ['type' => 'integer'],
                                'protein_g' => ['type' => 'integer'],
                                'carbs_g' => ['type' => 'integer'],
                                'fat_g' => ['type' => 'integer'],
                                'fiber_g' => ['type' => 'integer'],
                                'sugar_g' => ['type' => 'integer'],
                                'ingredients' => [
                                    'type' => 'array',
                                    'items' => [
                                        'type' => 'object',
                                        'properties' => [
                                            'name' => ['type' => 'string'],
                                            'amount' => ['type' => 'string'],
                                            'unit' => ['type' => 'string'],
                                        ],
                                        'required' => ['name', 'amount', 'unit'],
                                    ],
                                ],
                                'instructions' => [
                                    'type' => 'array',
                                    'items' => ['type' => 'string'],
                                ],
                                'prep_time_minutes' => ['type' => 'integer'],
                                'cook_time_minutes' => ['type' => 'integer'],
                                'difficulty' => ['type' => 'string', 'enum' => ['Easy', 'Medium', 'Hard']],
                                'tags' => ['type' => 'array', 'items' => ['type' => 'string']],
                                'allergens' => ['type' => 'array', 'items' => ['type' => 'string']],
                            ],
                            'required' => [
                                'type',
                                'name',
                                'calories',
                                'protein_g',
                                'carbs_g',
                                'fat_g',
                            ],
                        ],
                    ],
                ],
                'tool_choice' => 'required',
                'store' => true,
                'metadata' => [
                    'user_id' => (string) $user->id,
                    'meal_id' => (string) $this->meal->id,
                    'replacement' => 'true',
                ],
            ]);

            $duration = microtime(true) - $startTime;

            // Parse Responses API output
            $toolCall = null;
            foreach ($response->output ?? [] as $item) {
                if ($item->type === 'function_call' && $item->name === 'replace_meal') {
                    $toolCall = $item;
                    break;
                }
            }

            if (!$toolCall) {
                Log::warning('No tool call received for meal replacement', [
                    'meal_id' => $this->meal->id,
                ]);
                throw new \RuntimeException('Function call missing in Responses output');
            }

            $arguments = json_decode($toolCall->arguments, true);

            // Update the existing meal with new data
            $this->meal->update([
                'name' => $arguments['name'],
                'description' => $arguments['description'] ?? null,
                'calories' => $arguments['calories'],
                'protein_g' => $arguments['protein_g'],
                'carbs_g' => $arguments['carbs_g'],
                'fat_g' => $arguments['fat_g'],
                'fiber_g' => $arguments['fiber_g'] ?? null,
                'sugar_g' => $arguments['sugar_g'] ?? null,
                'ingredients' => $arguments['ingredients'] ?? [],
                'instructions' => $arguments['instructions'] ?? [],
                'prep_time_minutes' => $arguments['prep_time_minutes'] ?? null,
                'cook_time_minutes' => $arguments['cook_time_minutes'] ?? null,
                'difficulty' => $arguments['difficulty'] ?? 'Medium',
                'tags' => $arguments['tags'] ?? [],
                'allergens' => $arguments['allergens'] ?? [],
            ]);

            // Update meal plan totals
            $this->updateMealPlanTotals($mealPlan);

            Log::info('Meal replaced successfully', [
                'meal_id' => $this->meal->id,
                'new_name' => $this->meal->name,
                'duration_seconds' => round($duration, 2),
                'openai_response_id' => $response->id,
            ]);
        } catch (\Throwable $e) {
            Log::error('Failed to replace meal', [
                'meal_id' => $this->meal->id,
                'error' => $e->getMessage(),
                'error_class' => get_class($e),
            ]);

            throw $e;
        }
    }

    private function buildSystemPrompt($profile, User $user): string
    {
        $metabolismData = $profile->getMetabolismData();
        $language = $this->getLanguageInstruction($user);

        return <<<PROMPT
You are a world-class nutritionist specializing in personalized meal replacements.

**User Profile:**
- Age: {$profile->age} years
- Gender: {$profile->gender->value}
- Current Weight: {$profile->weight} kg
- Height: {$profile->height} cm
- Body Goal: {$profile->body_goal->value}
- Diet Type: {$profile->diet_type->value}
- Activity Level: {$profile->activity_level->value}

**Daily Nutritional Targets:**
- Calories: {$metabolismData['daily_calories']} kcal
- Protein: {$metabolismData['protein_g']}g
- Carbohydrates: {$metabolismData['carbs_g']}g
- Fat: {$metabolismData['fat_g']}g

**Your Mission:**
Replace a meal with an alternative that matches the nutritional profile.

**Critical Requirements:**

1. **Nutritional Matching:**
   - The replacement meal should have similar macros (±10%) to the original
   - Maintain the meal's contribution to daily nutritional goals

2. **Diet Compliance:**
   - STRICTLY follow {$profile->diet_type->value} diet principles
   - No ingredients that violate the diet type

3. **Recipe Quality:**
   - Use specific, measurable ingredients
   - Provide clear, step-by-step cooking instructions
   - Include realistic prep and cook times

4. **Variety & Appeal:**
   - Create an exciting, restaurant-quality meal
   - Use diverse ingredients
   - Add herbs, spices, and flavor enhancers for taste

5. **Practical Considerations:**
   - Achievable in a home kitchen
   - Common, accessible ingredients
   - Efficient use of cooking equipment

6. **Health & Safety:**
   - List ALL allergens clearly
   - Include fiber and sugar content

7. **Language:**
   - Use {$language} for ALL text fields

**Output Format:**
Use the replace_meal function with complete, accurate data.
PROMPT;
    }

    private function buildContextMessage(): string
    {
        $originalMeal = $this->meal;

        $message = "Replace the following {$originalMeal->type} meal:\n\n";
        $message .= "**Original Meal:** {$originalMeal->name}\n";
        $message .= "**Calories:** {$originalMeal->calories} kcal\n";
        $message .= "**Protein:** {$originalMeal->protein_g}g\n";
        $message .= "**Carbs:** {$originalMeal->carbs_g}g\n";
        $message .= "**Fat:** {$originalMeal->fat_g}g\n\n";

        if ($this->hint) {
            $message .= "**User's Request:** {$this->hint}\n\n";
            $message .= "Create a replacement that considers the user's preference while maintaining similar nutritional values.\n";
        } else {
            $message .= "Create a delicious alternative with similar nutritional values but different ingredients and flavors.\n";
        }

        return $message;
    }

    private function getLanguageInstruction(User $user): string
    {
        return match ($user->locale) {
            'de' => 'German language',
            default => 'English language',
        };
    }

    private function updateMealPlanTotals($mealPlan): void
    {
        $meals = $mealPlan->meals;

        $totals = [
            'total_calories' => $meals->sum('calories'),
            'total_protein_g' => $meals->sum('protein_g'),
            'total_carbs_g' => $meals->sum('carbs_g'),
            'total_fat_g' => $meals->sum('fat_g'),
        ];

        $mealPlan->update($totals);
    }
}

