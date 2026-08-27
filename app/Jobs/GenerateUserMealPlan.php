<?php

namespace App\Jobs;

use App\Ai\Consent\AiConsentBouncer;
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
        public Plan $plan,
        public ?int $maxDays = null,
    ) {
        $this->onQueue('nutrition');
    }

    public function handle(): void
    {
        if (! AiConsentBouncer::permits($this->user)) {
            Log::info('[MealGen] AI consent missing, aborting', ['user_id' => $this->user->id]);

            return;
        }

        $this->user->load('profile');
        $profile = $this->user->profile;

        if (! $profile) {
            Log::error('User profile not found', ['user_id' => $this->user->id]);

            return;
        }

        // Calculate today's day number within the plan
        $todayDayNumber = max(1, (int) $this->plan->start_date->diffInDays(now()->startOfDay()) + 1);
        $todayDayNumber = min($todayDayNumber, $this->plan->duration_days);

        $generatedDays = MealPlan::where('plan_id', $this->plan->id)
            ->where('status', 'generated')
            ->where('day_number', '>=', $todayDayNumber)
            ->pluck('day_number')
            ->toArray();

        // Find the first day from today onwards that is not yet generated
        $startDayNumber = null;
        for ($day = $todayDayNumber; $day <= $this->plan->duration_days; $day++) {
            if (! in_array($day, $generatedDays)) {
                $startDayNumber = $day;
                break;
            }
        }

        // All days from today onwards are generated
        if (! $startDayNumber) {
            Log::info('Meal plan already complete, skipping generation', [
                'plan_id' => $this->plan->id,
            ]);

            return;
        }

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
        $daysToGenerate = $this->maxDays ?? 7;
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
