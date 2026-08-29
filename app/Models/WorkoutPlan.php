<?php

namespace App\Models;

use App\Enums\Equipment;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class WorkoutPlan extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'plan_id',
        'date',
        'day_number',
        'status',
        'workout_name',
        'workout_type',
        'estimated_duration_minutes',
        'estimated_calories_burned',
        'difficulty',
        'description',
        'muscle_groups',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'muscle_groups' => 'array',
        ];
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    public function exercises(): HasMany
    {
        return $this->hasMany(WorkoutPlanExercise::class)->orderBy('order');
    }

    public function trackings(): HasMany
    {
        return $this->hasMany(WorkoutTracking::class);
    }

    public function latestTracking()
    {
        return $this->hasOne(WorkoutTracking::class)->latestOfMany('completed_at');
    }

    public function thumbnailUrl(): string
    {
        $baseUrl = config('services.r2.public_url');
        $muscleGroups = $this->muscle_groups ?? [];
        $type = $this->workout_type;

        $name = match (true) {
            in_array($type, ['rest', 'mobility']) => 'recovery_mobility',
            in_array($type, ['cardio', 'hiit']),
            in_array('cardio', $muscleGroups) => 'full_body',
            in_array('full_body', $muscleGroups) => 'full_body',
            ! empty(array_intersect(['chest', 'shoulders', 'triceps'], $muscleGroups)) => 'push',
            ! empty(array_intersect(['back', 'upper_back', 'lower_back', 'biceps', 'forearms', 'traps', 'rhomboids', 'rear_delts', 'rotator_cuff'], $muscleGroups)) => 'pull',
            in_array('glutes', $muscleGroups) => 'glutes',
            ! empty(array_intersect(['quadriceps', 'hamstrings', 'calves', 'hip_flexors', 'legs'], $muscleGroups)) => 'legs_lower',
            in_array('core', $muscleGroups) => 'push',
            default => 'full_body',
        };

        return "{$baseUrl}/workouts/thumbnails/{$name}.png";
    }

    public function equipmentDetails(): array
    {
        $baseUrl = config('services.r2.public_url');

        return $this->exercises
            ->flatMap(fn ($e) => $e->exercise?->equipment ?? [])
            ->unique()
            ->values()
            ->map(function (string $value) use ($baseUrl) {
                $enum = Equipment::tryFrom($value);

                return [
                    'name' => $value,
                    'label' => $enum?->label() ?? $value,
                    'image_url' => "{$baseUrl}/equipments/transparent/{$value}.webp",
                ];
            })
            ->all();
    }
}
