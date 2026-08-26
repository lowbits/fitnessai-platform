<?php

namespace App\Models;

use Database\Factories\HealthDailyMetricFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HealthDailyMetric extends Model
{
    /** @use HasFactory<HealthDailyMetricFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'date',
        'active_energy_kcal',
        'steps',
        'workouts',
        'credited_kcal',
        'synced_at',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'active_energy_kcal' => 'integer',
            'steps' => 'integer',
            'workouts' => 'array',
            'credited_kcal' => 'integer',
            'synced_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
