<?php

namespace App\Jobs;

use App\Ai\Agents\ExerciseImageAgent;
use App\Models\Exercise;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

class GenerateExerciseImage implements ShouldQueue
{
    use Queueable;

    public int $tries = 2;

    public int $backoff = 60;

    public int $timeout = 120;

    public function __construct(
        public Exercise $exercise,
        public string $gender = 'male',
    ) {
        $this->onQueue('default');
    }

    public function handle(): void
    {
        Log::info('[GenerateExerciseImage] Starting image generation', [
            'exercise' => $this->exercise->slug,
            'gender' => $this->gender,
        ]);

        try {
            $imageData = $this->generateImage();
            $path = $this->storeImage($imageData);

            $this->exercise->update(['image' => $path]);

            Log::info('[GenerateExerciseImage] Completed successfully', [
                'exercise' => $this->exercise->slug,
                'path' => $path,
            ]);
        } catch (\Throwable $e) {
            Log::error('[GenerateExerciseImage] Failed', [
                'exercise' => $this->exercise->slug,
                'error' => $e->getMessage(),
                'error_class' => get_class($e),
            ]);

            throw $e;
        }
    }

    private function generateImage(): string
    {
        $agent = new ExerciseImageAgent($this->exercise, $this->gender);

        return (string) $agent->generate();
    }

    private function storeImage(string $imageData): string
    {
        $webpData = $this->convertToWebp($imageData);
        $path = "exercises/{$this->exercise->slug}.webp";

        Storage::disk('public')->put($path, $webpData);

        Log::debug('[GenerateExerciseImage] Stored image', [
            'exercise' => $this->exercise->slug,
            'path' => $path,
            'size_bytes' => strlen($webpData),
        ]);

        return $path;
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
