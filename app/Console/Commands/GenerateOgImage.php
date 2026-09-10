<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Str;

class GenerateOgImage extends Command
{
    protected $signature = 'og:generate
                            {name : output filename without extension}
                            {--eyebrow= : small label above the title}
                            {--title= : main headline}
                            {--subtitle= : one-line subheading}';

    protected $description = 'Render a branded 1200x630 Open Graph card to public/assets/images/og';

    public function handle(): int
    {
        foreach (['rsvg-convert', 'cwebp'] as $binary) {
            if (Process::run(['which', $binary])->failed()) {
                $this->error("Missing binary '{$binary}'. Install with: brew install librsvg webp");

                return self::FAILURE;
            }
        }

        $name = Str::slug($this->argument('name'));

        $svg = view('og.card', [
            'eyebrow' => Str::upper((string) $this->option('eyebrow')),
            'titleLines' => explode("\n", wordwrap((string) $this->option('title'), 16, "\n")),
            'subtitle' => (string) $this->option('subtitle'),
        ])->render();

        $svgPath = storage_path("app/og-{$name}.svg");
        $pngPath = storage_path("app/og-{$name}.png");
        $webpPath = public_path("assets/images/og/{$name}.webp");

        file_put_contents($svgPath, $svg);
        Process::run(['rsvg-convert', '-w', '1200', '-h', '630', $svgPath, '-o', $pngPath])->throw();
        Process::run(['cwebp', '-quiet', '-q', '88', $pngPath, '-o', $webpPath])->throw();

        @unlink($svgPath);
        @unlink($pngPath);

        $this->info("OG card written: {$webpPath}");

        return self::SUCCESS;
    }
}
