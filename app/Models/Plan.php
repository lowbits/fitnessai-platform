<?php

namespace App\Models;

use App\Enums\DayAccess;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

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
     * The access level for a given date: free preview window, full (subscribed),
     * preview-locked, expired, or before start. Single source of truth for the
     * plan access rule.
     */
    public function accessOn(CarbonImmutable $date, bool $hasActiveSubscription): DayAccess
    {
        $start = CarbonImmutable::parse($this->start_date->toDateString());
        $lastDay = $start->addDays((int) $this->duration_days - 1);

        return DayAccess::forDate(
            $date,
            $start,
            $lastDay,
            (int) config('subscription.preview_days'),
            $hasActiveSubscription,
        );
    }

    protected function nextGenerationAt(): Attribute
    {
        return Attribute::get(function (): ?Carbon {
            if (! $this->start_date) {
                return null;
            }

            $genDow = ($this->start_date->dayOfWeek + 3) % 7;
            $today = now()->startOfDay();

            return $today->copy()->addDays(($genDow - $today->dayOfWeek + 7) % 7);
        });
    }

    /**
     * Get the current day index based on start date (1-based)
     */
    protected function currentDay(): Attribute
    {
        return Attribute::make(
            get: function (): int {
                if (! $this->start_date) {
                    return 0;
                }

                $daysSinceStart = $this->start_date->diffInDays(now(), false);

                if ($daysSinceStart < 0) {
                    return 0;
                }

                if ($this->end_date && now()->isAfter($this->end_date)) {
                    return $this->duration_days;
                }

                return min((int) $daysSinceStart + 1, $this->duration_days);
            }
        );
    }
}
