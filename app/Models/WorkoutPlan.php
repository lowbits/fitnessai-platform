<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WorkoutPlan extends Model
{
    use HasFactory;

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
        return $this->hasMany(Exercise::class)->orderBy('order');
    }

    public function trackings(): HasMany
    {
        return $this->hasMany(WorkoutTracking::class);
    }
}

