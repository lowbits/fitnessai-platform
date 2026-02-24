<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\UserResource;
use App\Models\User;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LatestUsersWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected int|string|array $columnSpan = 1;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                User::query()->latest()
            )
            ->columns([
                TextColumn::make('email')
                    ->searchable(),
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('source')
                    ->badge(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->recordUrl(fn (User $record): string => UserResource::getUrl('view', ['record' => $record]))
            ->defaultPaginationPageOption(5);
    }
}
