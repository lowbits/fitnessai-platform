<?php

namespace App\Enums;

enum UserSource: string
{
    case WEB = 'web';
    case MOBILE_APPLE = 'mobile_apple';
    case MOBILE_ANDROID = 'mobile_android';

    public function label(): string
    {
        return __('enums.userSource.' . $this->value);
    }

    public function icon(): string
    {
        return match($this) {
            self::WEB => '💻',
            self::MOBILE_APPLE => '📱',
            self::MOBILE_ANDROID => '📱',
        };
    }
}
