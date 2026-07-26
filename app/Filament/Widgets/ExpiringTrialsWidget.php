<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\UserResource;
use App\Models\Plan;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Facades\DB;

class ExpiringTrialsWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected int|string|array $columnSpan = 1;

    protected static ?string $heading = 'Expiring Trials';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Plan::query()
                    ->where('status', 'active')
                    ->whereBetween('end_date', [now()->startOfDay(), now()->addDays(7)->endOfDay()])
                    ->whereNotExists(function ($query) {
                        $query->select(DB::raw(1))
                            ->from('subscriptions')
                            ->whereColumn('subscriptions.billable_id', 'plans.user_id')
                            ->where('subscriptions.billable_type', 'App\\Models\\User')
                            ->where('subscriptions.status', 'active');
                    })
                    ->with('user')
                    ->orderBy('end_date')
            )
            ->columns([
                TextColumn::make('user.email')
                    ->label('Email')
                    ->searchable(),
                TextColumn::make('user.name')
                    ->label('Name'),
                TextColumn::make('end_date')
                    ->label('Expires')
                    ->date(),
                TextColumn::make('days_left')
                    ->label('Days Left')
                    ->state(fn (Plan $record): int => (int) now()->startOfDay()->diffInDays($record->end_date, false))
                    ->badge()
                    ->color(fn (int $state): string => match (true) {
                        $state <= 1 => 'danger',
                        $state <= 3 => 'warning',
                        default => 'success',
                    })
                    ->sortable(query: fn ($query, string $direction) => $query->orderBy('end_date', $direction)),
            ])
            ->recordUrl(fn (Plan $record): string => UserResource::getUrl('view', ['record' => $record->user]))
            ->defaultPaginationPageOption(5);
    }
}
