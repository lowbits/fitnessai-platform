<?php

namespace Database\Factories;

use App\Models\Recipe;
use App\Models\RecipeTranslation;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class RecipeFactory extends Factory
{
    protected $model = Recipe::class;

    public function definition(): array
    {
        $name = fake()->unique()->words(3, true);

        return [
            'name' => ucwords($name),
            'description' => fake()->sentence(),
            'ingredients' => [
                ['name' => fake()->word(), 'amount' => fake()->numberBetween(1, 500), 'unit' => fake()->randomElement(['g', 'ml', 'tbsp', 'tsp', 'cup'])],
                ['name' => fake()->word(), 'amount' => fake()->numberBetween(1, 500), 'unit' => fake()->randomElement(['g', 'ml', 'tbsp', 'tsp', 'cup'])],
            ],
            'instructions' => [fake()->sentence(), fake()->sentence()],
            'prep_time_minutes' => fake()->numberBetween(5, 30),
            'cook_time_minutes' => fake()->numberBetween(0, 60),
            'difficulty' => fake()->randomElement(['easy', 'medium', 'hard']),
            'servings' => fake()->numberBetween(1, 4),
            'calories' => fake()->numberBetween(200, 800),
            'protein_g' => fake()->randomFloat(2, 10, 50),
            'carbs_g' => fake()->randomFloat(2, 20, 100),
            'fat_g' => fake()->randomFloat(2, 5, 40),
            'fiber_g' => fake()->randomFloat(2, 2, 15),
            'sugar_g' => fake()->randomFloat(2, 5, 30),
            'tags' => fake()->words(3),
            'allergens' => fake()->randomElement([['gluten', 'dairy'], ['nuts'], []]),
            'primary_protein' => fake()->randomElement(['chicken', 'beef', 'tofu', 'salmon', 'eggs', null]),
            'cuisine' => fake()->randomElement(['italian', 'mexican', 'asian', 'american', 'mediterranean', null]),
            'is_verified' => true,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }

    /**
     * @param  array<string, string>  $translations  Keyed by locale, e.g. ['de' => 'Hähnchenbrust']
     */
    public function withTranslations(array $translations): static
    {
        return $this->afterCreating(function (Recipe $recipe) use ($translations) {
            foreach ($translations as $locale => $name) {
                RecipeTranslation::create([
                    'recipe_id' => $recipe->id,
                    'locale' => $locale,
                    'name' => $name,
                ]);
            }
        });
    }
}
