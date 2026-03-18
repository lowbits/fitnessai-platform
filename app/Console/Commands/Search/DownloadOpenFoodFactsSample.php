<?php

namespace App\Console\Commands\Search;

use Illuminate\Console\Command;

class DownloadOpenFoodFactsSample extends Command
{
    protected $signature = 'off:download-sample
        {--limit=2000 : Max number of products to keep}
        {--output=storage/app/off-sample.json.gz : Output file path}';

    protected $description = 'Download a small Open Food Facts sample for local development (German products)';

    /** @see https://world.openfoodfacts.org/data Country-specific JSONL exports (~1-2 GB for DE) */
    private const DUMP_URL = 'https://static.openfoodfacts.org/data/openfoodfacts-products.jsonl.gz';

    public function handle(): int
    {
        $limit = (int) $this->option('limit');
        $output = $this->option('output');
        $outputPath = base_path($output);

        $dir = dirname($outputPath);
        if (! is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $this->info("Streaming German products from Open Food Facts (keeping up to {$limit})...");
        $this->warn('This streams from a large remote file — it may take a few minutes.');

        $handle = gzopen(self::DUMP_URL, 'r');

        if (! $handle) {
            $this->error('Could not open the Open Food Facts dump.');

            return Command::FAILURE;
        }

        $tmpPath = $outputPath.'.tmp';
        $out = gzopen($tmpPath, 'w9');

        if (! $out) {
            gzclose($handle);
            $this->error("Could not create output file: {$tmpPath}");

            return Command::FAILURE;
        }

        $written = 0;
        $scanned = 0;

        $this->output->progressStart($limit);

        while (! gzeof($handle) && $written < $limit) {
            $line = gzgets($handle);
            if (! $line) {
                continue;
            }

            $scanned++;

            $data = json_decode($line, true);
            if (! is_array($data)) {
                continue;
            }

            $countries = $data['countries_tags'] ?? [];
            if (! in_array('en:germany', $countries)) {
                continue;
            }

            if (empty($data['product_name']) && empty($data['product_name_de'])) {
                continue;
            }

            gzputs($out, $line);
            $written++;

            $this->output->progressAdvance();
        }

        gzclose($handle);
        gzclose($out);

        if ($written === 0) {
            unlink($tmpPath);
            $this->warn('No German products found.');

            return Command::FAILURE;
        }

        rename($tmpPath, $outputPath);

        $this->output->progressFinish();

        $size = $this->formatBytes((int) filesize($outputPath));
        $this->info("Saved {$written} German products ({$size}) to {$output}");
        $this->info("Scanned {$scanned} total products to find them.");
        $this->newLine();
        $this->line("Import them with: <comment>php artisan off:import --file={$output} --fresh</comment>");

        return Command::SUCCESS;
    }

    private function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $i = 0;

        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }

        return round($bytes, 1).' '.$units[$i];
    }
}
