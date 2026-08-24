<?php

namespace Database\Factories;

use App\Enums\FoodSource;
use App\Models\Food;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Food>
 */
class FoodFactory extends Factory
{
    protected $model = Food::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'source' => FoodSource::OpenFoodFacts,
            'user_id' => null,
            'barcode' => (string) fake()->unique()->ean13(),
            'name' => fake()->words(2, true),
            'brand' => fake()->optional()->company(),
            'kcal' => fake()->randomFloat(2, 0, 600),
            'protein_g' => fake()->randomFloat(2, 0, 40),
            'carbs_g' => fake()->randomFloat(2, 0, 80),
            'fat_g' => fake()->randomFloat(2, 0, 40),
            'verified' => true,
        ];
    }

    public function custom(): static
    {
        return $this->state(fn () => [
            'source' => FoodSource::Custom,
            'barcode' => null,
        ]);
    }
}
