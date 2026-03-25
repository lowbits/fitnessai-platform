<?php

namespace App\Console\Commands\Exercises;

use Illuminate\Console\Command;
use OpenAI\Laravel\Facades\OpenAI;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ReverseMatchBundleCommand extends Command
{
    protected $signature = 'exercises:reverse-match-bundle
                            {xlsx : Path to the bundle XLSX file}
                            {audit : Path to the existing audit XLSX (from audit-bundle command)}
                            {--video-dir= : Path to video directory}
                            {--illustration-dir= : Path to illustrations directory}
                            {--output= : Output XLSX path}
                            {--mysql= : MySQL DSN}
                            {--mysql-user=forge : MySQL username}
                            {--mysql-password= : MySQL password}
                            {--batch-size=40 : AI batch size}';

    protected $description = 'Reverse-match unmatched DB exercises (mobile-used) against bundle exercises using AI';

    private array $videoIndex = [];

    private array $illustrationIndex = [];

    public function handle(): int
    {
        ini_set('memory_limit', '512M');

        $outputPath = $this->option('output') ?? storage_path('app/exercise-final-report.xlsx');
        $mysqlDsn = $this->option('mysql');

        if (! $mysqlDsn) {
            $this->error('--mysql is required for this command (needs prod data).');

            return self::FAILURE;
        }

        $pdo = new \PDO($mysqlDsn, $this->option('mysql-user'), $this->option('mysql-password'));
        $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

        // Step 1: Load the existing audit to get already-matched DB IDs
        $this->info('Loading existing audit report...');
        $audit = $this->loadAudit($this->argument('audit'));
        $matchedDbIds = collect($audit)->where('action', 'UPDATE_MEDIA')->pluck('db_id')->filter()->unique()->toArray();
        $this->info('Already matched DB exercises: ' . count($matchedDbIds));

        // Step 2: Get mobile-used exercises that are NOT already matched
        $this->info('Loading mobile-used exercises from prod...');
        $mobileExercises = $this->getMobileUsedExercises($pdo);
        $this->info('Total mobile-used: ' . count($mobileExercises));

        $unmatched = array_filter($mobileExercises, fn ($ex) => ! in_array((int) $ex['id'], $matchedDbIds));
        $this->info('Unmatched mobile-used: ' . count($unmatched));

        // Step 3: Parse bundle and index media
        $this->info('Parsing bundle...');
        $bundleExercises = $this->parseBundleXlsx($this->argument('xlsx'));
        $this->info('Bundle exercises: ' . count($bundleExercises));

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

        // Step 4: For each unmatched DB exercise, find top 3 bundle candidates and AI-verify
        $this->info('Finding bundle candidates for unmatched exercises...');
        $bundleNames = array_keys($bundleExercises);
        $reverseMatches = $this->findAndVerifyMatches($unmatched, $bundleExercises, $bundleNames);

        // Step 5: Build final combined report
        $this->info('Building final report...');
        $finalReport = $this->buildFinalReport($audit, $reverseMatches, $mobileExercises, $bundleExercises, $pdo);

        $this->generateReport($finalReport, $outputPath);

        // Summary
        $this->printSummary($finalReport, $outputPath);

        return self::SUCCESS;
    }

    private function loadAudit(string $path): array
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
                'match_score' => $row['B'],
                'match_type' => $row['C'],
                'bundle_name' => $row['D'],
                'db_name' => $row['E'],
                'db_id' => $row['F'] ? (int) $row['F'] : null,
                'ai_reason' => $row['G'] ?? '',
                'bundle_category' => $row['H'],
                'bundle_equipment' => $row['I'],
                'db_equipment' => $row['J'],
                'bundle_primary_muscles' => $row['K'],
                'db_primary_muscles' => $row['L'],
                'bundle_secondary_muscles' => $row['M'],
                'bundle_has_instructions' => $row['N'] === 'YES',
                'bundle_has_tips' => $row['O'] === 'YES',
                'db_has_description' => $row['P'] === 'YES',
                'db_has_instructions' => $row['Q'] === 'YES',
                'db_has_image' => $row['R'] === 'YES',
                'db_has_video' => $row['S'] === 'YES',
                'has_video_male' => $row['T'] === 'YES',
                'has_video_female' => $row['U'] === 'YES',
                'has_illustration_male' => $row['V'] === 'YES',
                'has_illustration_female' => $row['W'] === 'YES',
                'video_path_male' => $row['X'] ?? '',
                'video_path_female' => $row['Y'] ?? '',
            ];
        }

        return $entries;
    }

    private function getMobileUsedExercises(\PDO $pdo): array
    {
        $stmt = $pdo->query("
            SELECT
                e.id, e.name, e.slug, e.type, e.equipment, e.primary_muscles,
                e.secondary_muscles, e.description, e.instructions, e.form_cues,
                e.image, e.video_url,
                COUNT(DISTINCT CASE WHEN u.source = 'mobile_apple' THEN wpe.id END) as mobile_usage
            FROM exercises e
            LEFT JOIN workout_plan_exercises wpe ON wpe.exercise_id = e.id
            LEFT JOIN workout_plans wp ON wp.id = wpe.workout_plan_id
            LEFT JOIN plans p ON p.id = wp.plan_id
            LEFT JOIN users u ON u.id = p.user_id
            WHERE e.deleted_at IS NULL
            GROUP BY e.id, e.name, e.slug, e.type, e.equipment, e.primary_muscles,
                     e.secondary_muscles, e.description, e.instructions, e.form_cues,
                     e.image, e.video_url
            HAVING mobile_usage > 0
            ORDER BY mobile_usage DESC
        ");

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    private function findAndVerifyMatches(array $unmatchedDb, array $bundleExercises, array $bundleNames): array
    {
        $results = [];
        $pairsForAi = [];

        $bar = $this->output->createProgressBar(count($unmatchedDb));

        foreach ($unmatchedDb as $dbEx) {
            $bar->advance();

            $dbNorm = $this->normalize($dbEx['name']);

            // Try exact match first
            if (isset($bundleExercises[$dbNorm])) {
                $bundle = $bundleExercises[$dbNorm];
                $results[$dbEx['id']] = [
                    'match_type' => 'exact',
                    'bundle_key' => $dbNorm,
                    'bundle_name' => $bundle['raw_name'],
                    'score' => 100,
                ];

                continue;
            }

            // Find top 3 fuzzy candidates
            $candidates = $this->findTopCandidates($dbNorm, $bundleNames, 3);

            if (empty($candidates)) {
                $results[$dbEx['id']] = ['match_type' => 'none'];

                continue;
            }

            // Queue for AI verification
            $pairsForAi[$dbEx['id']] = [
                'db_name' => $dbEx['name'],
                'db_equipment' => $dbEx['equipment'],
                'candidates' => $candidates,
            ];
        }

        $bar->finish();
        $this->newLine();

        // AI verify all candidates
        if (! empty($pairsForAi)) {
            $this->info('AI-verifying ' . count($pairsForAi) . ' exercises with candidates...');
            $aiResults = $this->aiVerifyCandidates($pairsForAi, $bundleExercises);

            foreach ($aiResults as $dbId => $result) {
                $results[$dbId] = $result;
            }
        }

        return $results;
    }

    private function findTopCandidates(string $dbNorm, array $bundleNames, int $top): array
    {
        $scores = [];

        foreach ($bundleNames as $bundleName) {
            if (abs(strlen($dbNorm) - strlen($bundleName)) > 30) {
                continue;
            }

            similar_text($dbNorm, $bundleName, $percent);

            if ($percent >= 50) {
                $scores[$bundleName] = round($percent);
            }
        }

        arsort($scores);

        return array_slice($scores, 0, $top, true);
    }

    private function aiVerifyCandidates(array $pairsForAi, array $bundleExercises): array
    {
        $results = [];
        $batchSize = (int) $this->option('batch-size');

        // Flatten into individual verification items
        $items = [];
        foreach ($pairsForAi as $dbId => $data) {
            $items[] = [
                'db_id' => $dbId,
                'db_name' => $data['db_name'],
                'db_equipment' => $data['db_equipment'],
                'candidates' => $data['candidates'],
            ];
        }

        $batches = array_chunk($items, $batchSize);
        $bar = $this->output->createProgressBar(count($items));

        foreach ($batches as $batch) {
            $batchResults = $this->callAiMultiCandidate($batch, $bundleExercises);

            foreach ($batchResults as $dbId => $result) {
                $results[$dbId] = $result;
                $bar->advance();
            }
        }

        $bar->finish();
        $this->newLine();

        return $results;
    }

    private function callAiMultiCandidate(array $batch, array $bundleExercises): array
    {
        $prompt = "You are an exercise database expert. For each numbered DB exercise below, I provide up to 3 candidate matches from a bundle. Determine which candidate (if any) is the SAME exercise.\n\n";
        $prompt .= "Rules:\n";
        $prompt .= "- Same exercise = identical movement pattern, target muscles, and equipment type\n";
        $prompt .= "- Minor name differences (plurals, word order) = SAME\n";
        $prompt .= "- Different equipment (barbell vs dumbbell vs cable vs band) = DIFFERENT\n";
        $prompt .= "- Different angles (incline/decline/flat) = DIFFERENT\n";
        $prompt .= "- Generic warmup/cooldown names (e.g., 'Dynamic Warmup') have NO match unless exact same name exists\n";
        $prompt .= "- Supersets/compound exercises = DIFFERENT from individual exercises\n\n";
        $prompt .= "Respond with ONLY a JSON array. Each element: {\"db_id\": <id>, \"best_match\": \"<bundle_name or null>\", \"reason\": \"brief\"}\n\n";

        foreach ($batch as $i => $item) {
            $n = $i + 1;
            $prompt .= "{$n}. DB: \"{$item['db_name']}\"";
            $dbEquip = json_decode($item['db_equipment'] ?? '[]', true);
            if ($dbEquip) {
                $prompt .= ' (equipment: ' . implode(', ', $dbEquip) . ')';
            }
            $prompt .= "\n   Candidates:\n";

            foreach ($item['candidates'] as $bundleKey => $score) {
                $bundle = $bundleExercises[$bundleKey] ?? null;
                $bundleName = $bundle ? $bundle['raw_name'] : $bundleKey;
                $bundleEquip = $bundle['equipment'] ?? '';
                $prompt .= "   - \"{$bundleName}\" [{$score}%]";
                if ($bundleEquip) {
                    $prompt .= " (equipment: {$bundleEquip})";
                }
                $prompt .= "\n";
            }

            $prompt .= "\n";
        }

        $maxRetries = 3;

        for ($attempt = 1; $attempt <= $maxRetries; $attempt++) {
            try {
                $response = OpenAI::chat()->create([
                    'model' => config('ai.models.simple'),
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
                    $dbId = $item['db_id'] ?? null;
                    if (! $dbId) {
                        continue;
                    }

                    $bestMatch = $item['best_match'] ?? null;

                    if ($bestMatch) {
                        // Find the bundle key for this match
                        $bundleKey = $this->normalize($bestMatch);
                        if (isset($bundleExercises[$bundleKey])) {
                            $results[$dbId] = [
                                'match_type' => 'ai_verified',
                                'bundle_key' => $bundleKey,
                                'bundle_name' => $bundleExercises[$bundleKey]['raw_name'],
                                'score' => 0,
                                'reason' => $item['reason'] ?? '',
                            ];
                        } else {
                            // Try to find by raw name match
                            $found = false;
                            foreach ($bundleExercises as $bk => $bv) {
                                if (strcasecmp($bv['raw_name'], $bestMatch) === 0 || strcasecmp($bk, $this->normalize($bestMatch)) === 0) {
                                    $results[$dbId] = [
                                        'match_type' => 'ai_verified',
                                        'bundle_key' => $bk,
                                        'bundle_name' => $bv['raw_name'],
                                        'score' => 0,
                                        'reason' => $item['reason'] ?? '',
                                    ];
                                    $found = true;
                                    break;
                                }
                            }
                            if (! $found) {
                                $results[$dbId] = [
                                    'match_type' => 'none',
                                    'reason' => "AI matched to '{$bestMatch}' but not found in bundle index",
                                ];
                            }
                        }
                    } else {
                        $results[$dbId] = [
                            'match_type' => 'none',
                            'reason' => $item['reason'] ?? 'No match',
                        ];
                    }
                }

                // Fill missing
                foreach ($batch as $batchItem) {
                    if (! isset($results[$batchItem['db_id']])) {
                        $results[$batchItem['db_id']] = [
                            'match_type' => 'none',
                            'reason' => 'AI did not return result',
                        ];
                    }
                }

                return $results;
            } catch (\Exception $e) {
                $this->warn("AI error (attempt {$attempt}): " . $e->getMessage());
            }
        }

        // Fallback
        $results = [];
        foreach ($batch as $item) {
            $results[$item['db_id']] = ['match_type' => 'none', 'reason' => 'AI failed'];
        }

        return $results;
    }

    private function buildFinalReport(array $audit, array $reverseMatches, array $mobileExercises, array $bundleExercises, \PDO $pdo): array
    {
        $mobileUsageById = [];
        foreach ($mobileExercises as $ex) {
            $mobileUsageById[(int) $ex['id']] = (int) $ex['mobile_usage'];
        }

        // Get ALL exercises with total usage
        $stmt = $pdo->query("
            SELECT e.id, e.name, e.type, e.equipment, e.primary_muscles, e.image, e.video_url,
                   e.description, e.instructions, e.form_cues, e.slug,
                   COUNT(DISTINCT wpe.id) as total_usage
            FROM exercises e
            LEFT JOIN workout_plan_exercises wpe ON wpe.exercise_id = e.id
            WHERE e.deleted_at IS NULL
            GROUP BY e.id
            ORDER BY e.name
        ");
        $allDbExercises = [];
        foreach ($stmt->fetchAll(\PDO::FETCH_ASSOC) as $row) {
            $allDbExercises[(int) $row['id']] = $row;
        }

        $report = [];

        // Track which bundle exercises are used (to find truly new ones)
        $usedBundleKeys = [];

        // Process existing audit entries (bundle→DB matches from forward pass)
        foreach ($audit as $entry) {
            if ($entry['action'] === 'UPDATE_MEDIA' && $entry['db_id']) {
                $dbId = (int) $entry['db_id'];
                $mobileUsage = $mobileUsageById[$dbId] ?? 0;
                $bundleKey = $this->normalize($entry['bundle_name']);
                $usedBundleKeys[$bundleKey] = true;

                $report[] = [
                    'action' => 'UPDATE_MEDIA',
                    'db_id' => $dbId,
                    'db_name' => $entry['db_name'],
                    'bundle_name' => $entry['bundle_name'],
                    'match_type' => $entry['match_type'],
                    'match_score' => $entry['match_score'],
                    'mobile_usage' => $mobileUsage,
                    'total_usage' => $allDbExercises[$dbId]['total_usage'] ?? 0,
                    'bundle_equipment' => $entry['bundle_equipment'],
                    'db_equipment' => $entry['db_equipment'],
                    'bundle_primary_muscles' => $entry['bundle_primary_muscles'],
                    'db_primary_muscles' => $entry['db_primary_muscles'],
                    'has_video_male' => $entry['has_video_male'],
                    'has_video_female' => $entry['has_video_female'],
                    'has_illustration_male' => $entry['has_illustration_male'],
                    'has_illustration_female' => $entry['has_illustration_female'],
                    'reason' => '',
                ];
            }
        }

        // Process reverse matches (DB→bundle for previously unmatched)
        foreach ($reverseMatches as $dbId => $match) {
            $dbEx = $allDbExercises[$dbId] ?? null;
            if (! $dbEx) {
                continue;
            }

            $mobileUsage = $mobileUsageById[$dbId] ?? 0;

            if ($match['match_type'] === 'exact' || $match['match_type'] === 'ai_verified') {
                $bundleKey = $match['bundle_key'];
                $usedBundleKeys[$bundleKey] = true;
                $bundle = $bundleExercises[$bundleKey] ?? null;

                $videoInfo = $this->videoIndex[$bundleKey] ?? ['male' => null, 'female' => null];
                $illustrationInfo = $this->illustrationIndex[$bundleKey] ?? ['male' => null, 'female' => null];

                $report[] = [
                    'action' => 'UPDATE_MEDIA',
                    'db_id' => $dbId,
                    'db_name' => $dbEx['name'],
                    'bundle_name' => $match['bundle_name'],
                    'match_type' => 'reverse_' . $match['match_type'],
                    'match_score' => $match['score'] ?? 0,
                    'mobile_usage' => $mobileUsage,
                    'total_usage' => $dbEx['total_usage'],
                    'bundle_equipment' => $bundle['equipment'] ?? '',
                    'db_equipment' => implode(', ', json_decode($dbEx['equipment'] ?? '[]', true) ?: []),
                    'bundle_primary_muscles' => $bundle['primary_muscles'] ?? '',
                    'db_primary_muscles' => implode(', ', json_decode($dbEx['primary_muscles'] ?? '[]', true) ?: []),
                    'has_video_male' => ! empty($videoInfo['male']),
                    'has_video_female' => ! empty($videoInfo['female']),
                    'has_illustration_male' => ! empty($illustrationInfo['male']),
                    'has_illustration_female' => ! empty($illustrationInfo['female']),
                    'reason' => $match['reason'] ?? '',
                ];
            } else {
                // No match found — categorize
                $action = 'DB_ONLY_MOBILE';
                if ($mobileUsage === 0) {
                    $action = 'SAFE_TO_REMOVE';
                } elseif (in_array($dbEx['type'], ['warmup', 'cooldown', 'stretch'])) {
                    $isGeneric = (bool) preg_match('/^(dynamic |general |full body |5|10|joint|light |warm|cool|stretch|mobility|foam)/i', $dbEx['name']);
                    $action = $isGeneric ? 'GENERIC_ROUTINE' : 'DB_ONLY_MOBILE';
                }

                $report[] = [
                    'action' => $action,
                    'db_id' => $dbId,
                    'db_name' => $dbEx['name'],
                    'bundle_name' => '',
                    'match_type' => 'none',
                    'match_score' => 0,
                    'mobile_usage' => $mobileUsage,
                    'total_usage' => $dbEx['total_usage'],
                    'bundle_equipment' => '',
                    'db_equipment' => implode(', ', json_decode($dbEx['equipment'] ?? '[]', true) ?: []),
                    'bundle_primary_muscles' => '',
                    'db_primary_muscles' => implode(', ', json_decode($dbEx['primary_muscles'] ?? '[]', true) ?: []),
                    'has_video_male' => false,
                    'has_video_female' => false,
                    'has_illustration_male' => false,
                    'has_illustration_female' => false,
                    'reason' => $match['reason'] ?? '',
                ];
            }
        }

        // Add DB-only exercises NOT in reverse matches (not mobile-used)
        $handledDbIds = collect($report)->pluck('db_id')->filter()->unique()->toArray();
        foreach ($allDbExercises as $dbId => $dbEx) {
            if (in_array($dbId, $handledDbIds)) {
                continue;
            }

            $mobileUsage = $mobileUsageById[$dbId] ?? 0;
            $action = $mobileUsage > 0 ? 'DB_ONLY_MOBILE' : 'SAFE_TO_REMOVE';

            $report[] = [
                'action' => $action,
                'db_id' => $dbId,
                'db_name' => $dbEx['name'],
                'bundle_name' => '',
                'match_type' => 'none',
                'match_score' => 0,
                'mobile_usage' => $mobileUsage,
                'total_usage' => $dbEx['total_usage'],
                'bundle_equipment' => '',
                'db_equipment' => implode(', ', json_decode($dbEx['equipment'] ?? '[]', true) ?: []),
                'bundle_primary_muscles' => '',
                'db_primary_muscles' => implode(', ', json_decode($dbEx['primary_muscles'] ?? '[]', true) ?: []),
                'has_video_male' => false,
                'has_video_female' => false,
                'has_illustration_male' => false,
                'has_illustration_female' => false,
                'reason' => '',
            ];
        }

        // Add new bundle exercises (not matched to any DB exercise)
        foreach ($bundleExercises as $bundleKey => $bundle) {
            if (isset($usedBundleKeys[$bundleKey])) {
                continue;
            }

            $videoInfo = $this->videoIndex[$bundleKey] ?? ['male' => null, 'female' => null];
            $illustrationInfo = $this->illustrationIndex[$bundleKey] ?? ['male' => null, 'female' => null];

            $report[] = [
                'action' => 'NEW_FROM_BUNDLE',
                'db_id' => null,
                'db_name' => '',
                'bundle_name' => $bundle['raw_name'],
                'match_type' => 'new',
                'match_score' => 0,
                'mobile_usage' => 0,
                'total_usage' => 0,
                'bundle_equipment' => $bundle['equipment'],
                'db_equipment' => '',
                'bundle_primary_muscles' => $bundle['primary_muscles'],
                'db_primary_muscles' => '',
                'has_video_male' => ! empty($videoInfo['male']),
                'has_video_female' => ! empty($videoInfo['female']),
                'has_illustration_male' => ! empty($illustrationInfo['male']),
                'has_illustration_female' => ! empty($illustrationInfo['female']),
                'reason' => '',
            ];
        }

        // Sort: UPDATE_MEDIA (by mobile_usage desc) → GENERIC_ROUTINE → DB_ONLY_MOBILE → SAFE_TO_REMOVE → NEW_FROM_BUNDLE
        usort($report, function ($a, $b) {
            $order = [
                'UPDATE_MEDIA' => 1,
                'DB_ONLY_MOBILE' => 2,
                'GENERIC_ROUTINE' => 3,
                'SAFE_TO_REMOVE' => 4,
                'NEW_FROM_BUNDLE' => 5,
            ];
            $aOrder = $order[$a['action']] ?? 6;
            $bOrder = $order[$b['action']] ?? 6;
            if ($aOrder !== $bOrder) {
                return $aOrder <=> $bOrder;
            }

            return ($b['mobile_usage'] ?? 0) <=> ($a['mobile_usage'] ?? 0);
        });

        return $report;
    }

    private function printSummary(array $report, string $outputPath): void
    {
        $collected = collect($report);
        $updateMedia = $collected->where('action', 'UPDATE_MEDIA');

        $this->newLine();
        $this->info('=== FINAL REPORT SUMMARY ===');
        $this->table(
            ['Action', 'Count', 'Notes'],
            [
                ['UPDATE_MEDIA', $updateMedia->count(), 'DB exercises that get video+illustration from bundle'],
                ['  - mobile-used', $updateMedia->where('mobile_usage', '>', 0)->count(), 'Of those, used by mobile users'],
                ['DB_ONLY_MOBILE', $collected->where('action', 'DB_ONLY_MOBILE')->count(), 'Mobile-used, no bundle match — keep, no media yet'],
                ['GENERIC_ROUTINE', $collected->where('action', 'GENERIC_ROUTINE')->count(), 'Generic warmup/cooldown names — consolidate'],
                ['SAFE_TO_REMOVE', $collected->where('action', 'SAFE_TO_REMOVE')->count(), 'Not mobile-used, no bundle — soft-delete'],
                ['NEW_FROM_BUNDLE', $collected->where('action', 'NEW_FROM_BUNDLE')->count(), 'New exercises to import from bundle'],
            ]
        );

        $this->info("Report saved to: {$outputPath}");
    }

    private function generateReport(array $report, string $outputPath): void
    {
        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Final Exercise Report');

        $headers = [
            'A' => 'Action',
            'B' => 'Mobile Usage',
            'C' => 'Total Usage',
            'D' => 'DB Name',
            'E' => 'Bundle Match',
            'F' => 'DB ID',
            'G' => 'Match Type',
            'H' => 'DB Equipment',
            'I' => 'Bundle Equipment',
            'J' => 'DB Muscles',
            'K' => 'Bundle Muscles',
            'L' => 'Video (M)',
            'M' => 'Video (F)',
            'N' => 'Illustration (M)',
            'O' => 'Illustration (F)',
            'P' => 'Reason',
        ];

        foreach ($headers as $col => $header) {
            $sheet->setCellValue($col . '1', $header);
        }

        $headerRange = 'A1:P1';
        $sheet->getStyle($headerRange)->getFont()->setBold(true);
        $sheet->getStyle($headerRange)->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFD9E1F2');
        $sheet->setAutoFilter($headerRange);

        $row = 2;
        foreach ($report as $entry) {
            $sheet->setCellValue("A{$row}", $entry['action']);
            $sheet->setCellValue("B{$row}", $entry['mobile_usage']);
            $sheet->setCellValue("C{$row}", $entry['total_usage']);
            $sheet->setCellValue("D{$row}", $entry['db_name']);
            $sheet->setCellValue("E{$row}", $entry['bundle_name']);
            $sheet->setCellValue("F{$row}", $entry['db_id']);
            $sheet->setCellValue("G{$row}", $entry['match_type']);
            $sheet->setCellValue("H{$row}", $entry['db_equipment']);
            $sheet->setCellValue("I{$row}", $entry['bundle_equipment']);
            $sheet->setCellValue("J{$row}", $entry['db_primary_muscles']);
            $sheet->setCellValue("K{$row}", $entry['bundle_primary_muscles']);
            $sheet->setCellValue("L{$row}", $entry['has_video_male'] ? 'YES' : 'NO');
            $sheet->setCellValue("M{$row}", $entry['has_video_female'] ? 'YES' : 'NO');
            $sheet->setCellValue("N{$row}", $entry['has_illustration_male'] ? 'YES' : 'NO');
            $sheet->setCellValue("O{$row}", $entry['has_illustration_female'] ? 'YES' : 'NO');
            $sheet->setCellValue("P{$row}", $entry['reason']);

            $actionColor = match ($entry['action']) {
                'UPDATE_MEDIA' => 'FFC6EFCE',
                'DB_ONLY_MOBILE' => 'FFFFF2CC',
                'GENERIC_ROUTINE' => 'FFFFD7D7',
                'SAFE_TO_REMOVE' => 'FFD9D9D9',
                'NEW_FROM_BUNDLE' => 'FFDCE6F1',
                default => 'FFFFFFFF',
            };

            $sheet->getStyle("A{$row}")->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()->setARGB($actionColor);

            $row++;
        }

        foreach (range('A', 'P') as $col) {
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

            $rawName = trim($row['B']);
            $canonical = trim(preg_replace('/_?(female|male)$/i', '', $rawName));
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
