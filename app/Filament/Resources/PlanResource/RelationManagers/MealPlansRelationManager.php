<?php

namespace App\Filament\Resources\PlanResource\RelationManagers;

use App\Actions\RetryPlanGeneration;
use App\Models\MealPlan;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;

class MealPlansRelationManager extends RelationManager
{
    protected static string $relationship = 'mealPlans';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('day_number')
                    ->label('Day')
                    ->sortable(),
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
                            $plan = \App\Models\Plan::with('user')->find($planId);
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
            ->defaultSort('day_number');
    }
}
