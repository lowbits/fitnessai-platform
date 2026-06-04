<?php

use App\Events\EmailVerified;
use App\Jobs\GenerateUserMealPlan;
use App\Jobs\GenerateUserWorkoutPlan;
use App\Listeners\GenerateMealPlan;
use App\Listeners\GenerateWorkoutPlan;
use App\Models\Plan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;

uses(RefreshDatabase::class);

test('email verification dispatches generation for the full plan', function () {
    Bus::fake([GenerateUserWorkoutPlan::class, GenerateUserMealPlan::class]);

    $user = User::factory()->create();
    $plan = Plan::factory()->active()->create([
        'user_id' => $user->id,
        'duration_days' => 7,
    ]);

    $event = new EmailVerified($user, $plan);
    (new GenerateWorkoutPlan)->handle($event);
    (new GenerateMealPlan)->handle($event);

    Bus::assertDispatched(GenerateUserWorkoutPlan::class);
    Bus::assertDispatched(GenerateUserMealPlan::class);
});
