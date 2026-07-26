<?php

namespace App\Enums;

use Carbon\CarbonImmutable;

/**
 * A user's entitlement to a given plan day — the "access" axis, kept separate
 * from generation readiness (see {@see DayGenerationStatus}).
 */
enum DayAccess: string
{
    case Full = 'full';           // owns the day (day 1, or an active subscription)
    case Preview = 'preview';     // within plan, day >= 2, no sub — teaser + paywall
    case Expired = 'expired';     // beyond the plan — upgrade to unlock more days
    case BeforeStart = 'before_start'; // date precedes plan start

    public static function forDate(
        CarbonImmutable $date,
        CarbonImmutable $start,
        CarbonImmutable $lastDay,
        int $previewDays,
        bool $hasActiveSubscription,
    ): self {
        $withinPreview = $date->lessThan($start->addDays($previewDays));

        return match (true) {
            $date->lessThan($start) => self::BeforeStart,
            $date->greaterThan($lastDay) => self::Expired,
            $withinPreview || $hasActiveSubscription => self::Full,
            default => self::Preview,
        };
    }

    /** Whether the day has real meal/workout content to load and render. */
    public function hasContent(): bool
    {
        return $this === self::Full || $this === self::Preview;
    }
}
