<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkoutTrackingSet extends Model
{
    use HasFactory;

    protected $fillable = [
        'workout_tracking_exercise_id',
        'set_number',
        'reps',
        'weight',
        'duration',
        'rpe',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'set_number' => 'integer',
            'reps' => 'integer',
            'weight' => 'decimal:2',
            'duration' => 'integer',
            'rpe' => 'integer',
        ];
    }

    public function workoutTrackingExercise(): BelongsTo
    {
        return $this->belongsTo(WorkoutTrackingExercise::class);
    }
}

