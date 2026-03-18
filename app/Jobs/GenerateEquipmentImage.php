<?php

namespace App\Jobs;

use App\Ai\Agents\EquipmentImageAgent;
use App\Enums\Equipment;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

class GenerateEquipmentImage implements ShouldQueue
{
    use Queueable;

    public int $tries = 2;

    public int $backoff = 60;

    public int $timeout = 120;

    public function __construct(public Equipment $equipment)
    {
        $this->onQueue('default');
    }

    public function handle(): void
    {
        $slug = $this->equipment->value;

        Log::info('[GenerateEquipmentImage] Starting image generation', [
            'equipment' => $slug,
        ]);

        try {
            $imageData = $this->generateImage();
            $path = $this->storeImage($imageData, $slug);

            Log::info('[GenerateEquipmentImage] Completed successfully', [
                'equipment' => $slug,
                'path' => $path,
            ]);
        } catch (\Throwable $e) {
            Log::error('[GenerateEquipmentImage] Failed', [
                'equipment' => $slug,
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
        $agent = new EquipmentImageAgent($this->equipment);

        return (string) $agent->generate();
    }

    /**
     * Convert raw image to WebP and store locally.
     */
    private function storeImage(string $imageData, string $slug): string
    {
        $webpData = $this->convertToWebp($imageData);
        $path = "equipments/{$slug}.webp";

        Storage::disk('public')->put($path, $webpData);

        Log::debug('[GenerateEquipmentImage] Stored image', [
            'equipment' => $slug,
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
