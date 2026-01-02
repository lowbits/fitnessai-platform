<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use NotificationChannels\Expo\ExpoPushToken;

class UserDevice extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'device_id',
        'expo_push_token',
        'device_name',
        'platform',
        'last_used_at',
    ];

    protected function casts(): array
    {
        return [
            'expo_push_token' => ExpoPushToken::class,
            'last_used_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

