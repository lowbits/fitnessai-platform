<?php

namespace App\Jobs;

use App\Models\Exercise;
use App\Models\Plan;
use App\Models\User;
use App\Models\WorkoutPlan;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use OpenAI;

class GenerateUserWorkoutPlan implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public User $user,
        public Plan $plan
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $profile = $this->user->profile;

        if (!$profile) {
            Log::error('User profile not found', ['user_id' => $this->user->id]);
            return;
        }

        Log::info('Starting workout plan generation', [
            'user_id' => $this->user->id,
            'plan_id' => $this->plan->id,
            'locale' => $this->user->locale,
        ]);

        $client = OpenAI::client(config('services.openai.api_key'));

        // Generate workout plans for configured number of days (respecting rest days)
        $totalDays = config('plans.duration_days');
        $workoutsPerWeek = $this->plan->workouts_per_week ?? 3;

        // Build system prompt ONCE
        $systemPrompt = [
            'role' => 'system',
            'content' => $this->buildSystemPrompt($profile),
        ];

        // Track generated workouts for lightweight context
        $generatedWorkoutsSummary = [];

        Log::debug('System prompt created', [
            'prompt_length' => strlen($systemPrompt['content']),
            'total_days' => $totalDays,
            'workouts_per_week' => $workoutsPerWeek,
        ]);

        for ($day = 1; $day <= $totalDays; $day++) {
            $date = $this->plan->start_date->copy()->addDays($day - 1);

            // Determine if this is a workout day or rest day
            $isRestDay = $this->isRestDay($day, $workoutsPerWeek);

            Log::debug("Processing day {$day}/{$totalDays}", [
                'day' => $day,
                'date' => $date->format('Y-m-d'),
                'is_rest_day' => $isRestDay,
            ]);

            // Create workout plan record (or find existing)
            $workoutPlan = WorkoutPlan::firstOrCreate(
                [
                    'plan_id' => $this->plan->id,
                    'day_number' => $day,
                ],
                [
                    'date' => $date,
                    'status' => 'pending',
                    'workout_name' => $isRestDay ? 'Rest Day' : 'Workout Day',
                    'workout_type' => $isRestDay ? 'rest' : 'strength',
                ]
            );

            // Skip if already generated
            if ($workoutPlan->status === 'generated') {
                Log::info("Workout plan for day {$day} already generated, skipping", [
                    'workout_plan_id' => $workoutPlan->id,
                    'existing_exercises_count' => $workoutPlan->exercises()->count(),
                ]);

                // Add to summary for lightweight context
                if (!$isRestDay) {
                    $exercises = $workoutPlan->exercises()->get();
                    $exerciseNames = $exercises->pluck('name')->take(3)->implode(', ');
                    $generatedWorkoutsSummary[] = "Day {$day}: {$workoutPlan->workout_name} ({$exerciseNames}...)";
                }

                continue;
            }

            if ($isRestDay) {
                // Simple rest day - no AI generation needed
                $workoutPlan->update([
                    'status' => 'generated',
                    'workout_name' => 'Active Recovery / Rest Day',
                    'workout_type' => 'rest',
                    'description' => 'Take a rest day to allow your muscles to recover. Light stretching or walking is encouraged.',
                    'estimated_duration_minutes' => 0,
                ]);
                Log::info("Created rest day for day {$day}", ['workout_plan_id' => $workoutPlan->id]);
                continue;
            }

            try {
                // Build context-aware user message with recent workouts
                $contextMessage = "Generate a complete workout plan for day {$day} of {$totalDays}. Consider the workout split and ensure proper muscle group rotation.";

                // Add recent workouts context (last 5 workouts)
                if (count($generatedWorkoutsSummary) > 0) {
                    $recentWorkouts = array_slice($generatedWorkoutsSummary, -5);
                    $contextMessage .= "\n\nRecent workouts (ensure variety):\n" . implode("\n", $recentWorkouts);
                }

                // CONSTANT size: only system + current request
                $requestMessages = [
                    $systemPrompt,
                    [
                        'role' => 'user',
                        'content' => $contextMessage,
                    ],
                ];

                Log::debug("Calling OpenAI for day {$day}", [
                    'model' => 'gpt-5-mini',
                    'messages_count' => count($requestMessages),
                    'context_workouts_count' => count($recentWorkouts ?? []),
                ]);

                $startTime = microtime(true);

                // Use Tool Calls (more reliable than JSON Schema!)
                $response = $client->chat()->create([
                    'model' => 'gpt-5-mini',
                    'reasoning_effort' => 'minimal',
                    'messages' => $requestMessages,
                    'tools' => [
                        [
                            'type' => 'function',
                            'function' => [
                                'name' => 'create_workout_plan',
                                'description' => 'Creates a complete workout plan with exercises',
                                'parameters' => [
                                    'type' => 'object',
                                    'properties' => [
                                        'workout_name' => ['type' => 'string'],
                                        'workout_type' => ['type' => 'string', 'enum' => ['strength', 'cardio', 'hiit', 'mobility']],
                                        'description' => ['type' => 'string'],
                                        'estimated_duration_minutes' => ['type' => 'integer'],
                                        'difficulty' => ['type' => 'string', 'enum' => ['Beginner', 'Intermediate', 'Advanced']],
                                        'muscle_groups' => ['type' => 'array', 'items' => ['type' => 'string']],
                                        'exercises' => [
                                            'type' => 'array',
                                            'items' => [
                                                'type' => 'object',
                                                'properties' => [
                                                    'name' => ['type' => 'string'],
                                                    'type' => ['type' => 'string', 'enum' => ['strength', 'cardio', 'warmup', 'cooldown', 'stretch']],
                                                    'description' => ['type' => 'string'],
                                                    'sets' => ['type' => 'integer'],
                                                    'reps' => ['type' => 'integer'],
                                                    'duration_seconds' => ['type' => 'integer'],
                                                    'rest_seconds' => ['type' => 'string'],
                                                    'tempo' => ['type' => 'string'],
                                                    'weight_recommendation' => ['type' => 'string'],
                                                    'muscle_groups' => ['type' => 'array', 'items' => ['type' => 'string']],
                                                    'equipment' => ['type' => 'array', 'items' => ['type' => 'string']],
                                                    'form_cues' => ['type' => 'string'],
                                                    'alternatives' => ['type' => 'array', 'items' => ['type' => 'string']],
                                                    'difficulty' => ['type' => 'string', 'enum' => ['Beginner', 'Intermediate', 'Advanced']],
                                                ],
                                                'required' => ['name', 'type'],
                                            ],
                                        ],
                                    ],
                                    'required' => ['workout_name', 'workout_type', 'exercises'],
                                ],
                            ],
                        ],
                    ],
                    'tool_choice' => ['type' => 'function', 'function' => ['name' => 'create_workout_plan']],
                ]);

                $duration = microtime(true) - $startTime;

                Log::debug("OpenAI response received for day {$day}", [
                    'duration_seconds' => round($duration, 2),
                    'finish_reason' => $response->choices[0]->finishReason ?? 'unknown',
                    'usage' => [
                        'prompt_tokens' => $response->usage->promptTokens ?? 0,
                        'completion_tokens' => $response->usage->completionTokens ?? 0,
                        'total_tokens' => $response->usage->totalTokens ?? 0,
                    ],
                ]);

                // Parse tool call (more reliable than JSON parsing!)
                $toolCall = $response->choices[0]->message->toolCalls[0] ?? null;

                if ($toolCall && $toolCall->function->name === 'create_workout_plan') {
                    $arguments = json_decode($toolCall->function->arguments, true);

                    Log::debug("Tool call received for day {$day}", [
                        'exercises_count' => count($arguments['exercises'] ?? []),
                        'workout_name' => $arguments['workout_name'] ?? 'unknown',
                        'muscle_groups' => $arguments['muscle_groups'] ?? [],
                    ]);

                    // Update workout plan with details
                    $workoutPlan->update([
                        'status' => 'generated',
                        'workout_name' => $arguments['workout_name'],
                        'workout_type' => $arguments['workout_type'],
                        'description' => $arguments['description'] ?? null,
                        'estimated_duration_minutes' => $arguments['estimated_duration_minutes'] ?? null,
                        'difficulty' => $arguments['difficulty'] ?? null,
                        'muscle_groups' => $arguments['muscle_groups'] ?? [],
                    ]);

                    // Save exercises
                    $this->saveExercises($workoutPlan, $arguments['exercises']);

                    // Add workout to summary for next iterations (lightweight context)
                    $exerciseNames = array_slice(array_column($arguments['exercises'], 'name'), 0, 3);
                    $generatedWorkoutsSummary[] = "Day {$day}: {$arguments['workout_name']} (" . implode(', ', $exerciseNames) . "...)";

                    Log::info("Generated workout plan for day {$day}", [
                        'workout_plan_id' => $workoutPlan->id,
                        'exercises_created' => count($arguments['exercises']),
                        'workout_name' => $arguments['workout_name'],
                        'duration_seconds' => round($duration, 2),
                        'total_workouts_in_summary' => count($generatedWorkoutsSummary),
                    ]);
                } else {
                    Log::warning("No tool call received for day {$day}", [
                        'response' => json_encode($response->choices[0] ?? 'empty'),
                    ]);
                }
            } catch (\Exception $e) {
                Log::error("Failed to generate workout plan for day {$day}", [
                    'error' => $e->getMessage(),
                    'error_class' => get_class($e),
                    'workout_plan_id' => $workoutPlan->id,
                    'trace' => $e->getTraceAsString(),
                ]);

                $workoutPlan->update(['status' => 'failed']);

                // Don't break the loop - continue with next day
                continue;
            }
        }

        Log::info('Workout plan generation completed', [
            'user_id' => $this->user->id,
            'plan_id' => $this->plan->id,
            'total_days' => $totalDays,
            'generated_count' => WorkoutPlan::where('plan_id', $this->plan->id)
                ->where('status', 'generated')
                ->count(),
            'failed_count' => WorkoutPlan::where('plan_id', $this->plan->id)
                ->where('status', 'failed')
                ->count(),
        ]);
    }

    private function isRestDay(int $day, int $workoutsPerWeek): bool
    {
        // Calculate which day of the week this is (0-6)
        $dayOfWeek = ($day - 1) % 7;

        $workoutDays = match($workoutsPerWeek) {
            1 => [0], // Monday only
            2 => [0, 3], // Monday, Thursday
            3 => [0, 2, 4], // Monday, Wednesday, Friday
            4 => [0, 2, 4, 6], // Mon, Wed, Fri, Sun
            5 => [0, 1, 2, 4, 5], // Mon-Wed, Fri-Sat
            6 => [0, 1, 2, 3, 4, 5], // Mon-Sat
            7 => [0, 1, 2, 3, 4, 5, 6], // Every day
            default => [0, 2, 4],
        };

        return !in_array($dayOfWeek, $workoutDays);
    }

    private function getWorkoutSplit(int $sessionsPerWeek): string
    {
        return match($sessionsPerWeek) {
            1 => 'Full Body',
            2 => 'Upper/Lower Split',
            3 => 'Push/Pull/Legs',
            4 => 'Upper/Lower/Upper/Lower',
            5 => 'Push/Pull/Legs/Upper/Lower',
            6 => 'Push/Pull/Legs/Push/Pull/Legs',
            7 => 'Daily Specialization',
            default => 'Full Body',
        };
    }

    private function getTargetMuscleGroups(int $day, int $sessionsPerWeek): string
    {
        $workoutDayNumber = $this->getWorkoutDayNumber($day, $sessionsPerWeek);

        return match($sessionsPerWeek) {
            1 => 'Full Body',
            2 => $workoutDayNumber === 1 ? 'Upper Body (Chest, Back, Shoulders, Arms)' : 'Lower Body (Quads, Hamstrings, Glutes, Calves)',
            3 => match($workoutDayNumber) {
                1 => 'Push (Chest, Shoulders, Triceps)',
                2 => 'Pull (Back, Biceps, Rear Delts)',
                3 => 'Legs (Quads, Hamstrings, Glutes, Calves)',
                default => 'Full Body',
            },
            4 => match($workoutDayNumber) {
                1 => 'Upper Body (Chest, Back, Shoulders)',
                2 => 'Lower Body (Quads, Hamstrings, Glutes)',
                3 => 'Upper Body (Emphasis: Back and Arms)',
                4 => 'Lower Body (Emphasis: Glutes and Hamstrings)',
                default => 'Full Body',
            },
            default => 'Full Body',
        };
    }

    private function getWorkoutDayNumber(int $day, int $sessionsPerWeek): int
    {
        $workoutCount = 0;

        for ($d = 1; $d <= $day; $d++) {
            if (!$this->isRestDay($d, $sessionsPerWeek)) {
                $workoutCount++;
            }
        }

        // Return position in the weekly cycle (1-based)
        return (($workoutCount - 1) % $sessionsPerWeek) + 1;
    }

    private function getGoalGuidelines(string $bodyGoal): string
    {
        return match($bodyGoal) {
            'muscle_gain' => "
**Muscle Gain Protocol:**
- Sets: 3-4 per exercise
- Rep range: 8-12 reps (hypertrophy)
- Rest: 60-90 seconds
- Tempo: 3-0-1-0 (controlled eccentric, explosive concentric)
- RPE: 7-9 (leave 1-3 reps in reserve)
- Exercise selection: 70% compound, 30% isolation
        ",
            'weight_loss' => "
**Weight Loss Protocol:**
- Sets: 3 per exercise
- Rep range: 12-15 reps
- Rest: 30-45 seconds (circuit style when possible)
- Tempo: 2-0-1-0 (moderate pace)
- RPE: 6-8
- Include cardio intervals between exercises
        ",
            'strength' => "
**Strength Protocol:**
- Sets: 4-6 per exercise
- Rep range: 3-6 reps (strength)
- Rest: 2-5 minutes (full recovery)
- Tempo: 1-0-X-0 (explosive concentric)
- RPE: 8-10
- 90% compound movements
        ",
            'endurance' => "
**Endurance Protocol:**
- Sets: 2-3 per exercise
- Rep range: 15-25 reps
- Rest: 30 seconds or less
- Tempo: 1-0-1-0 (fast pace)
- RPE: 5-7
- Mix strength and cardio
        ",
            default => "Balanced training protocol with 3 sets, 10-12 reps, 60 seconds rest",
        };
    }

    private function buildSystemPrompt($profile): string
    {
        $totalDays = config('plans.duration_days');
        $trainingPlace = $profile->training_place->value;
        $skillLevel = $profile->skill_level->value;
        $bodyGoal = $profile->body_goal->value;
        $workoutSplit = $this->getWorkoutSplit($profile->training_sessions_per_week);

        return <<<PROMPT
You are an expert personal trainer and workout programmer specializing in evidence-based training methods. Create personalized workout plans based on the user profile below.

**User Profile:**
- Age: {$profile->age} years
- Gender: {$profile->gender->label()}
- Body Goal: {$bodyGoal}
- Skill Level: {$skillLevel}
- Training Place: {$trainingPlace}
- Activity Level: {$profile->activity_level->value}
- Training Sessions per Week: {$profile->training_sessions_per_week}
- Workout Split: {$workoutSplit}

**Requirements:**
1. All exercises MUST be suitable for {$trainingPlace} training
2. ONLY use equipment available in {$trainingPlace}
3. Difficulty should match {$skillLevel} level
4. Workouts should align with {$bodyGoal} goal
5. Ensure proper muscle group distribution across the {$totalDays}-day plan
6. Include warmup (5-8 min) and cooldown (5 min) when appropriate
7. Provide clear form cues for each exercise
8. Use {$this->getLanguageInstruction()} for exercise names, descriptions, and instructions
9. Main workout: 6-8 exercises for {$skillLevel} level
10. Specify sets, reps, rest periods, tempo, and RPE

**Workout Programming by Goal:**
{$this->getGoalGuidelines($bodyGoal)}

**Output Format:**
Return a valid JSON object with workout_name, workout_type, description, estimated_duration_minutes, difficulty, muscle_groups, and exercises array.
Each exercise must include: name, type, description, sets, reps (or duration_seconds), rest_seconds, equipment, and form_cues.

Generate complete, safe, and effective workout plans that the user can easily follow.
PROMPT;
    }

    /**
     * Get language instruction for OpenAI prompt
     */
    private function getLanguageInstruction(): string
    {
        $language = $this->user->locale;

        return match($language) {
            'de' => 'German language',
            default => 'English language',
        };
    }

    private function saveExercises(WorkoutPlan $workoutPlan, array $exercises): void
    {
        foreach ($exercises as $index => $exerciseData) {
            Exercise::create([
                'workout_plan_id' => $workoutPlan->id,
                'order' => $index + 1,
                'name' => $exerciseData['name'],
                'type' => $exerciseData['type'],
                'description' => $exerciseData['description'] ?? null,
                'sets' => $exerciseData['sets'] ?? null,
                'reps' => $exerciseData['reps'] ?? null,
                'duration_seconds' => $exerciseData['duration_seconds'] ?? null,
                'rest_seconds' => $exerciseData['rest_seconds'] ?? null,
                'tempo' => $exerciseData['tempo'] ?? null,
                'weight_recommendation' => $exerciseData['weight_recommendation'] ?? null,
                'muscle_groups' => $exerciseData['muscle_groups'] ?? [],
                'equipment' => $exerciseData['equipment'] ?? [],
                'form_cues' => $exerciseData['form_cues'] ?? null,
                'alternatives' => $exerciseData['alternatives'] ?? [],
                'difficulty' => $exerciseData['difficulty'] ?? null,
            ]);
        }
    }
}

