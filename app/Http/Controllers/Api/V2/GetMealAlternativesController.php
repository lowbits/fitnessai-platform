<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Controller;
use App\Models\Meal;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use OpenAI\Laravel\Facades\OpenAI;


class GetMealAlternativesController extends Controller
{
    /**
     * Get meal alternatives for a specific meal
     * Returns 5 alternative meal suggestions based on user preferences
     */
    public function __invoke(Request $request, int $mealId): JsonResponse
    {
        $user = $request->user();

        // Validate input
        $validator = Validator::make($request->all(), [
            'hint' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Get meal from database with relations
        $meal = Meal::with('mealPlan.plan')->find($mealId);

        if (!$meal) {
            return response()->json([
                'error' => 'Meal not found',
                'message' => 'The requested meal does not exist',
            ], 404);
        }

        // Verify the meal belongs to user's plan
        $mealPlan = $meal->mealPlan;
        if (!$mealPlan || $mealPlan->plan->user_id !== $user->id) {
            return response()->json([
                'error' => 'Unauthorized',
                'message' => 'You do not have access to this meal',
            ], 403);
        }

        $profile = $user->profile;
        if (!$profile) {
            return response()->json([
                'error' => 'Profile not found',
                'message' => 'User profile is required to generate alternatives',
            ], 400);
        }

        try {
            $hint = $request->input('hint');
            $titles = $this->generateAlternativeTitles($meal, $profile, $user, $hint);

            return response()->json([
                'titles' => $titles,
                'original_meal' => [
                    'id' => $meal->id,
                    'name' => $meal->name,
                    'type' => $meal->type,
                    'calories' => $meal->calories,
                    'protein_g' => $meal->protein_g,
                    'carbs_g' => $meal->carbs_g,
                    'fat_g' => $meal->fat_g,
                ],
            ]);
        } catch (\Throwable $e) {
            Log::error('Failed to generate meal alternatives', [
                'meal_id' => $meal->id,
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'error' => 'Failed to generate alternatives',
                'message' => 'An error occurred while generating meal alternatives. Please try again.',
            ], 500);
        }
    }

    private function generateAlternativeTitles(Meal $meal, $profile, $user, ?string $hint): array
    {
        $language = $this->getLanguageInstruction($user);

        $systemPrompt = <<<PROMPT
You are a world-class nutritionist specializing in personalized meal alternatives.

**User Profile:**
- Body Goal: {$profile->body_goal->value}
- Diet Type: {$profile->diet_type->value}

**Original Meal Context:**
- Meal Type: {$meal->type}
- Current Meal: {$meal->name}
- Target Calories: {$meal->calories} kcal
- Target Protein: {$meal->protein_g}g
- Target Carbs: {$meal->carbs_g}g
- Target Fat: {$meal->fat_g}g

**Your Mission:**
Generate 5 appealing meal TITLES ONLY (not full recipes) as alternatives for this {$meal->type}.

**Critical Requirements:**
1. Each title should suggest a meal with similar macros (±15%)
2. STRICTLY follow {$profile->diet_type->value} diet principles
3. Match the meal type ({$meal->type})
4. Create exciting, varied options (different proteins, cooking methods, cuisines)
5. Use {$language} for ALL titles
6. Make titles appetizing and descriptive (e.g., "Grilled Salmon with Lemon Herb Quinoa")

Generate exactly 5 creative meal titles that match the nutritional profile!
PROMPT;

        $userMessage = "Current {$meal->type}: {$meal->name} ({$meal->calories} kcal)\n";
        $userMessage .= "Macros: Protein {$meal->protein_g}g, Carbs {$meal->carbs_g}g, Fat {$meal->fat_g}g\n\n";

        if ($hint) {
            $userMessage .= "User preference: {$hint}\n\n";
            $userMessage .= "Generate 5 meal TITLES that consider this preference.\n";
        } else {
            $userMessage .= "Generate 5 diverse meal TITLES as alternatives.\n";
        }

        Log::info('Generating meal title alternatives', [
            'meal_id' => $meal->id,
            'meal_type' => $meal->type,
            'meal_calories' => $meal->calories,
            'diet_type' => $profile->diet_type->value,
            'with_hint' => !empty($hint),
        ]);

        $response = OpenAI::chat()->create([
            'model' => 'gpt-4o-mini',
            'messages' => [
                ['role' => 'system', 'content' => $systemPrompt],
                ['role' => 'user', 'content' => $userMessage],
            ],
            'tools' => [
                [
                    'type' => 'function',
                    'function' => [
                        'name' => 'provide_meal_titles',
                        'description' => 'Provides 5 alternative meal title suggestions',
                        'parameters' => [
                            'type' => 'object',
                            'properties' => [
                                'titles' => [
                                    'type' => 'array',
                                    'items' => ['type' => 'string'],
                                    'minItems' => 5,
                                    'maxItems' => 5,
                                    'description' => 'Array of 5 appetizing meal titles',
                                ],
                            ],
                            'required' => ['titles'],
                        ],
                    ],
                ],
            ],
            'tool_choice' => ['type' => 'function', 'function' => ['name' => 'provide_meal_titles']],
        ]);

        dd($response);


        // Parse the function call response
        $toolCall = $response->choices[0]->message->toolCalls[0] ?? null;

        if (!$toolCall || $toolCall->function->name !== 'provide_meal_titles') {
            throw new \RuntimeException('No valid meal titles returned from AI');
        }

        $arguments = json_decode($toolCall->function->arguments, true);

        if (!isset($arguments['titles']) || count($arguments['titles']) !== 5) {
            throw new \RuntimeException('Expected exactly 5 meal titles');
        }

        Log::info('Generated meal title alternatives successfully', [
            'meal_id' => $meal->id,
            'titles_count' => count($arguments['titles']),
        ]);

        // Return simple title array
        return $arguments['titles'];
    }

    private function getLanguageInstruction($user): string
    {
        return match ($user->locale) {
            'de' => 'German language',
            default => 'English language',
        };
    }
}

