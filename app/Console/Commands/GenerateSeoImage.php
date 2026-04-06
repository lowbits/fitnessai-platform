<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Laravel\Ai\Image;

class GenerateSeoImage extends Command
{
    protected $signature = 'seo:generate-image
                            {prompt : The image generation prompt}
                            {--name= : SEO-friendly filename (without extension)}
                            {--dir=seo : Subdirectory within public disk (e.g. blog, og, landing)}
                            {--orientation=landscape : Image orientation (landscape, portrait, square)}
                            {--quality=high : Image quality (high, medium, low)}
                            {--disk=public : Storage disk to save to}
                            {--dry-run : Show the prompt and filename without generating}';

    protected $description = 'Generate an SEO-optimized AI image and store it on disk';

    public function handle(): int
    {
        $prompt = $this->argument('prompt');
        $orientation = $this->option('orientation');
        $quality = $this->option('quality');
        $disk = $this->option('disk');
        $dir = $this->option('dir');
        $dryRun = $this->option('dry-run');

        $filename = $this->resolveFilename($prompt);
        $path = "{$dir}/{$filename}.webp";

        $this->info('Image Generation Details:');
        $this->table(['Setting', 'Value'], [
            ['Prompt', Str::limit($prompt, 80)],
            ['Filename', $path],
            ['Orientation', $orientation],
            ['Quality', $quality],
            ['Disk', $disk],
        ]);

        if ($dryRun) {
            $this->info('Dry run complete. No image generated.');

            return Command::SUCCESS;
        }

        if (Storage::disk($disk)->exists($path)) {
            if (! $this->confirm("File '{$path}' already exists. Overwrite?")) {
                $this->info('Aborted.');

                return Command::SUCCESS;
            }
        }

        $this->info('Generating image...');

        $image = Image::of($prompt)
            ->{$orientation}()
            ->quality($quality)
            ->timeout(120)
            ->generate();

        $storedPath = $image->storeAs($path, disk: $disk);

        $this->newLine();
        $this->info("Image saved to: {$storedPath}");

        if ($disk === 'public') {
            $url = Storage::disk($disk)->url($storedPath);
            $this->info("Public URL: {$url}");
        }

        $this->newLine();
        $this->comment('SEO Checklist:');
        $this->line("  Alt text suggestion: \"{$this->suggestAltText($prompt)}\"");
        $this->line('  Remember to optimize file size (<200KB) if needed.');

        return Command::SUCCESS;
    }

    private function resolveFilename(string $prompt): string
    {
        if ($name = $this->option('name')) {
            return Str::slug($name);
        }

        return Str::slug(Str::limit($prompt, 60, ''));
    }

    private function suggestAltText(string $prompt): string
    {
        $cleaned = preg_replace('/\b(style|composition|centered|flat design|photorealistic|3D|modern|clean|professional)\b/i', '', $prompt);
        $cleaned = preg_replace('/\s{2,}/', ' ', $cleaned);

        return Str::limit(trim($cleaned), 125, '');
    }
}
