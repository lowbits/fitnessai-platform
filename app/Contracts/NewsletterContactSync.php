<?php

namespace App\Contracts;

use App\Models\NewsletterSubscriber;

interface NewsletterContactSync
{
    public function sync(NewsletterSubscriber $subscriber): void;
}
