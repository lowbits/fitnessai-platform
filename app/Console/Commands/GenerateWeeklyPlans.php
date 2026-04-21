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
    protected $signature = 'plans:generate-weekly
                            {--force : Generate for all users regardless of schedule}
                            {--email= : Generate for a specific user by email}
                            {--only= : Generate only "workouts" or "meals"}';

    protected $description = 'Generate next 7 days of plans for users based on their individual schedule';

    public function handle(): int
    {
        $this->info('Starting weekly plan generation...');
        $force = $this->option('force');
        $email = $this->option('email');
        $only = $this->option('only');

        if ($only && ! in_array($only, ['workouts', 'meals'])) {
            $this->error('--only must be "workouts" or "meals"');

            return Command::FAILURE;
        }

        // If email is provided, generate only for that specific user
        if ($email) {
            return $this->generateForSpecificUser($email, $force, $only);
        }

        // Get users with active subscriptions and active plans
        $users = User::where(function ($query) {
            $query->whereHas('subscriptions', function ($q) {
                $q->whereNull('current_period_ended_at')
                    ->orWhere('current_period_ended_at', '>', now());
            })->orWhereHas('legacySubscription', function ($q) {
                $q->active();
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

            if (! $plan) {
                $skipped++;

                continue;
            }

            // Check if today is this user's generation day
            if (! $force && ! $this->isUserGenerationDay($plan)) {
                $this->line("⏭️  Skipped user {$user->id} ({$user->email}) - not their generation day");
                $skipped++;

                continue;
            }

            // Check if we need to generate for this user
            if (! $this->shouldGenerateForUser($plan)) {
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
                if (! $only || $only === 'workouts') {
                    GenerateUserWorkoutPlan::dispatch($user, $plan);
                }
                if (! $only || $only === 'meals') {
                    GenerateUserMealPlan::dispatch($user, $plan);
                }

                $daysToGenerate = $startDate->diffInDays($endDate) + 1;
                $generationDay = $this->getUserGenerationDayName($plan);
                $type = $only ? $only : 'workouts + meals';

                $this->info("✅ Queued generation ({$type}) for user {$user->id} ({$user->email})");
                $this->line("   Generation Day: {$generationDay}");
                $this->line("   Start: {$startDate->format('Y-m-d')} | End: {$endDate->format('Y-m-d')} | Days: {$daysToGenerate}");

                // Send notification at 08:00 AM for push, email immediate
                $notificationTime = now()->setHour(8)->setMinute(0)->setSecond(0);

                // If it's already past 08:00, send tomorrow at 08:00
                if (now()->hour >= 8) {
                    $notificationTime = $notificationTime->addDay();
                }

                $delay = now()->diffInSeconds($notificationTime);

                // Send single notification with both channels
                // - Email: sent immediately (professional, minimal emojis)
                // - Push: delayed until 08:00 AM
                $user->notify(
                    (new WeeklyPlansGeneratedNotification(
                        $startDate->format('Y-m-d'),
                        $endDate->format('Y-m-d')
                    ))->delay($delay)
                );

                $this->line("   📱 Push notification scheduled for: {$notificationTime->format('Y-m-d H:i')}");
                $this->line('   📧 Email sent immediately');

                $generated++;
            } catch (\Exception $e) {
                $this->error("❌ Failed for user {$user->id}: {$e->getMessage()}");
                $failed++;
            }
        }

        $this->newLine();
        $this->info('Summary:');
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
     * Generate plans for a specific user by email
     */
    private function generateForSpecificUser(string $email, bool $force, ?string $only = null): int
    {
        $this->info("Generating plans for user: {$email}");

        $user = User::where('email', $email)
            ->with(['plans' => function ($query) {
                $query->where('status', 'active')
                    ->latest()
                    ->limit(1);
            }])
            ->first();

        if (! $user) {
            $this->error("❌ User not found: {$email}");

            return Command::FAILURE;
        }

        $plan = $user->plans->first();

        if (! $plan) {
            $this->error("❌ User {$email} has no active plan");

            return Command::FAILURE;
        }

        // Check if user has active subscription (optional - can generate even without)
        $hasActiveSubscription = ($user->hasActiveLegacySubscription() || $user->hasActiveSubscription());

        if (! $hasActiveSubscription && ! $force) {
            $this->warn("⚠️  User {$email} has no active subscription. Use --force to generate anyway.");

            if (! $this->confirm('Generate anyway?', false)) {
                return Command::FAILURE;
            }
        }

        // Check if today is generation day (can be bypassed with --force)
        if (! $force && ! $this->isUserGenerationDay($plan)) {
            $generationDay = $this->getUserGenerationDayName($plan);
            $this->warn("⚠️  Today is not {$email}'s generation day (scheduled for {$generationDay})");

            if (! $this->confirm('Generate anyway?', false)) {
                return Command::FAILURE;
            }
        }

        // Check if already has enough plans
        if (! $force && ! $this->shouldGenerateForUser($plan)) {
            $this->warn("⚠️  User {$email} already has plans for next week");

            if (! $this->confirm('Generate anyway?', false)) {
                return Command::FAILURE;
            }
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

            $daysToGenerate = $startDate->diffInDays($endDate) + 1;

            $this->info("\n📋 Generation Details:");
            $this->table(
                ['Property', 'Value'],
                [
                    ['User', "{$user->name} ({$user->email})"],
                    ['Plan ID', $plan->id],
                    ['Plan Start', $plan->start_date->format('Y-m-d')],
                    ['Plan End', $plan->end_date->format('Y-m-d')],
                    ['Generation Day', $this->getUserGenerationDayName($plan)],
                    ['Start Date', $startDate->format('Y-m-d')],
                    ['End Date', $endDate->format('Y-m-d')],
                    ['Days to Generate', $daysToGenerate],
                    ['Has Subscription', $hasActiveSubscription ? 'Yes' : 'No'],
                ]
            );

            // Dispatch jobs to generate next 7 days
            if (! $only || $only === 'workouts') {
                GenerateUserWorkoutPlan::dispatch($user, $plan);
                $this->line('   🏋️  Workout plans queued');
            }
            if (! $only || $only === 'meals') {
                GenerateUserMealPlan::dispatch($user, $plan);
                $this->line('   🍽️  Meal plans queued');
            }

            $type = $only ? $only : 'workouts + meals';
            $this->info("\n✅ Generation jobs dispatched ({$type})!");

            // Send notification
            $notificationTime = now()->setHour(8)->setMinute(0)->setSecond(0);
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

            $this->line("\n📬 Notifications:");
            $this->line('   📧 Email sent immediately');
            $this->line("   📱 Push notification scheduled for: {$notificationTime->format('Y-m-d H:i')}");

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error("\n❌ Failed to generate plans: {$e->getMessage()}");
            $this->error($e->getTraceAsString());

            return Command::FAILURE;
        }
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

        if (! $lastWorkoutDate) {
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
