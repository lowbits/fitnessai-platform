<?php

namespace App\Filament\Resources\PlanResource\RelationManagers;

use App\Jobs\GenerateUserMealPlan;
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
                    ->visible(fn (MealPlan $record): bool => $record->status === 'failed')
                    ->action(function (MealPlan $record): void {
                        $plan = $record->plan;
                        $plan->mealPlans()->where('status', 'failed')->update(['status' => 'pending']);
                        GenerateUserMealPlan::dispatch($plan->user, $plan);

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
                        $plans = $records->where('status', 'failed')->pluck('plan_id')->unique();

                        foreach ($plans as $planId) {
                            $plan = \App\Models\Plan::with('user')->find($planId);
                            if ($plan) {
                                $plan->mealPlans()->where('status', 'failed')->update(['status' => 'pending']);
                                GenerateUserMealPlan::dispatch($plan->user, $plan);
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
