<?php

namespace App\Filament\Resources\PlanResource\Pages;

use App\Filament\Resources\PlanResource;
use App\Models\Plan;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Facades\Artisan;

class ViewPlan extends ViewRecord
{
    protected static string $resource = PlanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ActionGroup::make([
                Action::make('generateWeek')
                    ->label('Generate Week')
                    ->icon('heroicon-o-calendar')
                    ->requiresConfirmation()
                    ->modalHeading('Generate Full Week')
                    ->modalDescription('This will dispatch jobs to generate the next 7 days of both workout and meal plans.')
                    ->action(function (Plan $record): void {
                        Artisan::call('plans:generate-weekly', [
                            '--email' => $record->user->email,
                            '--force' => true,
                        ]);

                        Notification::make()
                            ->title('Weekly generation dispatched')
                            ->body("Workouts + Meals queued for {$record->user->name}")
                            ->success()
                            ->send();
                    }),
                Action::make('generateWorkouts')
                    ->label('Generate Workouts')
                    ->icon('heroicon-o-bolt')
                    ->requiresConfirmation()
                    ->modalHeading('Generate Workout Plans')
                    ->modalDescription('This will dispatch a job to generate the next 7 days of workout plans.')
                    ->action(function (Plan $record): void {
                        Artisan::call('plans:generate-weekly', [
                            '--email' => $record->user->email,
                            '--force' => true,
                            '--only' => 'workouts',
                        ]);

                        Notification::make()
                            ->title('Workout generation dispatched')
                            ->body("Queued for {$record->user->name}")
                            ->success()
                            ->send();
                    }),
                Action::make('generateMeals')
                    ->label('Generate Meals')
                    ->icon('heroicon-o-fire')
                    ->requiresConfirmation()
                    ->modalHeading('Generate Meal Plans')
                    ->modalDescription('This will dispatch a job to generate the next 7 days of meal plans.')
                    ->action(function (Plan $record): void {
                        Artisan::call('plans:generate-weekly', [
                            '--email' => $record->user->email,
                            '--force' => true,
                            '--only' => 'meals',
                        ]);

                        Notification::make()
                            ->title('Meal generation dispatched')
                            ->body("Queued for {$record->user->name}")
                            ->success()
                            ->send();
                    }),
            ])
                ->label('Generate Plans')
                ->icon('heroicon-o-arrow-path')
                ->color('warning')
                ->button()
                ->visible(fn (): bool => $this->record->status === 'active'),

            EditAction::make(),
        ];
    }
}
