<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MealPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'plan_id',
        'date',
        'day_number',
        'status',
        'total_calories',
        'total_protein_g',
        'total_carbs_g',
        'total_fat_g',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
        ];
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    public function meals(): HasMany
    {
        return $this->hasMany(Meal::class);
    }
}

