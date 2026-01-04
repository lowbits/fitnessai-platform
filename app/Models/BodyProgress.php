<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BodyProgress extends Model
{
    /** @use HasFactory<\Database\Factories\BodyProgressFactory> */
    use HasFactory;

    protected $table = 'body_progress';

    protected $fillable = [
        'user_id',
        'weight_kg',
        'body_fat_percentage',
        'muscle_mass_kg',
        'waist_circumference_cm',
        'chest_circumference_cm',
        'hip_circumference_cm',
        'arm_circumference_cm',
        'thigh_circumference_cm',
        'notes',
        'recorded_at',
    ];

    protected $casts = [
        'weight_kg' => 'decimal:2',
        'body_fat_percentage' => 'decimal:2',
        'muscle_mass_kg' => 'decimal:2',
        'waist_circumference_cm' => 'decimal:2',
        'chest_circumference_cm' => 'decimal:2',
        'hip_circumference_cm' => 'decimal:2',
        'arm_circumference_cm' => 'decimal:2',
        'thigh_circumference_cm' => 'decimal:2',
        'recorded_at' => 'datetime',
    ];

    protected $hidden = [
        'weight_kg',
        'muscle_mass_kg',
        'waist_circumference_cm',
        'chest_circumference_cm',
        'hip_circumference_cm',
        'arm_circumference_cm',
        'thigh_circumference_cm',
    ];

    protected $appends = [
        'weight',
        'muscle_mass',
        'waist_circumference',
        'chest_circumference',
        'hip_circumference',
        'arm_circumference',
        'thigh_circumference',
        'trend',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Accessors for API (without suffix)
    public function getWeightAttribute(): ?float
    {
        return $this->weight_kg;
    }

    public function getMuscleMassAttribute(): ?float
    {
        return $this->muscle_mass_kg;
    }

    public function getWaistCircumferenceAttribute(): ?float
    {
        return $this->waist_circumference_cm;
    }

    public function getChestCircumferenceAttribute(): ?float
    {
        return $this->chest_circumference_cm;
    }

    public function getHipCircumferenceAttribute(): ?float
    {
        return $this->hip_circumference_cm;
    }

    public function getArmCircumferenceAttribute(): ?float
    {
        return $this->arm_circumference_cm;
    }

    public function getThighCircumferenceAttribute(): ?float
    {
        return $this->thigh_circumference_cm;
    }

    /**
     * Get the weight trend compared to the previous entry.
     * Returns 'up', 'down', 'stable', or null if no previous entry
     */
    public function getTrendAttribute(): ?string
    {
        if (!$this->weight_kg) {
            return null;
        }

        $previousEntry = static::where('user_id', $this->user_id)
            ->where('recorded_at', '<', $this->recorded_at)
            ->whereNotNull('weight_kg')
            ->orderBy('recorded_at', 'desc')
            ->first();

        if (!$previousEntry || !$previousEntry->weight_kg) {
            return null;
        }

        $difference = $this->weight_kg - $previousEntry->weight_kg;

        // Consider differences less than 0.1kg as stable
        if (abs($difference) < 0.1) {
            return 'stable';
        }

        return $difference > 0 ? 'up' : 'down';
    }
}

