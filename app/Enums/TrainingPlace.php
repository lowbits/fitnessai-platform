<?php

namespace App\Enums;

/**
 * Training Place Enum
 *
 * Represents where the user prefers to work out.
 * Used to generate appropriate workout plans with available equipment.
 */
enum TrainingPlace: string
{
    case GYM = 'gym';
    case HOME = 'home';
    case OUTDOOR = 'outdoor';
    case NO_PREFERENCE = 'no_preference';

    /**
     * Get human-readable label for frontend display.
     */
    public function label(): string
    {
        return match ($this) {
            self::GYM => 'Gym',
            self::HOME => 'Home',
            self::OUTDOOR => 'Outdoor',
            self::NO_PREFERENCE => 'No Preference',
        };
    }

    /**
     * Get detailed description of this training place.
     */
    public function description(): string
    {
        return match ($this) {
            self::GYM => 'Access to full gym equipment (barbells, dumbbells, machines, cables)',
            self::HOME => 'Limited equipment at home (bodyweight, dumbbells, resistance bands)',
            self::OUTDOOR => 'Parks, trails, or outdoor spaces (bodyweight, minimal equipment)',
            self::NO_PREFERENCE => 'Flexible - can adapt to any environment',
        };
    }


    /**
     * Get list of available equipment for this training place.
     *
     * @return array<string>
     */
    public function availableEquipment(): array
    {
        return match ($this) {
            self::GYM => [
                'barbell',
                'dumbbells',
                'cable_machine',
                'leg_press',
                'bench',
                'squat_rack',
                'pull_up_bar',
                'machines',
                'kettlebells',
                'resistance_bands',
            ],
            self::HOME => [
                'dumbbells',
                'resistance_bands',
                'bodyweight',
                'pull_up_bar',
                'yoga_mat',
            ],
            self::OUTDOOR => [
                'bodyweight',
                'pull_up_bar',
                'park_bench',
                'stairs',
                'resistance_bands',
            ],
            self::NO_PREFERENCE => [
                'bodyweight',
                'dumbbells',
                'resistance_bands',
                'barbell',
                'machines',
            ],
        };
    }

    /**
     * Determine if this location supports heavy compound lifts.
     */
    public function supportsCompoundLifts(): bool
    {
        return match ($this) {
            self::GYM, self::NO_PREFERENCE => true,
            self::HOME, self::OUTDOOR => false,
        };
    }

    /**
     * Get recommended workout duration adjustment.
     * Home workouts might be shorter due to limited equipment.
     *
     * @return float Multiplier for workout duration
     */
    public function durationMultiplier(): float
    {
        return match ($this) {
            self::GYM, self::NO_PREFERENCE => 1.0,         // Full workouts
            self::HOME => 0.85,       // 15% shorter (less equipment variation)
            self::OUTDOOR => 0.9, // 10% shorter (less equipment)
        };
    }

    /**
     * Determine if cardio/running is emphasized for this location.
     */
    public function emphasizesCardio(): bool
    {
        return match ($this) {
            self::GYM => false,
            self::HOME => false,
            self::OUTDOOR => true,   // Running, sprints, trails
            self::NO_PREFERENCE => false,
        };
    }


    /**
     * Get workout style for this location.
     */
    public function workoutStyle(): string
    {
        return match ($this) {
            self::GYM => 'Traditional strength training',
            self::HOME => 'Functional bodyweight training',
            self::OUTDOOR => 'Calisthenics & cardio',
            self::NO_PREFERENCE => 'Mixed training styles',
        };
    }
}
