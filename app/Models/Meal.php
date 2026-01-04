<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Meal extends Model
{
    use HasFactory;

    protected $fillable = [
        'meal_plan_id',
        'type',
        'name',
        'description',
        'image',
        'calories',
        'protein_g',
        'carbs_g',
        'fat_g',
        'fiber_g',
        'sugar_g',
        'ingredients',
        'instructions',
        'prep_time_minutes',
        'cook_time_minutes',
        'difficulty',
        'servings',
        'tags',
        'allergens',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'ingredients' => 'array',
            'instructions' => 'array',
            'tags' => 'array',
            'allergens' => 'array',
            'completed_at' => 'datetime',
        ];
    }

    public function mealPlan(): BelongsTo
    {
        return $this->belongsTo(MealPlan::class);
    }

    /**
     * Mark this meal as completed
     */
    public function markAsCompleted(): void
    {
        $this->update(['completed_at' => now()]);
    }

    /**
     * Mark this meal as incomplete
     */
    public function markAsIncomplete(): void
    {
        $this->update(['completed_at' => null]);
    }

    /**
     * Check if meal is completed
     */
    public function isCompleted(): bool
    {
        return $this->completed_at !== null;
    }
}
