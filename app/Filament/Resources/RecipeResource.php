<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RecipeResource\Pages;
use App\Models\Recipe;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class RecipeResource extends Resource
{
    protected static ?string $model = Recipe::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-cake';

    protected static string|\UnitEnum|null $navigationGroup = 'Content';

    protected static ?int $navigationSort = 10;

    public static function infolist(Schema $infolist): Schema
    {
        $r2Url = config('services.r2.public_url');

        return $infolist->schema([
            // Hero: image, name, description, nutrition
            Section::make()->schema([
                ImageEntry::make('image_full')
                    ->label('Full')
                    ->state(fn (Recipe $record) => $record->image_full ? "{$r2Url}/{$record->image_full}" : null)
                    ->height(250)
                    ->url(fn (Recipe $record) => $record->image_full ? "{$r2Url}/{$record->image_full}" : null)
                    ->openUrlInNewTab()
                    ->columnSpan(2),
                ImageEntry::make('image_isolated')
                    ->label('Isolated')
                    ->state(fn (Recipe $record) => $record->image_isolated ? "{$r2Url}/{$record->image_isolated}" : null)
                    ->height(250)
                    ->url(fn (Recipe $record) => $record->image_isolated ? "{$r2Url}/{$record->image_isolated}" : null)
                    ->openUrlInNewTab()
                    ->columnSpan(2),
                TextEntry::make('name')
                    ->label('')
                    ->size('lg')
                    ->weight('bold')
                    ->columnSpanFull(),
                TextEntry::make('description')
                    ->label('')
                    ->columnSpanFull(),
                TextEntry::make('calories')->suffix(' kcal'),
                TextEntry::make('protein_g')->label('Protein')->suffix(' g'),
                TextEntry::make('carbs_g')->label('Carbs')->suffix(' g'),
                TextEntry::make('fat_g')->label('Fat')->suffix(' g'),
                TextEntry::make('meal_types')
                    ->label('Meal type')
                    ->badge()
                    ->separator(','),
                TextEntry::make('allergens')
                    ->badge()
                    ->color('danger')
                    ->separator(',')
                    ->placeholder('None'),
            ])->columns(4)->columnSpanFull(),

            // Ingredients: are they realistic and complete?
            Section::make('Ingredients')
                ->description('Check if amounts and items look reasonable')
                ->schema([
                    TextEntry::make('ingredients')
                        ->label('')
                        ->state(fn (Recipe $record) => collect($record->ingredients)->map(
                            fn (array $item) => trim(($item['amount'] ?? '').' '.($item['unit'] ?? '').' '.$item['name'])
                        )->all())
                        ->listWithLineBreaks()
                        ->columnSpanFull(),
                ]),

            // Instructions: do the steps make sense?
            Section::make('Instructions')
                ->description('Check if the steps are clear and in the right order')
                ->schema([
                    TextEntry::make('instructions')
                        ->label('')
                        ->state(fn (Recipe $record) => collect($record->instructions)->map(
                            fn (string|array $step, int $i) => is_array($step)
                                ? ($step['step'] ?? $i + 1).'. '.$step['text']
                                : ($i + 1).'. '.$step
                        )->all())
                        ->listWithLineBreaks()
                        ->columnSpanFull(),
                ]),

            // Translations
            Section::make('Translations')
                ->schema([
                    TextEntry::make('translations_summary')
                        ->label('')
                        ->state(function (Recipe $record) {
                            $translations = $record->translations;

                            if ($translations->isEmpty()) {
                                return 'No translations yet. Use the Translate button to generate.';
                            }

                            return $translations->map(fn ($t) => strtoupper($t->locale).': '.$t->name)->all();
                        })
                        ->listWithLineBreaks()
                        ->columnSpanFull(),
                ]),

            // Metadata: collapsed by default, only if you need to check
            Section::make('Details')
                ->collapsed()
                ->schema([
                    TextEntry::make('difficulty')->badge(),
                    TextEntry::make('cuisine'),
                    TextEntry::make('primary_protein'),
                    TextEntry::make('servings'),
                    TextEntry::make('prep_time_minutes')->label('Prep time')->suffix(' min'),
                    TextEntry::make('cook_time_minutes')->label('Cook time')->suffix(' min'),
                    TextEntry::make('fiber_g')->label('Fiber')->suffix(' g'),
                    TextEntry::make('sugar_g')->label('Sugar')->suffix(' g'),
                    TextEntry::make('tags')->badge()->separator(','),
                ])->columns(3),
        ]);
    }

    public static function table(Table $table): Table
    {
        $r2Url = config('services.r2.public_url');

        return $table
            ->columns([
                ImageColumn::make('image_full')
                    ->label('Image')
                    ->state(fn (Recipe $record) => $record->image_full ? "{$r2Url}/{$record->image_full}" : null)
                    ->circular()
                    ->size(40),
                TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->limit(40),
                TextColumn::make('calories')
                    ->sortable()
                    ->suffix(' kcal'),
                TextColumn::make('protein_g')
                    ->label('Protein')
                    ->sortable()
                    ->suffix(' g'),
                TextColumn::make('difficulty')
                    ->badge()
                    ->sortable(),
                TextColumn::make('meal_types')
                    ->badge()
                    ->separator(','),
                IconColumn::make('is_verified')
                    ->boolean()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TernaryFilter::make('is_verified'),
                SelectFilter::make('meal_type')
                    ->label('Meal type')
                    ->options([
                        'breakfast' => 'Breakfast',
                        'lunch' => 'Lunch',
                        'dinner' => 'Dinner',
                        'snack' => 'Snack',
                    ])
                    ->query(fn ($query, array $data) => $data['value']
                        ? $query->whereJsonContains('meal_types', $data['value'])
                        : $query),
                SelectFilter::make('diet')
                    ->label('Diet')
                    ->options([
                        'vegetarian' => 'Vegetarian',
                        'vegan' => 'Vegan',
                        'high-protein' => 'High Protein',
                        'low-carb' => 'Low Carb',
                        'quick' => 'Quick',
                        'meal-prep' => 'Meal Prep',
                    ])
                    ->query(fn ($query, array $data) => $data['value']
                        ? $query->whereJsonContains('tags', $data['value'])
                        : $query),
                SelectFilter::make('primary_protein')
                    ->label('Protein source')
                    ->options(fn () => Recipe::query()->distinct()->pluck('primary_protein', 'primary_protein')->filter()->all()),
                SelectFilter::make('difficulty')
                    ->options(fn () => Recipe::query()->distinct()->pluck('difficulty', 'difficulty')->filter()->all()),
                SelectFilter::make('cuisine')
                    ->options(fn () => Recipe::query()->distinct()->pluck('cuisine', 'cuisine')->filter()->all()),
            ])
            ->recordActions([
                ViewAction::make(),
                Action::make('verify')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn (Recipe $record) => ! $record->is_verified)
                    ->action(fn (Recipe $record) => $record->update(['is_verified' => true])),
                Action::make('unverify')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(fn (Recipe $record) => $record->is_verified)
                    ->action(fn (Recipe $record) => $record->update(['is_verified' => false])),
                DeleteAction::make(),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRecipes::route('/'),
            'view' => Pages\ViewRecipe::route('/{record}'),
        ];
    }
}
