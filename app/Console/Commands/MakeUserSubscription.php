<?php

namespace App\Console\Commands;

use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Console\Command;

class MakeUserSubscription extends Command
{
    protected $signature = 'subscription:create {email} {--type=beta} {--months=1}';
    protected $description = 'Create a subscription for a user and update their plan duration';

    public function handle(): int
    {
        $email = $this->argument('email');
        $type = $this->option('type');
        $months = (int) $this->option('months');

        $user = User::where('email', $email)->first();

        if (!$user) {
            $this->error("User with email '{$email}' not found");
            return Command::FAILURE;
        }

        // Check if user already has an active subscription
        if ($user->hasActiveSubscription()) {
            $this->warn("User already has an active subscription");

            if (!$this->confirm('Do you want to create another subscription anyway?')) {
                return Command::FAILURE;
            }
        }

        $subscription = Subscription::create([
            'user_id' => $user->id,
            'type' => $type,
            'status' => 'active',
            'starts_at' => now(),
            'ends_at' => now()->addMonths($months),
        ]);

        $this->info("✅ Subscription created successfully!");

        // Update existing plan duration from 7 days to 30 days
        $activePlan = $user->plans()->where('status', 'active')->latest()->first();

        if ($activePlan) {
            $this->info("\n📋 Updating existing plan...");

            $oldDuration = $activePlan->duration_days;
            $oldEndDate = $activePlan->end_date;

            // Calculate new duration (30 days per month)
            $newDuration = 30 * $months;
            $newEndDate = $activePlan->start_date->copy()->addDays($newDuration);

            $activePlan->update([
                'duration_days' => $newDuration,
                'end_date' => $newEndDate,
            ]);

            $this->table(
                ['Field', 'Old Value', 'New Value'],
                [
                    ['Duration', "{$oldDuration} days", "{$newDuration} days"],
                    ['End Date', $oldEndDate->format('Y-m-d'), $newEndDate->format('Y-m-d')],
                ]
            );

            $this->info("✅ Plan updated from {$oldDuration} days to {$newDuration} days");
        } else {
            $this->warn("⚠️  No active plan found for this user");
        }

        $this->newLine();
        $this->table(
            ['Field', 'Value'],
            [
                ['User', $user->email],
                ['Subscription Type', $subscription->type],
                ['Status', $subscription->status],
                ['Starts At', $subscription->starts_at->format('Y-m-d H:i:s')],
                ['Ends At', $subscription->ends_at->format('Y-m-d H:i:s')],
                ['Duration', "{$months} month(s)"],
            ]
        );

        return Command::SUCCESS;
    }
}

