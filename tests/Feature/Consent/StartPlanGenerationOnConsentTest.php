<?php

use App\Events\AiConsentGranted;
use App\Jobs\GenerateUserMealPlan;
use App\Jobs\GenerateUserWorkoutPlan;
use App\Listeners\StartPlanGenerationOnConsent;
use App\Models\Plan;
use App\Models\User;
use Illuminate\Support\Facades\Bus;

test('it dispatches plan generation for an ungenerated active plan', function () {
    Bus::fake([GenerateUserWorkoutPlan::class, GenerateUserMealPlan::class]);
    $user = User::factory()->create();
    Plan::factory()->create(['user_id' => $user->id, 'status' => 'active', 'generation_completed_at' => null]);

    app(StartPlanGenerationOnConsent::class)->handle(new AiConsentGranted($user));

    Bus::assertDispatched(GenerateUserWorkoutPlan::class);
    Bus::assertDispatched(GenerateUserMealPlan::class);
});

test('it skips a plan that is already generated', function () {
    Bus::fake([GenerateUserWorkoutPlan::class, GenerateUserMealPlan::class]);
    $user = User::factory()->create();
    Plan::factory()->create(['user_id' => $user->id, 'status' => 'active', 'generation_completed_at' => now()]);

    app(StartPlanGenerationOnConsent::class)->handle(new AiConsentGranted($user));

    Bus::assertNotDispatched(GenerateUserWorkoutPlan::class);
    Bus::assertNotDispatched(GenerateUserMealPlan::class);
});
