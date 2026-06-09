<?php

namespace App\Filament\Resources\RecipeResource\Pages;

use App\Filament\Resources\RecipeResource;
use App\Jobs\EnrichRecipeTranslationsJob;
use App\Models\Recipe;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class ViewRecipe extends ViewRecord
{
    protected static string $resource = RecipeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('enhance')
                ->label('Translate')
                ->icon('heroicon-o-language')
                ->color('info')
                ->requiresConfirmation()
                ->modalHeading('Generate translations')
                ->modalDescription('This will dispatch a job to generate German translations for this recipe using AI.')
                ->action(function () {
                    /** @var Recipe $record */
                    $record = $this->record;

                    EnrichRecipeTranslationsJob::dispatch($record, 'de');

                    Notification::make()
                        ->title('Translation job dispatched')
                        ->body('German translation will be generated in the background.')
                        ->success()
                        ->send();
                }),
            Action::make('verify')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->requiresConfirmation()
                ->visible(fn () => ! $this->record->is_verified)
                ->action(fn () => $this->record->update(['is_verified' => true])),
            Action::make('unverify')
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->requiresConfirmation()
                ->visible(fn () => $this->record->is_verified)
                ->action(fn () => $this->record->update(['is_verified' => false])),
        ];
    }
}
