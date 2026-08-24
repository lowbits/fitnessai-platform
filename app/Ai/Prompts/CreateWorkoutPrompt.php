<?php

namespace App\Ai\Prompts;

use App\Ai\Support\PhysicalLimitations;
use App\Models\UserProfile;
use Stringable;

class CreateWorkoutPrompt implements Stringable
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
        $age = $this->profile->age;
        $gender = $this->profile->gender->value;
        $bodyGoal = $this->profile->body_goal->value;
        $skillLevel = $this->profile->skill_level->value;
        $trainingPlace = $this->profile->training_place->value;
        $activityLevel = $this->profile->activity_level->value;
        $sessionsPerWeek = $this->profile->training_sessions_per_week;
        $workoutSplit = $this->getWorkoutSplit();
        $language = $this->getLanguageInstruction();
        $exerciseCount = $this->getExerciseCount();
        $durationGuideline = $this->getDurationGuideline();

        $workoutDayContext = $this->getWorkoutDayContext();
        $genderContext = $this->getGenderContext();
        $trainingPlaceContext = $this->getTrainingPlaceContext();
        $goalGuidelines = $this->getGoalGuidelines();
        $weightRecommendation = $this->getWeightRecommendation();
        $structureGuideline = $this->getStructureGuideline();
        $warmupGuideline = $this->getWarmupGuideline();
        $recentWorkoutsSection = $this->getRecentWorkoutsSection();

        $limitations = PhysicalLimitations::forProfile($this->profile);
        $limitationsSection = $limitations === '' ? '' : "\n\n**INJURIES / LIMITATIONS:** {$limitations}";

        return <<<PROMPT
**User Profile:**
- Age: {$age} years
- Gender: {$gender}
- Body Goal: {$bodyGoal}
- Skill Level: {$skillLevel}
- Training Place: {$trainingPlace}
- Activity Level: {$activityLevel}
- Training Sessions per Week: {$sessionsPerWeek}
- Workout Split: {$workoutSplit}
{$genderContext}{$limitationsSection}

{$workoutDayContext}

{$trainingPlaceContext}

**Constraints:**
- Main workout: {$exerciseCount} strength/main exercises
- Order: compound movements first, isolation exercises last
- Target total duration: {$durationGuideline}
- IMPORTANT: Be conservative with duration estimates. Account for transitions between exercises, water breaks, equipment setup, and reading instructions. A workout with 6 exercises × 3 sets × 12 reps with 60s rest realistically takes 45-50 min, not 30 min.

**CRITICAL: Exercise Database Lookup**

You MUST use the MeilisearchSimilaritySearch tool to find exercises from our database. Do NOT invent exercises.

For each exercise in the workout plan:
1. Search using the MeilisearchSimilaritySearch tool
2. Use the exact `id` and `name` from the search results
3. If no match is found, search with broader terms before falling back
4. Every exercise MUST include the `exercise_id` from search results

The database contains exercises indexed as "name | description | type" (e.g. "Barbell Row | Horizontal pulling movement targeting mid-back, lats, and posterior shoulders to build thickness and pulling power. | strength").

Search strategies — phrase queries naturally to match against name, description, and type:
- "horizontal pull back thickness strength" → rowing movements
- "chest press compound barbell strength" → bench press variations
- "hip flexor stretch warmup" → warmup stretches
- "quad dominant squat strength" → squat variations
- "bodyweight push upper body" → push-up variations (home/outdoor)
- "shoulder mobility warmup activation" → shoulder warmup drills

Batch related searches to be efficient.

**Language:**
Generate descriptions, instructions, and form cues in {$language}.
Exercise names MUST match database entries exactly — do not translate.

**Workout Programming:**
{$goalGuidelines}

{$weightRecommendation}

{$structureGuideline}

{$warmupGuideline}

**Field Guidelines:**

- **workout_name**: 2-4 words, muscle groups or training focus (e.g. "Push Day", "Upper Body Power"). No day numbers, difficulty, or goals.
- **description**: 1-2 sentences on workout purpose
- **estimated_duration_minutes**: Total time including warmup and cooldown (be conservative — round up)
- **exercises**: Use exact `id` and `name` from search results
  - **instructions**: Step-by-step in {$language} (positioning, movement, breathing). 3-5 clear steps.
  - **form_cues**: 1-2 key safety/technique points in {$language}. Focus on the most common mistake for this exercise.
  - **alternatives**: 1-2 alternatives via MeilisearchSimilaritySearch (must differ from alternatives of other exercises in this workout)
  - Sets, reps, rest, tempo, execution_style, RPE, weight_recommendation: per goal protocol
{$recentWorkoutsSection}
**CRITICAL: Call the `saveWorkoutPlan` tool to submit the plan. Do NOT output as text.**
PROMPT;
    }

    // =========================================================================
    // Language & Split
    // =========================================================================

    private function getLanguageInstruction(): string
    {
        return match ($this->locale) {
            'de' => 'German',
            default => 'English',
        };
    }

    private function getWorkoutSplit(): string
    {
        return match ($this->profile->training_sessions_per_week) {
            2 => 'Upper/Lower Split',
            3 => 'Push/Pull/Legs',
            4 => 'Upper/Lower/Upper/Lower',
            5 => 'Push/Pull/Legs/Upper/Lower',
            6 => 'Push/Pull/Legs/Push/Pull/Legs',
            7 => 'Daily Specialization',
            default => 'Full Body',
        };
    }

    // =========================================================================
    // Gender Context
    // =========================================================================

    private function getGenderContext(): string
    {
        return match ($this->profile->gender->value) {
            'female' => '- Consider typically lower upper body baseline strength when selecting exercise variations and rep ranges
- Favor glute-focused variations for lower body days (e.g. hip thrust over leg extension)
- Include posterior chain emphasis (hamstrings, glutes) for injury prevention',
            'male' => '',
            default => '',
        };
    }

    // =========================================================================
    // Workout Day Context
    // =========================================================================

    private function getWorkoutDayContext(): string
    {
        $workoutNumber = $this->getWorkoutNumberInCycle();
        $split = $this->getWorkoutSplit();
        $targetMuscles = $this->getTargetMuscleGroups($workoutNumber);
        $totalDays = $this->totalDays;
        $dayNumber = $this->dayNumber;
        $workoutsPerWeek = $this->workoutsPerWeek;

        return <<<CONTEXT
**Current Workout Context:**
- Day {$dayNumber} of {$totalDays}-day plan
- Workout {$workoutNumber} of {$workoutsPerWeek} this week
- Split: {$split}
- Today's focus: {$targetMuscles}
CONTEXT;
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
                1 => 'Upper Body A (chest, back, shoulders)',
                2 => 'Lower Body A (quadriceps, hamstrings, glutes)',
                3 => 'Upper Body B (back, biceps, triceps, rear_delts)',
                4 => 'Lower Body B (glutes, hamstrings, calves)',
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
                1 => 'Push (chest, shoulders, triceps)',
                2 => 'Pull (back, biceps, rear_delts)',
                3 => 'Legs (quadriceps, hamstrings, glutes, calves)',
                4 => 'Push (shoulders, chest, triceps)',
                5 => 'Pull (back, biceps, rear_delts)',
                6 => 'Legs (hamstrings, glutes, calves)',
                default => 'Full Body (full_body)',
            },
            default => 'Full Body (full_body)',
        };
    }

    // =========================================================================
    // Training Place Context
    // =========================================================================

    private function getTrainingPlaceContext(): string
    {
        return match ($this->profile->training_place->value) {
            'gym' => <<<'PLACE'
**Training Environment: Gym**
- Full equipment: barbells, dumbbells, cable machines, plate-loaded machines, benches, racks
- Prioritize free weights and compound barbell movements for main lifts
- Use cables for constant tension on isolation work
- Machines for safe burnout/failure sets
- Dumbbells for unilateral work and stabilizer activation
PLACE,
            'home' => <<<'PLACE'
**Training Environment: Home**
- Equipment: bodyweight, resistance bands, possibly light dumbbells
- Prefer standard bodyweight variations over weighted versions
- Prioritize bodyweight compound movements as main lifts
- Use tempo manipulation (slow eccentrics, pauses) to increase difficulty without weight
- Unilateral exercises (single-leg, single-arm) for added challenge and balance
- Higher rep ranges may be necessary to reach target RPE with limited load
- Creative use of furniture (chair dips, elevated push-ups, table rows) when appropriate
- NEVER prescribe barbell or heavy dumbbell exercises
PLACE,
            'outdoor' => <<<'PLACE'
**Training Environment: Outdoor**
- Equipment: bodyweight, park infrastructure (pull-up bars, benches, stairs, railings)
- Prioritize calisthenics and plyometric movements
- Use running/sprints for cardio components
- Incorporate hill work, stairs, or incline variations when possible
- Higher rep ranges and explosive movements to compensate for limited load
- Bodyweight progressions (e.g. archer push-ups, pistol squat progressions) for advanced
- NEVER prescribe gym machines or heavy free weight exercises
PLACE,
            default => <<<'PLACE'
**Training Environment: General**
- Adapt exercises to available equipment
- Prioritize bodyweight movements as baseline
PLACE,
        };
    }

    // =========================================================================
    // Exercise Count & Duration
    // =========================================================================

    private function getExerciseCount(): string
    {
        return match ($this->profile->skill_level->value) {
            'beginner' => '4-5',
            'intermediate' => '5-6',
            'advanced' => '6-8',
            default => '5-6',
        };
    }

    private function getDurationGuideline(): string
    {
        $level = $this->profile->skill_level->value;
        $goal = $this->profile->body_goal->value;

        // Base duration by skill level (minutes)
        $base = match ($level) {
            'beginner' => [25, 35],
            'intermediate' => [35, 45],
            'advanced' => [45, 60],
            default => [35, 45],
        };

        // Resolve deprecated goals to canonical
        $canonicalGoal = $this->profile->body_goal->resolveCanonical()->value;

        // Adjust for goal (longer rest periods = longer workout)
        $adjustment = match ($canonicalGoal) {
            'build_muscle' => [5, 10],
            'lose_weight' => [0, 5],
            'get_fit' => [5, 5],
            default => [0, 5],
        };

        $min = $base[0] + $adjustment[0];
        $max = $base[1] + $adjustment[1];

        return "{$min}-{$max} minutes including warmup and cooldown";
    }

    // =========================================================================
    // Workout Structure
    // =========================================================================

    private function getStructureGuideline(): string
    {
        $goal = $this->profile->body_goal->resolveCanonical()->value;
        $level = $this->profile->skill_level->value;

        if ($level === 'beginner') {
            return <<<'STRUCTURE'
**Workout Structure:**
- Straight sets only (complete all sets of one exercise before moving to the next)
- Rest fully between sets — no rushing
- Keep it simple — no supersets, circuits, or advanced techniques
- Focus on learning movement patterns with proper form
- Each exercise should feel manageable and confidence-building
STRUCTURE;
        }

        return match ($goal) {
            'lose_weight' => <<<'STRUCTURE'
**Workout Structure:**
- Circuit or paired exercises to maintain elevated heart rate
- Pair upper/lower body exercises for active recovery between sets
- Minimize rest between exercises within a circuit
- Include 1-2 high-intensity intervals between strength blocks
- Goal: keep heart rate in the fat-burning zone throughout
STRUCTURE,
            'build_muscle' => match ($level) {
                'intermediate' => <<<'STRUCTURE'
**Workout Structure:**
- Straight sets for main compound lifts (first 2-3 exercises)
- Superset antagonist muscles for isolation work (e.g. biceps/triceps)
- Finish with 1-2 isolation burnout sets at higher reps
- Progressive overload: prioritize adding reps before adding weight
STRUCTURE,
                'advanced' => <<<'STRUCTURE'
**Workout Structure:**
- Straight sets for primary compound lifts (first 2-3 exercises)
- Supersets for antagonist pairs (e.g. chest fly + rear delt fly)
- Drop sets or rest-pause on final isolation exercise for metabolic stress
- Build intensity progressively through the workout (RPE increases each exercise)
- Consider mechanical drop sets for bodyweight exercises (e.g. push-up → incline push-up)
STRUCTURE,
                default => <<<'STRUCTURE'
**Workout Structure:**
- Straight sets for compound movements
- Supersets allowed for isolation work
STRUCTURE,
            },
            'get_fit' => <<<'STRUCTURE'
**Workout Structure:**
- Straight sets for compound movements
- Moderate intensity throughout (RPE 6-7)
- Optional supersets for isolation work to save time
- Balanced approach — enough stimulus to maintain, not so much to overtrain
STRUCTURE,
            default => <<<'STRUCTURE'
**Workout Structure:**
- Straight sets for compound movements
- Supersets optional for isolation work
STRUCTURE,
        };
    }

    // =========================================================================
    // Warmup & Cooldown
    // =========================================================================

    private function getWarmupGuideline(): string
    {
        $workoutNumber = $this->getWorkoutNumberInCycle();
        $targetMuscles = $this->getTargetMuscleGroups($workoutNumber);
        $level = $this->profile->skill_level->value;

        [$warmupExercises, $warmupDuration] = match ($level) {
            'beginner' => ['2-3', '5-8'],
            'intermediate' => ['1-2', '3-5'],
            'advanced' => ['1-2', '3-5'],
            default => ['1-2', '3-5'],
        };

        [$cooldownExercises, $cooldownDuration] = match ($level) {
            'beginner' => ['3-4', '5-8'],
            'intermediate' => ['2-3', '3-5'],
            'advanced' => ['2-3', '3-5'],
            default => ['2-3', '3-5'],
        };

        return <<<WARMUP
**Warmup ({$warmupDuration} min, {$warmupExercises} exercises):**
- Target the muscles used today: {$targetMuscles}
- Mobility/activation exercises specific to today's focus
- Progress from light to moderate intensity
- No static stretching before strength work — use dynamic movements only
- Include at least one movement that mimics today's main lift pattern

**Cooldown ({$cooldownDuration} min, {$cooldownExercises} stretches):**
- Static stretches targeting today's worked muscles
- Hold each stretch 20-30 seconds
- Focus on muscles that were under the most tension during the workout
WARMUP;
    }

    // =========================================================================
    // Goal Guidelines
    // =========================================================================

    private function getGoalGuidelines(): string
    {
        return match ($this->profile->body_goal->resolveCanonical()->value) {
            'build_muscle' => <<<'GOAL'
**Build Muscle (Hypertrophy) Protocol:**
- Volume: 3-4 sets per exercise
- Rep range: 8-12 reps (hypertrophy zone) — vary across exercises (e.g. 8, 10, 12)
- Rest periods: 60-90 seconds between sets
- Tempo: 3-0-1-0 for compound lifts (slow eccentric for time under tension), 2-0-1-0 for isolation
- RPE: 7-9 (leave 1-3 reps in reserve on most sets)
- Exercise selection: 70% compound movements, 30% isolation
- Progressive overload: increase weight when RPE drops below 7
- Focus on mind-muscle connection and controlled eccentric phase
- Last set of each exercise can push to RPE 9 for additional stimulus
GOAL,
            'lose_weight' => <<<'GOAL'
**Lose Weight (Fat Loss) Protocol:**
- Volume: 3 sets per exercise
- Rep range: 12-15 reps — vary across exercises (e.g. 12, 15, 12)
- Rest periods: 30-45 seconds (metabolic conditioning)
- Tempo: 2-0-1-0 (controlled, steady pace)
- RPE: 6-8 (sustainable intensity — should be able to talk but not sing)
- Circuit-style training when possible to maintain heart rate
- Include HIIT or cardio intervals between strength blocks
- Prioritize compound movements for maximum calorie expenditure
- Maintain muscle mass while creating caloric deficit through activity
GOAL,
            'get_fit' => <<<'GOAL'
**General Fitness Protocol:**
- Volume: 3 sets per exercise
- Rep range: 10-12 reps
- Rest periods: 60 seconds
- Tempo: 2-0-1-0 (controlled, moderate pace)
- RPE: 6-8 (challenging but sustainable)
- Balanced mix of compound and isolation exercises
- Include mobility and flexibility work
- Focus on movement quality and consistency
GOAL,
            default => <<<'GOAL'
**Balanced Training Protocol:**
- Volume: 3 sets per exercise
- Rep range: 10-12 reps
- Rest periods: 60 seconds
- Tempo: 2-0-1-0 (controlled pace)
- RPE: 6-8
- Mix of compound and isolation movements
GOAL,
        };
    }

    // =========================================================================
    // Weight Recommendation
    // =========================================================================

    private function getWeightRecommendation(): string
    {
        return match ($this->profile->skill_level->value) {
            'beginner' => <<<'WEIGHT'
**Weight Recommendations (Beginner):**
- Use qualitative descriptions only: "Light", "Moderate", "Challenging"
- Never reference 1RM or percentages — beginners don't know these values
- Focus on learning proper form before increasing load
- "Light" = easy to complete all reps, minimal effort, focus on form
- "Moderate" = challenging but manageable, 3-4 reps left in reserve
- "Challenging" = hard to complete last 2-3 reps with good form
- When in doubt, go lighter — building the habit matters more than the load
WEIGHT,
            'intermediate' => <<<'WEIGHT'
**Weight Recommendations (Intermediate):**
- Use qualitative descriptions with context
- "Moderate — complete all reps with 2-3 left in reserve"
- "Challenging — last 1-2 reps should be difficult but with good form"
- "Light to moderate — focus on squeeze and control, not load"
- Never reference 1RM percentages
- Include practical cues (e.g. "choose a band tension where..." or "pick a dumbbell weight where...")
WEIGHT,
            'advanced' => <<<'WEIGHT'
**Weight Recommendations (Advanced):**
- Use percentage of 1RM (One Rep Maximum) for main compound lifts
- Match to rep range:
  - 1-3 reps → 90-95% 1RM
  - 3-6 reps → 80-90% 1RM
  - 8-12 reps → 65-80% 1RM
  - 12-15 reps → 60-70% 1RM
- Examples: "70-75% 1RM", "80-85% 1RM"
- For isolation/accessory work, use qualitative: "Moderate-heavy" or "to RPE 8"
WEIGHT,
            default => <<<'WEIGHT'
**Weight Recommendations:**
- Use qualitative descriptions: "Light", "Moderate", "Challenging"
WEIGHT,
        };
    }

    // =========================================================================
    // Recent Workouts Context
    // =========================================================================
    private function getRecentWorkoutsSection(): string
    {
        if (empty($this->recentWorkouts)) {
            return '';
        }

        $list = implode("\n", array_map(fn ($w) => "- {$w}", $this->recentWorkouts));

        return <<<SECTION

**Recent workouts (for context and intelligent variation):**
{$list}
- Main compound lifts MAY repeat across sessions targeting the same muscles — this is normal programming
- Vary accessories and isolation exercises between sessions with the same focus
- If repeating an exercise, consider changing a variable (rep range, tempo, or execution style)

SECTION;
    }
}
