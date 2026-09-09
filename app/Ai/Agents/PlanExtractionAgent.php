<?php

namespace App\Ai\Agents;

use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Attributes\Provider;
use Laravel\Ai\Attributes\Temperature;
use Laravel\Ai\Attributes\Timeout;
use Laravel\Ai\Contracts\Agent;
use Laravel\Ai\Contracts\HasStructuredOutput;
use Laravel\Ai\Enums\Lab;
use Laravel\Ai\Promptable;

/**
 * Turns a free-text training plan into structured facts. The LLM only extracts
 * (which it does reliably); the scoring is done deterministically by the rubric
 * so the verdict is consistent and defensible.
 */
#[Provider(Lab::OpenAI)]
#[Temperature(0.1)]
#[Timeout(60)]
class PlanExtractionAgent implements Agent, HasStructuredOutput
{
    use Promptable;

    public function model(): string
    {
        return config('ai.models.agent');
    }

    public function instructions(): string
    {
        return <<<'INSTRUCTIONS'
        Read the user's training plan and extract it day by day. Do not judge it, only extract. Read every
        image and every training day you can see; do not skip any. Map each exercise to one of these muscle
        groups: legs, back, chest, shoulders, arms, core.

        Output one entry per distinct training DAY the plan shows. For each day, list the muscle groups it
        trains and the total HARD WORKING sets that group gets ON THAT DAY (sum the working sets of all
        exercises for the group on that day).

        Count only true working sets: the heaviest, hardest sets near failure. A set clearly lighter than the
        top set does NOT count, whether it comes before the top set (warmup ramp) or after it (backoff,
        reduction or drop set). Example: in "12x70, 10x90, 3x8x110, 10x90, 12x70" only the three sets of
        8x110 are working sets; the 70 and 90 kg sets are warmups and backoff sets. So that exercise is 3
        working sets, not 7. Do not count warmups, backoff sets, drop sets, stretches or mobility.

        Do NOT sum across days and do NOT compute weekly totals or frequency yourself; only report per day.
        If the same muscle appears on two days, it will simply appear in both days' entries.

        For each day set times_per_week: how often that exact session is performed in a week, usually 1. Many
        plans repeat a small set of sessions. An Upper/Lower run twice a week is two day templates each with
        times_per_week 2, giving four sessions. If the user tells you how often they train per week, make the
        day templates and their times_per_week add up to that number. Never report fewer weekly sessions than
        the user stated.

        Judge overall intensity from any cues: RIR or RPE, percentages of 1RM, or notes like "to failure",
        "near failure" or "leave two in the tank". Hard means most working sets are close to failure or heavy
        (about 3 RIR or less, or roughly 75 percent of 1RM and up). Light means mostly submaximal work far
        from failure, for example 5x10 at 50 percent of 1RM, which is a weak growth stimulus even if the set
        count looks high. If the plan gives no intensity cues at all, set intensity to unspecified.

        Set has_progression true only if the plan describes progressive overload, meaning increasing weight,
        reps, RPE or percentages over time. If the text is a nutrition plan or unclear, set plan_type
        accordingly and leave the days empty.
        INSTRUCTIONS;
    }

    /**
     * @return array<string, mixed>
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'plan_type' => $schema->string()->enum(['workout', 'nutrition', 'unknown'])->required(),
            'split' => $schema->string()->description('Short label, e.g. "PPL", "Upper/Lower", "full body".'),
            'has_progression' => $schema->boolean()->description('True only if progressive overload is described.'),
            'intensity' => $schema->string()->enum(['hard', 'moderate', 'light', 'unspecified'])->description('Overall working-set intensity from RIR/RPE, % of 1RM, or failure cues; unspecified if no cues.'),
            'days' => $schema->array()->description('One entry per distinct training day shown in the plan.')->items(
                $schema->object([
                    'label' => $schema->string()->description('Day label, e.g. "Push", "Day 1", "Vorderseite".'),
                    'times_per_week' => $schema->integer()->description('How many times per week this exact session is performed. Usually 1; set 2 if the plan repeats it (e.g. Upper/Lower run twice). Must match any weekly frequency the user stated.'),
                    'groups' => $schema->array()->items(
                        $schema->object([
                            'group' => $schema->string()->enum(['legs', 'back', 'chest', 'shoulders', 'arms', 'core'])->required(),
                            'sets' => $schema->integer()->description('Total hard working sets this group gets on this day.')->required(),
                        ])
                    ),
                ])
            ),
        ];
    }
}
