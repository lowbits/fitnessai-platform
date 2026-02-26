<?php

namespace App\Filament\Pages;

use Filament\Actions\Action;
use Filament\Forms\Components\CheckboxList;
use Filament\Notifications\Notification;
use Filament\Pages\Dashboard as BaseDashboard;
use Illuminate\Support\Facades\Artisan;

class Dashboard extends BaseDashboard
{
    protected function getHeaderActions(): array
    {
        return [
            Action::make('retryFailedPlans')
                ->label('Retry Plans')
                ->icon('heroicon-o-arrow-path')
                ->color('warning')
                ->requiresConfirmation()
                ->modalDescription('Select which plan types and statuses to retry.')
                ->schema([
                    CheckboxList::make('types')
                        ->label('Plan Types')
                        ->options([
                            'workouts' => 'Workouts',
                            'nutrition' => 'Nutrition',
                        ])
                        ->default(['workouts', 'nutrition']),
                    CheckboxList::make('statuses')
                        ->label('Statuses')
                        ->options([
                            'failed' => 'Failed',
                            'pending' => 'Pending',
                        ])
                        ->default(['failed']),
                ])
                ->action(function (array $data): void {
                    $types = $data['types'] ?? [];
                    $statuses = $data['statuses'] ?? [];

                    if (empty($types) || empty($statuses)) {
                        Notification::make()
                            ->title('Please select at least one type and status')
                            ->warning()
                            ->send();

                        return;
                    }

                    $results = [];

                    foreach ($types as $type) {
                        Artisan::call('plans:retry-failed', [
                            '--type' => $type,
                            '--status' => implode(',', $statuses),
                            '--no-interaction' => true,
                        ]);
                        $results[] = Artisan::output();
                    }

                    Notification::make()
                        ->title('Retry dispatched')
                        ->body(implode("\n", $results))
                        ->success()
                        ->send();
                }),
        ];
    }
}
