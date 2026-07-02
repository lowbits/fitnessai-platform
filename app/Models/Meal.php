<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Meal extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'meal_plan_id',
        'recipe_id',
        'type',
        'name',
        'description',
        'image',
        'image_full',
        'image_isolated',
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
        'primary_protein',
        'cuisine',
        'format',
        'hero_veg',
        'status',
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

    public function recipe(): BelongsTo
    {
        return $this->belongsTo(Recipe::class);
    }

    public function scopeInEatOrder($query)
    {
        return $query->orderByRaw("CASE type WHEN 'breakfast' THEN 1 WHEN 'lunch' THEN 2 WHEN 'snack' THEN 3 WHEN 'dinner' THEN 4 END");
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

    /**
     * Full hero image URL — meal's own first, then recipe's. Nullable when
     * neither exists; UI shows an empty state rather than a faked placeholder.
     */
    public function getImageUrlAttribute(): ?string
    {
        $path = $this->image_full ?? $this->recipe?->image_full;

        return $path ? $this->r2Url($path) : null;
    }

    /**
     * Card / list thumbnail — prefers the background-removed isolated cutout,
     * falls through meal → recipe → per-meal-type placeholder. Always returns
     * a URL so list views never have a missing image.
     */
    public function getThumbnailUrlAttribute(): string
    {
        $path = $this->image_isolated
            ?? $this->image_full
            ?? $this->recipe?->image_isolated
            ?? $this->recipe?->image_full
            ?? 'meals/thumbnails/'.mb_strtolower($this->type).'_placeholder.png';

        return $this->r2Url($path);
    }

    private function r2Url(string $path): string
    {
        return rtrim((string) config('services.r2.public_url'), '/').'/'.ltrim($path, '/');
    }
}
