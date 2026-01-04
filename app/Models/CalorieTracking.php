<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CalorieTracking extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'meal_id',
        'tracked_date',
        'calories',
        'protein_g',
        'carbs_g',
        'fat_g',
        'meal_name',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'tracked_date' => 'date',
            'calories' => 'decimal:2',
            'protein_g' => 'decimal:2',
            'carbs_g' => 'decimal:2',
            'fat_g' => 'decimal:2',
        ];
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
