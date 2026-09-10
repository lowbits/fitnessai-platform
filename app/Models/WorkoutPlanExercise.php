<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WorkoutPlanExercise extends Model
{
    use HasFactory;

    protected $table = 'workout_plan_exercises';

    protected $fillable = [
        'workout_plan_id',
        'exercise_id',
        'order',
        'type',
        'sets',
        'reps',
        'duration_seconds',
        'rest_seconds',
        'execution_style',
        'tempo',
        'rpe',
        'weight_recommendation',
        'alternatives',
        'difficulty',
    ];

    protected function casts(): array
    {
        return [
            'alternatives' => 'array',
        ];
    }

    public function workoutPlan(): BelongsTo
    {
        return $this->belongsTo(WorkoutPlan::class);
    }

    public function exercise(): BelongsTo
    {
        return $this->belongsTo(Exercise::class);
    }

    public function trackingExercises(): HasMany
    {
        return $this->hasMany(WorkoutTrackingExercise::class, 'workout_plan_exercise_id');
    }

    public function durationLabel(): string
    {
        $seconds = (int) $this->duration_seconds;

        if ($seconds <= 0) {
            return '';
        }

        $minutes = intdiv($seconds, 60);
        $rest = $seconds % 60;

        return match (true) {
            $minutes === 0 => $rest.'s',
            $rest === 0 => $minutes.'min',
            default => $minutes.'min '.$rest.'s',
        };
    }

    public function metricLabel(): string
    {
        if ($this->reps) {
            return $this->sets.' × '.$this->reps;
        }

        if ($this->duration_seconds) {
            return $this->sets ? $this->sets.' × '.$this->durationLabel() : $this->durationLabel();
        }

        return (string) $this->sets;
    }

    public function alternativeNames(int $limit = 2): string
    {
        return collect($this->alternatives ?? [])
            ->map(fn ($alt) => is_string($alt) ? $alt : ($alt['name'] ?? null))
            ->filter()
            ->take($limit)
            ->join(', ');
    }
}
