<?php

namespace App\Console\Commands\Recipes;

use App\Ai\Agents\RecipeSeederAgent;
use Illuminate\Console\Command;
use Throwable;

class SeedCommand extends Command
{
    protected $signature = 'recipes:seed
                            {--diet= : vegan|vegetarian|pescatarian|omnivore}
                            {--meal-type= : breakfast|lunch|snack|dinner}
                            {--locale=en : Recipe source locale}
                            {--count=5 : Number of recipes to generate}
                            {--dry-run : Show what would be created without writing}';

    protected $description = 'Proactively generate fresh recipes for a (diet, meal_type, locale) slot via AI.';

    private const DIETS = ['vegan', 'vegetarian', 'pescatarian', 'omnivore'];

    private const MEAL_TYPES = ['breakfast', 'lunch', 'snack', 'dinner'];

    public function handle(): int
    {
        $diet = (string) $this->option('diet');
        $mealType = (string) $this->option('meal-type');
        $locale = (string) $this->option('locale');
        $count = (int) $this->option('count');
        $dryRun = (bool) $this->option('dry-run');

        if (! in_array($diet, self::DIETS, true)) {
            $this->error('--diet must be one of: '.implode(', ', self::DIETS));

            return self::FAILURE;
        }

        if (! in_array($mealType, self::MEAL_TYPES, true)) {
            $this->error('--meal-type must be one of: '.implode(', ', self::MEAL_TYPES));

            return self::FAILURE;
        }

        $this->info("Seeding {$count} {$diet} {$mealType} recipes for locale={$locale}...");

        $agent = new RecipeSeederAgent($diet, $mealType, $locale, $count, $dryRun);

        try {
            $agent->prompt('Generate the recipes now.');
        } catch (Throwable $e) {
            $this->error('AI call failed: '.$e->getMessage());

            return self::FAILURE;
        }

        $created = count($agent->saveTool->createdIds);
        $this->info(($dryRun ? '[dry-run] ' : '')."Created: {$created} new recipes.");

        return self::SUCCESS;
    }
}
