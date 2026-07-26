<?php

namespace App\Filament\Resources;

use App\Actions\RetryPlanGeneration;
use App\Filament\Resources\MealPlanResource\Pages;
use App\Models\MealPlan;
use App\Models\Plan;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ViewEntry;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;

class MealPlanResource extends Resource
{
    protected static ?string $model = MealPlan::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-cake';

    protected static string|\UnitEnum|null $navigationGroup = 'Plans';

    protected static ?int $navigationSort = 4;

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->sortable(),
                TextColumn::make('plan.user.name')
                    ->label('User')
                    ->searchable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'generated' => 'success',
                        'pending' => 'warning',
                        'failed' => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('date')
                    ->date()
                    ->sortable(),
                TextColumn::make('day_number')
                    ->label('Day'),
                TextColumn::make('total_calories')
                    ->label('Calories'),
                TextColumn::make('total_protein_g')
                    ->label('Protein (g)'),
                TextColumn::make('total_carbs_g')
                    ->label('Carbs (g)'),
                TextColumn::make('total_fat_g')
                    ->label('Fat (g)'),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'generated' => 'Generated',
                        'failed' => 'Failed',
                    ]),
            ])
            ->recordActions([
                Action::make('retry')
                    ->label('Retry')
                    ->icon('heroicon-o-arrow-path')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->visible(fn (MealPlan $record): bool => in_array($record->status, ['failed', 'pending']))
                    ->action(function (MealPlan $record): void {
                        RetryPlanGeneration::meals($record->plan);

                        Notification::make()
                            ->title('Meal generation retry dispatched')
                            ->success()
                            ->send();
                    }),
            ])
            ->toolbarActions([
                BulkAction::make('retryAll')
                    ->label('Retry Selected')
                    ->icon('heroicon-o-arrow-path')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->action(function (Collection $records): void {
                        $plans = $records->whereIn('status', ['failed', 'pending'])->pluck('plan_id')->unique();

                        foreach ($plans as $planId) {
                            $plan = Plan::with('user')->find($planId);
                            if ($plan) {
                                RetryPlanGeneration::meals($plan);
                            }
                        }

                        Notification::make()
                            ->title('Retry dispatched for '.$plans->count().' plan(s)')
                            ->success()
                            ->send();
                    }),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function infolist(Schema $infolist): Schema
    {
        return $infolist
            ->schema([
                Section::make('Meal Plan Details')
                    ->schema([
                        TextEntry::make('plan.user.name')
                            ->label('User'),
                        TextEntry::make('status')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'generated' => 'success',
                                'pending' => 'warning',
                                'failed' => 'danger',
                                default => 'gray',
                            }),
                        TextEntry::make('date')
                            ->date(),
                        TextEntry::make('day_number'),
                        TextEntry::make('total_calories')
                            ->label('Calories'),
                        TextEntry::make('total_protein_g')
                            ->label('Protein (g)'),
                        TextEntry::make('total_carbs_g')
                            ->label('Carbs (g)'),
                        TextEntry::make('total_fat_g')
                            ->label('Fat (g)'),
                    ])
                    ->columns(4)
                    ->columnSpanFull(),

                Section::make('Meals')
                    ->schema([
                        ViewEntry::make('meals_grid')
                            ->hiddenLabel()
                            ->view('filament.meal-plan.contents'),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMealPlans::route('/'),
            'view' => Pages\ViewMealPlan::route('/{record}'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}
