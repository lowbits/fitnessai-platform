<?php

namespace App\Actions\Auth;

use App\Models\Plan;
use App\Models\User;
use Carbon\Carbon;

class ProvisionOnboardingUser
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function execute(User $user, array $data): Plan
    {
        $profile = $user->profile()->create([
            'birthdate' => Carbon::parse($data['birthdate'])->startOfDay(),
            'gender' => $data['gender'],
            'weight_kg' => $data['weight_kg'],
            'goal_weight_kg' => $data['goal_weight_kg'] ?? null,
            'height_cm' => $data['height_cm'],
            'body_goal' => $data['body_goal'],
            'skill_level' => $data['skill_level'],
            'activity_level' => $data['activity_level'],
            'training_place' => $data['training_place'],
            'training_sessions_per_week' => $data['training_sessions_per_week'],
            'training_days' => $data['training_days'] ?? null,
            'dietary_preference' => $data['dietary_preference'],
            'selected_meals' => $data['selected_meals'] ?? null,
            'food_dislikes' => $data['food_dislikes'] ?? [],
            'disliked_recipe_ids' => $data['disliked_recipes'] ?? [],
            'cooking_preference' => $data['cooking_preference'] ?? 'normal',
            'cooking_frequency' => $data['cooking_frequency'] ?? null,
            'meal_variety' => $data['meal_variety'] ?? 'medium',
            'physical_limitations' => $data['physical_limitations'] ?? [],
            'physical_limitations_note' => $data['physical_limitations_note'] ?? null,
        ]);

        $dailyCalories = $profile->calculateDailyCalories();
        $macros = $profile->calculateMacros();
        $trialDays = (int) config('subscription.trial_days');

        $plan = $user->plans()->create([
            'plan_name' => $profile->body_goal->label().' Plan',
            'start_date' => now(),
            'duration_days' => $trialDays,
            'end_date' => now()->addDays($trialDays),
            'daily_calories' => $dailyCalories,
            'daily_protein_g' => $macros->proteinGrams,
            'daily_carbs_g' => $macros->carbsGrams,
            'daily_fat_g' => $macros->fatGrams,
            'workouts_per_week' => $data['training_sessions_per_week'],
        ]);

        if (! empty($data['favorite_recipes'])) {
            $user->favoriteRecipes()->attach(array_values(array_unique($data['favorite_recipes'])));
        }

        return $plan;
    }
}
