<?php

namespace App\Jobs;

use App\Models\Meal;
use App\Models\MealPlan;
use App\Models\Plan;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use OpenAI;

class GenerateUserMealPlan implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public User $user,
        public Plan $plan
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $profile = $this->user->profile;

        if (!$profile) {
            Log::error('User profile not found', ['user_id' => $this->user->id]);
            return;
        }

        $client = OpenAI::client(config('services.openai.api_key'));

        // Generate meal plans for all 28 days
        $totalDays = 3;

        for ($day = 1; $day <= $totalDays; $day++) {
            $date = $this->plan->start_date->copy()->addDays($day - 1);

            // Create meal plan record (or find existing)
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

            // Skip if already generated
            if ($mealPlan->status === 'generated') {
                Log::info("Meal plan for day {$day} already generated, skipping", ['meal_plan_id' => $mealPlan->id]);
                continue;
            }

            try {
                // Create system prompt with user profile data
                $systemPrompt = $this->buildSystemPrompt($profile);

                // Call OpenAI with tool calling
                $response = $client->chat()->create([
                    'model' => 'gpt-5-mini',
                    'messages' => [
                        [
                            'role' => 'system',
                            'content' => $systemPrompt,
                        ],
                        [
                            'role' => 'user',
                            'content' => "Generate a complete day meal plan for day {$day} of 28. Include breakfast, lunch, snack, and dinner. Ensure variety across the 28-day plan.",
                        ],
                    ],
                    'tools' => [
                        [
                            'type' => 'function',
                            'function' => [
                                'name' => 'create_meal_plan',
                                'description' => 'Creates a complete daily meal plan with all meals',
                                'parameters' => [
                                    'type' => 'object',
                                    'properties' => [
                                        'meals' => [
                                            'type' => 'array',
                                            'items' => [
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
                                                    'tags' => [
                                                        'type' => 'array',
                                                        'items' => ['type' => 'string'],
                                                    ],
                                                    'allergens' => [
                                                        'type' => 'array',
                                                        'items' => ['type' => 'string'],
                                                    ],
                                                ],
                                                'required' => ['type', 'name', 'calories', 'protein_g', 'carbs_g', 'fat_g'],
                                            ],
                                        ],
                                    ],
                                    'required' => ['meals'],
                                ],
                            ],
                        ],
                    ],
                    'tool_choice' => ['type' => 'function', 'function' => ['name' => 'create_meal_plan']],
                ]);

                $toolCall = $response->choices[0]->message->toolCalls[0] ?? null;

                if ($toolCall && $toolCall->function->name === 'create_meal_plan') {
                    $arguments = json_decode($toolCall->function->arguments, true);
                    $this->saveMeals($mealPlan, $arguments['meals']);

                    // Update meal plan status and totals
                    $totals = $this->calculateTotals($arguments['meals']);
                    $mealPlan->update([
                        'status' => 'generated',
                        'total_calories' => $totals['calories'],
                        'total_protein_g' => $totals['protein_g'],
                        'total_carbs_g' => $totals['carbs_g'],
                        'total_fat_g' => $totals['fat_g'],
                    ]);

                    Log::info("Generated meal plan for day {$day}", ['meal_plan_id' => $mealPlan->id]);
                }
            } catch (\Exception $e) {
                Log::error("Failed to generate meal plan for day {$day}", [
                    'error' => $e->getMessage(),
                    'meal_plan_id' => $mealPlan->id,
                ]);

                $mealPlan->update(['status' => 'failed']);
            }
        }
    }

    private function buildSystemPrompt($profile): string
    {
        $metabolismData = $profile->getMetabolismData();

        return <<<PROMPT
You are an expert nutritionist and meal planner. Create personalized meal plans based on the following user profile:

**User Profile:**
- Age: {$profile->age}
- Gender: {$profile->gender->value}
- Weight: {$profile->weight} kg
- Height: {$profile->height} cm
- Body Goal: {$profile->body_goal->value}
- Diet Type: {$profile->diet_type->value}
- Activity Level: {$profile->activity_level->value}
- Training Sessions per Week: {$profile->training_sessions_per_week}

**Nutritional Targets:**
- Daily Calories: {$metabolismData['daily_calories']} kcal
- Protein: {$metabolismData['protein_g']}g
- Carbs: {$metabolismData['carbs_g']}g
- Fat: {$metabolismData['fat_g']}g

**Requirements:**
1. All meals must fit the user's diet type ({$profile->diet_type->value})
2. Total daily nutrition should match the targets (±5% tolerance)
3. Recipes should be practical, with clear ingredients and instructions
4. Provide variety across the 28-day plan - avoid repetitive meals
5. Consider the user's body goal when planning meals
6. All measurements in metric (grams, ml)
7. Use German language for meal names and instructions

Generate complete, practical meal plans that the user can easily follow.
PROMPT;
    }

    private function saveMeals(MealPlan $mealPlan, array $meals): void
    {
        foreach ($meals as $mealData) {
            Meal::create([
                'meal_plan_id' => $mealPlan->id,
                'type' => $mealData['type'],
                'name' => $mealData['name'],
                'description' => $mealData['description'] ?? null,
                'calories' => $mealData['calories'],
                'protein_g' => $mealData['protein_g'],
                'carbs_g' => $mealData['carbs_g'],
                'fat_g' => $mealData['fat_g'],
                'fiber_g' => $mealData['fiber_g'] ?? null,
                'sugar_g' => $mealData['sugar_g'] ?? null,
                'ingredients' => $mealData['ingredients'] ?? [],
                'instructions' => $mealData['instructions'] ?? [],
                'prep_time_minutes' => $mealData['prep_time_minutes'] ?? null,
                'cook_time_minutes' => $mealData['cook_time_minutes'] ?? null,
                'difficulty' => $mealData['difficulty'] ?? 'Medium',
                'servings' => 1,
                'tags' => $mealData['tags'] ?? [],
                'allergens' => $mealData['allergens'] ?? [],
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
