<?php

namespace App\Console\Commands\Exercises;

use Illuminate\Console\Command;
use OpenAI\Laravel\Facades\OpenAI;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class SmartMatchCommand extends Command
{
    protected $signature = 'exercises:smart-match
                            {xlsx : Path to the bundle XLSX file}
                            {--video-dir= : Path to video directory}
                            {--illustration-dir= : Path to illustrations directory}
                            {--output= : Output XLSX path}
                            {--mysql= : MySQL DSN}
                            {--mysql-user=forge : MySQL username}
                            {--mysql-password= : MySQL password}
                            {--batch-size=20 : DB exercises per AI batch}';

    protected $description = 'Smart AI-powered matching: sends full bundle list to AI for each batch of DB exercises';

    private array $videoIndex = [];

    private array $illustrationIndex = [];

    public function handle(): int
    {
        ini_set('memory_limit', '512M');

        $outputPath = $this->option('output') ?? storage_path('app/exercise-smart-match.xlsx');

        // Step 1: Load bundle exercises
        $this->info('Loading bundle...');
        $bundleExercises = $this->parseBundleXlsx($this->argument('xlsx'));
        $this->info('Bundle: ' . count($bundleExercises) . ' exercises');

        // Build bundle name list grouped by category for the AI prompt
        $bundleByCategory = [];
        foreach ($bundleExercises as $key => $bundle) {
            $cat = $bundle['category'] ?: 'Uncategorized';
            $bundleByCategory[$cat][] = $bundle['raw_name'];
        }

        // Step 2: Index media
        $videoDir = $this->option('video-dir');
        if ($videoDir && is_dir($videoDir)) {
            $this->indexVideos($videoDir);
            $this->info('Videos: ' . count($this->videoIndex));
        }

        $illustrationDir = $this->option('illustration-dir');
        if ($illustrationDir && is_dir($illustrationDir)) {
            $this->indexIllustrations($illustrationDir);
            $this->info('Illustrations: ' . count($this->illustrationIndex));
        }

        // Step 3: Load DB exercises with mobile usage
        $this->info('Loading DB exercises...');
        $pdo = new \PDO(
            $this->option('mysql'),
            $this->option('mysql-user'),
            $this->option('mysql-password')
        );
        $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

        $dbExercises = $this->loadDbExercises($pdo);
        $this->info('DB: ' . count($dbExercises) . ' exercises');

        // Step 4: First pass — exact matches (no AI needed)
        $this->info('Finding exact matches...');
        $exactMatches = [];
        $unmatched = [];

        foreach ($dbExercises as $dbId => $dbEx) {
            $dbNorm = $this->normalize($dbEx['name']);
            if (isset($bundleExercises[$dbNorm])) {
                $exactMatches[$dbId] = $dbNorm;
            } else {
                $unmatched[$dbId] = $dbEx;
            }
        }
        $this->info('Exact matches: ' . count($exactMatches));
        $this->info('Need AI matching: ' . count($unmatched));

        // Step 5: AI matching — send batches of DB exercises with full bundle list
        $this->info('AI matching...');
        $bundleListText = $this->buildBundleListText($bundleByCategory);
        $aiMatches = $this->aiMatchBatches($unmatched, $bundleListText, $bundleExercises);

        // Step 6: Build report
        $this->info('Building report...');
        $report = $this->buildReport($dbExercises, $bundleExercises, $exactMatches, $aiMatches);
        $this->generateReport($report, $outputPath);
        $this->printSummary($report, $outputPath);

        return self::SUCCESS;
    }

    private function loadDbExercises(\PDO $pdo): array
    {
        $stmt = $pdo->query("
            SELECT
                e.id, e.name, e.slug, e.type, e.equipment, e.primary_muscles,
                e.description, e.image, e.video_url,
                COUNT(DISTINCT wpe.id) as total_usage,
                COUNT(DISTINCT CASE WHEN u.source = 'mobile_apple' THEN wpe.id END) as mobile_usage
            FROM exercises e
            LEFT JOIN workout_plan_exercises wpe ON wpe.exercise_id = e.id
            LEFT JOIN workout_plans wp ON wp.id = wpe.workout_plan_id
            LEFT JOIN plans p ON p.id = wp.plan_id
            LEFT JOIN users u ON u.id = p.user_id
            WHERE e.deleted_at IS NULL
            GROUP BY e.id, e.name, e.slug, e.type, e.equipment, e.primary_muscles,
                     e.description, e.image, e.video_url
            ORDER BY mobile_usage DESC, total_usage DESC
        ");

        $exercises = [];
        foreach ($stmt->fetchAll(\PDO::FETCH_ASSOC) as $row) {
            $exercises[(int) $row['id']] = $row;
        }

        return $exercises;
    }

    private function buildBundleListText(array $bundleByCategory): string
    {
        $text = '';
        foreach ($bundleByCategory as $category => $names) {
            $text .= "\n[{$category}]\n";
            $text .= implode("\n", $names) . "\n";
        }

        return $text;
    }

    private function aiMatchBatches(array $unmatched, string $bundleListText, array $bundleExercises): array
    {
        $batchSize = (int) $this->option('batch-size');
        $batches = array_chunk($unmatched, $batchSize, true);
        $allMatches = [];

        $bar = $this->output->createProgressBar(count($unmatched));

        foreach ($batches as $batch) {
            $results = $this->callAiSmartMatch($batch, $bundleListText, $bundleExercises);

            foreach ($results as $dbId => $result) {
                $allMatches[$dbId] = $result;
                $bar->advance();
            }
        }

        $bar->finish();
        $this->newLine();

        $matched = count(array_filter($allMatches, fn ($r) => $r['bundle_key'] !== null));
        $noMatch = count($allMatches) - $matched;
        $this->info("AI results: {$matched} matched, {$noMatch} no match");

        return $allMatches;
    }

    private function callAiSmartMatch(array $batch, string $bundleListText, array $bundleExercises): array
    {
        $dbList = '';
        $dbIds = [];
        $i = 1;
        foreach ($batch as $dbId => $dbEx) {
            $dbIds[$i] = $dbId;
            $equip = json_decode($dbEx['equipment'] ?? '[]', true) ?: [];
            $dbList .= "{$i}. \"{$dbEx['name']}\" (type: {$dbEx['type']}";
            if ($equip) {
                $dbList .= ', equipment: ' . implode(', ', $equip);
            }
            $dbList .= ")\n";
            $i++;
        }

        $prompt = <<<PROMPT
You are an exercise database expert. Match each DB exercise to the BEST match from the bundle list below.

Rules:
- Same exercise = identical core movement, even if named differently
- "Romanian Deadlift" in DB can match "Barbell romanian deadlift" in bundle (same movement, barbell is default)
- "Bench Press" can match "Barbell Bench Press" (barbell is default for bench press)
- "Back Squat" can match "Barbell Back Squat" (barbell is default)
- "Plank" can match "Plank on elbows" or "High plank" — pick the closest standard version
- "Child's Pose Stretch" can match "Child pose"
- Stretches: match if they target the same muscle/area
- Do NOT match generic routine names (e.g., "Dynamic Warmup", "Cooldown Stretch", "5 Minute Warmup") — these are routines, not exercises. Return null for these.
- Do NOT match if equipment type differs significantly (dumbbell vs barbell = different exercises ONLY when the DB name explicitly specifies the equipment)
- Supersets/compound exercises usually have no match — return null
- If unsure, return null

Respond with ONLY a JSON array: [{"n": <number>, "match": "<exact bundle name or null>"}]

DB EXERCISES:
{$dbList}

BUNDLE EXERCISES:
{$bundleListText}
PROMPT;

        $maxRetries = 3;

        for ($attempt = 1; $attempt <= $maxRetries; $attempt++) {
            try {
                $response = OpenAI::chat()->create([
                    'model' => config('ai.models.agent'),
                    'messages' => [['role' => 'user', 'content' => $prompt]],
                    'temperature' => 0,
                ]);

                $content = $response->choices[0]->message->content;
                $content = preg_replace('/^```(?:json)?\s*/m', '', $content);
                $content = preg_replace('/\s*```$/m', '', $content);

                $parsed = json_decode(trim($content), true);
                if (! is_array($parsed)) {
                    $this->warn("Invalid JSON (attempt {$attempt})");

                    continue;
                }

                $results = [];
                foreach ($parsed as $item) {
                    $num = $item['n'] ?? null;
                    $matchName = $item['match'] ?? null;
                    $dbId = $dbIds[$num] ?? null;

                    if (! $dbId) {
                        continue;
                    }

                    if ($matchName) {
                        $bundleKey = $this->normalize($matchName);
                        if (isset($bundleExercises[$bundleKey])) {
                            $results[$dbId] = [
                                'bundle_key' => $bundleKey,
                                'bundle_name' => $bundleExercises[$bundleKey]['raw_name'],
                            ];
                        } else {
                            // Try case-insensitive search
                            $found = false;
                            foreach ($bundleExercises as $bk => $bv) {
                                if (strcasecmp(trim($bv['raw_name']), trim($matchName)) === 0) {
                                    $results[$dbId] = ['bundle_key' => $bk, 'bundle_name' => $bv['raw_name']];
                                    $found = true;
                                    break;
                                }
                            }
                            if (! $found) {
                                $results[$dbId] = ['bundle_key' => null, 'bundle_name' => null, 'note' => "AI said '{$matchName}' but not in bundle"];
                            }
                        }
                    } else {
                        $results[$dbId] = ['bundle_key' => null, 'bundle_name' => null];
                    }
                }

                // Fill missing
                foreach ($dbIds as $num => $dbId) {
                    if (! isset($results[$dbId])) {
                        $results[$dbId] = ['bundle_key' => null, 'bundle_name' => null, 'note' => 'AI did not return'];
                    }
                }

                return $results;
            } catch (\Exception $e) {
                $this->warn("AI error (attempt {$attempt}): " . $e->getMessage());
            }
        }

        $results = [];
        foreach ($batch as $dbId => $dbEx) {
            $results[$dbId] = ['bundle_key' => null, 'bundle_name' => null, 'note' => 'AI failed'];
        }

        return $results;
    }

    private function buildReport(array $dbExercises, array $bundleExercises, array $exactMatches, array $aiMatches): array
    {
        $report = [];
        $usedBundleKeys = [];

        foreach ($dbExercises as $dbId => $dbEx) {
            $mobileUsage = (int) $dbEx['mobile_usage'];
            $totalUsage = (int) $dbEx['total_usage'];
            $equip = implode(', ', json_decode($dbEx['equipment'] ?? '[]', true) ?: []);
            $muscles = implode(', ', json_decode($dbEx['primary_muscles'] ?? '[]', true) ?: []);

            $bundleKey = null;
            $bundleName = '';
            $matchType = 'none';
            $note = '';

            if (isset($exactMatches[$dbId])) {
                $bundleKey = $exactMatches[$dbId];
                $bundleName = $bundleExercises[$bundleKey]['raw_name'];
                $matchType = 'exact';
            } elseif (isset($aiMatches[$dbId]) && $aiMatches[$dbId]['bundle_key'] !== null) {
                $bundleKey = $aiMatches[$dbId]['bundle_key'];
                $bundleName = $aiMatches[$dbId]['bundle_name'];
                $matchType = 'ai_matched';
            } else {
                $note = $aiMatches[$dbId]['note'] ?? '';
            }

            if ($bundleKey) {
                $usedBundleKeys[$bundleKey] = true;
            }

            // Determine action
            $action = 'DB_ONLY_NO_MOBILE';
            if ($bundleKey) {
                $action = 'UPDATE_MEDIA';
            } elseif ($mobileUsage > 0) {
                $isGeneric = (bool) preg_match('/^(dynamic |general |full body |5|10|joint|light |warm|cool|stretch|mobility|foam)/i', $dbEx['name']);
                $isSuperset = (bool) preg_match('/(superset| \+ |to |\/)/i', $dbEx['name']);
                if ($isGeneric) {
                    $action = 'GENERIC_ROUTINE';
                } elseif ($isSuperset) {
                    $action = 'SUPERSET_NO_MEDIA';
                } else {
                    $action = 'DB_ONLY_MOBILE';
                }
            }

            $videoInfo = $bundleKey ? ($this->videoIndex[$bundleKey] ?? ['male' => null, 'female' => null]) : ['male' => null, 'female' => null];
            $illuInfo = $bundleKey ? ($this->illustrationIndex[$bundleKey] ?? ['male' => null, 'female' => null]) : ['male' => null, 'female' => null];

            $report[] = [
                'action' => $action,
                'mobile_usage' => $mobileUsage,
                'total_usage' => $totalUsage,
                'db_id' => $dbId,
                'db_name' => $dbEx['name'],
                'db_type' => $dbEx['type'],
                'bundle_name' => $bundleName,
                'match_type' => $matchType,
                'db_equipment' => $equip,
                'bundle_equipment' => $bundleKey ? ($bundleExercises[$bundleKey]['equipment'] ?? '') : '',
                'db_muscles' => $muscles,
                'bundle_muscles' => $bundleKey ? ($bundleExercises[$bundleKey]['primary_muscles'] ?? '') : '',
                'has_video_m' => ! empty($videoInfo['male']),
                'has_video_f' => ! empty($videoInfo['female']),
                'has_illu_m' => ! empty($illuInfo['male']),
                'has_illu_f' => ! empty($illuInfo['female']),
                'note' => $note,
            ];
        }

        // Add new bundle exercises
        foreach ($bundleExercises as $bundleKey => $bundle) {
            if (isset($usedBundleKeys[$bundleKey])) {
                continue;
            }

            $videoInfo = $this->videoIndex[$bundleKey] ?? ['male' => null, 'female' => null];
            $illuInfo = $this->illustrationIndex[$bundleKey] ?? ['male' => null, 'female' => null];

            $report[] = [
                'action' => 'NEW_FROM_BUNDLE',
                'mobile_usage' => 0,
                'total_usage' => 0,
                'db_id' => null,
                'db_name' => '',
                'db_type' => '',
                'bundle_name' => $bundle['raw_name'],
                'match_type' => 'new',
                'db_equipment' => '',
                'bundle_equipment' => $bundle['equipment'],
                'db_muscles' => '',
                'bundle_muscles' => $bundle['primary_muscles'],
                'has_video_m' => ! empty($videoInfo['male']),
                'has_video_f' => ! empty($videoInfo['female']),
                'has_illu_m' => ! empty($illuInfo['male']),
                'has_illu_f' => ! empty($illuInfo['female']),
                'note' => '',
            ];
        }

        // Sort
        usort($report, function ($a, $b) {
            $order = ['UPDATE_MEDIA' => 1, 'DB_ONLY_MOBILE' => 2, 'SUPERSET_NO_MEDIA' => 3, 'GENERIC_ROUTINE' => 4, 'DB_ONLY_NO_MOBILE' => 5, 'NEW_FROM_BUNDLE' => 6];

            $cmp = ($order[$a['action']] ?? 9) <=> ($order[$b['action']] ?? 9);
            if ($cmp !== 0) {
                return $cmp;
            }

            return $b['mobile_usage'] <=> $a['mobile_usage'];
        });

        return $report;
    }

    private function printSummary(array $report, string $outputPath): void
    {
        $c = collect($report);
        $updateMedia = $c->where('action', 'UPDATE_MEDIA');

        $this->newLine();
        $this->info('=== FINAL SMART MATCH SUMMARY ===');
        $this->table(
            ['Action', 'Count', 'Notes'],
            [
                ['UPDATE_MEDIA', $updateMedia->count(), 'Matched — will get video + illustration'],
                ['  exact', $updateMedia->where('match_type', 'exact')->count(), ''],
                ['  ai_matched', $updateMedia->where('match_type', 'ai_matched')->count(), ''],
                ['  mobile-used', $updateMedia->where('mobile_usage', '>', 0)->count(), 'Priority for upload'],
                ['DB_ONLY_MOBILE', $c->where('action', 'DB_ONLY_MOBILE')->count(), 'Mobile-used, no bundle — keep'],
                ['SUPERSET_NO_MEDIA', $c->where('action', 'SUPERSET_NO_MEDIA')->count(), 'Compound exercises — no media expected'],
                ['GENERIC_ROUTINE', $c->where('action', 'GENERIC_ROUTINE')->count(), 'Generic warmup/cooldown — consolidate'],
                ['DB_ONLY_NO_MOBILE', $c->where('action', 'DB_ONLY_NO_MOBILE')->count(), 'Not mobile-used — soft-delete candidates'],
                ['NEW_FROM_BUNDLE', $c->where('action', 'NEW_FROM_BUNDLE')->count(), 'Import from bundle'],
            ]
        );

        $this->info("Report: {$outputPath}");
    }

    private function generateReport(array $report, string $outputPath): void
    {
        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Smart Match Report');

        $headers = [
            'A' => 'Action', 'B' => 'Mobile Usage', 'C' => 'Total Usage',
            'D' => 'DB Name', 'E' => 'Bundle Match', 'F' => 'DB ID',
            'G' => 'Match Type', 'H' => 'DB Type', 'I' => 'DB Equipment',
            'J' => 'Bundle Equipment', 'K' => 'DB Muscles', 'L' => 'Bundle Muscles',
            'M' => 'Video (M)', 'N' => 'Video (F)', 'O' => 'Illust (M)',
            'P' => 'Illust (F)', 'Q' => 'Note',
        ];

        foreach ($headers as $col => $header) {
            $sheet->setCellValue($col . '1', $header);
        }

        $sheet->getStyle('A1:Q1')->getFont()->setBold(true);
        $sheet->getStyle('A1:Q1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFD9E1F2');
        $sheet->setAutoFilter('A1:Q1');

        $row = 2;
        foreach ($report as $e) {
            $sheet->setCellValue("A{$row}", $e['action']);
            $sheet->setCellValue("B{$row}", $e['mobile_usage']);
            $sheet->setCellValue("C{$row}", $e['total_usage']);
            $sheet->setCellValue("D{$row}", $e['db_name']);
            $sheet->setCellValue("E{$row}", $e['bundle_name']);
            $sheet->setCellValue("F{$row}", $e['db_id']);
            $sheet->setCellValue("G{$row}", $e['match_type']);
            $sheet->setCellValue("H{$row}", $e['db_type']);
            $sheet->setCellValue("I{$row}", $e['db_equipment']);
            $sheet->setCellValue("J{$row}", $e['bundle_equipment']);
            $sheet->setCellValue("K{$row}", $e['db_muscles']);
            $sheet->setCellValue("L{$row}", $e['bundle_muscles']);
            $sheet->setCellValue("M{$row}", $e['has_video_m'] ? 'YES' : 'NO');
            $sheet->setCellValue("N{$row}", $e['has_video_f'] ? 'YES' : 'NO');
            $sheet->setCellValue("O{$row}", $e['has_illu_m'] ? 'YES' : 'NO');
            $sheet->setCellValue("P{$row}", $e['has_illu_f'] ? 'YES' : 'NO');
            $sheet->setCellValue("Q{$row}", $e['note']);

            $color = match ($e['action']) {
                'UPDATE_MEDIA' => 'FFC6EFCE',
                'DB_ONLY_MOBILE' => 'FFFFF2CC',
                'SUPERSET_NO_MEDIA' => 'FFDAEEF3',
                'GENERIC_ROUTINE' => 'FFFFD7D7',
                'DB_ONLY_NO_MOBILE' => 'FFD9D9D9',
                'NEW_FROM_BUNDLE' => 'FFDCE6F1',
                default => 'FFFFFFFF',
            };
            $sheet->getStyle("A{$row}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB($color);
            $row++;
        }

        foreach (range('A', 'Q') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        $sheet->freezePane('A2');

        $dir = dirname($outputPath);
        if (! is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        (new Xlsx($spreadsheet))->save($outputPath);
    }

    private function normalize(string $name): string
    {
        $name = strtolower(trim($name));
        $name = preg_replace('/_?(female|male)$/i', '', $name);
        $name = str_replace(['-', '_', "\u{2013}", "\u{2014}"], ' ', $name);

        return trim(preg_replace('/\s+/', ' ', $name));
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
            $basename = preg_replace('/\d+$/', '', pathinfo($file, PATHINFO_FILENAME));
            $isFemale = (bool) preg_match('/_?female$/i', $basename);
            $key = $this->normalize(preg_replace('/_?(female|male)$/i', '', $basename));
            if (! isset($this->illustrationIndex[$key])) {
                $this->illustrationIndex[$key] = ['male' => null, 'female' => null];
            }
            $this->illustrationIndex[$key][$isFemale ? 'female' : 'male'] = dirname($file);
        }
    }
}
