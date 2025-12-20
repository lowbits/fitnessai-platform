<?php

// app/Enums/SkillLevel.php
namespace App\Enums;

/**
 * Skill Level Enum
 *
 * Represents user's training experience level.
 * Used to adjust workout complexity, volume, and progression rate.
 */
enum SkillLevel: string
{
    case BEGINNER = 'beginner';
    case INTERMEDIATE = 'intermediate';
    case ADVANCED = 'advanced';
    case ELITE = 'elite';

    /**
     * Get human-readable label for frontend display.
     */
    public function label(): string
    {
        return match($this) {
            self::BEGINNER => 'Beginner',
            self::INTERMEDIATE => 'Intermediate',
            self::ADVANCED => 'Advanced',
            self::ELITE => 'Elite',
        };
    }

    /**
     * Get detailed description of this skill level.
     */
    public function description(): string
    {
        return match($this) {
            self::BEGINNER => 'New to fitness and nutrition or have limited experience. No solid understanding of basic exercise principles or nutrition concepts.',
            self::INTERMEDIATE => 'Some experience with fitness and nutrition. Basic understanding of exercise principles and nutrition concepts, but may need guidance to progress.',
            self::ADVANCED => 'Extended training experience with focus on nutrition. Good understanding of exercise principles and nutrition concepts, looking for specific guidance to achieve goals.',
            self::ELITE => 'Highly experienced, may have competed in fitness or bodybuilding competitions. Deep understanding of exercise principles and nutrition concepts, seeking very specific guidance for next-level performance.',
        };
    }

    /**
     * Get recommended training duration in months.
     */
    public function experienceMonths(): string
    {
        return match($this) {
            self::BEGINNER => '0-6 months',
            self::INTERMEDIATE => '6-24 months',
            self::ADVANCED => '24-60 months',
            self::ELITE => '60+ months',
        };
    }

    /**
     * Get volume multiplier for workout intensity.
     * Used to scale sets/reps based on skill level.
     */
    public function volumeMultiplier(): float
    {
        return match($this) {
            self::BEGINNER => 1.0,     // Base volume
            self::INTERMEDIATE => 1.2, // 20% more volume
            self::ADVANCED => 1.4,     // 40% more volume
            self::ELITE => 1.6,        // 60% more volume
        };
    }

    /**
     * Get recommended exercises per workout session.
     */
    public function exercisesPerWorkout(): int
    {
        return match($this) {
            self::BEGINNER => 5,      // Fewer exercises, focus on form
            self::INTERMEDIATE => 7,  // More variety
            self::ADVANCED => 9,      // Higher volume
            self::ELITE => 12,        // Maximum variety and volume
        };
    }

    /**
     * Determine if this level requires coach supervision.
     */
    public function requiresSupervision(): bool
    {
        return match($this) {
            self::BEGINNER => true,      // Needs guidance for form
            self::INTERMEDIATE => false, // Can train independently
            self::ADVANCED => false,     // Experienced
            self::ELITE => false,        // Expert level
        };
    }
}
