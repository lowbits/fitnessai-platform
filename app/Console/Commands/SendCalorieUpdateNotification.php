<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Notifications\CalorieCalculationUpdate;
use Illuminate\Console\Command;

class SendCalorieUpdateNotification extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'notification:send-calorie-update
                            {--email= : Send to specific email address}
                            {--user-id= : Send to specific user ID}
                            {--all-with-password : Send to all users with password}
                            {--dry-run : Show what would be sent without actually sending}';

    /**
     * The console command description.
     */
    protected $description = 'Send calorie calculation update notification to users with password';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $users = $this->getUsersToNotify();

        if ($users->isEmpty()) {
            $this->warn('No users found matching criteria.');
            return Command::FAILURE;
        }

        $this->info("Found {$users->count()} user(s) to notify.");

        $sentCount = 0;
        $errorCount = 0;

        foreach ($users as $user) {
            if ($this->option('dry-run')) {
                $this->line("Would send to: {$user->email} (Name: {$user->name}, Locale: {$user->locale})");
                $sentCount++;
                continue;
            }

            try {
                $user->notify(new CalorieCalculationUpdate());
                $this->info("✓ Sent to: {$user->email}");
                $sentCount++;
            } catch (\Exception $e) {
                $this->error("✗ Failed to send to {$user->email}: {$e->getMessage()}");
                $errorCount++;
            }
        }

        $this->newLine();
        $this->info("Completed! Sent: {$sentCount}, Errors: {$errorCount}");

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

        if ($this->option('all-with-password')) {
            return User::whereNotNull('password')
                ->where('password', '!=', '')
                ->whereNotNull('email_verified_at')
                ->get();
        }

        $this->error('Please specify one of: --email, --user-id, or --all-with-password');
        return collect();
    }
}
