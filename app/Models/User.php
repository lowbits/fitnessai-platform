<?php

namespace App\Models;

use App\Enums\UserSource;
use Database\Factories\UserFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Contracts\Translation\HasLocalePreference;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Password;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Sanctum\HasApiTokens;
use NoopStudios\LaravelRevenueCat\Concerns\Billable;
use NotificationChannels\Expo\ExpoPushToken;

class User extends Authenticatable implements HasLocalePreference, MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use Billable, HasApiTokens, HasFactory, Notifiable, TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'locale',
        'source',
        'trial_ends_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'two_factor_confirmed_at' => 'datetime',
            'source' => UserSource::class,
            'trial_ends_at' => 'datetime',
        ];
    }

    /**
     * Route notifications for the Expo channel.
     * Returns all device tokens for multicasting.
     *
     * @return Collection<int, ExpoPushToken>
     */
    public function routeNotificationForExpo(): Collection
    {
        return $this->devices->pluck('expo_push_token');
    }

    public function profile(): HasOne
    {
        return $this->hasOne(UserProfile::class);
    }

    public function plans(): HasMany
    {
        return $this->hasMany(Plan::class);
    }

    public function workoutTrackings(): HasMany
    {
        return $this->hasMany(WorkoutTracking::class);
    }

    public function calorieTrackings(): HasMany
    {
        return $this->hasMany(CalorieTracking::class);
    }

    public function bodyProgress(): HasMany
    {
        return $this->hasMany(BodyProgress::class);
    }

    public function devices(): HasMany
    {
        return $this->hasMany(UserDevice::class);
    }

    public function recipeFavorites(): HasMany
    {
        return $this->hasMany(RecipeFavorite::class);
    }

    public function favoriteRecipes(): BelongsToMany
    {
        return $this->belongsToMany(Recipe::class, 'recipe_favorites');
    }

    public function legacySubscription(): HasOne
    {
        return $this->hasOne(SubscriptionLegacy::class)->latestOfMany();
    }

    public function legacySubscriptions(): HasMany
    {
        return $this->hasMany(SubscriptionLegacy::class);
    }

    public function hasActiveLegacySubscription(): bool
    {
        return $this->legacySubscription && $this->legacySubscription->isActive();
    }

    public function preferredLocale()
    {
        return $this->locale ?? 'en';
    }

    public function getTimezone(): string
    {
        return match ($this->locale) {
            'de' => 'Europe/Berlin',
            default => 'UTC',
        };
    }

    /**
     * Get the current weight from the latest body progress entry or user profile.
     */
    public function getCurrentWeight(): ?float
    {
        return once(fn () => $this->bodyProgress()
            ->latest('recorded_at')
            ->value('weight_kg')
            ?? $this->profile?->weight_kg
        );
    }

    public function getPasswordResetToken(): string
    {
        $cacheKey = "password_reset_token:{$this->id}";

        return Cache::remember($cacheKey, now()->addHours(24), function () {
            return Password::broker(config('fortify.passwords'))
                ->createToken($this);
        });
    }

    public function isOnFreeTrial(): bool
    {
        return $this->trial_ends_at !== null && $this->trial_ends_at->isFuture();
    }

    public function hasPaidSubscription(): bool
    {
        if ($this->subscriptions()->whereIn('status', ['active', 'trial'])->exists()) {
            return true;
        }

        if ($this->legacySubscription?->isActive()) {
            return true;
        }

        return false;
    }

    public function hasActiveSubscription(): bool
    {
        return $this->isOnFreeTrial() || $this->hasPaidSubscription();
    }

    public function getSubscriptionDetails(): array
    {
        if ($rc = $this->subscriptions()->where('status', 'active')->first()) {
            return [
                'has_active_subscription' => true,
                'tier' => $rc->product_id,
                'status' => $rc->status,
                'started_at' => $rc->created_at,
                'expires_at' => $rc->current_period_ended_at,
                'source' => 'revenuecat',
            ];
        }

        if ($this->legacySubscription?->isActive()) {
            return [
                'has_active_subscription' => true,
                'tier' => __($this->legacySubscription->type),
                'status' => 'active',
                'started_at' => $this->legacySubscription->starts_at,
                'expires_at' => $this->legacySubscription->ends_at,
                'source' => 'legacy',
            ];
        }

        if ($this->isOnFreeTrial()) {
            return [
                'has_active_subscription' => true,
                'tier' => 'trial',
                'status' => 'trial',
                'started_at' => $this->created_at,
                'expires_at' => $this->trial_ends_at,
                'source' => 'app_trial',
            ];
        }

        return [
            'has_active_subscription' => false,
            'tier' => 'free',
            'status' => 'free',
            'started_at' => $this->created_at,
            'expires_at' => $this->plans()->where('status', 'active')->first()?->end_date,
            'source' => 'free',
        ];
    }
}
