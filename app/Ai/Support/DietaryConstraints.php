<?php

namespace App\Ai\Support;

use App\Models\UserProfile;

/**
 * The single source of truth for a user's dietary constraints as prompt text.
 * Any agent, tool or prompt that recommends, logs or generates food must fold
 * this in so we never suggest something that breaks their diet or dislikes.
 */
final class DietaryConstraints
{
    public static function forProfile(?UserProfile $profile): string
    {
        if (! $profile) {
            return '';
        }

        $parts = [];

        if (($diet = $profile->dietary_preference?->value) && $diet !== 'omnivore') {
            $parts[] = "They eat {$diet} — never recommend, log or suggest anything that violates this.";
        }

        if ($style = $profile->diet_style?->value) {
            $parts[] = "Preferred diet style: {$style}.";
        }

        if (! empty($dislikes = $profile->food_dislikes ?? [])) {
            $list = implode(', ', $dislikes);
            $parts[] = "HARD CONSTRAINT — disliked ingredients: {$list}. Never include or recommend a "
                .'dish that contains any of these (case-insensitive, including compound words like '
                ."'apfel' blocking 'Apfelmus').";
        }

        return implode(' ', $parts);
    }
}
