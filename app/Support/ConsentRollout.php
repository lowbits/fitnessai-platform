<?php

namespace App\Support;

final class ConsentRollout
{
    /**
     * Whether a client of the given app version runs the consent flow and will
     * therefore trigger plan generation itself via the consent grant. Older
     * clients (or none reported) keep generating at signup so they are never
     * left plan-less.
     *
     * TODO(consent-rollout): temporary. Remove once every client reports a
     * version >= consent.min_app_version and generation always waits for consent.
     */
    public static function clientCollectsConsent(?string $appVersion): bool
    {
        if ($appVersion === null) {
            return false;
        }

        return version_compare($appVersion, config('consent.min_app_version'), '>=');
    }
}
