<?php

namespace App\Filament\Resources\RecipeResource\Pages;

use App\Filament\Resources\RecipeResource;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class ListRecipes extends ListRecords
{
    protected static string $resource = RecipeResource::class;

    protected function getHeaderActions(): array
    {
        $unlinkedMeals = DB::table('meals')
            ->whereNull('recipe_id')
            ->whereNotNull('name')
            ->where('name', '!=', '')
            ->count();

        return [
            Action::make('extractFromMeals')
                ->label('Extract from Meals')
                ->icon('heroicon-o-arrow-path')
                ->color('info')
                ->modalHeading('Extract recipes from meals')
                ->modalDescription("Found {$unlinkedMeals} unlinked meals. Extracts unique recipes using Meilisearch deduplication.")
                ->schema([
                    Toggle::make('popular')
                        ->label('Most popular first')
                        ->helperText('Process meals that appear most often across users first.')
                        ->default(true),
                    Select::make('meal_type')
                        ->label('Meal type')
                        ->options([
                            'breakfast' => 'Breakfast',
                            'lunch' => 'Lunch',
                            'dinner' => 'Dinner',
                            'snack' => 'Snack',
                        ])
                        ->placeholder('All types'),
                    TextInput::make('min_count')
                        ->label('Min. times generated')
                        ->helperText('Only include meals generated at least this many times.')
                        ->numeric()
                        ->default(2)
                        ->minValue(1),
                    TextInput::make('limit')
                        ->label('Limit')
                        ->helperText('Max unique meal names to process. Leave empty for all.')
                        ->numeric()
                        ->minValue(1),
                    Toggle::make('dry_run')
                        ->label('Dry run')
                        ->helperText('Preview without writing to the database.'),
                ])
                ->action(function (array $data) {
                    $options = ['--no-interaction' => true];

                    if ($data['popular'] ?? false) {
                        $options['--popular'] = true;
                    }

                    if (! empty($data['meal_type'])) {
                        $options['--meal-type'] = $data['meal_type'];
                    }

                    if (! empty($data['min_count']) && (int) $data['min_count'] > 1) {
                        $options['--min-count'] = (int) $data['min_count'];
                    }

                    if (! empty($data['limit'])) {
                        $options['--limit'] = (int) $data['limit'];
                    }

                    if ($data['dry_run'] ?? false) {
                        $options['--dry-run'] = true;
                    }

                    Artisan::call('recipes:extract', $options);

                    Notification::make()
                        ->title('Recipe extraction completed')
                        ->body($data['dry_run'] ? 'Dry run finished. Check logs for details.' : 'Recipes extracted and meals linked.')
                        ->success()
                        ->send();
                }),
        ];
    }
}
