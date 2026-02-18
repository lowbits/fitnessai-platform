<?php

namespace App\Jobs;

use App\Models\Meal;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;

class RemoveMealImageBackground implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public int $backoff = 30;

    public int $timeout = 90;

    public function __construct(public Meal $meal)
    {
        $this->onQueue('nutrition');
    }

    public function handle(): void
    {
        $slug = Str::slug($this->meal->name);

        Log::info('[RemoveMealImageBackground] Starting background removal', [
            'meal_id' => $this->meal->id,
            'image_full' => $this->meal->image_full,
        ]);

        if (! $this->meal->image_full) {
            Log::warning('[RemoveMealImageBackground] No full image to process', [
                'meal_id' => $this->meal->id,
            ]);

            return;
        }

        try {
            $imageData = Storage::disk('r2')->get($this->meal->image_full);

            if (! $imageData) {
                throw new RuntimeException("Full image not found on R2: {$this->meal->image_full}");
            }

            $isolatedPath = $this->removeBackgroundAndUpload($imageData, $slug);

            $this->meal->update([
                'image_isolated' => $isolatedPath,
            ]);

            Log::info('[RemoveMealImageBackground] Completed successfully', [
                'meal_id' => $this->meal->id,
                'image_isolated' => $isolatedPath,
            ]);
        } catch (\Throwable $e) {
            Log::error('[RemoveMealImageBackground] Failed', [
                'meal_id' => $this->meal->id,
                'error' => $e->getMessage(),
                'error_class' => get_class($e),
            ]);

            throw $e;
        }
    }

    /**
     * Remove background and upload the result to R2.
     */
    private function removeBackgroundAndUpload(string $imageData, string $slug): string
    {
        $pngData = rembg()->remove($imageData);
        $path = "meals/isolated/{$slug}.webp";

        Storage::disk('r2')->put($path, $pngData, 'public');

        Log::debug('[RemoveMealImageBackground] Uploaded isolated image', [
            'meal_id' => $this->meal->id,
            'path' => $path,
            'size_bytes' => strlen($pngData),
        ]);

        return $path;
    }
}
