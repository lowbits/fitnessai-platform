<?php

namespace Database\Factories;

use App\Models\PlanEvaluation;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PlanEvaluation>
 */
class PlanEvaluationFactory extends Factory
{
    protected $model = PlanEvaluation::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'score' => fake()->numberBetween(0, 100),
            'plan_type' => 'workout',
            'source' => fake()->randomElement(['paste', 'txt', 'pdf', 'image']),
            'facts' => ['plan_type' => 'workout', 'days_per_week' => 4, 'muscle_groups' => []],
            'dimensions' => [],
            'plan_text' => fake()->optional()->paragraph(),
            'tone' => fake()->randomElement(['roast', 'neutral']),
            'locale' => fake()->randomElement(['de', 'en']),
        ];
    }
}
