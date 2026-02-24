<?php

namespace App\Jobs;

use App\Ai\Agents\NutritionPlannerAgent;
use App\Ai\Prompts\CreateMealPlanPrompt;
use App\Models\MealPlan;
use App\Models\Plan;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Laravel\Ai\Enums\Lab;

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
        $this->user->load('profile');
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

            try {
                $prompt = new CreateMealPlanPrompt(
                    profile: $profile,
                    locale: $this->user->locale,
                    dayNumber: $day,
                    date: $date,
                    bodyGoal: $profile->body_goal->value,
                );

                Log::debug("Calling NutritionPlannerAgent for day {$day}");

                $startTime = microtime(true);

                (new NutritionPlannerAgent($mealPlan))
                    ->prompt((string) $prompt, provider: [Lab::OpenAI, Lab::Mistral], model: 'gpt-5-mini');

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
}
