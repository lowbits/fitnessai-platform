<?php

namespace Database\Factories;

use App\Models\CheckIn;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CheckIn>
 */
class CheckInFactory extends Factory
{
    protected $model = CheckIn::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'body_progress_id' => null,
            'mood' => fake()->numberBetween(1, 5),
            'energy' => fake()->numberBetween(1, 5),
            'checked_in_at' => fake()->dateTimeBetween('-30 days', 'now'),
        ];
    }
}
