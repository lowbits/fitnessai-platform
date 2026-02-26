<?php

namespace App\Console\Commands;

use App\Actions\RetryPlanGeneration;
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
                            {--status=failed : Comma-separated statuses to retry (e.g. "failed,pending")}
                            {--reset : Reset status to pending without re-dispatching jobs}';

    protected $description = 'Retry all failed workout and nutrition plans';

    public function handle(): int
    {
        $type = $this->option('type');

        if ($type && ! in_array($type, ['workouts', 'nutrition'])) {
            $this->error('Invalid type. Use "workouts" or "nutrition".');

            return Command::FAILURE;
        }

        $statuses = $this->getTargetStatuses();

        if (empty($statuses)) {
            return Command::FAILURE;
        }

        $this->info('Targeting statuses: '.implode(', ', $statuses));

        $workoutResults = ['found' => 0, 'plans' => 0, 'dispatched' => 0];
        $nutritionResults = ['found' => 0, 'plans' => 0, 'dispatched' => 0];

        if (! $type || $type === 'workouts') {
            $workoutResults = $this->retryWorkouts($statuses);
        }

        if (! $type || $type === 'nutrition') {
            $nutritionResults = $this->retryNutrition($statuses);
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
    /**
     * @param  string[]  $statuses
     * @return array{found: int, plans: int, dispatched: int}
     */
    private function retryWorkouts(array $statuses): array
    {
        $this->info('Looking for retryable workout plans...');

        $query = WorkoutPlan::whereIn('status', $statuses)->with('plan.user');
        $this->applyFilters($query);

        $failed = $query->get();

        if ($failed->isEmpty()) {
            $this->info('No retryable workout plans found.');

            return ['found' => 0, 'plans' => 0, 'dispatched' => 0];
        }

        $this->info("Found {$failed->count()} retryable workout day(s).");
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

            RetryPlanGeneration::workouts($plan);

            $failedCount = $failed->where('plan_id', $planId)->count();
            $this->line("  Dispatched workout job for Plan #{$planId} (User #{$plan->user->id}) - {$failedCount} day(s)");

            $dispatched++;
        }

        return ['found' => $failed->count(), 'plans' => $planIds->count(), 'dispatched' => $dispatched];
    }

    /**
     * @return array{found: int, plans: int, dispatched: int}
     */
    /**
     * @param  string[]  $statuses
     * @return array{found: int, plans: int, dispatched: int}
     */
    private function retryNutrition(array $statuses): array
    {
        $this->info('Looking for retryable nutrition plans...');

        $query = MealPlan::whereIn('status', $statuses)->with('plan.user');
        $this->applyFilters($query);

        $failed = $query->get();

        if ($failed->isEmpty()) {
            $this->info('No retryable nutrition plans found.');

            return ['found' => 0, 'plans' => 0, 'dispatched' => 0];
        }

        $this->info("Found {$failed->count()} retryable meal plan day(s).");
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

            RetryPlanGeneration::meals($plan);

            $failedCount = $failed->where('plan_id', $planId)->count();
            $this->line("  Dispatched nutrition job for Plan #{$planId} (User #{$plan->user->id}) - {$failedCount} day(s)");

            $dispatched++;
        }

        return ['found' => $failed->count(), 'plans' => $planIds->count(), 'dispatched' => $dispatched];
    }

    /**
     * @return string[]
     */
    private function getTargetStatuses(): array
    {
        $valid = ['failed', 'pending'];
        $statuses = array_map('trim', explode(',', $this->option('status')));

        $invalid = array_diff($statuses, $valid);

        if (! empty($invalid)) {
            $this->error('Invalid status: '.implode(', ', $invalid).'. Valid statuses: '.implode(', ', $valid));

            return [];
        }

        return $statuses;
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
