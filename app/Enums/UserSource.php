<?php

namespace App\Enums;

enum UserSource: string
{
    case WEB = 'web';
    case MOBILE_APPLE = 'mobile_apple';

    public function label(): string
    {
        return __('enums.userSource.' . $this->value);
    }
}
