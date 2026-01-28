<?php

namespace App\Models;

use App\Enums\MealType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CalorieTracking extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'meal_id',
        'external_id',
        'tracked_date',
        'calories',
        'protein_g',
        'carbs_g',
        'fat_g',
        'meal_name',
        'notes',
        'meal_type',
    ];

    protected $appends = ['source'];

    protected function casts(): array
    {
        return [
            'tracked_date' => 'date',
            'calories' => 'decimal:2',
            'protein_g' => 'decimal:2',
            'carbs_g' => 'decimal:2',
            'fat_g' => 'decimal:2',
            'meal_type' => MealType::class,
        ];
    }

    public function getSourceAttribute(): string
    {
        return match (true) {
            !is_null($this->external_id) => 'search',
            !is_null($this->meal_id) => 'plan',
            default => 'manual',
        };
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function meal(): BelongsTo
    {
        return $this->belongsTo(Meal::class);
    }
}
