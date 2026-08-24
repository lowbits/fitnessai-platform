<?php

namespace App\Ai\Support;

use App\Models\UserProfile;

/**
 * The single source of truth for a user's physical limitations as prompt text.
 * Any agent or tool that suggests training, exercises or rescheduling must fold
 * this in so we never recommend movement that risks an injury.
 */
final class PhysicalLimitations
{
    public static function forProfile(?UserProfile $profile): string
    {
        if (! $profile) {
            return '';
        }

        $areas = $profile->physical_limitations ?? [];
        $note = trim((string) ($profile->physical_limitations_note ?? ''));

        if (empty($areas) && $note === '') {
            return '';
        }

        $parts = [];

        if (! empty($areas)) {
            $parts[] = 'affected areas: '.implode(', ', $areas);
        }

        if ($note !== '') {
            $parts[] = "their note: {$note}";
        }

        return 'HARD CONSTRAINT — physical limitations ('.implode('; ', $parts).'). Never suggest '
            .'training, exercises or rescheduling that loads or risks these areas; adapt movement '
            .'advice around them and offer safer alternatives when relevant.';
    }
}
