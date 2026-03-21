<?php

namespace App\Ai\Agents;

use App\Models\Exercise;
use Laravel\Ai\Attributes\Timeout;
use Laravel\Ai\Enums\Lab;
use Laravel\Ai\Image;
use Laravel\Ai\Responses\ImageResponse;

#[Timeout(120)]
class ExerciseImageAgent
{
    public function __construct(
        private readonly Exercise $exercise,
        private readonly string $gender = 'male',
    ) {}

    /**
     * Build the image generation prompt for an exercise.
     */
    public function prompt(): string
    {
        return sprintf(
            '3D anatomical exercise illustration of a %s figure performing a **%s**. Realistic human proportions — lean, athletic build (not bodybuilder), correct 7.5-head-tall ratio with long legs proportional to torso. Full body visible from head to toe, including all gym equipment, nothing cropped. Square 1024x1024 composition. The body shows only raw muscle tissue with no skin — non-activated muscles are white/light gray with clearly visible muscle fiber striations, primary activated muscles highlighted in bright red and orange. The face is simple, neutral and mannequin-like — not photorealistic, no makeup, no expressive features. %s Style must match Jefit/BodyBuilding.com exercise diagrams — purely anatomical muscle diagram. Pure white background. High resolution, studio lighting, no text, no labels.',
            $this->gender,
            $this->exercise->name,
            $this->gender === 'female'
                ? 'Female figures wear a dark sports bra and dark athletic shorts.'
                : 'Male figures wear dark athletic shorts.',
        );
    }

    /**
     * Generate the exercise image.
     */
    public function generate(): ImageResponse
    {
        return Image::of($this->prompt())
            ->square()
            ->quality('high')
            ->timeout(120)
            ->generate(Lab::OpenAI, config('ai.models.image'));
    }
}
