<?php

namespace Database\Factories;

use App\Actions\Health\CreditActiveEnergy;
use App\Models\HealthDailyMetric;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<HealthDailyMetric>
 */
class HealthDailyMetricFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $activeEnergy = $this->faker->numberBetween(100, 900);

        return [
            'user_id' => User::factory(),
            'date' => today()->toDateString(),
            'active_energy_kcal' => $activeEnergy,
            'steps' => $this->faker->numberBetween(0, 20000),
            'workouts' => [],
            'credited_kcal' => app(CreditActiveEnergy::class)($activeEnergy),
            'synced_at' => now(),
        ];
    }
}
