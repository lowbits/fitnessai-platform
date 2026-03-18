<?php

namespace App\Ai\Agents;

use App\Ai\Tools\MeilisearchSimilaritySearch;
use App\Ai\Tools\SaveWorkoutPlanTool;
use App\Models\WorkoutPlan;
use Laravel\Ai\Attributes\MaxSteps;
use Laravel\Ai\Attributes\Provider;
use Laravel\Ai\Attributes\Timeout;
use Laravel\Ai\Contracts\Agent;
use Laravel\Ai\Contracts\Conversational;
use Laravel\Ai\Contracts\HasTools;
use Laravel\Ai\Enums\Lab;
use Laravel\Ai\Promptable;

#[MaxSteps(25)]
#[Timeout(300)]
#[Provider([Lab::OpenAI, Lab::Mistral])]
class WorkoutProgrammerAgent implements Agent, Conversational, HasTools
{
    use Promptable;

    public function __construct(private readonly WorkoutPlan $workoutPlan) {}

    public function model(): string
    {
        return config('ai.models.agent');
    }

    /**
     * Static training knowledge — cached by the provider.
     * Same for every user, every generation.
     */
    public function instructions(): string
    {
        return 'You are an expert personal trainer and workout programmer specializing in evidence-based training methods. Create personalized workout plans based on the user profile below.';
    }

    /**
     * Get the list of messages comprising the conversation so far.
     */
    public function messages(): iterable
    {
        return [];
    }

    /**
     * Get the tools available to the agent.
     *
     * @return Tool[]
     */
    public function tools(): iterable
    {
        return [
            app(MeilisearchSimilaritySearch::class),
            app(SaveWorkoutPlanTool::class, ['workoutPlan' => $this->workoutPlan]),
        ];
    }
}
