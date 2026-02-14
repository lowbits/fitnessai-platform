<?php

namespace App\Console\Commands;

use App\Jobs\GenerateUserMealPlan;
use App\Jobs\GenerateUserWorkoutPlan;
use App\Models\MealPlan;
use App\Models\Plan;
use App\Models\WorkoutPlan;
use Illuminate\Console\Command;

class RetryFailedNutritionPlans extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'nutrition:retry-failed
                            {--plan= : Specific plan ID to retry}
                            {--user= : Specific user ID to retry}
                            {--reset : Reset status to pending without re-dispatching job}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Retry generating failed nutrition plans';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Looking for failed nutrition plans...');

        $query = MealPlan::where('status', 'failed')
            ->with('plan.user');

        // Filter by plan ID if specified
        if ($planId = $this->option('plan')) {
            $query->where('plan_id', $planId);
        }

        // Filter by user ID if specified
        if ($userId = $this->option('user')) {
            $query->whereHas('plan', function ($q) use ($userId) {
                $q->where('user_id', $userId);
            });
        }

        $failedMealPlans = $query->get();

        if ($failedMealPlans->isEmpty()) {
            $this->info('No failed workout plans found.');
            return Command::SUCCESS;
        }

        $this->info("Found {$failedMealPlans->count()} failed meal plan(s).");

        if ($this->option('reset')) {
            // Just reset status to pending without re-dispatching
            $this->info('Resetting status to pending...');

            foreach ($failedMealPlans as $plan) {
                $plan->update(['status' => 'pending']);
                $this->line("  - MealPlan #{$plan->id} (Plan #{$plan->plan_id}, Day {$plan->day_number}) reset to pending");
            }

            $this->info('Status reset complete. Run workout generation job to retry.');
            return Command::SUCCESS;
        }

        // Group by plan to avoid duplicate job dispatches
        $planIds = $failedMealPlans->pluck('plan_id')->unique();

        $this->info("Plans affected: {$planIds->count()}");

        if (!$this->confirm('Do you want to retry generating these meal plans?', true)) {
            $this->info('Operation cancelled.');
            return Command::SUCCESS;
        }

        $jobsDispatched = 0;

        foreach ($planIds as $planId) {
            $plan = Plan::with('user')->find($planId);

            if (!$plan) {
                $this->error("Plan #{$planId} not found, skipping.");
                continue;
            }

            if (!$plan->user) {
                $this->error("User for Plan #{$planId} not found, skipping.");
                continue;
            }

            // Reset all failed workouts for this plan to pending
            MealPlan::where('plan_id', $planId)
                ->where('status', 'failed')
                ->update(['status' => 'pending']);

            // Dispatch the generation job
            GenerateUserMealPlan::dispatch($plan->user, $plan);

            $failedCount = $failedMealPlans->where('plan_id', $planId)->count();
            $this->line("  ✓ Dispatched job for Plan #{$planId} (User #{$plan->user->id}) - {$failedCount} failed workout(s)");

            $jobsDispatched++;
        }

        $this->newLine();
        $this->info("Successfully dispatched {$jobsDispatched} generation job(s).");
        $this->info('Jobs are queued and will be processed by your queue worker.');

        return Command::SUCCESS;
    }
}

