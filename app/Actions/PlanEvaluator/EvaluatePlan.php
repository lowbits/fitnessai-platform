<?php

namespace App\Actions\PlanEvaluator;

use App\Ai\Agents\PlanExtractionAgent;
use App\Support\PlanEvaluator\DayAggregator;
use App\Support\PlanEvaluator\Dimension;
use App\Support\PlanEvaluator\WorkoutRubric;
use Illuminate\Http\UploadedFile;
use Laravel\Ai\Files\Image;

/**
 * Orchestrates the plan evaluation: the LLM extracts structured facts, then the
 * deterministic rubric scores them. Keeping scoring out of the model is what
 * makes the verdict reproducible and defensible.
 */
class EvaluatePlan
{
    public function __construct(
        private WorkoutRubric $workoutRubric,
        private DayAggregator $aggregator,
    ) {}

    /**
     * @return array{plan_type: string, facts: array<string, mixed>, score?: int, dimensions?: list<Dimension>}
     */
    public function forText(string $plan): array
    {
        return $this->evaluate((new PlanExtractionAgent)->prompt($plan)->toArray());
    }

    /**
     * Several photos are treated as one plan (e.g. three days shot separately),
     * so all images go into a single extraction call. An optional note the user
     * typed alongside the images is passed as context.
     *
     * @param  list<UploadedFile>  $images
     * @return array{plan_type: string, facts: array<string, mixed>, score?: int, dimensions?: list<Dimension>}
     */
    public function forImages(array $images, string $context = ''): array
    {
        $attachments = array_map(fn (UploadedFile $image) => Image::fromUpload($image), $images);

        $intro = trim($context) === ''
            ? 'Extract the full training plan shown across these images. They may be different days or pages of one plan; combine them.'
            : "The user wrote: \"{$context}\". Extract the full training plan shown across these images (they may be different days or pages of one plan; combine them).";

        $facts = (new PlanExtractionAgent)->prompt($intro, $attachments)->toArray();

        return $this->evaluate($facts);
    }

    /**
     * @param  array<string, mixed>  $facts
     * @return array{plan_type: string, facts: array<string, mixed>, score?: int, dimensions?: list<Dimension>}
     */
    public function evaluate(array $facts): array
    {
        $facts = $this->aggregator->aggregate($facts);
        $planType = $facts['plan_type'] ?? 'unknown';

        if ($planType !== 'workout') {
            return ['plan_type' => $planType, 'facts' => $facts];
        }

        return ['plan_type' => 'workout', 'facts' => $facts, ...$this->workoutRubric->score($facts)];
    }
}
