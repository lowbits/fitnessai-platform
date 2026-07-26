<?php

namespace App\Actions\Auth;

use App\DataTransferObjects\SocialUserData;
use App\Enums\UserSource;
use App\Models\User;
use App\Services\Auth\SocialAuthService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class ResolveAccountFromCredential
{
    public function __construct(private readonly SocialAuthService $social) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public function forSignup(array $data): User
    {
        $auth = $data['auth'];

        if ($auth['type'] === 'password') {
            return User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($auth['password']),
                'locale' => $data['locale'] ?? 'en',
                'source' => $data['source'] ?? UserSource::MOBILE_APPLE,
            ]);
        }

        $social = $this->social->verify($auth['type'], $auth['token']);

        if ($this->socialAccountExists($social)) {
            throw ValidationException::withMessages([
                'auth' => ['An account with this email already exists. Please log in instead.'],
            ]);
        }

        $user = User::create([
            'name' => $social->name ?: ($data['name'] ?? $this->fallbackName($social)),
            'email' => $social->email,
            'provider' => $social->provider,
            'provider_id' => $social->providerId,
            'avatar' => $social->avatar,
            'locale' => $data['locale'] ?? 'en',
            'source' => $data['source'] ?? UserSource::MOBILE_APPLE,
        ]);

        $user->markEmailAsVerified();

        return $user;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function forLogin(array $data): User
    {
        $auth = $data['auth'];

        if ($auth['type'] === 'password') {
            $user = User::where('email', $data['email'])->first();

            if (! $user || ! Auth::attempt(['email' => $data['email'], 'password' => $auth['password']])) {
                throw ValidationException::withMessages([
                    'email' => ['The provided credentials are incorrect.'],
                ]);
            }

            return $user;
        }

        $social = $this->social->verify($auth['type'], $auth['token']);

        return $this->linkExistingSocial($social) ?? throw ValidationException::withMessages([
            'auth' => ['No account found for this sign-in. Please sign up first.'],
        ]);
    }

    private function linkExistingSocial(SocialUserData $social): ?User
    {
        $user = User::where('provider', $social->provider)
            ->where('provider_id', $social->providerId)
            ->first();

        if ($user) {
            return $user;
        }

        if ($social->email) {
            $user = User::where('email', $social->email)->first();

            if ($user) {
                $user->forceFill([
                    'provider' => $social->provider,
                    'provider_id' => $social->providerId,
                    'avatar' => $user->avatar ?? $social->avatar,
                    'email_verified_at' => $user->email_verified_at ?? now(),
                ])->save();

                return $user;
            }
        }

        return null;
    }

    private function socialAccountExists(SocialUserData $social): bool
    {
        $byProvider = User::where('provider', $social->provider)
            ->where('provider_id', $social->providerId)
            ->exists();

        if ($byProvider) {
            return true;
        }

        return $social->email !== null
            && User::where('email', $social->email)->exists();
    }

    private function fallbackName(SocialUserData $social): string
    {
        return Str::of((string) $social->email)->before('@')->trim()->value() ?: 'fytrr';
    }
}
