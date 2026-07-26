<?php

namespace App\Actions\Auth;

use App\Models\User;

class IssueApiToken
{
    public function execute(User $user, ?string $deviceName): string
    {
        $deviceName = $deviceName ?: 'mobile';

        $user->tokens()->where('name', $deviceName)->delete();

        return $user->createToken($deviceName)->plainTextToken;
    }
}
