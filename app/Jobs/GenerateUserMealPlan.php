<?php

namespace App\Jobs;

use App\Models\MealPlan;
use App\Models\Plan;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class GenerateUserMealPlan implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public User $user,
        public Plan $plan
    ) {
        $this->onQueue('nutrition');
    }

    public function handle(): void
    {
        $this->user->load('profile');
        $profile = $this->user->profile;

        if (!$profile) {
            Log::error('User profile not found', ['user_id' => $this->user->id]);
            return;
        }

        // Find the earliest failed day that needs retry
        $firstFailedDay = MealPlan::where('plan_id', $this->plan->id)
            ->where('status', 'failed')
            ->min('day_number');

        $lastGeneratedDayNumber = MealPlan::where('plan_id', $this->plan->id)
            ->where('status', 'generated')
            ->max('day_number') ?? 0;

        if ($lastGeneratedDayNumber >= $this->plan->duration_days && !$firstFailedDay) {
            Log::info('Meal plan already complete, skipping generation', [
                'plan_id' => $this->plan->id,
            ]);
            return;
        }

        // Start from the earliest failed day, or after the last generated day
        $startDayNumber = $firstFailedDay
            ? min($firstFailedDay, $lastGeneratedDayNumber + 1)
            : $lastGeneratedDayNumber + 1;

        // Dispatch batches of 2-3 days instead of processing all at once
        $this->dispatchBatches($startDayNumber);
    }

    /**
     * Dispatch job batches for meal plan generation
     * Each batch handles 2-3 days for better parallelization and error handling
     */
    private function dispatchBatches(int $startDayNumber): void
    {
        $batchSize = 3; // Generate 3 days per batch
        $daysToGenerate = 7;
        $totalDays = min($startDayNumber + $daysToGenerate - 1, $this->plan->duration_days);

        $batches = [];

        for ($day = $startDayNumber; $day <= $totalDays; $day += $batchSize) {
            $endDay = min($day + $batchSize - 1, $totalDays);

            $batches[] = new GenerateMealPlanBatch(
                $this->user,
                $this->plan,
                $day,
                $endDay
            );
        }

        if (empty($batches)) {
            Log::info('No meal plan batches to generate', [
                'plan_id' => $this->plan->id,
                'start_day_number' => $startDayNumber,
            ]);
            return;
        }

        Log::info('Dispatching meal plan generation batches', [
            'plan_id' => $this->plan->id,
            'total_batches' => count($batches),
            'days_range' => "{$startDayNumber}-{$totalDays}",
        ]);

        // Dispatch all batches
        foreach ($batches as $batch) {
            dispatch($batch);
        }
    }
}

