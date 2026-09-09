<?php

namespace App\Support\PlanEvaluator;

/**
 * One scored dimension of a plan: a stable key, a rating, and the raw context
 * behind it (e.g. which groups are missing). Presentation (label, evidence text)
 * lives in lang files; the verdict prose is voiced by the agent from the context.
 */
readonly class Dimension
{
    /**
     * @param  array<string, mixed>  $context
     */
    public function __construct(
        public string $key,
        public Rating $rating,
        public array $context = [],
    ) {}
}
