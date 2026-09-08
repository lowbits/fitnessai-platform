<?php

namespace App\Enums;

enum NewsletterStatus: string
{
    case Pending = 'pending';
    case Confirmed = 'confirmed';
    case Unsubscribed = 'unsubscribed';
}
