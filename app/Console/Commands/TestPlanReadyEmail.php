<?php

namespace App\Console\Commands;

use App\Models\Plan;
use App\Models\User;
use App\Notifications\PlanGenerationComplete;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Password;

class TestPlanReadyEmail extends Command
{
    protected $signature = 'email:test-plan-ready {user_id}';

    protected $description = 'Send a test plan ready email to a user';

    public function handle(): int
    {
        $user = User::with('profile')->find($this->argument('user_id'));

        if (!$user) {
            $this->error('User not found');
            return Command::FAILURE;
        }

        $plan = Plan::where('user_id', $user->id)->first();

        if (!$plan) {
            $this->error('No plan found for user');
            return Command::FAILURE;
        }

        $this->info("Sending test email to {$user->email}...");

        // Generate password reset token if user has no password
        $passwordResetToken = null;
        if (!filled($user->password)) {
            $passwordResetToken = Password::broker(config('fortify.passwords'))
                ->createToken($user);
            $this->info('✓ Password reset token generated');
        }

        $user->notify(new PlanGenerationComplete($plan, $passwordResetToken));

        $this->info('✅ Email queued successfully!');

        return Command::SUCCESS;
    }
}
