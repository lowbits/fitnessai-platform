<?php

namespace App\Jobs;

use App\Ai\Agents\NutritionPlannerAgent;
use App\Ai\Prompts\CreateMealPlanPrompt;
use App\Enums\MealVariety;
use App\Models\Meal;
use App\Models\MealPlan;
use App\Models\Plan;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Laravel\Ai\Enums\Lab;
use Laravel\Ai\Messages\UserMessage;

/**
 * Job that generates meal plans for a specific range of days (2-3 days)
 * Part of the batched meal plan generation system
 */
class GenerateMealPlanBatch implements ShouldQueue
{
    use Queueable;

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
        $this->user->load(['profile', 'favoriteRecipes']);
        $profile = $this->user->profile;

        if (! $profile) {
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

            // For low-variety / meal-prep users: copy previous day's meals instead of regenerating
            if ($this->canCopyPreviousDay($day, $profile)) {
                $this->copyMealsFromPreviousDay($mealPlan, $day);

                continue;
            }

            // Clean up any partial meals from a previous failed attempt
            if ($mealPlan->meals()->exists()) {
                $mealPlan->meals()->delete();
            }

            try {
                $prompt = new CreateMealPlanPrompt(
                    profile: $profile,
                    locale: $this->user->locale,
                    dayNumber: $day,
                    date: $date,
                    bodyGoal: $profile->body_goal->value,
                );

                $agent = new NutritionPlannerAgent($mealPlan);
                $messages = collect($agent->messages());

                Log::debug('[MealGen] System prompt', [
                    'user_id' => $this->user->id,
                    'system' => $agent->instructions(),
                ]);

                Log::debug("[MealGen] Conversation history for day {$day}", [
                    'user_id' => $this->user->id,
                    'history_messages' => $messages->map(fn ($m) => [
                        'role' => $m instanceof UserMessage ? 'user' : 'assistant',
                        'content' => $m->content,
                    ])->all(),
                ]);

                Log::debug("[MealGen] User prompt for day {$day}", [
                    'user_id' => $this->user->id,
                    'prompt' => (string) $prompt,
                ]);

                $startTime = microtime(true);

                $agent->prompt((string) $prompt, provider: [Lab::OpenAI, Lab::Mistral], model: config('ai.models.agent'));

                $duration = microtime(true) - $startTime;

                $mealPlan->refresh();

                if ($mealPlan->status === 'generated') {
                    Log::info("Generated meal plan for day {$day}", [
                        'meal_plan_id' => $mealPlan->id,
                        'meals_created' => $mealPlan->meals()->count(),
                        'duration_seconds' => round($duration, 2),
                    ]);
                } else {
                    Log::error("Meal plan agent did not save for day {$day}", [
                        'meal_plan_id' => $mealPlan->id,
                        'status' => $mealPlan->status,
                    ]);
                    $mealPlan->update(['status' => 'failed']);
                }
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

    /**
     * Check if this day can reuse the previous day's meals.
     * Only for low-variety + meal-prep users, and not on the first day or prep days.
     */
    private function canCopyPreviousDay(int $day, $profile): bool
    {
        if ($day <= 1) {
            return false;
        }

        $variety = $profile->meal_variety ?? MealVariety::MEDIUM;
        $mealPrep = $profile->meal_prep_enabled ?? false;

        // Only low variety + meal prep users get copied days
        if ($variety !== MealVariety::LOW || ! $mealPrep) {
            return false;
        }

        // Don't copy on prep days (Sunday or day 1) — generate fresh
        $date = $this->plan->start_date->copy()->addDays($day - 1);
        if ($date->isSunday()) {
            return false;
        }

        // Check that the previous day was actually generated
        $previousDay = MealPlan::where('plan_id', $this->plan->id)
            ->where('day_number', $day - 1)
            ->where('status', 'generated')
            ->exists();

        return $previousDay;
    }

    /**
     * Copy meals from the previous day to this day's meal plan.
     * Saves an LLM call for low-variety meal-prep users.
     */
    private function copyMealsFromPreviousDay(MealPlan $mealPlan, int $day): void
    {
        $previousMealPlan = MealPlan::where('plan_id', $this->plan->id)
            ->where('day_number', $day - 1)
            ->where('status', 'generated')
            ->with('meals')
            ->first();

        if (! $previousMealPlan || $previousMealPlan->meals->isEmpty()) {
            return;
        }

        // Clean up any partial meals
        $mealPlan->meals()->delete();

        foreach ($previousMealPlan->meals as $meal) {
            Meal::create([
                'meal_plan_id' => $mealPlan->id,
                'type' => $meal->type,
                'name' => $meal->name,
                'description' => $meal->description,
                'calories' => $meal->calories,
                'protein_g' => $meal->protein_g,
                'carbs_g' => $meal->carbs_g,
                'fat_g' => $meal->fat_g,
                'fiber_g' => $meal->fiber_g,
                'sugar_g' => $meal->sugar_g,
                'ingredients' => $meal->ingredients,
                'instructions' => $meal->instructions,
                'prep_time_minutes' => $meal->prep_time_minutes,
                'cook_time_minutes' => $meal->cook_time_minutes,
                'difficulty' => $meal->difficulty,
                'servings' => $meal->servings,
                'tags' => $meal->tags,
                'allergens' => $meal->allergens,
                'primary_protein' => $meal->primary_protein,
                'cuisine' => $meal->cuisine,
                'status' => 'generated',
            ]);
        }

        $mealPlan->update([
            'status' => 'generated',
            'total_calories' => $previousMealPlan->total_calories,
            'total_protein_g' => $previousMealPlan->total_protein_g,
            'total_carbs_g' => $previousMealPlan->total_carbs_g,
            'total_fat_g' => $previousMealPlan->total_fat_g,
        ]);

        Log::info("[MealGen] Copied day {$day} from day ".($day - 1).' (low variety + meal prep)', [
            'user_id' => $this->user->id,
            'meal_plan_id' => $mealPlan->id,
            'meals_copied' => $previousMealPlan->meals->count(),
        ]);
    }
}
