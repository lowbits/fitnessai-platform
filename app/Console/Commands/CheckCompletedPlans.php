<?php

namespace App\Console\Commands;

use App\Models\Plan;
use App\Notifications\PlanGenerationComplete;
use App\Notifications\PlanReadyForDelivery;
use App\Services\AppPromoNotificationService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;

class CheckCompletedPlans extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'plans:check-completed
                            {--dry-run : Show what would be notified without sending}';

    /**
     * The console command description.
     */
    protected $description = 'Check for completed plan generations and notify users';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Checking for completed plans...');

        // Find plans that are active, haven't been notified yet, and user has verified email
        $plans = Plan::with(['user', 'mealPlans', 'workoutPlans'])
            ->where('status', 'active')
            ->whereNull('generation_completed_at')
            ->whereHas('user', function ($query) {
                $query->whereNotNull('email_verified_at');
            })
            ->get();


        $notifiedCount = 0;

        foreach ($plans as $plan) {
            // Check if plan is 100% complete
            $totalDays = (int)config('plans.duration_days');
            $mealPlansGenerated = $plan->mealPlans()->where('status', 'generated')->count();
            $workoutPlansGenerated = $plan->workoutPlans()->where('status', 'generated')->count();

            $isComplete = ($mealPlansGenerated === $totalDays) && ($workoutPlansGenerated === $totalDays);

            if (!$isComplete) {
                continue;
            }

            // Check if we already notified (add a column to track this)
            if ($plan->generation_completed_at !== null) {
                continue;
            }


            if ($this->option('dry-run')) {
                $this->line("Would notify: {$plan->user->email} - Plan: {$plan->plan_name}");
                $notifiedCount++;
                continue;
            }

            // Send notification
            try {
                $this->notifyAdmins($plan);

                // Generate password reset token if user has no password
                $passwordResetToken = null;
                if (!filled($plan->user->password)) {
                    $passwordResetToken = $plan->user->getPasswordResetToken();
                }

                // Send plan ready notification with optional password reset token
                $plan->user->notify(new PlanGenerationComplete($plan, $passwordResetToken));

                // Send app promo notification (delayed by 24h) as fallback/reminder
                app(AppPromoNotificationService::class)->sendToUser($plan->user, $plan);

                // Mark as notified (add this column via migration)
                $plan->update([
                    'generation_completed_at' => now(),
                ]);

                $this->line("✓ Notified: {$plan->user->email}");
                $notifiedCount++;
            } catch (\Exception $e) {
                $this->error("✗ Failed to notify {$plan->user->email}: {$e->getMessage()}");
            }
        }

        $this->info("Completed! Notified {$notifiedCount} users.");

        return Command::SUCCESS;
    }

    /**
     * Notify admin(s) about new onboarding.
     */
    private function notifyAdmins(Plan $plan): void
    {
        $adminEmails = config('app.admin_emails');

        try {
            Notification::route('mail', $adminEmails)
                ->notify(new PlanReadyForDelivery($plan));
        } catch (\Exception $e) {
            $this->error("✗ Failed to notify admins $adminEmails: {$e->getMessage()}");
        }

    }
}

