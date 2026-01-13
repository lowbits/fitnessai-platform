<?php

namespace App\Events\RevenueCat;

use App\Models\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class InitialPurchaseProcessed
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public User $user,
        public array $event
    ) {}
}
