<?php

namespace App\Filament\Resources\UserResource\RelationManagers;

use App\Filament\Resources\PlanResource;
use App\Models\Plan;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Artisan;

class PlansRelationManager extends RelationManager
{
    protected static string $relationship = 'plans';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('plan_name')
                    ->label('Plan'),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'paused' => 'warning',
                        'completed' => 'info',
                        'cancelled' => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('start_date')
                    ->date(),
                TextColumn::make('end_date')
                    ->date(),
                TextColumn::make('duration_days')
                    ->label('Days'),
                TextColumn::make('workout_plans_count')
                    ->counts('workoutPlans')
                    ->label('Workouts'),
                TextColumn::make('failed_workouts_count')
                    ->label('Failed WO')
                    ->state(fn ($record) => $record->workoutPlans()->where('status', 'failed')->count())
                    ->color(fn (int $state): string => $state > 0 ? 'danger' : 'success')
                    ->badge(),
                TextColumn::make('meal_plans_count')
                    ->counts('mealPlans')
                    ->label('Meals'),
                TextColumn::make('failed_meals_count')
                    ->label('Failed MP')
                    ->state(fn ($record) => $record->mealPlans()->where('status', 'failed')->count())
                    ->color(fn (int $state): string => $state > 0 ? 'danger' : 'success')
                    ->badge(),
                TextColumn::make('daily_calories'),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->recordActions([
                ActionGroup::make([
                    Action::make('generateWeek')
                        ->label('Generate Week')
                        ->icon('heroicon-o-calendar')
                        ->requiresConfirmation()
                        ->action(function (Plan $record): void {
                            Artisan::call('plans:generate-weekly', [
                                '--email' => $record->user->email,
                                '--force' => true,
                            ]);

                            Notification::make()
                                ->title('Weekly generation dispatched')
                                ->success()
                                ->send();
                        }),
                    Action::make('generateWorkouts')
                        ->label('Generate Workouts')
                        ->icon('heroicon-o-bolt')
                        ->requiresConfirmation()
                        ->action(function (Plan $record): void {
                            Artisan::call('plans:generate-weekly', [
                                '--email' => $record->user->email,
                                '--force' => true,
                                '--only' => 'workouts',
                            ]);

                            Notification::make()
                                ->title('Workout generation dispatched')
                                ->success()
                                ->send();
                        }),
                    Action::make('generateMeals')
                        ->label('Generate Meals')
                        ->icon('heroicon-o-fire')
                        ->requiresConfirmation()
                        ->action(function (Plan $record): void {
                            Artisan::call('plans:generate-weekly', [
                                '--email' => $record->user->email,
                                '--force' => true,
                                '--only' => 'meals',
                            ]);

                            Notification::make()
                                ->title('Meal generation dispatched')
                                ->success()
                                ->send();
                        }),
                ])
                    ->label('Generate')
                    ->icon('heroicon-o-arrow-path')
                    ->color('warning')
                    ->visible(fn (Plan $record): bool => $record->status === 'active'),
                Action::make('view')
                    ->label('View')
                    ->icon('heroicon-o-eye')
                    ->url(fn ($record) => PlanResource::getUrl('view', ['record' => $record])),
                Action::make('edit')
                    ->label('Edit')
                    ->icon('heroicon-o-pencil')
                    ->url(fn ($record) => PlanResource::getUrl('edit', ['record' => $record])),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
