<?php

namespace App\Http\Controllers\Api\V2;

use App\Helpers\ToolCallHelper;
use App\Http\Controllers\Controller;
use App\Models\Meal;
use App\OpenAITools\MealToolDefinition;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use OpenAI\Laravel\Facades\OpenAI;

class GetMealAlternativesController extends Controller
{
    /**
     * Get meal alternatives for a specific meal
     * Returns 5 alternative meal suggestions based on user preferences
     */
    public function __invoke(Request $request, Meal $meal): JsonResponse
    {

        Gate::authorize('update', $meal);

        $user = $request->user();

        // Validate input
        $validated = Validator::validate($request->all(), [
            'hint' => 'nullable|string|max:500',
        ]);

        try {
            $titles = $this->generateAlternativeTitles($meal, $user->profile, $user, $validated['hint'] ?? null);

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
- Diet Type: {$profile->getDietaryInfo()}

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
2. STRICTLY follow {$profile->getDietaryInfo()} diet principles
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
            'dietary_preference' => $profile->getDietaryInfo(),
            'with_hint' => ! empty($hint),
        ]);

        $response = OpenAI::responses()->create([
            'model' => config('ai.models.simple'),
            'input' => $userMessage,
            'instructions' => $systemPrompt,
            'tools' => [
                MealToolDefinition::getProvideMealTitlesTool(),
            ],
            'tool_choice' => 'required',
        ]);

        $arguments = ToolCallHelper::extractToolCall(
            $response,
            'provide_meal_titles',
            fn ($args) => isset($args['titles'])
                && is_array($args['titles'])
        );

        Log::info('Generated meal title alternatives', [
            'meal_id' => $meal->id,
            'titles_count' => count($arguments['titles']),
        ]);

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
