<?php

namespace App\Jobs;

use App\Actions\PlanMealSlotsForDay;
use App\Ai\Agents\NutritionPlannerAgent;
use App\Ai\Prompts\CreateMealPlanPrompt;
use App\Models\Meal;
use App\Models\MealPlan;
use App\Models\Plan;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Collection;
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
        $slotPlanner = new PlanMealSlotsForDay;

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

            // Clean up any partial meals from a previous failed attempt
            if ($mealPlan->meals()->exists()) {
                $mealPlan->meals()->delete();
            }

            $slotPlan = $slotPlanner->handle($this->plan, $day, $profile);

            $this->logSlotPlan($day, $slotPlan);

            try {
                $this->generateDay($mealPlan, $day, $date, $slotPlan);
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
     * @param  array<string, array{action: 'new'|'repeat', forbidden_meals?: Collection, repeat_from?: Meal}>  $slotPlan
     */
    private function generateDay(MealPlan $mealPlan, int $day, Carbon $date, array $slotPlan): void
    {
        $hasNewSlots = collect($slotPlan)->contains(fn ($s) => $s['action'] === 'new');

        if ($hasNewSlots) {
            $prompt = new CreateMealPlanPrompt(
                profile: $this->user->profile,
                locale: $this->user->locale,
                dayNumber: $day,
                date: $date,
                bodyGoal: $this->user->profile->body_goal->value,
                slotPlan: $slotPlan,
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

            Log::info("AI generation finished for day {$day}", [
                'meal_plan_id' => $mealPlan->id,
                'meals_created' => $mealPlan->meals()->count(),
                'duration_seconds' => round($duration, 2),
            ]);
        }

        $this->insertRepeatedMeals($mealPlan, $slotPlan);
        $this->finalizeMealPlan($mealPlan, $day);
    }

    /**
     * For each REPEAT slot, duplicate the chosen prior Meal record exactly.
     * Same name, ingredients, grams, macros — no AI regeneration. Any meal of
     * the same type the AI happened to generate is removed first.
     *
     * @param  array<string, array{action: 'new'|'repeat', forbidden_meals?: Collection, repeat_from?: Meal}>  $slotPlan
     */
    private function insertRepeatedMeals(MealPlan $mealPlan, array $slotPlan): void
    {
        foreach ($slotPlan as $type => $plan) {
            if ($plan['action'] !== 'repeat' || ! isset($plan['repeat_from'])) {
                continue;
            }

            $mealPlan->meals()->where('type', $type)->delete();

            $source = $plan['repeat_from'];

            Meal::create([
                'meal_plan_id' => $mealPlan->id,
                'type' => $source->type,
                'name' => $source->name,
                'description' => $source->description,
                'calories' => $source->calories,
                'protein_g' => $source->protein_g,
                'carbs_g' => $source->carbs_g,
                'fat_g' => $source->fat_g,
                'fiber_g' => $source->fiber_g,
                'sugar_g' => $source->sugar_g,
                'ingredients' => $source->ingredients,
                'instructions' => $source->instructions,
                'prep_time_minutes' => $source->prep_time_minutes,
                'cook_time_minutes' => $source->cook_time_minutes,
                'difficulty' => $source->difficulty,
                'servings' => $source->servings,
                'tags' => $source->tags,
                'allergens' => $source->allergens,
                'primary_protein' => $source->primary_protein,
                'cuisine' => $source->cuisine,
                'status' => 'generated',
            ]);
        }
    }

    private function finalizeMealPlan(MealPlan $mealPlan, int $day): void
    {
        $meals = $mealPlan->meals()->get();

        if ($meals->isEmpty()) {
            Log::error("Meal plan ended day {$day} with no meals", [
                'meal_plan_id' => $mealPlan->id,
            ]);
            $mealPlan->update(['status' => 'failed']);

            return;
        }

        $mealPlan->update([
            'status' => 'generated',
            'total_calories' => $meals->sum('calories'),
            'total_protein_g' => $meals->sum('protein_g'),
            'total_carbs_g' => $meals->sum('carbs_g'),
            'total_fat_g' => $meals->sum('fat_g'),
        ]);

        Log::info("Generated meal plan for day {$day}", [
            'meal_plan_id' => $mealPlan->id,
            'meals_total' => $meals->count(),
        ]);
    }

    /**
     * @param  array<string, array{action: 'new'|'repeat', forbidden_meals?: Collection, repeat_from?: Meal}>  $slotPlan
     */
    private function logSlotPlan(int $day, array $slotPlan): void
    {
        $summary = collect($slotPlan)->map(function (array $plan, string $slot) {
            if ($plan['action'] === 'repeat') {
                $repeat = $plan['repeat_from'] ?? null;

                return "{$slot}=repeat(day{$repeat?->mealPlan?->day_number}: \"{$repeat?->name}\")";
            }

            $forbidden = ($plan['forbidden_meals'] ?? collect())->count();

            return "{$slot}=new(forbidden: {$forbidden})";
        })->values()->implode(' | ');

        Log::info("[MealGen] Slot plan day {$day}: {$summary}", [
            'user_id' => $this->user->id,
            'plan_id' => $this->plan->id,
        ]);
    }
}
