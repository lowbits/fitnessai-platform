<?php

namespace App\ValueObjects;

/**
 * Immutable result of evaluating whether a plan day is a "perfect day": the
 * calorie goal was met and — unless a rest day — the workout was done.
 */
final readonly class DayCompletion
{
    public function __construct(
        public bool $nutritionMet,
        public bool $workoutDone,
        public bool $isPerfect,
    ) {}
}
