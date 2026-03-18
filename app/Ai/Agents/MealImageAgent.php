<?php

namespace App\Ai\Agents;

use App\Models\Meal;
use Laravel\Ai\Attributes\Timeout;
use Laravel\Ai\Enums\Lab;
use Laravel\Ai\Image;
use Laravel\Ai\Responses\ImageResponse;

#[Timeout(120)]
class MealImageAgent
{
    public function __construct(private readonly Meal $meal) {}

    /**
     * Build the image generation prompt for a meal.
     */
    public function prompt(): string
    {
        $backgrounds = [
            'white marble countertop',
            'dark slate serving board',
            'light oak wood table',
            'white ceramic tabletop',
            'dark walnut wood table',
            'light grey concrete countertop',
            'natural linen tablecloth on white table',
            'matte black surface',
            'light birch wood table',
            'white kitchen countertop with subtle grain',
        ];

        $background = $backgrounds[array_rand($backgrounds)];

        return sprintf(
            'RAW photo, shot on Canon EOS R5 with 50mm f/1.8 lens, ISO 400. %s, beautifully plated with natural food textures, visible ingredient details, and realistic imperfections — slightly uneven portions, nothing too perfect. The complete plate must be fully visible and perfectly centered in the frame with at least 20%% empty space on all sides — no cropping, no cutting off any part of the plate or food. Served on a %s. 45-degree overhead angle, soft diffused natural daylight from the left side, shallow depth of field with soft bokeh background. Warm appetizing color tones with natural shadows. Film grain, subtle chromatic aberration. Real restaurant-style plating, not AI-generated, not stock photography, not illustrated. No text, no watermark, no hands, no logos, no utensils.',
            $this->meal->name,
            $background,
        );
    }

    /**
     * Generate the meal image.
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
