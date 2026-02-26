<?php

namespace App\Filament\Resources;

use App\Actions\RetryPlanGeneration;
use App\Filament\Resources\WorkoutPlanResource\Pages;
use App\Models\WorkoutPlan;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;

class WorkoutPlanResource extends Resource
{
    protected static ?string $model = WorkoutPlan::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-fire';

    protected static string|\UnitEnum|null $navigationGroup = 'Plans';

    protected static ?int $navigationSort = 3;

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->sortable(),
                TextColumn::make('plan.user.name')
                    ->label('User')
                    ->searchable(),
                TextColumn::make('workout_name')
                    ->label('Workout')
                    ->searchable(),
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
                    ->visible(fn (WorkoutPlan $record): bool => in_array($record->status, ['failed', 'pending']))
                    ->action(function (WorkoutPlan $record): void {
                        RetryPlanGeneration::workouts($record->plan);

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
                        $plans = $records->whereIn('status', ['failed', 'pending'])->pluck('plan_id')->unique();

                        foreach ($plans as $planId) {
                            $plan = \App\Models\Plan::with('user')->find($planId);
                            if ($plan) {
                                RetryPlanGeneration::workouts($plan);
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
                Section::make('Workout Details')
                    ->schema([
                        TextEntry::make('plan.user.name')
                            ->label('User'),
                        TextEntry::make('workout_name'),
                        TextEntry::make('workout_type')
                            ->badge(),
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
                        TextEntry::make('difficulty'),
                        TextEntry::make('estimated_duration_minutes')
                            ->label('Duration (min)'),
                        TextEntry::make('estimated_calories_burned')
                            ->label('Est. Calories'),
                        TextEntry::make('description')
                            ->columnSpanFull(),
                    ])->columns(4),

                Section::make('Exercises')
                    ->schema([
                        RepeatableEntry::make('exercises')
                            ->schema([
                                TextEntry::make('name'),
                                TextEntry::make('sets'),
                                TextEntry::make('reps'),
                                TextEntry::make('duration_seconds')
                                    ->label('Duration (s)'),
                                TextEntry::make('rest_seconds')
                                    ->label('Rest (s)'),
                                TextEntry::make('execution_style')
                                    ->label('Style'),
                            ])
                            ->columns(6),
                    ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListWorkoutPlans::route('/'),
            'view' => Pages\ViewWorkoutPlan::route('/{record}'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}
