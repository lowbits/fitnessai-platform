<?php

namespace App\Console\Commands\Exercises;

use App\Enums\Equipment;
use App\Enums\MuscleGroup;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;

class UploadBundleMediaCommand extends Command
{
    protected $signature = 'exercises:upload-bundle-media
                            {report : Path to the smart-match XLSX report}
                            {xlsx : Path to the bundle XLSX file}
                            {--video-dir= : Path to video directory}
                            {--illustration-dir= : Path to illustrations directory}
                            {--mysql= : MySQL DSN}
                            {--mysql-user=forge : MySQL username}
                            {--mysql-password= : MySQL password}
                            {--mobile-only : Only process exercises used by mobile users}
                            {--dry-run : Show what would be done without uploading}
                            {--limit= : Limit number of exercises to process}
                            {--skip-upload : Skip R2 upload (only update DB content)}';

    protected $description = 'Upload exercise videos and illustrations to R2 and update DB content from bundle';

    private \PDO $pdo;

    private array $videoIndex = [];

    private array $illustrationIndex = [];

    private string $r2BaseUrl;

    private int $uploaded = 0;

    private int $updated = 0;

    private int $skipped = 0;

    private array $unmappedEquipment = [];

    private array $unmappedMuscles = [];

    public function handle(): int
    {
        ini_set('memory_limit', '1G');

        $this->r2BaseUrl = config('services.r2.public_url');

        $this->pdo = new \PDO(
            $this->option('mysql'),
            $this->option('mysql-user'),
            $this->option('mysql-password')
        );
        $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

        $dryRun = $this->option('dry-run');
        $mobileOnly = $this->option('mobile-only');
        $skipUpload = $this->option('skip-upload');
        $limit = $this->option('limit') ? (int) $this->option('limit') : null;

        if ($dryRun) {
            $this->warn('DRY RUN — no uploads or DB changes.');
        }

        // Step 1: Load report
        $this->info('Loading match report...');
        $report = $this->loadReport($this->argument('report'));
        $updateMedia = array_filter($report, fn ($r) => $r['action'] === 'UPDATE_MEDIA');

        if ($mobileOnly) {
            $updateMedia = array_filter($updateMedia, fn ($r) => $r['mobile_usage'] > 0);
        }

        if ($limit) {
            $updateMedia = array_slice($updateMedia, 0, $limit);
        }

        $this->info('Exercises to process: ' . count($updateMedia));

        // Step 2: Load bundle data
        $this->info('Loading bundle data...');
        $bundleData = $this->parseBundleXlsx($this->argument('xlsx'));
        $this->info('Bundle entries: ' . count($bundleData));

        // Step 3: Index media files
        $videoDir = $this->option('video-dir');
        if ($videoDir && is_dir($videoDir)) {
            $this->indexVideos($videoDir);
            $this->info('Videos indexed: ' . count($this->videoIndex));
        }

        $illustrationDir = $this->option('illustration-dir');
        if ($illustrationDir && is_dir($illustrationDir)) {
            $this->indexIllustrations($illustrationDir);
            $this->info('Illustrations indexed: ' . count($this->illustrationIndex));
        }

        // Step 4: Process each exercise
        $this->info('Processing exercises...');
        $bar = $this->output->createProgressBar(count($updateMedia));

        foreach ($updateMedia as $entry) {
            $bar->advance();
            $this->processExercise($entry, $bundleData, $dryRun, $skipUpload);
        }

        $bar->finish();
        $this->newLine(2);

        // Summary
        $this->info('=== SUMMARY ===');
        $this->table(['Metric', 'Count'], [
            ['Media uploaded', $this->uploaded],
            ['DB records updated', $this->updated],
            ['Skipped (no media found)', $this->skipped],
        ]);

        if (! empty($this->unmappedEquipment)) {
            $this->warn('Unmapped equipment values:');
            foreach (array_count_values($this->unmappedEquipment) as $val => $cnt) {
                $this->line("  [{$cnt}x] {$val}");
            }
        }

        if (! empty($this->unmappedMuscles)) {
            $this->warn('Unmapped muscle values:');
            foreach (array_count_values($this->unmappedMuscles) as $val => $cnt) {
                $this->line("  [{$cnt}x] {$val}");
            }
        }

        return self::SUCCESS;
    }

    private function processExercise(array $entry, array $bundleData, bool $dryRun, bool $skipUpload): void
    {
        $dbId = $entry['db_id'];
        $bundleName = $entry['bundle_name'];
        $bundleKey = $this->normalize($bundleName);

        $bundle = $bundleData[$bundleKey] ?? null;
        $videoInfo = $this->videoIndex[$bundleKey] ?? ['male' => null, 'female' => null];
        $illustrationInfo = $this->illustrationIndex[$bundleKey] ?? null;

        // Determine which gender to use (female preferred, male fallback)
        $videoPath = $videoInfo['female'] ?? $videoInfo['male'] ?? null;
        $illustrationPath = $illustrationInfo ? ($illustrationInfo['female'] ?? $illustrationInfo['male'] ?? null) : null;

        if (! $videoPath && ! $illustrationPath && ! $bundle) {
            $this->skipped++;

            return;
        }

        // Get exercise slug for R2 naming
        $slug = $entry['db_slug'] ?? null;
        if (! $slug) {
            $stmt = $this->pdo->prepare('SELECT slug FROM exercises WHERE id = ?');
            $stmt->execute([$dbId]);
            $slug = $stmt->fetchColumn();
        }

        if (! $slug) {
            $this->skipped++;

            return;
        }

        $updates = [];

        // Upload video
        if ($videoPath && ! $skipUpload) {
            $r2Path = "exercises/videos/{$slug}.mp4";

            if ($dryRun) {
                $this->line("  Would upload: {$videoPath} -> {$r2Path}");
            } else {
                $this->uploadToR2($videoPath, $r2Path);
                $this->uploaded++;
            }

            $updates['video_url'] = "{$this->r2BaseUrl}/{$r2Path}";
        }

        // Upload illustration (frame 1 only)
        if ($illustrationPath && ! $skipUpload) {
            $r2Path = "exercises/illustrations/{$slug}.jpg";

            if ($dryRun) {
                $this->line("  Would upload: {$illustrationPath} -> {$r2Path}");
            } else {
                $this->uploadToR2($illustrationPath, $r2Path);
                $this->uploaded++;
            }

            $updates['image'] = "{$this->r2BaseUrl}/{$r2Path}";
        }

        // Update content from bundle
        if ($bundle) {
            if (! empty($bundle['instructions'])) {
                $instructions = $this->parseInstructions($bundle['instructions']);
                if (! empty($instructions)) {
                    $updates['instructions'] = json_encode($instructions);
                }
            }

            if (! empty($bundle['tips'])) {
                $updates['form_cues'] = $bundle['tips'];
            }

            // Map equipment
            if (! empty($bundle['equipment'])) {
                $mappedEquipment = $this->mapEquipment($bundle['equipment']);
                if (! empty($mappedEquipment)) {
                    $updates['equipment'] = json_encode(array_values(array_unique($mappedEquipment)));
                }
            }

            // Map muscles
            if (! empty($bundle['primary_muscles'])) {
                $mappedMuscles = MuscleGroup::parseBundleMuscles($bundle['primary_muscles']);
                if (! empty($mappedMuscles)) {
                    $updates['primary_muscles'] = json_encode($mappedMuscles);
                }
            }

            if (! empty($bundle['secondary_muscles'])) {
                $mappedSecondary = MuscleGroup::parseBundleMuscles($bundle['secondary_muscles']);
                if (! empty($mappedSecondary)) {
                    $updates['secondary_muscles'] = json_encode($mappedSecondary);
                }
            }
        }

        if (empty($updates)) {
            $this->skipped++;

            return;
        }

        if ($dryRun) {
            $this->line("  [{$dbId}] {$entry['db_name']} -> " . count($updates) . ' field(s)');

            return;
        }

        // Update DB
        $setClauses = [];
        $params = [];
        foreach ($updates as $col => $val) {
            $setClauses[] = "`{$col}` = ?";
            $params[] = $val;
        }
        $params[] = $dbId;

        $sql = 'UPDATE exercises SET ' . implode(', ', $setClauses) . ' WHERE id = ?';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        $this->updated++;
    }

    private function uploadToR2(string $localPath, string $r2Path): void
    {
        $content = file_get_contents($localPath);
        Storage::disk('r2')->put($r2Path, $content, 'public');
    }

    private function parseInstructions(string $raw): array
    {
        // Bundle instructions are numbered like "1. Do this\n2. Do that"
        $lines = preg_split('/\n/', $raw);
        $instructions = [];

        foreach ($lines as $line) {
            $line = trim($line);
            // Remove leading number and period/parenthesis
            $line = preg_replace('/^\d+[\.\)]\s*/', '', $line);
            $line = trim($line);

            if (! empty($line)) {
                $instructions[] = $line;
            }
        }

        return $instructions;
    }

    private function mapEquipment(string $raw): array
    {
        $mapped = [];

        // Bundle equipment can have multiple items: "Barbell, Box"
        $items = preg_split('/\s*,\s*/', $raw);

        foreach ($items as $item) {
            $item = trim($item);
            if (empty($item)) {
                continue;
            }

            $enum = Equipment::fromRaw($item);
            if ($enum) {
                $mapped[] = $enum->value;
            } else {
                $this->unmappedEquipment[] = $item;
            }
        }

        return $mapped;
    }

    private function loadReport(string $path): array
    {
        $spreadsheet = IOFactory::load($path);
        $data = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);

        $entries = [];
        foreach ($data as $i => $row) {
            if ($i === 1) {
                continue;
            }
            $entries[] = [
                'action' => $row['A'],
                'mobile_usage' => (int) ($row['B'] ?? 0),
                'total_usage' => (int) ($row['C'] ?? 0),
                'db_id' => $row['F'] ? (int) $row['F'] : null,
                'db_name' => $row['D'] ?? '',
                'db_slug' => null, // Will be fetched from DB
                'bundle_name' => $row['E'] ?? '',
            ];
        }

        return $entries;
    }

    private function parseBundleXlsx(string $path): array
    {
        $spreadsheet = IOFactory::load($path);
        $data = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);

        $exercises = [];
        foreach ($data as $i => $row) {
            if ($i === 1 || empty($row['B'])) {
                continue;
            }

            $canonical = trim(preg_replace('/_?(female|male)$/i', '', trim($row['B'])));
            $key = $this->normalize($canonical);

            if (isset($exercises[$key]) && empty($row['C'])) {
                continue;
            }

            $exercises[$key] = [
                'raw_name' => $canonical,
                'category' => trim($row['A'] ?? ''),
                'instructions' => trim($row['C'] ?? ''),
                'tips' => trim($row['D'] ?? ''),
                'primary_muscles' => trim($row['E'] ?? ''),
                'secondary_muscles' => trim($row['F'] ?? ''),
                'equipment' => trim($row['G'] ?? ''),
            ];
        }

        return $exercises;
    }

    private function normalize(string $name): string
    {
        $name = strtolower(trim($name));
        $name = preg_replace('/_?(female|male)$/i', '', $name);
        $name = str_replace(['-', '_', "\u{2013}", "\u{2014}"], ' ', $name);

        return trim(preg_replace('/\s+/', ' ', $name));
    }

    private function indexVideos(string $dir): void
    {
        foreach (glob($dir . '/*/*.mp4') as $file) {
            $basename = pathinfo($file, PATHINFO_FILENAME);
            $isFemale = (bool) preg_match('/_?female$/i', $basename);
            $key = $this->normalize(preg_replace('/_?(female|male)$/i', '', $basename));

            if (! isset($this->videoIndex[$key])) {
                $this->videoIndex[$key] = ['male' => null, 'female' => null];
            }
            $this->videoIndex[$key][$isFemale ? 'female' : 'male'] = $file;
        }
    }

    private function indexIllustrations(string $dir): void
    {
        foreach (glob($dir . '/*/*.{jpeg,jpg,png}', GLOB_BRACE) as $file) {
            $basename = pathinfo($file, PATHINFO_FILENAME);
            // Only take frame 1 (no trailing digit)
            if (preg_match('/\d+$/', $basename)) {
                continue;
            }

            $isFemale = (bool) preg_match('/_?female$/i', $basename);
            $key = $this->normalize(preg_replace('/_?(female|male)$/i', '', $basename));

            if (! isset($this->illustrationIndex[$key])) {
                $this->illustrationIndex[$key] = ['male' => null, 'female' => null];
            }
            $this->illustrationIndex[$key][$isFemale ? 'female' : 'male'] = $file;
        }
    }
}
