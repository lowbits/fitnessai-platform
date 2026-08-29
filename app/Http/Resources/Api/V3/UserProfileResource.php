<?php

namespace App\Http\Resources\Api\V3;

use App\Models\UserProfile;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin UserProfile */
class UserProfileResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            // Body
            'birthdate' => $this->birthdate?->format('Y-m-d'),
            'age' => $this->age,
            'gender' => $this->gender?->value,
            'start_weight_kg' => (float) $this->weight_kg,
            'weight_kg' => $this->user?->getCurrentWeight(),
            'height_cm' => (int) $this->height_cm,

            // Goal & training
            'goal_weight_kg' => $this->goal_weight_kg !== null ? (float) $this->goal_weight_kg : null,
            'body_goal' => $this->body_goal?->value,
            'skill_level' => $this->skill_level?->value,
            'activity_level' => $this->activity_level?->value,
            'training_place' => $this->training_place?->value,
            'training_sessions_per_week' => $this->training_sessions_per_week,
            'training_days' => $this->training_days ?? [],

            // Diet
            'dietary_preference' => $this->dietary_preference?->value,
            'diet_style' => $this->diet_style?->value,

            // Meal preferences
            'selected_meals' => $this->selected_meals ?? [],
            'food_dislikes' => $this->food_dislikes ?? [],
            'disliked_recipe_ids' => $this->disliked_recipe_ids ?? [],
            'cooking_preference' => $this->cooking_preference?->value,
            'cooking_frequency' => $this->cooking_frequency?->value,
            'meal_variety' => $this->meal_variety?->value,
            'auto_fill_calories' => (bool) $this->auto_fill_calories,

            // Limitations
            'physical_limitations' => $this->physical_limitations ?? [],
            'physical_limitations_note' => $this->physical_limitations_note,
        ];
    }
}
