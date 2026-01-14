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
        $profile = $this->user->profile;

        if (!$profile) {
            Log::error('User profile not found', ['user_id' => $this->user->id]);
            return;
        }

        $lastGeneratedDayNumber = MealPlan::where('plan_id', $this->plan->id)
            ->where('status', 'generated')
            ->max('day_number') ?? 0;

        if ($lastGeneratedDayNumber >= $this->plan->duration_days) {
            Log::info('Meal plan already complete, skipping generation', [
                'plan_id' => $this->plan->id,
            ]);
            return;
        }

        // Dispatch batches of 2-3 days instead of processing all at once
        $this->dispatchBatches($lastGeneratedDayNumber);
    }

    /**
     * Dispatch job batches for meal plan generation
     * Each batch handles 2-3 days for better parallelization and error handling
     */
    private function dispatchBatches(int $lastGeneratedDayNumber): void
    {
        $batchSize = 3; // Generate 3 days per batch
        $startDayNumber = $lastGeneratedDayNumber + 1;
        $totalDays = $this->plan->duration_days;

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
                'last_generated_day' => $lastGeneratedDayNumber,
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

