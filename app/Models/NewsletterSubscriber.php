<?php

namespace App\Models;

use App\Enums\NewsletterStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class NewsletterSubscriber extends Model
{
    use Notifiable;

    /** @var list<string> */
    protected $fillable = [
        'email',
        'name',
        'locale',
        'country',
        'platform',
        'source',
        'consent_text',
        'consented_at',
        'consent_ip',
        'status',
        'confirmed_at',
        'unsubscribed_at',
        'resend_contact_id',
        'utm_source',
        'utm_medium',
        'utm_campaign',
        'utm_content',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => NewsletterStatus::class,
            'consented_at' => 'datetime',
            'confirmed_at' => 'datetime',
            'unsubscribed_at' => 'datetime',
        ];
    }

    /**
     * Route notifications for the mail channel.
     */
    public function routeNotificationForMail(): string
    {
        return $this->email;
    }

    public function isConfirmed(): bool
    {
        return $this->status === NewsletterStatus::Confirmed;
    }
}
