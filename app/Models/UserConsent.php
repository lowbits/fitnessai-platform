<?php

namespace App\Models;

use App\Enums\ConsentSource;
use App\Enums\ConsentType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Append-only consent ledger (GDPR Art. 7): every grant is a new row, a
 * withdrawal only stamps revoked_at, and rows are never updated or deleted.
 */
class UserConsent extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'consent_type',
        'version',
        'granted_at',
        'revoked_at',
        'source',
        'locale',
    ];

    protected function casts(): array
    {
        return [
            'consent_type' => ConsentType::class,
            'source' => ConsentSource::class,
            'granted_at' => 'datetime',
            'revoked_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->whereNull('revoked_at');
    }

    public static function activeFor(User $user, ConsentType $type): ?self
    {
        return $user->consents()
            ->active()
            ->where('consent_type', $type)
            ->latest('granted_at')
            ->first();
    }

    public function revoke(): void
    {
        if ($this->revoked_at === null) {
            $this->update(['revoked_at' => now()]);
        }
    }
}
