<?php

namespace App\Ai\Tools;

use App\Ai\Tools\Support\ToolResult;
use App\Enums\MealType;
use App\Models\User;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Tools\Request;
use Stringable;

/**
 * Logs food the user ate as tracked calories for today, into the same
 * calorie_trackings store the tracking screen and the calorie status read from.
 * Used after the user confirms they want an analyzed or described meal tracked
 * as eaten.
 */
class LogMealTool implements Tool
{
    public function __construct(private readonly User $user) {}

    public function description(): Stringable|string
    {
        return 'Logs food the user actually ate as tracked calories for today so it counts toward their daily total. Use this only AFTER the user confirms they want the meal tracked (e.g. you analyzed a photo, gave the calories, and they said yes). Pass items with name, calories and macros, and optional meal_type (breakfast|lunch|dinner|snack).';
    }

    /**
     * @return array<string, mixed>
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'items' => $schema->array()->items(
                $schema->object([
                    'name' => $schema->string()->required(),
                    'calories' => $schema->number()->description('kcal for the eaten portion')->required(),
                    'protein_g' => $schema->number(),
                    'carbs_g' => $schema->number(),
                    'fat_g' => $schema->number(),
                ])
            )->description('The foods to log for today.')->required(),
            'meal_type' => $schema->string()->description('breakfast, lunch, dinner or snack.'),
        ];
    }

    public function handle(Request $request): Stringable|string
    {
        $items = $request['items'] ?? [];

        if (! is_array($items) || $items === []) {
            return ToolResult::error('no_items', 'There are no foods to log — ask the user what they ate.');
        }

        $mealType = MealType::tryFrom(strtolower((string) ($request['meal_type'] ?? '')));

        $totalKcal = 0.0;

        foreach ($items as $item) {
            $calories = (float) ($item['calories'] ?? 0);

            $this->user->calorieTrackings()->create([
                'tracked_date' => today(),
                'meal_type' => $mealType,
                'meal_name' => trim((string) ($item['name'] ?? '')) ?: null,
                'calories' => $calories,
                'protein_g' => isset($item['protein_g']) ? (float) $item['protein_g'] : null,
                'carbs_g' => isset($item['carbs_g']) ? (float) $item['carbs_g'] : null,
                'fat_g' => isset($item['fat_g']) ? (float) $item['fat_g'] : null,
            ]);

            $totalKcal += $calories;
        }

        return json_encode([
            'logged' => true,
            'item_count' => count($items),
            'total_kcal' => (int) round($totalKcal),
        ]);
    }
}
