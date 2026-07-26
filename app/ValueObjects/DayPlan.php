<?php

namespace App\ValueObjects;

use App\Enums\DayAccess;
use App\Enums\DayGenerationStatus;
use App\Models\MealPlan;
use App\Models\Plan;
use App\Models\WorkoutPlan;
use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;

final readonly class DayPlan
{
    public function __construct(
        public Plan $plan,
        public CarbonImmutable $date,
        public int $totalDays,
        public DayAccess $access,
        public DayGenerationStatus $status,
        public ?MealPlan $mealPlan,
        public ?WorkoutPlan $workoutPlan,
        public Collection $calorieTrackings,
        public Collection $workoutTrackings,
        /** @var array<int, mixed> */
        public array $weekStrip,
        public DayCompletion $completion,
    ) {}
}
