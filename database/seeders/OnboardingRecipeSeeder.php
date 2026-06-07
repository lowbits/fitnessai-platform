<?php

namespace Database\Seeders;

use App\Models\Recipe;
use Illuminate\Database\Seeder;

class OnboardingRecipeSeeder extends Seeder
{
    public function run(): void
    {
        $recipes = json_decode(
            file_get_contents(database_path('seeders/data/onboarding_recipes.json')),
            true
        );

        $this->command->info('Seeding '.count($recipes).' onboarding recipes...');

        foreach ($recipes as $data) {
            // Decode JSON string fields that come from MySQL export
            foreach (['ingredients', 'instructions', 'tags', 'allergens'] as $field) {
                if (is_string($data[$field] ?? null)) {
                    $data[$field] = json_decode($data[$field], true);
                }
            }

            unset($data['id']);

            Recipe::updateOrCreate(
                ['slug' => $data['slug']],
                $data
            );
        }

        $this->command->info('Done!');
    }
}
