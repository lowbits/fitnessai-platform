<?php

namespace App\OpenAITools;

/**
 * Reusable OpenAI tool definition for meal generation
 * Used in both GenerateMealPlanBatch and ReplaceMealJob
 */
class MealToolDefinition
{
    /**
     * Get the complete meal schema for OpenAI function calling
     */
    public static function getMealSchema(): array
    {
        return [
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
        ];
    }

    /**
     * Get the tool definition for creating a complete meal plan (multiple meals)
     */
    public static function getCreateMealPlanTool(): array
    {
        return [
            'type' => 'function',
            'name' => 'create_meal_plan',
            'description' => 'Creates a complete daily meal plan with all meals',
            'parameters' => [
                'type' => 'object',
                'properties' => [
                    'meals' => [
                        'type' => 'array',
                        'items' => self::getMealSchema(),
                    ],
                ],
                'required' => ['meals'],
            ],
        ];
    }

    public static function getProvideMealTitlesTool()
    {

        return [
            'type' => 'function',
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
        ];

    }

    /**
     * Get the tool definition for replacing a single meal
     */
    public static function getReplaceMealTool(): array
    {
        return [
            'type' => 'function',
            'name' => 'replace_meal',
            'description' => 'Creates a replacement meal',
            'parameters' => self::getMealSchema(),
        ];
    }

    /**
     * Create a fake response for testing meal plan generation
     * Returns the output array structure expected by OpenAI Responses API
     */
    public static function fakeCreateMealPlanResponse(array $meals = null): array
    {
        $defaultMeals = $meals ?? self::getDefaultTestMeals();

        return [
            'output' => [
                [
                    'id' => 'call_' . uniqid(),
                    'status' => 'completed',
                    'type' => 'function_call',
                    'call_id' => 'call_' . uniqid(),
                    'name' => 'create_meal_plan',
                    'arguments' => json_encode([
                        'meals' => $defaultMeals,
                    ]),
                    'function' => [
                        'name' => 'create_meal_plan',
                        'arguments' => json_encode([
                            'meals' => $defaultMeals,
                        ]),
                    ],
                ],
            ],
            'outputText' => '',
        ];
    }

    /**
     * Create a fake response for testing meal replacement
     * Returns the output array structure expected by OpenAI Responses API
     */
    public static function fakeReplaceMealResponse(array $mealData = null): array
    {
        $defaultMeal = $mealData ?? [
            'type' => 'lunch',
            'name' => 'Grilled Salmon with Lemon Herb Quinoa',
            'description' => 'Fresh grilled salmon fillet served with fluffy quinoa infused with lemon and herbs',
            'calories' => 520,
            'protein_g' => 42,
            'carbs_g' => 38,
            'fat_g' => 18,
            'fiber_g' => 6,
            'sugar_g' => 3,
            'ingredients' => [
                ['name' => 'Salmon fillet', 'amount' => '200', 'unit' => 'g'],
                ['name' => 'Quinoa', 'amount' => '80', 'unit' => 'g'],
                ['name' => 'Lemon', 'amount' => '1', 'unit' => 'piece'],
                ['name' => 'Fresh herbs', 'amount' => '10', 'unit' => 'g'],
                ['name' => 'Olive oil', 'amount' => '1', 'unit' => 'tbsp'],
            ],
            'instructions' => [
                'Cook quinoa according to package instructions',
                'Season salmon with salt and pepper',
                'Grill salmon for 4-5 minutes per side',
                'Mix cooked quinoa with lemon juice and herbs',
                'Serve salmon on top of quinoa',
            ],
            'prep_time_minutes' => 10,
            'cook_time_minutes' => 15,
            'difficulty' => 'Medium',
            'tags' => ['high-protein', 'healthy-fats', 'omega-3'],
            'allergens' => ['fish'],
        ];

        return [
            'output' => [
                [
                    'id' => 'call_' . uniqid(),
                    'status' => 'completed',
                    'type' => 'function_call',
                    'call_id' => 'call_' . uniqid(),
                    'name' => 'replace_meal',
                    'arguments' => json_encode($defaultMeal),
                    'function' => [
                        'name' => 'replace_meal',
                        'arguments' => json_encode($defaultMeal),
                    ],
                ],
            ],
            'outputText' => '',
        ];
    }


    /**
     * Create a fake response for testing meal replacement
     * Returns the output array structure expected by OpenAI Responses API
     */
    public static function fakeMealAlternativeResponse(array $mealData = null): array
    {

        $defaultTitles = [
            'titles' => [
                'Pan-Seared Salmon with Garlic Asparagus',
                'Baked Salmon Fillet with Dill Yogurt Sauce',
                'Salmon Teriyaki Bowl with Edamame',
                'Grilled Salmon Caesar Salad',
                'Honey Mustard Glazed Salmon with Quinoa',
            ],
        ];

        return [
            'output' => [
                [
                    'id' => 'call_' . uniqid(),
                    'status' => 'completed',
                    'type' => 'function_call',
                    'call_id' => 'call_' . uniqid(),
                    'name' => 'provide_meal_titles',
                    'arguments' => json_encode($defaultTitles),
                    'function' => [
                        'name' => 'replace_meal',
                        'arguments' => json_encode($defaultTitles),
                    ],
                ],
            ],
            'outputText' => '',
        ];
    }

    /**
     * Get default test meals for a complete day
     */
    private static function getDefaultTestMeals(): array
    {
        return [
            [
                'type' => 'breakfast',
                'name' => 'Greek Yogurt Parfait with Berries',
                'description' => 'Creamy Greek yogurt layered with fresh berries and granola',
                'calories' => 380,
                'protein_g' => 28,
                'carbs_g' => 42,
                'fat_g' => 12,
                'fiber_g' => 6,
                'sugar_g' => 18,
                'ingredients' => [
                    ['name' => 'Greek yogurt', 'amount' => '250', 'unit' => 'g'],
                    ['name' => 'Mixed berries', 'amount' => '100', 'unit' => 'g'],
                    ['name' => 'Granola', 'amount' => '40', 'unit' => 'g'],
                    ['name' => 'Honey', 'amount' => '1', 'unit' => 'tbsp'],
                ],
                'instructions' => [
                    'Layer Greek yogurt in a bowl',
                    'Top with fresh berries',
                    'Sprinkle granola on top',
                    'Drizzle with honey',
                ],
                'prep_time_minutes' => 5,
                'cook_time_minutes' => 0,
                'difficulty' => 'Easy',
                'tags' => ['quick', 'high-protein', 'breakfast'],
                'allergens' => ['dairy', 'gluten'],
            ],
            [
                'type' => 'lunch',
                'name' => 'Grilled Chicken Caesar Salad',
                'description' => 'Classic Caesar salad with grilled chicken breast',
                'calories' => 480,
                'protein_g' => 45,
                'carbs_g' => 28,
                'fat_g' => 20,
                'fiber_g' => 4,
                'sugar_g' => 3,
                'ingredients' => [
                    ['name' => 'Chicken breast', 'amount' => '200', 'unit' => 'g'],
                    ['name' => 'Romaine lettuce', 'amount' => '150', 'unit' => 'g'],
                    ['name' => 'Caesar dressing', 'amount' => '30', 'unit' => 'ml'],
                    ['name' => 'Parmesan cheese', 'amount' => '20', 'unit' => 'g'],
                    ['name' => 'Croutons', 'amount' => '30', 'unit' => 'g'],
                ],
                'instructions' => [
                    'Season and grill chicken breast',
                    'Slice grilled chicken',
                    'Toss lettuce with Caesar dressing',
                    'Top with chicken, parmesan, and croutons',
                ],
                'prep_time_minutes' => 10,
                'cook_time_minutes' => 15,
                'difficulty' => 'Easy',
                'tags' => ['high-protein', 'lunch'],
                'allergens' => ['dairy', 'gluten', 'eggs'],
            ],
            [
                'type' => 'snack',
                'name' => 'Protein Smoothie',
                'description' => 'Refreshing protein-packed smoothie',
                'calories' => 280,
                'protein_g' => 25,
                'carbs_g' => 32,
                'fat_g' => 6,
                'fiber_g' => 4,
                'sugar_g' => 22,
                'ingredients' => [
                    ['name' => 'Protein powder', 'amount' => '30', 'unit' => 'g'],
                    ['name' => 'Banana', 'amount' => '1', 'unit' => 'piece'],
                    ['name' => 'Almond milk', 'amount' => '250', 'unit' => 'ml'],
                    ['name' => 'Spinach', 'amount' => '30', 'unit' => 'g'],
                ],
                'instructions' => [
                    'Add all ingredients to blender',
                    'Blend until smooth',
                    'Serve immediately',
                ],
                'prep_time_minutes' => 5,
                'cook_time_minutes' => 0,
                'difficulty' => 'Easy',
                'tags' => ['quick', 'high-protein', 'post-workout'],
                'allergens' => ['dairy'],
            ],
            [
                'type' => 'dinner',
                'name' => 'Teriyaki Salmon with Vegetables',
                'description' => 'Glazed salmon with stir-fried vegetables',
                'calories' => 540,
                'protein_g' => 48,
                'carbs_g' => 35,
                'fat_g' => 22,
                'fiber_g' => 6,
                'sugar_g' => 12,
                'ingredients' => [
                    ['name' => 'Salmon fillet', 'amount' => '220', 'unit' => 'g'],
                    ['name' => 'Teriyaki sauce', 'amount' => '40', 'unit' => 'ml'],
                    ['name' => 'Broccoli', 'amount' => '150', 'unit' => 'g'],
                    ['name' => 'Bell peppers', 'amount' => '100', 'unit' => 'g'],
                    ['name' => 'Brown rice', 'amount' => '60', 'unit' => 'g'],
                ],
                'instructions' => [
                    'Cook brown rice',
                    'Marinate salmon in teriyaki sauce',
                    'Pan-fry salmon until cooked',
                    'Stir-fry vegetables',
                    'Serve salmon with vegetables and rice',
                ],
                'prep_time_minutes' => 15,
                'cook_time_minutes' => 20,
                'difficulty' => 'Medium',
                'tags' => ['high-protein', 'omega-3', 'dinner'],
                'allergens' => ['fish', 'soy'],
            ],
        ];
    }
}

