<?php

use App\Ai\Tools\GetCalorieStatusTool;
use App\Models\CalorieTracking;
use App\Models\HealthDailyMetric;
use App\Models\Plan;
use App\Models\User;
use Laravel\Ai\Tools\Request;

function calorieStatus(User $user): array
{
    return json_decode((new GetCalorieStatusTool($user))->handle(new Request([])), true);
}

test('with no active plan it returns no_active_plan', function () {
    $result = calorieStatus(User::factory()->withProfile()->create());

    expect($result['error'])->toBe('no_active_plan');
});

test('it returns a read-only calorie_status widget with eaten, goal and remaining', function () {
    $user = User::factory()->withProfile()->create();
    Plan::factory()->create([
        'user_id' => $user->id,
        'status' => 'active',
        'daily_calories' => 2000,
        'daily_protein_g' => 150,
        'daily_carbs_g' => 200,
        'daily_fat_g' => 60,
    ]);

    CalorieTracking::factory()->for($user)->create([
        'tracked_date' => today(),
        'calories' => 800,
        'protein_g' => 60,
        'carbs_g' => 90,
        'fat_g' => 20,
    ]);

    $result = calorieStatus($user);

    expect($result['widget'])->toBe('calorie_status');
    expect($result['requires_input'])->toBeFalse();
    expect($result['data']['eaten'])->toBe(800);
    expect($result['data']['goal'])->toBe(2000);
    expect($result['data']['remaining'])->toBe(1200);
    expect($result['data']['protein'])->toBe(['eaten' => 60, 'target' => 150]);
});

test('only today counts toward eaten', function () {
    $user = User::factory()->withProfile()->create();
    Plan::factory()->create(['user_id' => $user->id, 'status' => 'active', 'daily_calories' => 2000]);

    CalorieTracking::factory()->for($user)->create(['tracked_date' => today(), 'calories' => 500]);
    CalorieTracking::factory()->for($user)->create(['tracked_date' => today()->subDay(), 'calories' => 900]);

    expect(calorieStatus($user)['data']['eaten'])->toBe(500);
});

test('an untracked day reports zero eaten and the full goal remaining', function () {
    $user = User::factory()->withProfile()->create();
    Plan::factory()->create(['user_id' => $user->id, 'status' => 'active', 'daily_calories' => 1800]);

    $result = calorieStatus($user);

    expect($result['data']['eaten'])->toBe(0);
    expect($result['data']['remaining'])->toBe(1800);
});

test('it folds the Apple Health activity credit into remaining without exposing it to the model', function () {
    $user = User::factory()->withProfile()->create(['activity_credit_enabled' => true]);
    Plan::factory()->create(['user_id' => $user->id, 'status' => 'active', 'daily_calories' => 2000]);

    HealthDailyMetric::factory()->for($user)->create([
        'date' => today()->toDateString(),
        'credited_kcal' => 150,
    ]);
    CalorieTracking::factory()->for($user)->create(['tracked_date' => today(), 'calories' => 800]);

    $data = calorieStatus($user)['data'];

    expect($data)->not->toHaveKey('activity_credit')
        ->and($data['remaining'])->toBe(1350); // 2000 + 150 - 800
});
