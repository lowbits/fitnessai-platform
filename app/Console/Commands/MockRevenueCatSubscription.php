<?php

namespace App\Console\Commands;

use App\Actions\RevenueCat\AdjustActivePlanAction;
use App\Models\User;
use Illuminate\Console\Command;
use NoopStudios\LaravelRevenueCat\Models\Subscription;
use NoopStudios\LaravelRevenueCat\Enums\SubscriptionStatus;
use function Symfony\Component\String\s;

class MockRevenueCatSubscription extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dev:mock-rc {email} {--period=monthly : monthly or yearly}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Mock a RevenueCat subscription for a user';

    /**
     * Execute the console command.
     */
    public function handle(AdjustActivePlanAction $adjustActivePlanAction): int
    {
        $email = $this->argument('email');
        $period = $this->option('period');
        $productId = $period === 'yearly' ? 'rc_premium_yearly' : 'rc_premium_monthly';

        $user = User::where('email', $email)->first();

        if (!$user) {
            $this->error("User with email '{$email}' not found.");
            return Command::FAILURE;
        }

        $user->customer()->updateOrCreate(
            ['revenuecat_id' => $user->id],
            [
                'email' => $user->email,
                'display_name' => $user->name,
                'metadata' => [],
            ]
        );

        Subscription::unguard();
        $user->subscriptions()->updateOrCreate(
            ['product_id' => $productId],
            [
                'name' => 'default',
                'status' => SubscriptionStatus::ACTIVE,
                'price' => $period === 'yearly' ? '59.99' : '9.99',
                'currency' => 'USD',
                'store' => 'app_store',
                'current_period_started_at' => now(),
                'current_period_ended_at' => $period === 'yearly' ? now()->addYear() : now()->addMonth(),
            ]
        );
        Subscription::reguard();

        $this->info("✅ Created mock RevenueCat subscription for {$email} ({$period})");

        // 2. Adjust the active plan duration
        // This extends the user's current 7-day plan to match the subscription
        $adjustActivePlanAction->execute($user, $productId);

        $this->info("✅ Adjusted user's active plan duration.");

        return Command::SUCCESS;
    }
}
