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

        Log::info('Starting meal plan generation', [
            'user_id' => $this->user->id,
            'plan_id' => $this->plan->id,
            'locale' => $this->user->locale,
        ]);

        $client = OpenAI::client(config('services.openai.api_key'));

        // Generate meal plans for configured number of days
        $totalDays = config('plans.duration_days');

        // Build system prompt ONCE
        $systemPrompt = [
            'role' => 'system',
            'content' => $this->buildSystemPrompt($profile),
        ];

        // Track generated meals for context (lightweight summary)
        $generatedMealsSummary = [];

        Log::debug('System prompt created', [
            'prompt_length' => strlen($systemPrompt['content']),
            'total_days' => $totalDays,
        ]);

        for ($day = 1; $day <= $totalDays; $day++) {
            $date = $this->plan->start_date->copy()->addDays($day - 1);

            Log::debug("Processing day {$day}/{$totalDays}", [
                'day' => $day,
                'date' => $date->format('Y-m-d'),
            ]);

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
                Log::info("Meal plan for day {$day} already generated, skipping", [
                    'meal_plan_id' => $mealPlan->id,
                    'existing_meals_count' => $mealPlan->meals()->count(),
                ]);

                // Add meals to summary for context
                $meals = $mealPlan->meals()->get();
                foreach ($meals as $meal) {
                    $generatedMealsSummary[] = "{$meal->type}: {$meal->name}";
                }

                continue;
            }

            try {
                // Build context-aware user message with recent meals summary
                $contextMessage = "Generate a complete day meal plan for day {$day} of {$totalDays}. Include breakfast, lunch, snack, and dinner.";

                // Add recent meals context (last 3 days = 12 meals to keep tokens low)
                if (count($generatedMealsSummary) > 0) {
                    $recentMeals = array_slice($generatedMealsSummary, -12);
                    $contextMessage .= "\n\nRecent meals (avoid repetition):\n" . implode("\n", $recentMeals);
                }

                // Keep ONLY system prompt + current request (constant size!)
                $requestMessages = [
                    $systemPrompt,
                    [
                        'role' => 'user',
                        'content' => $contextMessage,
                    ],
                ];

                Log::debug("Calling OpenAI for day {$day}", [
                    'model' => 'gpt-5-mini',
                    'messages_count' => count($requestMessages),
                    'context_meals_count' => count($recentMeals ?? []),
                ]);

                $startTime = microtime(true);

                // Use Tool Calls (more reliable than JSON Schema!)
                $response = $client->chat()->create([
                    'model' => 'gpt-5-mini',
                    'reasoning_effort' => 'minimal',
                    'messages' => $requestMessages,
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
                                                    'instructions' => ['type' => 'array', 'items' => ['type' => 'string']],
                                                    'prep_time_minutes' => ['type' => 'integer'],
                                                    'cook_time_minutes' => ['type' => 'integer'],
                                                    'difficulty' => ['type' => 'string', 'enum' => ['Easy', 'Medium', 'Hard']],
                                                    'tags' => ['type' => 'array', 'items' => ['type' => 'string']],
                                                    'allergens' => ['type' => 'array', 'items' => ['type' => 'string']],
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

                $duration = microtime(true) - $startTime;

                Log::debug("OpenAI response received for day {$day}", [
                    'duration_seconds' => round($duration, 2),
                    'finish_reason' => $response->choices[0]->finishReason ?? 'unknown',
                    'usage' => [
                        'prompt_tokens' => $response->usage->promptTokens ?? 0,
                        'completion_tokens' => $response->usage->completionTokens ?? 0,
                        'total_tokens' => $response->usage->totalTokens ?? 0,
                    ],
                ]);

                // Parse tool call (more reliable than JSON parsing!)
                $toolCall = $response->choices[0]->message->toolCalls[0] ?? null;

                if ($toolCall && $toolCall->function->name === 'create_meal_plan') {
                    $arguments = json_decode($toolCall->function->arguments, true);

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
                    ]);

                    // Add meals to lightweight summary for next iterations (NOT to messages array!)
                    foreach ($arguments['meals'] as $meal) {
                        $generatedMealsSummary[] = "{$meal['type']}: {$meal['name']}";
                    }

                    Log::info("Generated meal plan for day {$day}", [
                        'meal_plan_id' => $mealPlan->id,
                        'meals_created' => count($arguments['meals']),
                        'totals' => $totals,
                        'duration_seconds' => round($duration, 2),
                        'total_meals_in_summary' => count($generatedMealsSummary),
                    ]);
                } else {
                    Log::warning("No tool call received for day {$day}", [
                        'response' => json_encode($response->choices[0] ?? 'empty'),
                    ]);
                }
            } catch (\Exception $e) {
                Log::error("Failed to generate meal plan for day {$day}", [
                    'error' => $e->getMessage(),
                    'error_class' => get_class($e),
                    'meal_plan_id' => $mealPlan->id,
                    'trace' => $e->getTraceAsString(),
                ]);

                $mealPlan->update(['status' => 'failed']);

                // Don't break the loop - continue with next day
                continue;
            }
        }

        Log::info('Meal plan generation completed', [
            'user_id' => $this->user->id,
            'plan_id' => $this->plan->id,
            'total_days' => $totalDays,
            'generated_count' => MealPlan::where('plan_id', $this->plan->id)
                ->where('status', 'generated')
                ->count(),
            'failed_count' => MealPlan::where('plan_id', $this->plan->id)
                ->where('status', 'failed')
                ->count(),
        ]);
    }

    private function buildSystemPrompt($profile): string
    {
        $totalDays = config('plans.duration_days');
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
4. Provide variety across the $totalDays-day plan - avoid repetitive meals
5. Consider the user's body goal when planning meals
6. All measurements in metric (grams, ml)
7. Use {$this->getLanguageInstruction()} for meal names, allergens, tags, descriptions, and instructions

**Output Format:**
Return a valid JSON object with a "meals" array containing 4 meals (breakfast, lunch, snack, dinner).
Each meal must include: type, name, description, calories, protein_g, carbs_g, fat_g, ingredients (with name/amount/unit), and instructions.

Generate complete, practical meal plans that the user can easily follow.
PROMPT;
    }

    /**
     * Get language instruction for OpenAI prompt
     */
    private function getLanguageInstruction(): string
    {
        $language = $this->user->locale;

        return match($language) {
            'de' => 'German language',
            'en' => 'English language',
            default => 'English language',
        };
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
