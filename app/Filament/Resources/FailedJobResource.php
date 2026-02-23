<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FailedJobResource\Pages;
use App\Models\FailedJob;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\DeleteAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Artisan;

class FailedJobResource extends Resource
{
    protected static ?string $model = FailedJob::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-exclamation-triangle';

    protected static string|\UnitEnum|null $navigationGroup = 'System';

    protected static ?int $navigationSort = 10;

    protected static ?string $navigationLabel = 'Failed Jobs';

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->sortable(),
                TextColumn::make('uuid')
                    ->copyable()
                    ->limit(12),
                TextColumn::make('queue')
                    ->badge(),
                TextColumn::make('payload')
                    ->limit(60)
                    ->tooltip(function (FailedJob $record): string {
                        $payload = json_decode($record->payload, true);

                        return $payload['displayName'] ?? 'Unknown';
                    })
                    ->formatStateUsing(function (string $state): string {
                        $payload = json_decode($state, true);

                        return $payload['displayName'] ?? 'Unknown job';
                    }),
                TextColumn::make('exception')
                    ->limit(80)
                    ->tooltip(fn (string $state): string => substr($state, 0, 500)),
                TextColumn::make('failed_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->recordActions([
                Action::make('retry')
                    ->label('Retry')
                    ->icon('heroicon-o-arrow-path')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->action(function (FailedJob $record): void {
                        Artisan::call('queue:retry', ['id' => [$record->uuid]]);

                        Notification::make()
                            ->title('Job retry dispatched')
                            ->success()
                            ->send();
                    }),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkAction::make('retrySelected')
                    ->label('Retry Selected')
                    ->icon('heroicon-o-arrow-path')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->action(function (Collection $records): void {
                        $uuids = $records->pluck('uuid')->all();
                        Artisan::call('queue:retry', ['id' => $uuids]);

                        Notification::make()
                            ->title('Retry dispatched for '.count($uuids).' job(s)')
                            ->success()
                            ->send();
                    }),
                BulkAction::make('deleteSelected')
                    ->label('Delete Selected')
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(fn (Collection $records) => $records->each->delete()),
            ])
            ->defaultSort('failed_at', 'desc');
    }

    public static function infolist(Schema $infolist): Schema
    {
        return $infolist
            ->schema([
                Section::make('Job Details')
                    ->schema([
                        TextEntry::make('uuid')
                            ->copyable(),
                        TextEntry::make('queue')
                            ->badge(),
                        TextEntry::make('connection'),
                        TextEntry::make('failed_at')
                            ->dateTime(),
                    ])->columns(4),

                Section::make('Payload')
                    ->schema([
                        TextEntry::make('payload')
                            ->formatStateUsing(fn (string $state): string => json_encode(json_decode($state, true), JSON_PRETTY_PRINT))
                            ->prose()
                            ->columnSpanFull(),
                    ]),

                Section::make('Exception')
                    ->schema([
                        TextEntry::make('exception')
                            ->prose()
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFailedJobs::route('/'),
            'view' => Pages\ViewFailedJob::route('/{record}'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}
