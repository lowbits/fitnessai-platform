<?php

namespace App\Console\Commands;

use App\Jobs\GenerateUserMealPlan;
use App\Jobs\GenerateUserWorkoutPlan;
use App\Models\User;
use App\Notifications\WeeklyPlansGeneratedNotification;
use Carbon\Carbon;
use Illuminate\Console\Command;

class GenerateWeeklyPlans extends Command
{
    protected $signature = 'plans:generate-weekly {--force : Generate for all users regardless of schedule}';
    protected $description = 'Generate next 7 days of plans for users based on their individual schedule';

    public function handle(): int
    {
        $this->info('Starting weekly plan generation...');
        $force = $this->option('force');

        // Get users with active subscriptions and active plans
        $users = User::whereHas('subscription', function ($query) {
                $query->where('status', 'active')
                    ->where(function ($q) {
                        $q->whereNull('ends_at')
                            ->orWhere('ends_at', '>', now());
                    });
            })
            ->whereHas('plans', function ($query) {
                $query->where('status', 'active');
            })
            ->with(['plans' => function ($query) {
                $query->where('status', 'active')
                    ->latest()
                    ->limit(1);
            }])
            ->get();

        if ($users->isEmpty()) {
            $this->warn('No users with active subscriptions and plans found.');
            return Command::SUCCESS;
        }

        $this->info("Found {$users->count()} user(s) with active subscriptions");

        $generated = 0;
        $skipped = 0;
        $failed = 0;

        foreach ($users as $user) {
            $plan = $user->plans->first();

            if (!$plan) {
                $skipped++;
                continue;
            }

            // Check if today is this user's generation day
            if (!$force && !$this->isUserGenerationDay($plan)) {
                $this->line("⏭️  Skipped user {$user->id} ({$user->email}) - not their generation day");
                $skipped++;
                continue;
            }

            // Check if we need to generate for this user
            if (!$this->shouldGenerateForUser($plan)) {
                $this->line("⏭️  Skipped user {$user->id} ({$user->email}) - already has plans for next week");
                $skipped++;
                continue;
            }

            try {
                // Find the last generated date
                $lastWorkoutDate = $plan->workoutPlans()
                    ->where('status', 'generated')
                    ->max('date');

                // Start from the day after the last generated date
                $startDate = $lastWorkoutDate
                    ? Carbon::parse($lastWorkoutDate)->addDay()
                    : now()->startOfDay();

                // Ensure we don't generate beyond plan end date
                $endDate = $startDate->copy()->addDays(6); // 7 days total
                if ($endDate->gt($plan->end_date)) {
                    $endDate = $plan->end_date;
                }

                // Dispatch jobs to generate next 7 days
                GenerateUserWorkoutPlan::dispatch($user, $plan);
                GenerateUserMealPlan::dispatch($user, $plan);

                $daysToGenerate = $startDate->diffInDays($endDate) + 1;
                $generationDay = $this->getUserGenerationDayName($plan);

                $this->info("✅ Queued generation for user {$user->id} ({$user->email})");
                $this->line("   Generation Day: {$generationDay}");
                $this->line("   Start: {$startDate->format('Y-m-d')} | End: {$endDate->format('Y-m-d')} | Days: {$daysToGenerate}");

                // Send notification at 08:00 AM (not at midnight!)
                $notificationTime = now()->setHour(8)->setMinute(0)->setSecond(0);

                // If it's already past 08:00, send tomorrow at 08:00
                if (now()->hour >= 8) {
                    $notificationTime = $notificationTime->addDay();
                }

                $delay = now()->diffInSeconds($notificationTime);

                $user->notify(
                    (new WeeklyPlansGeneratedNotification(
                        $startDate->format('Y-m-d'),
                        $endDate->format('Y-m-d')
                    ))->delay($delay)
                );

                $this->line("   📱 Notification scheduled for: {$notificationTime->format('Y-m-d H:i')}");

                $generated++;
            } catch (\Exception $e) {
                $this->error("❌ Failed for user {$user->id}: {$e->getMessage()}");
                $failed++;
            }
        }

        $this->newLine();
        $this->info("Summary:");
        $this->table(
            ['Status', 'Count'],
            [
                ['Generated', $generated],
                ['Skipped', $skipped],
                ['Failed', $failed],
                ['Total', $users->count()],
            ]
        );

        return Command::SUCCESS;
    }

    /**
     * Check if today is the user's generation day (mid-week of their cycle)
     * Based on when their plan started
     */
    private function isUserGenerationDay($plan): bool
    {
        $planStartDate = Carbon::parse($plan->start_date);
        $today = now()->startOfDay();

        // Calculate which day of the week the plan started (0 = Sunday, 6 = Saturday)
        $planStartDayOfWeek = $planStartDate->dayOfWeek;

        // Calculate mid-week day (3-4 days after start day)
        // If plan started Monday (1), mid-week = Thursday (4)
        // If plan started Wednesday (3), mid-week = Saturday (6)
        $midWeekDay = ($planStartDayOfWeek + 3) % 7;

        // Check if today matches the mid-week day
        $todayDayOfWeek = $today->dayOfWeek;

        return $todayDayOfWeek === $midWeekDay;
    }

    /**
     * Get the name of the generation day for this user
     */
    private function getUserGenerationDayName($plan): string
    {
        $planStartDate = Carbon::parse($plan->start_date);
        $planStartDayOfWeek = $planStartDate->dayOfWeek;
        $midWeekDay = ($planStartDayOfWeek + 3) % 7;

        $days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
        return $days[$midWeekDay] ?? 'Unknown';
    }

    /**
     * Check if we should generate plans for this user
     * Returns true if user doesn't have enough future plans
     */
    private function shouldGenerateForUser($plan): bool
    {
        // Get the latest workout plan date
        $lastWorkoutDate = $plan->workoutPlans()
            ->where('status', 'generated')
            ->max('date');

        if (!$lastWorkoutDate) {
            // No workouts generated yet, definitely generate
            return true;
        }

        $lastDate = Carbon::parse($lastWorkoutDate);
        $today = now()->startOfDay();

        // If last generated date is less than 7 days in the future, generate more
        $daysInFuture = $today->diffInDays($lastDate, false);

        return $daysInFuture < 7;
    }
}

