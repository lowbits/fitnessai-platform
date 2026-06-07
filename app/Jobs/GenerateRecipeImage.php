<?php

namespace App\Jobs;

use App\Ai\Agents\MealImageAgent;
use App\Models\Recipe;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;

class GenerateRecipeImage implements ShouldQueue
{
    use Queueable;

    public int $tries = 2;

    public int $backoff = 60;

    public int $timeout = 180;

    public function __construct(public Recipe $recipe) {}

    public function handle(): void
    {
        $slug = Str::slug($this->recipe->name);

        Log::info('[RecipeImageGen] Starting', [
            'recipe_id' => $this->recipe->id,
            'name' => $this->recipe->name,
        ]);

        // Generate full image
        $agent = new MealImageAgent($this->recipe);
        $imageData = (string) $agent->generate();

        $webpData = $this->convertToWebp($imageData);
        $fullPath = "meals/full/{$slug}.webp";

        Storage::disk('r2')->put($fullPath, $webpData, 'public');

        // Remove background for isolated version
        $isolatedData = rembg()->remove($imageData);
        $isolatedPath = "meals/isolated/{$slug}.webp";

        Storage::disk('r2')->put($isolatedPath, $isolatedData, 'public');

        // Save all paths
        $baseUrl = config('services.r2.public_url');

        $this->recipe->update([
            'image' => "{$baseUrl}/{$fullPath}",
            'image_full' => $fullPath,
            'image_isolated' => $isolatedPath,
        ]);

        Log::info('[RecipeImageGen] Completed', [
            'recipe_id' => $this->recipe->id,
            'image_full' => $fullPath,
            'image_isolated' => $isolatedPath,
        ]);
    }

    private function convertToWebp(string $imageData): string
    {
        $image = imagecreatefromstring($imageData);

        if ($image === false) {
            throw new RuntimeException('Failed to create image from generated data');
        }

        ob_start();
        imagewebp($image, null, 85);
        $webpData = ob_get_clean();
        imagedestroy($image);

        if ($webpData === false || $webpData === '') {
            throw new RuntimeException('Failed to convert image to WebP');
        }

        return $webpData;
    }
}
