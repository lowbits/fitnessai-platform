<?php

namespace App\Console\Commands;

use App\Jobs\GenerateUserMealPlan;
use App\Jobs\GenerateUserWorkoutPlan;
use App\Models\MealPlan;
use App\Models\Plan;
use App\Models\WorkoutPlan;
use Illuminate\Console\Command;

class RetryAllFailedPlans extends Command
{
    protected $signature = 'plans:retry-failed
                            {--plan= : Specific plan ID to retry}
                            {--user= : Specific user ID to retry}
                            {--type= : Only retry "workouts" or "nutrition"}
                            {--reset : Reset status to pending without re-dispatching jobs}';

    protected $description = 'Retry all failed workout and nutrition plans';

    public function handle(): int
    {
        $type = $this->option('type');

        if ($type && ! in_array($type, ['workouts', 'nutrition'])) {
            $this->error('Invalid type. Use "workouts" or "nutrition".');

            return Command::FAILURE;
        }

        $workoutResults = ['found' => 0, 'plans' => 0, 'dispatched' => 0];
        $nutritionResults = ['found' => 0, 'plans' => 0, 'dispatched' => 0];

        if (! $type || $type === 'workouts') {
            $workoutResults = $this->retryWorkouts();
        }

        if (! $type || $type === 'nutrition') {
            $nutritionResults = $this->retryNutrition();
        }

        $this->newLine();
        $this->info('Summary:');
        $this->table(
            ['Type', 'Failed Days', 'Plans Affected', 'Jobs Dispatched'],
            [
                ['Workouts', $workoutResults['found'], $workoutResults['plans'], $workoutResults['dispatched']],
                ['Nutrition', $nutritionResults['found'], $nutritionResults['plans'], $nutritionResults['dispatched']],
            ]
        );

        return Command::SUCCESS;
    }

    /**
     * @return array{found: int, plans: int, dispatched: int}
     */
    private function retryWorkouts(): array
    {
        $this->info('Looking for failed workout plans...');

        $query = WorkoutPlan::where('status', 'failed')->with('plan.user');
        $this->applyFilters($query);

        $failed = $query->get();

        if ($failed->isEmpty()) {
            $this->info('No failed workout plans found.');

            return ['found' => 0, 'plans' => 0, 'dispatched' => 0];
        }

        $this->info("Found {$failed->count()} failed workout day(s).");
        $planIds = $failed->pluck('plan_id')->unique();

        if ($this->option('reset')) {
            foreach ($failed as $workout) {
                $workout->update(['status' => 'pending']);
                $this->line("  - WorkoutPlan #{$workout->id} (Plan #{$workout->plan_id}, Day {$workout->day_number}) reset to pending");
            }

            return ['found' => $failed->count(), 'plans' => $planIds->count(), 'dispatched' => 0];
        }

        $dispatched = 0;

        foreach ($planIds as $planId) {
            $plan = Plan::with('user')->find($planId);

            if (! $plan?->user) {
                $this->error("Plan #{$planId} or its user not found, skipping.");

                continue;
            }

            WorkoutPlan::where('plan_id', $planId)
                ->where('status', 'failed')
                ->update(['status' => 'pending']);

            GenerateUserWorkoutPlan::dispatch($plan->user, $plan);

            $failedCount = $failed->where('plan_id', $planId)->count();
            $this->line("  Dispatched workout job for Plan #{$planId} (User #{$plan->user->id}) - {$failedCount} failed day(s)");

            $dispatched++;
        }

        return ['found' => $failed->count(), 'plans' => $planIds->count(), 'dispatched' => $dispatched];
    }

    /**
     * @return array{found: int, plans: int, dispatched: int}
     */
    private function retryNutrition(): array
    {
        $this->info('Looking for failed nutrition plans...');

        $query = MealPlan::where('status', 'failed')->with('plan.user');
        $this->applyFilters($query);

        $failed = $query->get();

        if ($failed->isEmpty()) {
            $this->info('No failed nutrition plans found.');

            return ['found' => 0, 'plans' => 0, 'dispatched' => 0];
        }

        $this->info("Found {$failed->count()} failed meal plan day(s).");
        $planIds = $failed->pluck('plan_id')->unique();

        if ($this->option('reset')) {
            foreach ($failed as $mealPlan) {
                $mealPlan->update(['status' => 'pending']);
                $this->line("  - MealPlan #{$mealPlan->id} (Plan #{$mealPlan->plan_id}, Day {$mealPlan->day_number}) reset to pending");
            }

            return ['found' => $failed->count(), 'plans' => $planIds->count(), 'dispatched' => 0];
        }

        $dispatched = 0;

        foreach ($planIds as $planId) {
            $plan = Plan::with('user')->find($planId);

            if (! $plan?->user) {
                $this->error("Plan #{$planId} or its user not found, skipping.");

                continue;
            }

            MealPlan::where('plan_id', $planId)
                ->where('status', 'failed')
                ->update(['status' => 'pending']);

            GenerateUserMealPlan::dispatch($plan->user, $plan);

            $failedCount = $failed->where('plan_id', $planId)->count();
            $this->line("  Dispatched nutrition job for Plan #{$planId} (User #{$plan->user->id}) - {$failedCount} failed day(s)");

            $dispatched++;
        }

        return ['found' => $failed->count(), 'plans' => $planIds->count(), 'dispatched' => $dispatched];
    }

    private function applyFilters($query): void
    {
        if ($planId = $this->option('plan')) {
            $query->where('plan_id', $planId);
        }

        if ($userId = $this->option('user')) {
            $query->whereHas('plan', function ($q) use ($userId) {
                $q->where('user_id', $userId);
            });
        }
    }
}
