<?php

namespace App\Ai\Prompts;

use App\Models\UserProfile;
use Stringable;

/**
 * Dynamic user-specific context for each workout generation.
 * Goes into messages() — small token footprint, changes per call.
 */
class WorkoutUserPrompt implements Stringable
{
    private int $totalDays;

    public function __construct(
        private UserProfile $profile,
        private string $locale,
        private int $dayNumber,
        private int $workoutsPerWeek,
        private array $recentWorkouts = [],
    ) {
        $this->totalDays = config('plans.duration_days');
    }

    public function __toString(): string
    {
        $workoutNumber = $this->getWorkoutNumberInCycle();
        $language = $this->locale === 'de' ? 'German' : 'English';

        $parts = [
            $this->buildProfile(),
            $this->buildContext($workoutNumber),
            $this->buildGoalProtocol(),
            $this->buildStructure(),
            $this->buildWeightRec(),
            $this->buildTrainingEnvironment(),
            $this->buildGenderNote(),
            "Language: {$language}",
            $this->buildRecentWorkouts(),
            'Create this workout now. Use MeilisearchSimilaritySearch for all exercises, then call saveWorkoutPlan.',
        ];

        return implode("\n\n", array_filter($parts));
    }

    private function buildProfile(): string
    {
        $p = $this->profile;

        return implode("\n", [
            "Age: {$p->age} | Gender: {$p->gender->value} | Level: {$p->skill_level->value}",
            "Goal: {$p->body_goal->value} | Place: {$p->training_place->value} | Activity: {$p->activity_level->value}",
            "Sessions/week: {$p->training_sessions_per_week}",
        ]);
    }

    private function buildContext(int $workoutNumber): string
    {
        $split = $this->getWorkoutSplit();
        $focus = $this->getTargetMuscleGroups($workoutNumber);

        return implode("\n", [
            "Day {$this->dayNumber}/{$this->totalDays} | Workout {$workoutNumber}/{$this->workoutsPerWeek}",
            "Split: {$split} | Today: {$focus}",
        ]);
    }

    private function buildGenderNote(): string
    {
        if ($this->profile->gender->value !== 'female') {
            return '';
        }

        return 'Note: Favor glute-focused lower body variations (hip thrust > leg extension). Include posterior chain emphasis.';
    }

    private function buildRecentWorkouts(): string
    {
        if (empty($this->recentWorkouts)) {
            return '';
        }

        $list = implode("\n", array_map(fn ($w) => "- {$w}", $this->recentWorkouts));

        return "Recent workouts (vary accessories, compounds may repeat):\n{$list}";
    }

    // =========================================================================
    // Split & Target Logic (unchanged, just compact)
    // =========================================================================

    private function getWorkoutSplit(): string
    {
        return match ($this->profile->training_sessions_per_week) {
            2 => 'Upper/Lower',
            3 => 'Push/Pull/Legs',
            4 => 'Upper/Lower/Upper/Lower',
            5 => 'Push/Pull/Legs/Upper/Lower',
            6 => 'PPL/PPL',
            7 => 'Daily Specialization',
            default => 'Full Body',
        };
    }

    private function buildGoalProtocol(): string
    {
        return match ($this->profile->body_goal->resolveCanonical()->value) {
            'build_muscle' => 'Protocol: 3-4 sets, 8-12 reps, 60-90s rest, tempo 3-0-1-0/2-0-1-0, RPE 7-9. 70% compound, 30% isolation. Slow eccentrics. Last set RPE 9.',
            'lose_weight' => 'Protocol: 3 sets, 12-15 reps, 30-45s rest, tempo 2-0-1-0, RPE 6-8. Circuit-style, maintain heart rate. Include HIIT intervals.',
            'get_fit' => 'Protocol: 3 sets, 8-12 reps, 60-90s rest, tempo 2-0-1-0, RPE 6-7. Balanced compound/isolation. Moderate intensity.',
            default => 'Protocol: 3 sets, 10-12 reps, 60s rest, tempo 2-0-1-0, RPE 6-8.',
        };
    }

    private function buildStructure(): string
    {
        $level = $this->profile->skill_level->value;
        $goal = $this->profile->body_goal->value;

        if ($level === 'beginner') {
            return 'Structure: Straight sets only. No supersets/circuits. Focus on form and confidence.';
        }

        $canonicalGoal = $this->profile->body_goal->resolveCanonical()->value;

        return match ($canonicalGoal) {
            'build_muscle' => 'Structure: Straight sets for compounds → superset antagonist isolation → burnout set.',
            'lose_weight' => 'Structure: Circuit/paired exercises, upper/lower alternating, HIIT between blocks.',
            'get_fit' => 'Structure: Straight sets for compounds, optional supersets for isolation.',
            default => 'Structure: Straight sets for compounds, supersets optional for isolation.',
        };
    }

    private function buildWeightRec(): string
    {
        return match ($this->profile->skill_level->value) {
            'beginner' => 'Weight: Qualitative only — "Light"/"Moderate"/"Challenging". Never reference 1RM.',
            'intermediate' => 'Weight: Qualitative with context — "Moderate — 2-3 reps in reserve".',
            'advanced' => 'Weight: Main lifts use %1RM (3-6 reps → 80-90%, 8-12 → 65-80%). Isolation uses qualitative.',
            default => 'Weight: Qualitative — "Light"/"Moderate"/"Challenging".',
        };
    }

    private function buildTrainingEnvironment(): string
    {
        return match ($this->profile->training_place->value) {
            'gym' => 'Environment: Gym. Full equipment. Prioritize free weights for main lifts, cables for isolation, machines for burnout.',
            'home' => 'Environment: Home. Bodyweight, bands, light dumbbells only. Use tempo manipulation for difficulty. Never prescribe barbells.',
            'outdoor' => 'Environment: Outdoor. Calisthenics, park infrastructure, plyometrics. Never prescribe gym machines.',
            default => 'Environment: General. Adapt to available equipment, prioritize bodyweight.',
        };
    }

    private function getWorkoutNumberInCycle(): int
    {
        return (($this->dayNumber - 1) % $this->workoutsPerWeek) + 1;
    }

    private function getTargetMuscleGroups(int $workoutNumber): string
    {
        return match ($this->workoutsPerWeek) {
            2 => match ($workoutNumber) {
                1 => 'Upper Body (chest, back, shoulders, biceps, triceps)',
                2 => 'Lower Body (quadriceps, hamstrings, glutes, calves)',
                default => 'Full Body (full_body)',
            },
            3 => match ($workoutNumber) {
                1 => 'Push (chest, shoulders, triceps)',
                2 => 'Pull (back, biceps, rear_delts)',
                3 => 'Legs & Core (quadriceps, hamstrings, glutes, calves, core)',
                default => 'Full Body (full_body)',
            },
            4 => match ($workoutNumber) {
                1 => 'Upper A (chest, back, shoulders)',
                2 => 'Lower A (quadriceps, hamstrings, glutes)',
                3 => 'Upper B (back, biceps, triceps, rear_delts)',
                4 => 'Lower B (glutes, hamstrings, calves)',
                default => 'Full Body (full_body)',
            },
            5 => match ($workoutNumber) {
                1 => 'Push (chest, shoulders, triceps)',
                2 => 'Pull (back, biceps, rear_delts)',
                3 => 'Legs (quadriceps, hamstrings, glutes, calves)',
                4 => 'Upper Body (chest, back, shoulders)',
                5 => 'Lower Body (glutes, hamstrings, calves)',
                default => 'Full Body (full_body)',
            },
            6 => match ($workoutNumber) {
                1 => 'Push A (chest, shoulders, triceps)',
                2 => 'Pull A (back, biceps, rear_delts)',
                3 => 'Legs A (quadriceps, hamstrings, glutes)',
                4 => 'Push B (shoulders, chest, triceps)',
                5 => 'Pull B (back, biceps, rear_delts)',
                6 => 'Legs B (hamstrings, glutes, calves)',
                default => 'Full Body (full_body)',
            },
            default => 'Full Body (full_body)',
        };
    }
}
