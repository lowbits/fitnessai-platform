<?php

namespace Database\Factories;

use App\Enums\ConsentSource;
use App\Enums\ConsentType;
use App\Models\User;
use App\Models\UserConsent;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<UserConsent>
 */
class UserConsentFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'consent_type' => ConsentType::AiProcessing,
            'version' => '2026-08-27',
            'granted_at' => now(),
            'revoked_at' => null,
            'source' => ConsentSource::Onboarding,
            'locale' => 'de',
        ];
    }

    public function revoked(): static
    {
        return $this->state(['revoked_at' => now()]);
    }
}
