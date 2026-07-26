<?php

namespace App\Listeners;

use SocialiteProviders\Apple\Provider as AppleProvider;
use SocialiteProviders\Manager\SocialiteWasCalled;

class RegisterSocialiteProviders
{
    public function handle(SocialiteWasCalled $event): void
    {
        $event->extendSocialite('apple', AppleProvider::class);
    }
}
