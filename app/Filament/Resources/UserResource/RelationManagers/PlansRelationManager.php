<?php

namespace App\Filament\Resources\UserResource\RelationManagers;

use App\Filament\Resources\PlanResource;
use Filament\Actions\Action;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

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
