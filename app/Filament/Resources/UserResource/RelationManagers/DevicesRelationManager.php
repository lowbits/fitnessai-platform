<?php

namespace App\Filament\Resources\UserResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class DevicesRelationManager extends RelationManager
{
    protected static string $relationship = 'devices';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('device_name'),
                TextColumn::make('platform'),
                TextColumn::make('last_used_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('last_used_at', 'desc');
    }
}
