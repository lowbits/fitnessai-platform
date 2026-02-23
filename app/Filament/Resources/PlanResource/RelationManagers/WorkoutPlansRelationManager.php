<?php

namespace App\Filament\Resources\PlanResource\RelationManagers;

use App\Jobs\GenerateUserWorkoutPlan;
use App\Models\WorkoutPlan;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;

class WorkoutPlansRelationManager extends RelationManager
{
    protected static string $relationship = 'workoutPlans';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('day_number')
                    ->label('Day')
                    ->sortable(),
                TextColumn::make('workout_name')
                    ->label('Workout'),
                TextColumn::make('workout_type')
                    ->badge(),
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
                TextColumn::make('difficulty'),
                TextColumn::make('estimated_duration_minutes')
                    ->label('Duration (min)'),
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
                    ->visible(fn (WorkoutPlan $record): bool => $record->status === 'failed')
                    ->action(function (WorkoutPlan $record): void {
                        $plan = $record->plan;
                        $plan->workoutPlans()->where('status', 'failed')->update(['status' => 'pending']);
                        GenerateUserWorkoutPlan::dispatch($plan->user, $plan);

                        Notification::make()
                            ->title('Workout generation retry dispatched')
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
                                $plan->workoutPlans()->where('status', 'failed')->update(['status' => 'pending']);
                                GenerateUserWorkoutPlan::dispatch($plan->user, $plan);
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
