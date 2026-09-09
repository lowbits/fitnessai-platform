<?php

namespace App\Ai\Support;

use App\Models\UserProfile;

/**
 * The single source of truth for a user's training focus areas as prompt text,
 * so any agent that talks about or generates training folds in the muscle
 * groups they want emphasized.
 */
final class FocusAreas
{
    public static function forProfile(?UserProfile $profile): string
    {
        $areas = $profile?->focus_areas ?? [];

        if (empty($areas)) {
            return '';
        }

        return 'Training focus areas they want emphasized: '.implode(', ', $areas).'.';
    }
}
