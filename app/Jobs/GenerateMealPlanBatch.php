<?php

namespace App\Jobs;

use App\Helpers\ToolCallHelper;
use App\Models\Meal;
use App\Models\MealPlan;
use App\Models\Plan;
use App\Models\User;
use App\OpenAITools\MealToolDefinition;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use OpenAI\Laravel\Facades\OpenAI;



/**
 * Job that generates meal plans for a specific range of days (2-3 days)
 * Part of the batched meal plan generation system
 */
class GenerateMealPlanBatch implements ShouldQueue
{
    use Queueable;

    private $GPT_MODEL = 'gpt-5.2';

    public function __construct(
        public User $user,
        public Plan $plan,
        public int $startDay,
        public int $endDay
    ) {
        $this->onQueue('nutrition');
    }

    public function handle(): void
    {
        $profile = $this->user->profile;

        if (!$profile) {
            Log::error('User profile not found in batch', [
                'user_id' => $this->user->id,
                'batch_days' => "{$this->startDay}-{$this->endDay}",
            ]);
            return;
        }

        Log::info('Starting meal plan batch generation', [
            'user_id' => $this->user->id,
            'plan_id' => $this->plan->id,
            'days' => "{$this->startDay}-{$this->endDay}",
        ]);

        $instructions = $this->buildSystemPrompt($profile);

        for ($day = $this->startDay; $day <= $this->endDay; $day++) {
            $date = $this->plan->start_date->copy()->addDays($day - 1);

            $mealPlan = MealPlan::firstOrCreate(
                [
                    'plan_id' => $this->plan->id,
                    'day_number' => $day,
                ],
                [
                    'date' => $date,
                    'status' => 'pending',
                ]
            );

            if ($mealPlan->status === 'generated') {
                continue;
            }

            try {

                // Build rich context message
                $dayOfWeek = $date->format('l'); // Monday, Tuesday, etc.
                $contextMessage = "Generate a delicious and nutritionally balanced meal plan for Day {$day} ({$dayOfWeek}, {$date->format('Y-m-d')}).\n\n";
                $contextMessage .= "Create 4 complete meals: breakfast, lunch, snack, and dinner.\n";
                $contextMessage .= "Make each meal appetizing, practical, and aligned with the user's {$profile->body_goal->value} goal.\n";


                Log::debug("Calling OpenAI for day {$day}", [
                    'model' => $this->GPT_MODEL,
                ]);

                $startTime = microtime(true);

                $response = OpenAI::responses()->create([
                    'model' => $this->GPT_MODEL,
                    'instructions' => $instructions,
                    'input' => $contextMessage,
                    'tools' => [
                        MealToolDefinition::getCreateMealPlanTool(),
                    ],
                    'tool_choice' => 'required',
                    'store' => true,
                    'metadata' => [
                        'user_id' => (string) $this->user->id,
                        'plan_id' => (string) $this->plan->id,
                        'day_number' => (string) $day,
                    ],
                ]);

                $duration = microtime(true) - $startTime;

                Log::debug("OpenAI response received for day {$day}", [
                    'duration_seconds' => round($duration, 2),
                    'response_id' => $response->id ?? 'unknown',
                    'output_count' => count($response->output ?? []),
                ]);

                // Parse tool call using ToolCallHelper
                $arguments = ToolCallHelper::extractToolCall($response, 'create_meal_plan');

                Log::debug("Tool call received for day {$day}", [
                    'meals_count' => count($arguments['meals'] ?? []),
                    'meal_types' => array_column($arguments['meals'] ?? [], 'type'),
                ]);

                $this->saveMeals($mealPlan, $arguments['meals']);

                // Update meal plan status and totals
                $totals = $this->calculateTotals($arguments['meals']);
                $mealPlan->update([
                    'status' => 'generated',
                    'total_calories' => $totals['calories'],
                    'total_protein_g' => $totals['protein_g'],
                    'total_carbs_g' => $totals['carbs_g'],
                    'total_fat_g' => $totals['fat_g'],
                    'openai_response_id' => $response->id, // Store for context chaining
                ]);

                Log::info("Generated meal plan for day {$day}", [
                    'meal_plan_id' => $mealPlan->id,
                    'meals_created' => count($arguments['meals']),
                    'totals' => $totals,
                    'duration_seconds' => round($duration, 2),
                    'openai_response_id' => $response->id,
                ]);
            } catch (\Throwable $e) {

                Log::debug($e);
                Log::error("Failed to generate meal plan for day {$day}", [
                    'error' => $e->getMessage(),
                    'error_class' => get_class($e),
                    'batch_days' => "{$this->startDay}-{$this->endDay}",
                ]);

                $mealPlan->update(['status' => 'failed']);
            }
        }

        Log::info('Completed meal plan batch generation', [
            'user_id' => $this->user->id,
            'plan_id' => $this->plan->id,
            'days' => "{$this->startDay}-{$this->endDay}",
        ]);
    }

    private function buildSystemPrompt($profile): string
    {
        $metabolismData = $profile->getMetabolismData();
        $language = $this->getLanguageInstruction();
        $dietStyle = $profile->diet_style?->value ?? '-';
        $gender = $profile->gender?->value;
        $bodyGoal = $profile->body_goal?->value;
        $activityLevel = $profile->activity_level->value;
        $dietaryInfo = filled($profile->dietary_preference?->value)
            ? $profile->dietary_preference->value
            : ($profile->diet_type?->value ?? '-');

        Log::info('Building prompt:', [
            'gender' => $gender,
            'body_goal' => $bodyGoal,
            'activity_level' => $activityLevel,
            'dietary_preference' => $dietaryInfo,
            'diet_style' => $dietStyle,
            'metabolism' => $metabolismData,
            'language' => $language,
        ]);

        return <<<PROMPT
You are a world-class nutritionist and meal planner specializing in personalized nutrition for fitness and body composition goals.

**User Profile:**
- Age: {$profile->age} years
- Gender: {$gender}
- Current Weight: {$profile->weight} kg
- Height: {$profile->height} cm
- Body Goal: {$bodyGoal}
- Dietary Preference: $dietaryInfo
- Diet Style: $dietStyle
- Activity Level: {$activityLevel}
- Training Sessions per Week: {$profile->training_sessions_per_week}

**Daily Nutritional Targets:**
- Calories: {$metabolismData['daily_calories']} kcal
- Protein: {$metabolismData['protein_g']}g (essential for muscle recovery and growth)
- Carbohydrates: {$metabolismData['carbs_g']}g (energy for workouts)
- Fat: {$metabolismData['fat_g']}g (hormonal health and satiety)

**Your Mission:**
Create a complete, delicious, and practical meal plan for ONE day with 4 meals (breakfast, lunch, snack, dinner).

**Critical Requirements:**

1. **Nutritional Accuracy:**
   - Total daily macros MUST be within ±5% of targets
   - Each meal should contribute appropriately to daily goals
   - Consider meal timing: breakfast (25-30%), lunch (30-35%), snack (10-15%), dinner (25-30%)

2. **Diet Compliance:**
   - STRICTLY follow $dietaryInfo diet principles
   - No ingredients that violate the diet type
   - Ensure adequate variety of nutrient sources

3. **Recipe Quality:**
   - Use specific, measurable ingredients (e.g., "200g chicken breast" not "chicken")
   - Provide clear, step-by-step cooking instructions
   - Include realistic prep and cook times
   - Difficulty should match the recipe complexity

4. **Variety & Appeal:**
   - Create exciting, restaurant-quality meals
   - Use diverse ingredients across the day
   - Mix cooking methods (grilled, steamed, raw, baked)
   - Include different protein sources where possible
   - Add herbs, spices, and flavor enhancers for taste

5. **Practical Considerations:**
   - Recipes should be achievable in a home kitchen
   - Common, accessible ingredients (mention exotic ones clearly)
   - Efficient use of cooking equipment
   - Meals can be meal-prepped if needed

6. **Health & Safety:**
   - List ALL allergens clearly
   - Include fiber and sugar content
   - Provide appropriate tags (e.g., high-protein, quick, post-workout)

7. **Language:**
   - Use {$language} for ALL text fields:
     * Meal names
     * Descriptions (make them appetizing!)
     * Ingredient names
     * Cooking instructions
     * Tags and allergens

**Context Awareness:**
If you receive previous meal plans via conversation history, ensure TODAY's meals are:
- Different from recent days (no repetition within 3-4 days)
- Complementary in variety (if yesterday was chicken, try fish or beef today)
- Progressive in complexity if suitable

**Output Format:**
Use the create_meal_plan function with complete, accurate data for all 4 meals.

Make every meal something the user will genuinely look forward to eating!
PROMPT;
    }

    private function getLanguageInstruction(): string
    {
        return match ($this->user->locale) {
            'de' => 'German language',
            default => 'English language',
        };
    }

    private function getPreviousResponseId(int $currentDay): ?string
    {
        if ($currentDay <= 1) {
            return null;
        }

        return MealPlan::where('plan_id', $this->plan->id)
            ->where('day_number', $currentDay - 1)
            ->where('status', 'generated')
            ->value('openai_response_id');
    }

    private function saveMeals(MealPlan $mealPlan, array $meals): void
    {
        foreach ($meals as $meal) {
            Meal::create([
                'meal_plan_id' => $mealPlan->id,
                'type' => $meal['type'],
                'name' => $meal['name'],
                'description' => $meal['description'] ?? null,
                'calories' => $meal['calories'],
                'protein_g' => $meal['protein_g'],
                'carbs_g' => $meal['carbs_g'],
                'fat_g' => $meal['fat_g'],
                'fiber_g' => $meal['fiber_g'] ?? null,
                'sugar_g' => $meal['sugar_g'] ?? null,
                'ingredients' => $meal['ingredients'] ?? [],
                'instructions' => $meal['instructions'] ?? [],
                'prep_time_minutes' => $meal['prep_time_minutes'] ?? null,
                'cook_time_minutes' => $meal['cook_time_minutes'] ?? null,
                'difficulty' => $meal['difficulty'] ?? 'Medium',
                'servings' => 1,
                'tags' => $meal['tags'] ?? [],
                'allergens' => $meal['allergens'] ?? [],
                'status' => 'generated',
            ]);
        }
    }

    private function calculateTotals(array $meals): array
    {
        return [
            'calories' => array_sum(array_column($meals, 'calories')),
            'protein_g' => array_sum(array_column($meals, 'protein_g')),
            'carbs_g' => array_sum(array_column($meals, 'carbs_g')),
            'fat_g' => array_sum(array_column($meals, 'fat_g')),
        ];
    }
}

