<?php

namespace App\Providers;

use App\Events\EmailVerified;
use App\Events\RevenueCat\InitialPurchaseProcessed;
use App\Events\RevenueCat\SubscriptionRenewed;
use App\Listeners\AdjustPlanAfterPurchase;
use App\Listeners\AdjustPlanAfterRenewal;
use App\Listeners\GenerateMealPlan;
use App\Listeners\GenerateWorkoutPlan;
use App\Listeners\HandleRevenueCatWebhook;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use NoopStudios\LaravelRevenueCat\Events\WebhookReceived;
use Meilisearch\Client as MeilisearchClient;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(MeilisearchClient::class, fn () => new MeilisearchClient(
            config('services.meilisearch.host'),
            config('services.meilisearch.key')
        ));
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Trigger plan generation after email verification
        Event::listen(
            EmailVerified::class,
            [GenerateMealPlan::class, GenerateWorkoutPlan::class],
        );

        Event::listen(WebhookReceived::class, HandleRevenueCatWebhook::class);
        Event::listen(InitialPurchaseProcessed::class, AdjustPlanAfterPurchase::class);
        Event::listen(SubscriptionRenewed::class, AdjustPlanAfterRenewal::class);
    }
}
