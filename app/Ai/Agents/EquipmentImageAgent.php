<?php

namespace App\Ai\Agents;

use App\Enums\Equipment;
use Laravel\Ai\Attributes\Timeout;
use Laravel\Ai\Enums\Lab;
use Laravel\Ai\Image;
use Laravel\Ai\Responses\ImageResponse;

#[Timeout(120)]
class EquipmentImageAgent
{
    public function __construct(private readonly Equipment $equipment) {}

    /**
     * Build the image generation prompt for an equipment item.
     */
    public function prompt(): string
    {
        return sprintf(
            'Professional studio product photography of a %s, standard commercial gym fitness equipment, realistic existing product, as seen in real gyms, isolated on a pure white background, clean sharp edges, minimal shadow, no drop shadow, high contrast silhouette, centered composition, view from slightly below, hyper-realistic, 4K resolution, no text, no labels, no numbers, no watermark, no logo, no branding',
            str_replace('_', ' ', $this->equipment->value),
        );
    }

    /**
     * Generate the equipment image.
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
