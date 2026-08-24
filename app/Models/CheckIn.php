<?php

namespace App\Models;

use Database\Factories\CheckInFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CheckIn extends Model
{
    /** @use HasFactory<CheckInFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'body_progress_id',
        'mood',
        'energy',
        'checked_in_at',
    ];

    protected $casts = [
        'mood' => 'integer',
        'energy' => 'integer',
        'checked_in_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function bodyProgress(): BelongsTo
    {
        return $this->belongsTo(BodyProgress::class);
    }
}
