<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WorkoutTracking extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'workout_plan_id',
        'started_at',
        'completed_at',
        'notes',
        'feeling_rate',
    ];

    protected function casts(): array
    {
        return [
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
            'feeling_rate' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function workoutPlan(): BelongsTo
    {
        return $this->belongsTo(WorkoutPlan::class);
    }

    public function exercises(): HasMany
    {
        return $this->hasMany(WorkoutTrackingExercise::class);
    }
}

