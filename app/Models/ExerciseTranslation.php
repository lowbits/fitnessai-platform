<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExerciseTranslation extends Model
{
    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'aliases' => 'array',
            'instructions' => 'array',
        ];
    }

    public function exercise(): BelongsTo
    {
        return $this->belongsTo(Exercise::class);
    }
}
