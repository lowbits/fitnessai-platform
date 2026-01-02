<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Notifications\MealReminderNotification;
use Illuminate\Console\Command;

class SendMealReminders extends Command
{
    protected $signature = 'notifications:meal-reminders';
    protected $description = 'Send ONE daily meal reminder to users with active plans';

    public function handle(): int
    {
        $currentHour = now()->hour;
        $this->info("Starting meal reminder notifications for hour {$currentHour}:00...");

        // Get users with active plans and devices
        $users = User::whereHas('devices')
            ->whereHas('plans', function ($query) {
                $query->where('status', 'active')
                    ->whereNotNull('generation_completed_at');
            })
            ->with(['plans' => function ($query) {
                $query->where('status', 'active')
                    ->latest()
                    ->limit(1);
            }])
            ->get();

        $sentCount = 0;
        $skippedCount = 0;

        foreach ($users as $user) {
            $plan = $user->plans->first();

            if (!$plan) {
                continue;
            }

            // Check if we should send reminder now for this user
            $shouldSendNow = $this->shouldSendReminderNow($user, $currentHour);

            if (!$shouldSendNow) {
                $skippedCount++;
                continue;
            }

            // Get today's meal plan
            $todayMealPlan = $plan->mealPlans()
                ->whereDate('date', today())
                ->where('status', 'generated')
                ->first();

            if (!$todayMealPlan) {
                continue;
            }

            // Get the most important meal (lunch as default)
            $meal = $todayMealPlan->meals()
                ->where('meal_type', 'lunch')
                ->first();

            if (!$meal) {
                // Fallback to any available meal
                $meal = $todayMealPlan->meals()->first();
            }

            if (!$meal) {
                continue;
            }

            $user->notify(new MealReminderNotification(
                $meal->meal_type,
                $meal->name,
                $meal->id
            ));

            $sentCount++;
            $this->line("✅ Meal reminder sent to user {$user->id} ({$user->email}) - {$meal->meal_type}: {$meal->name}");
        }

        $this->info("✅ Meal reminders sent: {$sentCount}");
        $this->info("⏭️  Skipped (wrong time): {$skippedCount}");
        $this->info('Done!');

        return Command::SUCCESS;
    }

    /**
     * Determine if reminder should be sent now for this user
     * MVP Logic:
     * - New users (first 2 weeks): Send at 12:00 (lunch time)
     * - Experienced users: Send at 12:00 (lunch is most important)
     */
    private function shouldSendReminderNow(User $user, int $currentHour): bool
    {
        // Default: Send meal reminder at 12:00 (lunch time)
        $reminderHour = 12;

        // You could personalize based on user's workout time:
        // Early workout users might want breakfast reminder
        // Late workout users might want dinner reminder
        // But for MVP: Everyone gets lunch reminder at 12:00

        if ($currentHour === $reminderHour) {
            $this->line("User {$user->id}: Sending meal reminder at 12:00");
            return true;
        }

        return false;
    }
}
