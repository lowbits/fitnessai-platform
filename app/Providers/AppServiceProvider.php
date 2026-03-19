<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
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
        // Event listeners are auto-discovered from app/Listeners via type-hinted handle() methods.
    }
}
