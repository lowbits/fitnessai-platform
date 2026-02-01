<?php

namespace App\Console\Commands;

use App\Services\OpenFoodFacts\ProductExtractor;
use Illuminate\Console\Command;
use Meilisearch\Client as MeilisearchClient;

class ImportOpenFoodFacts extends Command
{
    protected $signature = 'off:import
        {--limit=100 : Number of products to import (0 = unlimited)}
        {--batch=500 : Batch size for Meilisearch}
        {--file= : Local file path instead of remote URL}
        {--fresh : Delete existing index before import}';

    protected $description = 'Import Open Food Facts products into Meilisearch';

    private const INDEX_SETTINGS = [
        'searchableAttributes' => [
            'product_name', 'brands', 'categories', 'ingredients_text',
        ],
        'filterableAttributes' => [
            'barcode', 'categories_tags', 'labels_tags', 'allergens_tags',
            'nutriscore_grade', 'nova_group', 'proteins_100g',
            'carbohydrates_100g', 'fat_100g', 'energy_kcal_100g',
        ],
        'sortableAttributes' => [
            'product_name', 'nutriscore_grade', 'proteins_100g',
            'energy_kcal_100g', 'popularity_key',
        ],
        'rankingRules' => [
            'words', 'typo', 'proximity', 'attribute',
            'sort', 'exactness', 'popularity_key:desc',
        ],
    ];

    public function __construct(
        private readonly MeilisearchClient $meili,
        private readonly ProductExtractor $extractor,
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $limit = (int) $this->option('limit');
        $batchSize = (int) $this->option('batch');
        $unlimited = $limit <= 0;

        $this->setupIndex();

        $source = $this->option('file') ?: config('services.openfoodfacts.db_dump_url');
        $handle = gzopen($source, 'r');

        if (!$handle) {
            $this->error("Could not open: {$source}");
            return Command::FAILURE;
        }

        $imported = $this->importProducts($handle, $batchSize, $unlimited ? null : $limit);

        gzclose($handle);

        $this->info("✅ Imported {$imported} products!");
        return Command::SUCCESS;
    }

    private function setupIndex(): void
    {
        $indexName = config('services.openfoodfacts.index', 'products');

        if ($this->option('fresh')) {
            $this->warn('Deleting existing index...');
            try {
                $this->meili->deleteIndex($indexName);
            } catch (\Exception) {
                // Index doesn't exist
            }
        }

        $this->meili->index($indexName)->updateSettings(self::INDEX_SETTINGS);
        $this->info('Index settings configured');
    }

    private function importProducts($handle, int $batchSize, ?int $limit): int
    {
        $index = $this->meili->index(config('services.openfoodfacts.index', 'products'));
        $imported = 0;
        $batch = [];

        $this->output->progressStart($limit ?? 0);

        while (!gzeof($handle)) {
            if ($limit !== null && $imported >= $limit) {
                break;
            }

            $line = gzgets($handle);
            if (!$line) continue;

            $data = json_decode($line, true);
            if (!is_array($data)) continue;

            $product = $this->extractor->extract($data);
            if (!$product) continue;

            $batch[] = $product;
            $imported++;

            $this->output->progressAdvance();

            if (count($batch) >= $batchSize) {
                $index->addDocuments($batch);
                $batch = [];
            }
        }

        if (count($batch) > 0) {
            $index->addDocuments($batch);
        }

        $this->output->progressFinish();

        return $imported;
    }
}
