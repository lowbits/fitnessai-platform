<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Exercise extends Model
{

    protected $fillable = [
        'workout_plan_id',
        'order',
        'name',
        'original_name',
        'type',
        'description',
        'instructions',
        'video_url',
        'image',
        'sets',
        'reps',
        'duration_seconds',
        'rest_seconds',
        'tempo',
        'weight_recommendation',
        'muscle_groups',
        'equipment',
        'form_cues',
        'alternatives',
        'difficulty',
    ];

    protected function casts(): array
    {
        return [
            'muscle_groups' => 'array',
            'equipment' => 'array',
            'alternatives' => 'array',
            'instructions' => 'array',
        ];
    }

    public function workoutPlan(): BelongsTo
    {
        return $this->belongsTo(WorkoutPlan::class);
    }
}
