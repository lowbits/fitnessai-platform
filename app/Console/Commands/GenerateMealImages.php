<?php

namespace App\Console\Commands;

use App\Enums\DietaryPreference;
use App\Enums\MealType;
use App\Jobs\GenerateMealImage;
use App\Jobs\RemoveMealImageBackground;
use App\Models\Meal;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Bus;

class GenerateMealImages extends Command
{
    protected $signature = 'meals:generate-images
                            {--meal-id= : Generate image for a specific meal}
                            {--limit= : Limit the number of meals to process}
                            {--type= : Filter by meal type (breakfast, lunch, snack, dinner)}
                            {--dietary-preference= : Filter by user dietary preference (omnivore, vegetarian, pescatarian, vegan)}
                            {--dry-run : Preview which meals would be processed}';

    protected $description = 'Generate AI food images for meals and upload to R2';

    public function handle(): int
    {
        // Flex meals always use the branded shaker placeholder — never an AI image.
        $query = Meal::query()->whereNull('image_full')->where('type', '!=', MealType::FLEX->value);

        if ($mealId = $this->option('meal-id')) {
            $query->where('id', $mealId);
        }

        if ($type = $this->option('type')) {
            $query->where('type', $type);
        }

        if ($preference = $this->option('dietary-preference')) {
            $dietary = DietaryPreference::tryFrom($preference);

            if (! $dietary) {
                $this->error("Invalid dietary preference: {$preference}. Valid options: ".implode(', ', array_column(DietaryPreference::cases(), 'value')));

                return Command::FAILURE;
            }

            $query->whereHas('mealPlan.plan.user.profile', function ($q) use ($dietary) {
                $q->where('dietary_preference', $dietary);
            });
        }

        if ($limit = $this->option('limit')) {
            $query->limit((int) $limit);
        }

        $meals = $query->get();

        if ($meals->isEmpty()) {
            $this->info('No meals to process.');

            return Command::SUCCESS;
        }

        $this->info("Found {$meals->count()} meal(s) to process.");

        if ($this->option('dry-run')) {
            $meals->loadMissing('mealPlan.plan.user.profile');

            $this->table(
                ['ID', 'Name', 'Type', 'Dietary Preference'],
                $meals->map(fn (Meal $meal) => [
                    $meal->id,
                    $meal->name,
                    $meal->type,
                    $meal->mealPlan?->plan?->user?->profile?->getDietaryInfo() ?? '-',
                ])->toArray(),
            );
            $this->info('Dry run complete. No jobs dispatched.');

            return Command::SUCCESS;
        }

        $dispatched = 0;

        foreach ($meals as $meal) {
            Bus::chain([
                new GenerateMealImage($meal),
                new RemoveMealImageBackground($meal),
            ])->onQueue('nutrition')->dispatch();
            $dispatched++;
            $this->line("Dispatched chain for meal #{$meal->id}: {$meal->name}");
        }

        $this->info("Dispatched {$dispatched} job(s).");

        return Command::SUCCESS;
    }
}
