<?php

namespace App\Console\Commands\Exercises;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

class UploadEquipmentImages extends Command
{
    protected $signature = 'equipment:upload-images
                            {--dry-run : Preview which files would be processed}
                            {--quality=80 : WebP quality (1-100)}';

    protected $description = 'Convert transparent equipment PNGs to WebP and upload to R2';

    public function handle(): int
    {
        $quality = (int) $this->option('quality');
        $sourcePath = 'equipment/transparent';
        $files = Storage::disk('public')->files($sourcePath);

        $pngFiles = array_filter($files, fn (string $f) => str_ends_with(strtolower($f), '.png'));

        if (empty($pngFiles)) {
            $this->info('No PNG files found in storage/app/public/equipment/transparent/.');

            return Command::SUCCESS;
        }

        $this->info(count($pngFiles).' PNG file(s) found.');

        if ($this->option('dry-run')) {
            foreach ($pngFiles as $file) {
                $this->line("  {$file}");
            }
            $this->info('Dry run complete. No files processed.');

            return Command::SUCCESS;
        }

        $processed = 0;
        $totalSaved = 0;

        foreach ($pngFiles as $file) {
            $filename = pathinfo($file, PATHINFO_FILENAME);
            $r2Path = "equipments/transparent/{$filename}.webp";

            $absolutePath = Storage::disk('public')->path($file);
            $pngSize = filesize($absolutePath);

            $webpData = $this->convertToWebp($absolutePath, $quality);
            $webpSize = strlen($webpData);
            $saved = $pngSize - $webpSize;
            $totalSaved += $saved;

            Storage::disk('r2')->put($r2Path, $webpData, 'public');

            $this->line(sprintf(
                '  Uploaded %s (%.1fKB → %.1fKB, saved %.1fKB)',
                $r2Path,
                $pngSize / 1024,
                $webpSize / 1024,
                $saved / 1024,
            ));

            $processed++;
        }

        $this->info(sprintf(
            'Uploaded %d file(s). Total saved: %.1fMB.',
            $processed,
            $totalSaved / 1024 / 1024,
        ));

        return Command::SUCCESS;
    }

    /**
     * Convert a PNG file to WebP, preserving alpha transparency.
     */
    private function convertToWebp(string $path, int $quality): string
    {
        $image = imagecreatefrompng($path);

        if ($image === false) {
            throw new RuntimeException("Failed to read PNG: {$path}");
        }

        imagepalettetotruecolor($image);
        imagealphablending($image, false);
        imagesavealpha($image, true);

        ob_start();
        imagewebp($image, null, $quality);
        $webpData = ob_get_clean();
        imagedestroy($image);

        if ($webpData === false || $webpData === '') {
            throw new RuntimeException("Failed to convert to WebP: {$path}");
        }

        return $webpData;
    }
}
