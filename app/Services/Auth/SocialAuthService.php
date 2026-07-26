<?php

namespace App\Services\Auth;

use App\DataTransferObjects\SocialUserData;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Laravel\Socialite\Facades\Socialite;

class SocialAuthService
{
    private const SUPPORTED = ['google', 'apple'];

    public function verify(string $provider, string $token): SocialUserData
    {
        if (! in_array($provider, self::SUPPORTED, true)) {
            throw ValidationException::withMessages([
                'auth.type' => ['Unsupported social provider.'],
            ]);
        }

        try {
            $user = Socialite::driver($provider)->stateless()->userFromToken($token);
        } catch (\Throwable $e) {
            Log::warning('[SocialAuth] Token verification failed', [
                'provider' => $provider,
                'exception' => $e::class,
                'message' => $e->getMessage(),
            ]);

            throw ValidationException::withMessages([
                'auth.token' => ['The social sign-in could not be verified.'],
            ]);
        }

        return SocialUserData::fromSocialite($provider, $user);
    }
}
