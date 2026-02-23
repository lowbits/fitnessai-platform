<?php

namespace App\Filament\Widgets;

use App\Jobs\GenerateUserWorkoutPlan;
use App\Models\WorkoutPlan;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class FailedGenerationsWidget extends BaseWidget
{
    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    protected static ?string $heading = 'Failed Generations';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                WorkoutPlan::query()
                    ->where('status', 'failed')
                    ->with('plan.user')
                    ->latest()
                    ->limit(10)
            )
            ->columns([
                TextColumn::make('plan.user.name')
                    ->label('User'),
                TextColumn::make('workout_name')
                    ->label('Name'),
                TextColumn::make('date')
                    ->date(),
                TextColumn::make('status')
                    ->badge()
                    ->color('danger'),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->recordActions([
                Action::make('retry')
                    ->label('Retry')
                    ->icon('heroicon-o-arrow-path')
                    ->color('warning')
                    ->requiresConfirmation()
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
            ->paginated(false);
    }
}
