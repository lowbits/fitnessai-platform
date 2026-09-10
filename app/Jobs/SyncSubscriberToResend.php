<?php

namespace App\Jobs;

use App\Models\NewsletterSubscriber;
use App\Services\NewsletterService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SyncSubscriberToResend implements ShouldQueue
{
    use Queueable;

    public function __construct(public NewsletterSubscriber $subscriber) {}

    public function handle(NewsletterService $newsletter): void
    {
        $newsletter->syncToResend($this->subscriber);
    }
}
