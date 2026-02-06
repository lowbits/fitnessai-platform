<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Notifications\CalorieCalculationUpdate;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class SendCalorieUpdateNotification extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'notification:send-calorie-update
                            {--email= : Send to specific email address}
                            {--user-id= : Send to specific user ID}
                            {--all-with-password : Send to all users with password}
                            {--limit= : Limit the number of users to process}
                            {--force : Force resend even if already sent}
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

        // Apply limit if specified
        $limit = $this->option('limit');
        if ($limit && $limit > 0) {
            $totalUsers = $users->count();
            $users = $users->take((int) $limit);
            $this->info("Limiting to {$limit} users (out of {$totalUsers} total)");
        }

        $this->info("Found {$users->count()} user(s) to notify.");

        if (!$this->option('dry-run')) {
            $this->info("⏱️  Rate limit protection: 0.5s delay between each notification");
        }

        $sentCount = 0;
        $errorCount = 0;
        $skippedCount = 0;
        $delaySeconds = 0;
        $force = $this->option('force');

        foreach ($users as $index => $user) {
            // Check if already sent (unless force flag is used)
            $cacheKey = CalorieCalculationUpdate::getCacheKey($user->id);
            $alreadySent = Cache::has($cacheKey);

            if ($alreadySent && !$force && !$this->option('dry-run')) {
                $this->line("⊘ Skipping {$user->email} - already received this notification");
                $skippedCount++;
                continue;
            }

            if ($this->option('dry-run')) {
                $status = $alreadySent ? '(already sent)' : '(new)';
                $this->line("Would send to: {$user->email} (Name: {$user->name}, Locale: {$user->locale}) {$status}");
                $sentCount++;
                continue;
            }

            try {
                // Add delay to each queued notification to prevent rate limiting
                // 0.5 seconds between each email (2 per second max)
                $notification = (new CalorieCalculationUpdate())->delay(now()->addSeconds($delaySeconds));
                $user->notify($notification);

                // Mark as sent in cache (store for 1 year)
                Cache::put($cacheKey, true, now()->addYear());

                $resendLabel = ($alreadySent && $force) ? ' (RESEND)' : '';
                $this->info("✓ Queued for {$user->email} (delay: {$delaySeconds}s){$resendLabel}");
                $sentCount++;

                // Increment delay for next notification
                $delaySeconds += 0.5;
            } catch (\Exception $e) {
                $this->error("✗ Failed to send to {$user->email}: {$e->getMessage()}");
                $errorCount++;
            }
        }

        $this->newLine();
        $this->info("Completed! Sent: {$sentCount}, Skipped: {$skippedCount}, Errors: {$errorCount}");

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
