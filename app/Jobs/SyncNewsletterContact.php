<?php

namespace App\Jobs;

use App\Contracts\NewsletterContactSync;
use App\Models\NewsletterSubscriber;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SyncNewsletterContact implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public int $timeout = 15;

    public function __construct(public NewsletterSubscriber $subscriber) {}

    public function handle(NewsletterContactSync $sync): void
    {
        $sync->sync($this->subscriber);
    }
}
