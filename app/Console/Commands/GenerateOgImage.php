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
                            {--subtitle= : one-line subheading}
                            {--icon= : lucide icon for the right-side motif (falls back to the brand mark)}';

    protected $description = 'Render a branded 1200x630 Open Graph card to public/assets/images/og';

    /** @var array<string, string> lucide icon inner markup (24x24, stroked) */
    private const ICONS = [
        'gauge' => '<path d="m12 14 4-4"/><path d="M3.34 19a10 10 0 1 1 17.32 0"/>',
        'clipboard-check' => '<rect width="8" height="4" x="8" y="2" rx="1" ry="1"/><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/><path d="m9 14 2 2 4-4"/>',
        'book-open' => '<path d="M12 7v14"/><path d="M3 18a1 1 0 0 1-1-1V4a1 1 0 0 1 1-1h5a4 4 0 0 1 4 4 4 4 0 0 1 4-4h5a1 1 0 0 1 1 1v13a1 1 0 0 1-1 1h-6a3 3 0 0 0-3 3 3 3 0 0 0-3-3z"/>',
        'list-checks' => '<path d="M13 5h8"/><path d="M13 12h8"/><path d="M13 19h8"/><path d="m3 17 2 2 4-4"/><path d="m3 7 2 2 4-4"/>',
        'calculator' => '<rect width="16" height="20" x="4" y="2" rx="2"/><line x1="8" x2="16" y1="6" y2="6"/><line x1="16" x2="16" y1="14" y2="18"/><path d="M16 10h.01"/><path d="M12 10h.01"/><path d="M8 10h.01"/><path d="M12 14h.01"/><path d="M8 14h.01"/><path d="M12 18h.01"/><path d="M8 18h.01"/>',
        'chart-pie' => '<path d="M21 12c.552 0 1.005-.449.95-.998a10 10 0 0 0-8.953-8.951c-.55-.055-.998.398-.998.95v8a1 1 0 0 0 1 1z"/><path d="M21.21 15.89A10 10 0 1 1 8 2.83"/>',
        'dumbbell' => '<path d="M17.596 12.768a2 2 0 1 0 2.829-2.829l-1.768-1.767a2 2 0 0 0 2.828-2.829l-2.828-2.828a2 2 0 0 0-2.829 2.828l-1.767-1.768a2 2 0 1 0-2.829 2.829z"/><path d="m2.5 21.5 1.4-1.4"/><path d="m20.1 3.9 1.4-1.4"/><path d="M5.343 21.485a2 2 0 1 0 2.829-2.828l1.767 1.768a2 2 0 1 0 2.829-2.829l-6.364-6.364a2 2 0 1 0-2.829 2.829l1.768 1.767a2 2 0 0 0-2.828 2.829z"/><path d="m9.6 14.4 4.8-4.8"/>',
    ];

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
            'iconSvg' => self::ICONS[(string) $this->option('icon')] ?? '',
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
