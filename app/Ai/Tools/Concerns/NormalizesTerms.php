<?php

namespace App\Ai\Tools\Concerns;

/**
 * Shared normalization for the profile-preference tools (dislikes, limitations,
 * focus areas): lowercase, trim, cap length, drop empties/duplicates, and
 * optionally keep only an allowed set. Keeps the tools consistent with the HTTP
 * profile validation (per-item max length) in one place.
 */
trait NormalizesTerms
{
    /**
     * @param  list<string>|null  $allowed  when set, only these values survive
     * @return list<string>
     */
    protected function normalizeTerms(mixed $terms, ?array $allowed = null, int $maxLength = 100): array
    {
        return collect(is_array($terms) ? $terms : [])
            ->map(fn ($term) => mb_substr(mb_strtolower(trim((string) $term)), 0, $maxLength))
            ->filter(fn (string $term) => $term !== '' && ($allowed === null || in_array($term, $allowed, true)))
            ->unique()
            ->values()
            ->all();
    }

    /**
     * @param  list<string>  $a
     * @param  list<string>  $b
     */
    protected function sameTerms(array $a, array $b): bool
    {
        return array_diff($a, $b) === [] && array_diff($b, $a) === [];
    }
}
