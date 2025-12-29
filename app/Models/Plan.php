<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Plan extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'plan_name',
        'status',
        'duration_days',
        'start_date',
        'end_date',
        'current_day',
        'daily_calories',
        'daily_protein_g',
        'daily_carbs_g',
        'daily_fat_g',
        'workouts_per_week',
        'generation_completed_at',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'generation_completed_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function mealPlans(): HasMany
    {
        return $this->hasMany(MealPlan::class);
    }

    public function workoutPlans(): HasMany
    {
        return $this->hasMany(WorkoutPlan::class);
    }

    /**
     * Get the current day index based on start date (1-based)
     */
    protected function currentDay(): Attribute
    {
        return Attribute::make(
            get: function (): int {
                if (!$this->start_date) {
                    return 0;
                }

                $daysSinceStart = $this->start_date->diffInDays(now(), false);

                if ($daysSinceStart < 0) {
                    return 0;
                }

                if ($this->end_date && now()->isAfter($this->end_date)) {
                    return $this->duration_days;
                }

                return min((int)$daysSinceStart + 1, $this->duration_days);
            }
        );
    }
}

