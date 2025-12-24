<?php

namespace App\Providers;

use App\Events\EmailVerified;
use App\Listeners\GenerateMealPlan;
use App\Listeners\GenerateWorkoutPlan;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
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
    }
}
