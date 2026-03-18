<?php

namespace App\Jobs;

use App\Helpers\ToolCallHelper;
use App\Models\Meal;
use App\Models\User;
use App\OpenAITools\MealToolDefinition;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use OpenAI\Laravel\Facades\OpenAI;
use Throwable;

/**
 * Job that replaces a meal with an alternative
 * The hint parameter is the recipe title instruction (e.g., "Grilled Salmon with Lemon Herb Quinoa")
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

        if (! $profile) {
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
            'with_hint' => ! empty($this->hint),
        ]);

        // Create new meal with placeholder data and status "generating"
        $newMeal = Meal::create([
            'meal_plan_id' => $mealPlan->id,
            'type' => $this->meal->type,
            'name' => 'Generating...',
            'description' => 'Generating meal replacement...',
            'calories' => $this->meal->calories,
            'protein_g' => $this->meal->protein_g,
            'carbs_g' => $this->meal->carbs_g,
            'fat_g' => $this->meal->fat_g,
            'status' => 'generating',
        ]);

        $this->meal->delete();

        try {
            $instructions = $this->buildSystemPrompt($profile, $user);
            $contextMessage = $this->buildContextMessage();

            $startTime = microtime(true);

            $response = OpenAI::responses()->create([
                'model' => config('ai.models.simple'),
                'instructions' => $instructions,
                'input' => $contextMessage,
                'tools' => [
                    MealToolDefinition::getReplaceMealTool(),
                ],
                'tool_choice' => 'required',
                'store' => true,
                'metadata' => [
                    'user_id' => (string) $user->id,
                    'old_meal_id' => (string) $this->meal->id,
                    'new_meal_id' => (string) $newMeal->id,
                    'replacement' => 'true',
                ],
            ]);

            $duration = microtime(true) - $startTime;

            // Parse tool call using ToolCallHelper
            $arguments = ToolCallHelper::extractToolCall($response, 'replace_meal');

            // Update the new meal with generated data
            $newMeal->update([
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
                'status' => 'generated',
            ]);

            // Update meal plan totals and set status back to "generated"
            $this->updateMealPlanTotals($mealPlan);
            $mealPlan->update(['status' => 'generated']);
            $this->meal->update(['status' => 'replaced']);

            Log::info('Meal replaced successfully', [
                'old_meal_id' => $this->meal->id,
                'new_meal_id' => $newMeal->id,
                'new_name' => $newMeal->name,
                'duration_seconds' => round($duration, 2),
                'openai_response_id' => $response->id,
            ]);
        } catch (Throwable $e) {
            Log::error('Failed to replace meal', [
                'old_meal_id' => $this->meal->id,
                'new_meal_id' => $newMeal->id,
                'error' => $e->getMessage(),
                'error_class' => get_class($e),
            ]);

            // Mark the new meal as failed instead of deleting it
            $newMeal->update(['status' => 'failed']);
            $mealPlan->update(['status' => 'generated']);
            // restore old meal
            $this->meal->update(['status' => 'generated']);
            $this->meal->restore();

            throw $e;
        }
    }

    private function buildSystemPrompt($profile, User $user): string
    {
        $metabolismData = $profile->getMetabolismData();
        $language = $this->getLanguageInstruction($user);
        $dietStyle = $profile->diet_style?->value ?? '';

        return <<<PROMPT
You are a world-class nutritionist specializing in personalized meal replacements.

**User Profile:**
- Age: {$profile->age} years
- Gender: {$profile->gender->value}
- Current Weight: {$profile->weight} kg
- Height: {$profile->height} cm
- Body Goal: {$profile->body_goal->value}
- Diet Type: {$profile->dietary_preference->value}
- Diet Style: $dietStyle
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
   - STRICTLY follow {$profile->dietary_preference->value} diet principles
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
            $message .= "**Recipe Title to Create:** {$this->hint}\n\n";
            $message .= "Create a complete recipe for this exact meal title while maintaining similar nutritional values to the original meal.\n";
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
