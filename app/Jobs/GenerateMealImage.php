<?php

namespace App\Jobs;

use App\Ai\Agents\MealImageAgent;
use App\Models\Meal;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;

class GenerateMealImage implements ShouldQueue
{
    use Queueable;

    public int $tries = 2;

    public int $backoff = 60;

    public int $timeout = 120;

    public function __construct(public Meal $meal)
    {
        $this->onQueue('nutrition');
    }

    public function handle(): void
    {
        $slug = Str::slug($this->meal->name);

        Log::info('[GenerateMealImage] Starting image generation', [
            'meal_id' => $this->meal->id,
            'meal_name' => $this->meal->name,
        ]);

        try {
            $imageData = $this->generateImage();
            $fullPath = $this->uploadFullImage($imageData, $slug);

            $this->meal->update([
                'image_full' => $fullPath,
            ]);

            Log::info('[GenerateMealImage] Completed successfully', [
                'meal_id' => $this->meal->id,
                'image_full' => $fullPath,
            ]);
        } catch (\Throwable $e) {
            Log::error('[GenerateMealImage] Failed', [
                'meal_id' => $this->meal->id,
                'error' => $e->getMessage(),
                'error_class' => get_class($e),
            ]);

            throw $e;
        }
    }

    /**
     * Generate an image via Laravel AI SDK and return raw bytes.
     */
    private function generateImage(): string
    {
        $startTime = microtime(true);

        $agent = new MealImageAgent($this->meal);
        $response = $agent->generate();

        $duration = microtime(true) - $startTime;

        Log::debug('[GenerateMealImage] Image generated', [
            'meal_id' => $this->meal->id,
            'duration_seconds' => round($duration, 2),
        ]);

        return (string) $response;
    }

    /**
     * Convert raw image to WebP and upload to R2.
     */
    private function uploadFullImage(string $imageData, string $slug): string
    {
        $webpData = $this->convertToWebp($imageData);
        $path = "meals/full/{$slug}.webp";

        Storage::disk('r2')->put($path, $webpData, 'public');

        Log::debug('[GenerateMealImage] Uploaded full image', [
            'meal_id' => $this->meal->id,
            'path' => $path,
            'size_bytes' => strlen($webpData),
        ]);

        return $path;
    }

    /**
     * Convert raw image bytes to WebP format using GD.
     */
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
