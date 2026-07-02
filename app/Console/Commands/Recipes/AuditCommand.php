<?php

namespace App\Console\Commands\Recipes;

use App\Models\Recipe;
use Illuminate\Console\Command;

class AuditCommand extends Command
{
    protected $signature = 'recipes:audit {--locale= : Filter by source_locale}';

    protected $description = 'Show recipe catalog distribution per (diet, meal_type) so you can spot thin slots.';

    private const DIETS = ['vegan', 'vegetarian', 'pescatarian', 'omnivore'];

    private const MEAL_TYPES = ['breakfast', 'lunch', 'snack', 'dinner'];

    public function handle(): int
    {
        $locale = $this->option('locale');

        $query = Recipe::query();
        if ($locale) {
            $query->where('source_locale', $locale);
        }

        $matrix = [];
        $query->get(['meal_types', 'tags', 'source_locale'])->each(function (Recipe $r) use (&$matrix) {
            $tags = $r->tags ?? [];
            foreach (($r->meal_types ?? []) as $mt) {
                foreach (self::DIETS as $diet) {
                    if (in_array($diet, $tags, true)) {
                        $matrix[$diet][$mt] = ($matrix[$diet][$mt] ?? 0) + 1;
                    }
                }
            }
        });

        $rows = [];
        foreach (self::DIETS as $diet) {
            $row = ['diet' => $diet];
            foreach (self::MEAL_TYPES as $mt) {
                $count = $matrix[$diet][$mt] ?? 0;
                $row[$mt] = $count === 0 ? '<error>0</error>' : (string) $count;
            }
            $rows[] = $row;
        }

        $label = $locale ? "Locale: {$locale}" : 'All locales';
        $this->info($label);
        $this->table(['diet', ...self::MEAL_TYPES], $rows);

        return self::SUCCESS;
    }
}
