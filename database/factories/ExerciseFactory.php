<?php

namespace Database\Factories;

use App\Models\Exercise;
use App\Models\ExerciseTranslation;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class ExerciseFactory extends Factory
{
    protected $model = Exercise::class;

    public function definition(): array
    {
        $name = fake()->unique()->words(3, true);

        return [
            'name' => ucwords($name),
            'type' => fake()->randomElement(['strength', 'cardio', 'stretch']),
            'difficulty' => fake()->randomElement(['beginner', 'intermediate', 'advanced']),
            'primary_muscles' => fake()->randomElements(['chest', 'back', 'legs', 'shoulders', 'arms', 'core'], fake()->numberBetween(1, 2)),
            'secondary_muscles' => [],
            'equipment' => fake()->randomElements(['barbell', 'dumbbell', 'bodyweight', 'resistance band'], fake()->numberBetween(1, 2)),
            'training_places' => ['gym'],
            'is_verified' => true,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }

    /**
     * @param  array<string, string>  $translations  Keyed by locale, e.g. ['de' => 'Bankdrücken']
     */
    public function withTranslations(array $translations): static
    {
        return $this->afterCreating(function (Exercise $exercise) use ($translations) {
            foreach ($translations as $locale => $name) {
                ExerciseTranslation::create([
                    'exercise_id' => $exercise->id,
                    'locale' => $locale,
                    'name' => $name,
                ]);
            }
        });
    }
}
