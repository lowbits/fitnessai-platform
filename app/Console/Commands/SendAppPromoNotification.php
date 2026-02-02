<?php

namespace App\Console\Commands;

use App\Models\Plan;
use App\Models\User;
use App\Services\AppPromoNotificationService;
use Illuminate\Console\Command;

class SendAppPromoNotification extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'notification:send-app-promo
                            {--email= : Send to specific email address}
                            {--user-id= : Send to specific user ID}
                            {--all-without-password : Send to all users without password}
                            {--immediate : Send immediately without 24h delay (even in production)}
                            {--dry-run : Show what would be sent without actually sending}';

    /**
     * The console command description.
     */
    protected $description = 'Send app promo notification to users without password';

    /**
     * Execute the console command.
     */
    public function handle(AppPromoNotificationService $promoService): int
    {
        $users = $this->getUsersToNotify();

        if ($users->isEmpty()) {
            $this->warn('No users found matching criteria.');
            return Command::FAILURE;
        }

        $this->info("Found {$users->count()} user(s) to notify.");

        $immediate = $this->option('immediate');
        if ($immediate) {
            $this->warn('⚡ Immediate mode: Notifications will be sent without 24h delay');
        } else {
            $this->info('⏰ In production, notifications will be delayed by 24 hours');
        }

        $sentCount = 0;
        $skippedCount = 0;

        foreach ($users as $user) {
            // Get user's active plan
            $plan = $user->plans()
                ->where('status', 'active')
                ->latest()
                ->first();

            if (!$plan) {
                $this->warn("⊘ Skipping {$user->email} - no active plan found");
                $skippedCount++;
                continue;
            }

            if ($this->option('dry-run')) {
                $this->line("Would send to: {$user->email} (Plan: {$plan->plan_name})");
                $sentCount++;
                continue;
            }

            try {
                $sent = $promoService->sendToUser($user, $plan, $immediate);

                if ($sent) {
                    $this->info("✓ Sent to: {$user->email}");
                    $sentCount++;
                } else {
                    $this->warn("⊘ Skipped {$user->email} - user already has password");
                    $skippedCount++;
                }
            } catch (\Exception $e) {
                $this->error("✗ Failed to send to {$user->email}: {$e->getMessage()}");
            }
        }

        $this->newLine();
        $this->info("Completed! Sent: {$sentCount}, Skipped: {$skippedCount}");

        return Command::SUCCESS;
    }

    /**
     * Get users to notify based on command options.
     */
    private function getUsersToNotify()
    {
        if ($email = $this->option('email')) {
            return User::where('email', $email)->get();
        }

        if ($userId = $this->option('user-id')) {
            return User::where('id', $userId)->get();
        }

        if ($this->option('all-without-password')) {
            return User::whereNull('password')
                ->orWhere('password', '')
                ->whereNotNull('email_verified_at')
                ->whereHas('plans', function ($query) {
                    $query->where('status', 'active');
                })
                ->get();
        }

        $this->error('Please specify one of: --email, --user-id, or --all-without-password');
        return collect();
    }
}

