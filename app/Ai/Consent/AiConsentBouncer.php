<?php

namespace App\Ai\Consent;

use App\Enums\ConsentType;
use App\Models\User;
use App\Models\UserConsent;

/**
 * Single gate for any user data leaving fytrr for OpenAI/Mistral. A user is
 * only cleared once they hold an active consent for the current version — an
 * older active consent no longer counts once the copy/version changes.
 *
 * Enforcement is behind config('consent.enforce'). Until the consent-collecting
 * app version is live and adopted, the gate stays a no-op so existing users
 * (who have no consent row yet) keep working. Flip CONSENT_ENFORCE=true to arm.
 */
final class AiConsentBouncer
{
    public static function granted(User $user): bool
    {
        return UserConsent::activeFor($user, ConsentType::AiProcessing)?->version
            === config('consent.current_version');
    }

    public static function permits(User $user): bool
    {
        return ! self::enforced() || self::granted($user);
    }

    /**
     * @throws ConsentRequiredException
     */
    public static function ensure(User $user): void
    {
        if (! self::permits($user)) {
            throw new ConsentRequiredException;
        }
    }

    private static function enforced(): bool
    {
        return (bool) config('consent.enforce', false);
    }
}
