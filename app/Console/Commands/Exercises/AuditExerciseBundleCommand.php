<?php

namespace App\Console\Commands\Exercises;

use App\Models\Exercise;
use Illuminate\Console\Command;
use Illuminate\Support\Str;
use OpenAI\Laravel\Facades\OpenAI;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class AuditExerciseBundleCommand extends Command
{
    protected $signature = 'exercises:audit-bundle
                            {xlsx : Path to the bundle XLSX file}
                            {--video-dir= : Path to video directory}
                            {--illustration-dir= : Path to illustrations directory}
                            {--output= : Output XLSX path (default: storage/app/exercise-audit.xlsx)}
                            {--threshold=60 : Minimum fuzzy match score (0-100)}
                            {--ai-verify : Use AI to verify fuzzy matches}
                            {--batch-size=50 : Number of pairs per AI verification batch}
                            {--mysql= : MySQL DSN for remote DB (e.g., "mysql:host=127.0.0.1;port=3307;dbname=fitnessai")}
                            {--mysql-user= : MySQL username}
                            {--mysql-password= : MySQL password}';

    protected $description = 'Audit exercise bundle against existing database exercises and generate a match report XLSX';

    private array $videoIndex = [];

    private array $illustrationIndex = [];

    public function handle(): int
    {
        ini_set('memory_limit', '512M');

        $xlsxPath = $this->argument('xlsx');
        $videoDir = $this->option('video-dir');
        $illustrationDir = $this->option('illustration-dir');
        $outputPath = $this->option('output') ?? storage_path('app/exercise-audit.xlsx');
        $threshold = (int) $this->option('threshold');
        $aiVerify = $this->option('ai-verify');

        if (! file_exists($xlsxPath)) {
            $this->error("XLSX file not found: {$xlsxPath}");

            return self::FAILURE;
        }

        // Step 1: Load DB exercises
        $this->info('Loading database exercises...');

        $mysqlDsn = $this->option('mysql');
        if ($mysqlDsn) {
            $dbExercises = $this->loadExercisesFromMysql(
                $mysqlDsn,
                $this->option('mysql-user') ?? 'forge',
                $this->option('mysql-password') ?? ''
            );
        } else {
            $dbExercises = Exercise::withTrashed()
                ->with('translations')
                ->get();
        }

        $this->info("Found {$dbExercises->count()} exercises in database.");

        $dbIndex = $this->buildDbIndex($dbExercises);

        // Step 2: Index media files
        if ($videoDir && is_dir($videoDir)) {
            $this->info('Indexing video files...');
            $this->indexVideos($videoDir);
            $this->info('Found ' . count($this->videoIndex) . ' video files.');
        }

        if ($illustrationDir && is_dir($illustrationDir)) {
            $this->info('Indexing illustration files...');
            $this->indexIllustrations($illustrationDir);
            $this->info('Found ' . count($this->illustrationIndex) . ' illustration entries.');
        }

        // Step 3: Parse bundle XLSX
        $this->info('Parsing bundle XLSX...');
        $bundleExercises = $this->parseBundleXlsx($xlsxPath);
        $this->info('Found ' . count($bundleExercises) . ' unique exercises in bundle.');

        // Step 4: Match exercises (fuzzy + exact)
        $this->info('Matching exercises...');
        $report = $this->matchExercises($bundleExercises, $dbIndex, $threshold);

        // Step 5: AI verification of fuzzy matches
        if ($aiVerify) {
            $report = $this->aiVerifyMatches($report);
        }

        // Step 6: Add DB exercises that have no bundle match
        $matchedDbIds = collect($report)->pluck('db_id')->filter()->unique()->toArray();
        $unmatchedDb = $dbExercises->filter(fn ($ex) => ! in_array($ex->id, $matchedDbIds) && empty($ex->deleted_at));

        foreach ($unmatchedDb as $ex) {
            $report[] = $this->buildDbOnlyEntry($ex);
        }

        // Step 7: Generate output XLSX
        $this->info('Generating report...');
        $this->generateReport($report, $outputPath);

        $this->printSummary($report, $dbExercises, $bundleExercises, $outputPath);

        return self::SUCCESS;
    }

    private function aiVerifyMatches(array $report): array
    {
        $fuzzyEntries = [];
        $fuzzyIndices = [];

        foreach ($report as $i => $entry) {
            if ($entry['match_type'] === 'fuzzy') {
                $fuzzyEntries[] = $entry;
                $fuzzyIndices[] = $i;
            }
        }

        if (empty($fuzzyEntries)) {
            $this->info('No fuzzy matches to verify.');

            return $report;
        }

        $count = count($fuzzyEntries);
        $this->info("Verifying {$count} fuzzy matches with AI...");

        $batchSize = (int) $this->option('batch-size');
        $batches = array_chunk(array_keys($fuzzyEntries), $batchSize);
        $verified = 0;
        $rejected = 0;

        $bar = $this->output->createProgressBar(count($fuzzyEntries));

        foreach ($batches as $batchKeys) {
            $pairs = [];
            foreach ($batchKeys as $key) {
                $entry = $fuzzyEntries[$key];
                $pairs[] = [
                    'index' => $key,
                    'bundle' => $entry['bundle_name'],
                    'db' => $entry['db_name'],
                    'bundle_equipment' => $entry['bundle_equipment'],
                    'db_equipment' => $entry['db_equipment'],
                ];
            }

            $results = $this->callAiVerification($pairs);

            foreach ($results as $result) {
                $key = $result['index'];
                $reportIndex = $fuzzyIndices[$key];

                if ($result['match']) {
                    $report[$reportIndex]['match_type'] = 'ai_verified';
                    $report[$reportIndex]['action'] = 'UPDATE_MEDIA';
                    $verified++;
                } else {
                    // Not a match — clear the DB association
                    $report[$reportIndex]['match_type'] = 'ai_rejected';
                    $report[$reportIndex]['action'] = 'NEW_EXERCISE';
                    $report[$reportIndex]['ai_reason'] = $result['reason'] ?? '';
                    $report[$reportIndex]['db_id'] = null;
                    $report[$reportIndex]['db_name'] = '';
                    $report[$reportIndex]['db_slug'] = '';
                    $report[$reportIndex]['db_equipment'] = '';
                    $report[$reportIndex]['db_primary_muscles'] = '';
                    $report[$reportIndex]['db_has_description'] = false;
                    $report[$reportIndex]['db_has_instructions'] = false;
                    $report[$reportIndex]['db_has_image'] = false;
                    $report[$reportIndex]['db_has_video'] = false;
                    $rejected++;
                }

                $bar->advance();
            }
        }

        $bar->finish();
        $this->newLine();
        $this->info("AI verification: {$verified} confirmed, {$rejected} rejected.");

        return $report;
    }

    private function callAiVerification(array $pairs): array
    {
        $pairList = '';
        foreach ($pairs as $i => $pair) {
            $pairList .= ($i + 1) . ". Bundle: \"{$pair['bundle']}\"";
            if ($pair['bundle_equipment']) {
                $pairList .= " (equipment: {$pair['bundle_equipment']})";
            }
            $pairList .= " <-> DB: \"{$pair['db']}\"";
            if ($pair['db_equipment']) {
                $pairList .= " (equipment: {$pair['db_equipment']})";
            }
            $pairList .= "\n";
        }

        $prompt = <<<PROMPT
You are an exercise database expert. For each numbered pair below, determine if the BUNDLE exercise and DB exercise refer to the SAME exercise (just named differently) or are DIFFERENT exercises.

Rules:
- "Same exercise" means identical movement pattern, equipment, and target muscles
- Different grip widths, angles (incline/decline/flat), or equipment (barbell vs dumbbell vs cable) make them DIFFERENT exercises
- Minor spelling/pluralization differences are the SAME (e.g., "Bicep Curl" = "Biceps Curl")
- Adding "Band" or "Cable" to a free weight exercise makes it DIFFERENT
- "Bodyweight squat" and "Barbell squat" are DIFFERENT
- Supersets are DIFFERENT from individual exercises

Respond with ONLY a JSON array. Each element: {"index": <original_pair_index>, "match": true/false, "reason": "brief reason if false"}

Pairs:
{$pairList}
PROMPT;

        $maxRetries = 3;
        $attempt = 0;

        while ($attempt < $maxRetries) {
            $attempt++;

            try {
                $response = OpenAI::chat()->create([
                    'model' => config('ai.models.simple'),
                    'messages' => [
                        ['role' => 'user', 'content' => $prompt],
                    ],
                    'temperature' => 0,
                ]);

                $content = $response->choices[0]->message->content;
                // Extract JSON from response (may be wrapped in ```json blocks)
                $content = preg_replace('/^```(?:json)?\s*/m', '', $content);
                $content = preg_replace('/\s*```$/m', '', $content);
                $content = trim($content);

                $parsed = json_decode($content, true);

                if (! is_array($parsed)) {
                    $this->warn("AI response was not valid JSON (attempt {$attempt}), retrying...");

                    continue;
                }

                // Map back to original indices
                $results = [];
                foreach ($parsed as $item) {
                    // The AI returns 1-based index from the prompt
                    $promptIndex = ($item['index'] ?? $item['pair'] ?? 0);
                    if ($promptIndex >= 1 && $promptIndex <= count($pairs)) {
                        $originalKey = $pairs[$promptIndex - 1]['index'];
                        $results[] = [
                            'index' => $originalKey,
                            'match' => $item['match'] ?? false,
                            'reason' => $item['reason'] ?? '',
                        ];
                    }
                }

                // If we got results for most pairs, return them
                if (count($results) >= count($pairs) * 0.8) {
                    // Fill in any missing pairs as rejected
                    $returnedIndices = array_column($results, 'index');
                    foreach ($pairs as $pair) {
                        if (! in_array($pair['index'], $returnedIndices)) {
                            $results[] = [
                                'index' => $pair['index'],
                                'match' => false,
                                'reason' => 'AI did not return result for this pair',
                            ];
                        }
                    }

                    return $results;
                }

                $this->warn("AI only returned " . count($results) . '/' . count($pairs) . " results (attempt {$attempt})");
            } catch (\Exception $e) {
                $this->warn("AI call failed (attempt {$attempt}): " . $e->getMessage());
            }
        }

        // Fallback: mark all as needing manual review
        $this->error('AI verification failed after retries — marking batch as manual review.');

        return array_map(fn ($pair) => [
            'index' => $pair['index'],
            'match' => false,
            'reason' => 'AI verification failed',
        ], $pairs);
    }

    private function loadExercisesFromMysql(string $dsn, string $user, string $password): \Illuminate\Support\Collection
    {
        $pdo = new \PDO($dsn, $user, $password);
        $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

        $stmt = $pdo->query('SELECT * FROM exercises');
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $exerciseIds = array_column($rows, 'id');
        $translations = [];
        if ($exerciseIds) {
            $stmt = $pdo->query('SELECT * FROM exercise_translations');
            foreach ($stmt->fetchAll(\PDO::FETCH_ASSOC) as $t) {
                $translations[$t['exercise_id']][] = $t;
            }
        }

        $exercises = collect();
        foreach ($rows as $row) {
            $ex = new \stdClass;
            $ex->id = $row['id'];
            $ex->name = $row['name'];
            $ex->slug = $row['slug'];
            $ex->equipment = json_decode($row['equipment'] ?? '[]', true) ?: [];
            $ex->primary_muscles = json_decode($row['primary_muscles'] ?? '[]', true) ?: [];
            $ex->secondary_muscles = json_decode($row['secondary_muscles'] ?? '[]', true) ?: [];
            $ex->description = $row['description'];
            $ex->instructions = $row['instructions'];
            $ex->form_cues = $row['form_cues'];
            $ex->image = $row['image'];
            $ex->video_url = $row['video_url'];
            $ex->deleted_at = $row['deleted_at'];

            // Build translation objects
            $ex->translations = collect();
            foreach ($translations[$row['id']] ?? [] as $t) {
                $trans = new \stdClass;
                $trans->name = $t['name'];
                $trans->aliases = json_decode($t['aliases'] ?? '[]', true) ?: [];
                $trans->locale = $t['locale'];
                $ex->translations->push($trans);
            }

            $exercises->push($ex);
        }

        return $exercises;
    }

    private function buildDbIndex($dbExercises): array
    {
        $index = [];

        foreach ($dbExercises as $exercise) {
            $normalized = $this->normalizeExerciseName($exercise->name);
            $index[$normalized] = $exercise;

            $index[$exercise->slug] = $exercise;

            foreach ($exercise->translations as $translation) {
                $transNorm = $this->normalizeExerciseName($translation->name);
                if (! isset($index[$transNorm])) {
                    $index[$transNorm] = $exercise;
                }

                foreach ($translation->aliases ?? [] as $alias) {
                    $aliasNorm = $this->normalizeExerciseName($alias);
                    if (! isset($index[$aliasNorm])) {
                        $index[$aliasNorm] = $exercise;
                    }
                }
            }
        }

        return $index;
    }

    private function normalizeExerciseName(string $name): string
    {
        $name = strtolower(trim($name));
        $name = preg_replace('/_?(female|male)$/i', '', $name);
        $name = str_replace(['-', '_', '–', '—'], ' ', $name);
        $name = preg_replace('/\s+/', ' ', $name);

        return trim($name);
    }

    private function generateSlug(string $name): string
    {
        return Str::slug($this->normalizeExerciseName($name));
    }

    private function indexVideos(string $dir): void
    {
        $files = glob($dir . '/*/*.mp4');
        foreach ($files as $file) {
            $basename = pathinfo($file, PATHINFO_FILENAME);
            $isFemale = (bool) preg_match('/_?female$/i', $basename);
            $canonical = preg_replace('/_?(female|male)$/i', '', $basename);
            $canonicalNorm = $this->normalizeExerciseName($canonical);

            if (! isset($this->videoIndex[$canonicalNorm])) {
                $this->videoIndex[$canonicalNorm] = ['male' => null, 'female' => null];
            }

            if ($isFemale) {
                $this->videoIndex[$canonicalNorm]['female'] = $file;
            } else {
                $this->videoIndex[$canonicalNorm]['male'] = $file;
            }
        }
    }

    private function indexIllustrations(string $dir): void
    {
        $files = glob($dir . '/*/*.{jpeg,jpg,png}', GLOB_BRACE);
        foreach ($files as $file) {
            $basename = pathinfo($file, PATHINFO_FILENAME);
            $basename = preg_replace('/\d+$/', '', $basename);
            $isFemale = (bool) preg_match('/_?female$/i', $basename);
            $canonical = preg_replace('/_?(female|male)$/i', '', $basename);
            $canonicalNorm = $this->normalizeExerciseName($canonical);

            if (! isset($this->illustrationIndex[$canonicalNorm])) {
                $this->illustrationIndex[$canonicalNorm] = ['male' => null, 'female' => null];
            }

            if ($isFemale) {
                $this->illustrationIndex[$canonicalNorm]['female'] = dirname($file);
            } else {
                $this->illustrationIndex[$canonicalNorm]['male'] = dirname($file);
            }
        }
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

            $rawName = trim($row['B']);
            $canonical = preg_replace('/_?(female|male)$/i', '', $rawName);
            $canonical = trim($canonical);
            $normalizedKey = $this->normalizeExerciseName($canonical);

            if (isset($exercises[$normalizedKey]) && empty($row['C'])) {
                continue;
            }

            $exercises[$normalizedKey] = [
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

    private function matchExercises(array $bundleExercises, array $dbIndex, int $threshold): array
    {
        $report = [];
        $dbNames = array_keys($dbIndex);

        $bar = $this->output->createProgressBar(count($bundleExercises));

        foreach ($bundleExercises as $normalizedName => $bundle) {
            $bar->advance();

            $match = null;
            $matchScore = 0;
            $matchType = 'new';

            // Try exact match
            if (isset($dbIndex[$normalizedName])) {
                $match = $dbIndex[$normalizedName];
                $matchScore = 100;
                $matchType = 'exact';
            }

            // Try slug match
            if (! $match) {
                $slug = $this->generateSlug($bundle['raw_name']);
                if (isset($dbIndex[$slug])) {
                    $match = $dbIndex[$slug];
                    $matchScore = 100;
                    $matchType = 'exact';
                }
            }

            // Try fuzzy match — find best candidate for AI to verify
            if (! $match) {
                $bestScore = 0;
                $bestName = null;

                foreach ($dbNames as $dbName) {
                    if (abs(strlen($normalizedName) - strlen($dbName)) > 25) {
                        continue;
                    }

                    similar_text($normalizedName, $dbName, $percent);

                    if ($percent > $bestScore) {
                        $bestScore = $percent;
                        $bestName = $dbName;
                    }
                }

                if ($bestScore >= $threshold && $bestName) {
                    $match = $dbIndex[$bestName];
                    $matchScore = round($bestScore);
                    $matchType = 'fuzzy';
                }
            }

            // Look up media
            $videoInfo = $this->videoIndex[$normalizedName] ?? ['male' => null, 'female' => null];
            $illustrationInfo = $this->illustrationIndex[$normalizedName] ?? ['male' => null, 'female' => null];

            $report[] = [
                'bundle_name' => $bundle['raw_name'],
                'bundle_category' => $bundle['category'],
                'bundle_equipment' => $bundle['equipment'],
                'bundle_primary_muscles' => $bundle['primary_muscles'],
                'bundle_secondary_muscles' => $bundle['secondary_muscles'],
                'bundle_has_instructions' => ! empty($bundle['instructions']),
                'bundle_has_tips' => ! empty($bundle['tips']),
                'db_id' => $match?->id,
                'db_name' => $match?->name ?? '',
                'db_slug' => $match?->slug ?? '',
                'db_equipment' => $match ? implode(', ', $match->equipment ?? []) : '',
                'db_primary_muscles' => $match ? implode(', ', $match->primary_muscles ?? []) : '',
                'db_has_description' => $match ? ! empty($match->description) : false,
                'db_has_instructions' => $match ? ! empty($match->instructions) : false,
                'db_has_image' => $match ? ! empty($match->image) : false,
                'db_has_video' => $match ? ! empty($match->video_url) : false,
                'match_score' => $matchScore,
                'match_type' => $matchType,
                'has_video_male' => ! empty($videoInfo['male']),
                'has_video_female' => ! empty($videoInfo['female']),
                'has_illustration_male' => ! empty($illustrationInfo['male']),
                'has_illustration_female' => ! empty($illustrationInfo['female']),
                'video_path_male' => $videoInfo['male'] ?? '',
                'video_path_female' => $videoInfo['female'] ?? '',
                'illustration_path_male' => $illustrationInfo['male'] ?? '',
                'illustration_path_female' => $illustrationInfo['female'] ?? '',
                'ai_reason' => '',
                'action' => $this->suggestAction($matchType, $matchScore, $match),
            ];
        }

        $bar->finish();
        $this->newLine();

        return $report;
    }

    private function suggestAction(string $matchType, int $score, mixed $match): string
    {
        if ($matchType === 'new') {
            return 'NEW_EXERCISE';
        }

        if ($matchType === 'exact') {
            return 'UPDATE_MEDIA';
        }

        return 'PENDING_AI_VERIFY';
    }

    private function buildDbOnlyEntry(mixed $ex): array
    {
        return [
            'bundle_name' => '',
            'bundle_category' => '',
            'bundle_equipment' => '',
            'bundle_primary_muscles' => '',
            'bundle_secondary_muscles' => '',
            'bundle_has_instructions' => false,
            'bundle_has_tips' => false,
            'db_id' => $ex->id,
            'db_name' => $ex->name,
            'db_slug' => $ex->slug,
            'db_equipment' => implode(', ', $ex->equipment ?? []),
            'db_primary_muscles' => implode(', ', $ex->primary_muscles ?? []),
            'db_has_description' => ! empty($ex->description),
            'db_has_instructions' => ! empty($ex->instructions),
            'db_has_image' => ! empty($ex->image),
            'db_has_video' => ! empty($ex->video_url),
            'match_score' => 0,
            'match_type' => 'db_only',
            'has_video_male' => false,
            'has_video_female' => false,
            'has_illustration_male' => false,
            'has_illustration_female' => false,
            'video_path_male' => '',
            'video_path_female' => '',
            'illustration_path_male' => '',
            'illustration_path_female' => '',
            'ai_reason' => '',
            'action' => 'NO_BUNDLE_MATCH',
        ];
    }

    private function printSummary(array $report, $dbExercises, array $bundleExercises, string $outputPath): void
    {
        $this->newLine();
        $this->info('=== AUDIT SUMMARY ===');
        $this->table(
            ['Metric', 'Count'],
            [
                ['DB exercises (active)', $dbExercises->filter(fn ($e) => empty($e->deleted_at))->count()],
                ['Bundle exercises', count($bundleExercises)],
                ['Exact matches', collect($report)->where('match_type', 'exact')->count()],
                ['AI verified matches', collect($report)->where('match_type', 'ai_verified')->count()],
                ['AI rejected (→ new)', collect($report)->where('match_type', 'ai_rejected')->count()],
                ['Unverified fuzzy', collect($report)->where('match_type', 'fuzzy')->count()],
                ['Bundle-only (new)', collect($report)->whereIn('action', ['NEW_EXERCISE'])->count()],
                ['DB-only (no bundle)', collect($report)->where('match_type', 'db_only')->count()],
                ['With video available', collect($report)->filter(fn ($r) => $r['has_video_male'] || $r['has_video_female'])->count()],
                ['With illustration available', collect($report)->filter(fn ($r) => $r['has_illustration_male'] || $r['has_illustration_female'])->count()],
            ]
        );

        $this->info("Report saved to: {$outputPath}");
    }

    private function generateReport(array $report, string $outputPath): void
    {
        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Exercise Audit');

        $headers = [
            'A' => 'Action',
            'B' => 'Match Score',
            'C' => 'Match Type',
            'D' => 'Bundle Name',
            'E' => 'DB Name',
            'F' => 'DB ID',
            'G' => 'AI Reason',
            'H' => 'Bundle Category',
            'I' => 'Bundle Equipment',
            'J' => 'DB Equipment',
            'K' => 'Bundle Primary Muscles',
            'L' => 'DB Primary Muscles',
            'M' => 'Bundle Secondary Muscles',
            'N' => 'Bundle Has Instructions',
            'O' => 'Bundle Has Tips',
            'P' => 'DB Has Description',
            'Q' => 'DB Has Instructions',
            'R' => 'DB Has Image',
            'S' => 'DB Has Video',
            'T' => 'Video (Male)',
            'U' => 'Video (Female)',
            'V' => 'Illustration (Male)',
            'W' => 'Illustration (Female)',
            'X' => 'Video Path (Male)',
            'Y' => 'Video Path (Female)',
        ];

        foreach ($headers as $col => $header) {
            $sheet->setCellValue($col . '1', $header);
        }

        $lastCol = 'Y';
        $headerRange = "A1:{$lastCol}1";
        $sheet->getStyle($headerRange)->getFont()->setBold(true);
        $sheet->getStyle($headerRange)->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFD9E1F2');
        $sheet->setAutoFilter($headerRange);

        // Sort report: exact/ai_verified first, then ai_rejected/new, then db_only
        usort($report, function ($a, $b) {
            $order = [
                'UPDATE_MEDIA' => 1,
                'PENDING_AI_VERIFY' => 2,
                'NEW_EXERCISE' => 3,
                'NO_BUNDLE_MATCH' => 4,
            ];

            return ($order[$a['action']] ?? 5) <=> ($order[$b['action']] ?? 5);
        });

        $row = 2;
        foreach ($report as $entry) {
            $sheet->setCellValue("A{$row}", $entry['action']);
            $sheet->setCellValue("B{$row}", $entry['match_score']);
            $sheet->setCellValue("C{$row}", $entry['match_type']);
            $sheet->setCellValue("D{$row}", $entry['bundle_name']);
            $sheet->setCellValue("E{$row}", $entry['db_name']);
            $sheet->setCellValue("F{$row}", $entry['db_id']);
            $sheet->setCellValue("G{$row}", $entry['ai_reason'] ?? '');
            $sheet->setCellValue("H{$row}", $entry['bundle_category']);
            $sheet->setCellValue("I{$row}", $entry['bundle_equipment']);
            $sheet->setCellValue("J{$row}", $entry['db_equipment']);
            $sheet->setCellValue("K{$row}", $entry['bundle_primary_muscles']);
            $sheet->setCellValue("L{$row}", $entry['db_primary_muscles']);
            $sheet->setCellValue("M{$row}", $entry['bundle_secondary_muscles']);
            $sheet->setCellValue("N{$row}", $entry['bundle_has_instructions'] ? 'YES' : 'NO');
            $sheet->setCellValue("O{$row}", $entry['bundle_has_tips'] ? 'YES' : 'NO');
            $sheet->setCellValue("P{$row}", $entry['db_has_description'] ? 'YES' : 'NO');
            $sheet->setCellValue("Q{$row}", $entry['db_has_instructions'] ? 'YES' : 'NO');
            $sheet->setCellValue("R{$row}", $entry['db_has_image'] ? 'YES' : 'NO');
            $sheet->setCellValue("S{$row}", $entry['db_has_video'] ? 'YES' : 'NO');
            $sheet->setCellValue("T{$row}", $entry['has_video_male'] ? 'YES' : 'NO');
            $sheet->setCellValue("U{$row}", $entry['has_video_female'] ? 'YES' : 'NO');
            $sheet->setCellValue("V{$row}", $entry['has_illustration_male'] ? 'YES' : 'NO');
            $sheet->setCellValue("W{$row}", $entry['has_illustration_female'] ? 'YES' : 'NO');
            $sheet->setCellValue("X{$row}", $entry['video_path_male']);
            $sheet->setCellValue("Y{$row}", $entry['video_path_female']);

            $actionColor = match ($entry['action']) {
                'UPDATE_MEDIA' => 'FFC6EFCE',
                'NEW_EXERCISE' => 'FFFCE4D6',
                'PENDING_AI_VERIFY' => 'FFFFF2CC',
                'NO_BUNDLE_MATCH' => 'FFD9D9D9',
                default => 'FFFFFFFF',
            };

            $sheet->getStyle("A{$row}")->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()->setARGB($actionColor);

            $row++;
        }

        foreach (range('A', $lastCol) as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $sheet->freezePane('A2');

        $dir = dirname($outputPath);
        if (! is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $writer = new Xlsx($spreadsheet);
        $writer->save($outputPath);
    }
}
