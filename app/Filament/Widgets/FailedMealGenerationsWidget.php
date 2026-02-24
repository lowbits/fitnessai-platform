<?php

namespace App\Filament\Widgets;

use App\Jobs\GenerateUserMealPlan;
use App\Models\MealPlan;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class FailedMealGenerationsWidget extends BaseWidget
{
    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 1;

    protected static ?string $heading = 'Failed Meals';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                MealPlan::query()
                    ->where('status', 'failed')
                    ->with('plan.user')
                    ->latest()
            )
            ->columns([
                TextColumn::make('plan.user.email')
                    ->label('User'),
                TextColumn::make('plan.plan_name')
                    ->label('Name'),
                TextColumn::make('day_number')
                    ->label('Day'),
                TextColumn::make('date')
                    ->date(),
            ])
            ->recordActions([
                Action::make('retry')
                    ->label('Retry')
                    ->icon('heroicon-o-arrow-path')
                    ->color('warning')
                    ->requiresConfirmation()
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
            ->defaultPaginationPageOption(5);
    }
}
