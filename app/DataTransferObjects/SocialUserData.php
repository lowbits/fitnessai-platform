<?php

namespace App\DataTransferObjects;

use Laravel\Socialite\Contracts\User as SocialiteUser;

final readonly class SocialUserData
{
    public function __construct(
        public string $provider,
        public string $providerId,
        public ?string $email,
        public ?string $name,
        public ?string $avatar,
    ) {}

    public static function fromSocialite(string $provider, SocialiteUser $user): self
    {
        return new self(
            provider: $provider,
            providerId: $user->getId(),
            email: $user->getEmail(),
            name: $user->getName() ?: $user->getNickname(),
            avatar: $user->getAvatar(),
        );
    }
}
