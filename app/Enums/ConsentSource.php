<?php

namespace App\Enums;

enum ConsentSource: string
{
    case Onboarding = 'onboarding';
    case UpdateModal = 'update_modal';
    case Settings = 'settings';
}
