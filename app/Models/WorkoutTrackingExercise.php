<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WorkoutTrackingExercise extends Model
{
    use HasFactory;

    protected $fillable = [
        'workout_tracking_id',
        'exercise_id',
        'order',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'order' => 'integer',
        ];
    }

    public function workoutTracking(): BelongsTo
    {
        return $this->belongsTo(WorkoutTracking::class);
    }

    public function exercise(): BelongsTo
    {
        return $this->belongsTo(Exercise::class);
    }

    public function sets(): HasMany
    {
        return $this->hasMany(WorkoutTrackingSet::class)->orderBy('set_number');
    }
}

