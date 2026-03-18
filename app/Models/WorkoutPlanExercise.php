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
}
