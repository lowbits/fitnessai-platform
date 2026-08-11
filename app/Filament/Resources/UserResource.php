<?php

namespace App\Filament\Resources;

use App\Enums\UserSource;
use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Livewire\UserPlanDayBrowser;
use App\Models\User;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ViewEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Livewire;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-users';

    protected static string|\UnitEnum|null $navigationGroup = 'Users';

    protected static ?int $navigationSort = 1;

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->withCount('tokens'))
            ->columns([
                TextColumn::make('id')
                    ->sortable(),
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('email')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('source')
                    ->badge()
                    ->sortable(),
                IconColumn::make('mobile_converted')
                    ->label('Converted')
                    ->state(fn (User $record): bool => $record->isConverted())
                    ->boolean()
                    ->trueIcon('heroicon-o-device-phone-mobile')
                    ->falseIcon('heroicon-o-minus')
                    ->trueColor('success')
                    ->falseColor('gray'),
                TextColumn::make('plans_count')
                    ->counts('plans')
                    ->label('Plans')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('source')
                    ->options(UserSource::class),
                TernaryFilter::make('is_mobile')
                    ->label('Mobile user')
                    ->queries(
                        true: fn ($query) => $query->mobile(),
                        false: fn ($query) => $query->whereNot(fn ($query) => $query->mobile()),
                    ),
                TernaryFilter::make('mobile_converted')
                    ->label('Web → Mobile')
                    ->queries(
                        true: fn ($query) => $query->converted(),
                        false: fn ($query) => $query->where('source', UserSource::WEB)
                            ->whereNot(fn ($query) => $query->converted()),
                    ),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function infolist(Schema $infolist): Schema
    {
        return $infolist
            ->columns(2)
            ->schema([
                Section::make('User Details')
                    ->schema([
                        TextEntry::make('name'),
                        TextEntry::make('email'),
                        TextEntry::make('source')
                            ->badge(),
                        TextEntry::make('tokens_count')
                            ->label('Mobile Converted')
                            ->state(fn (User $record): string => $record->source === UserSource::WEB
                                ? ($record->isConverted() ? 'Yes' : 'No')
                                : 'N/A (native)')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'Yes' => 'success',
                                'No' => 'danger',
                                default => 'gray',
                            }),
                        TextEntry::make('locale'),
                        TextEntry::make('email_verified_at')
                            ->dateTime(),
                        TextEntry::make('created_at')
                            ->dateTime(),
                    ])->columns(2),

                Section::make('Profile')
                    ->schema([
                        TextEntry::make('profile.age'),
                        TextEntry::make('profile.gender'),
                        TextEntry::make('profile.weight_kg')
                            ->label('Weight (kg)'),
                        TextEntry::make('profile.height_cm')
                            ->label('Height (cm)'),
                        TextEntry::make('profile.body_goal')
                            ->label('Goal'),
                        TextEntry::make('profile.skill_level')
                            ->label('Skill Level'),
                        TextEntry::make('profile.activity_level')
                            ->label('Activity Level'),
                        TextEntry::make('profile.training_place')
                            ->label('Training Place'),
                        TextEntry::make('profile.diet_type')
                            ->label('Diet Type'),
                        TextEntry::make('profile.dietary_preference')
                            ->label('Dietary Preference'),
                        TextEntry::make('profile.training_sessions_per_week')
                            ->label('Sessions/Week'),
                    ])->columns(2),

                Section::make('Computed Targets')
                    ->description('Derived inputs the AI uses to build meal plans')
                    ->schema([
                        ViewEntry::make('computed_targets')
                            ->hiddenLabel()
                            ->view('filament.user.computed-targets'),
                    ])
                    ->columnSpanFull()
                    ->collapsible(),

                Section::make('Day Preview')
                    ->description('Browse meals + workout day-by-day')
                    ->schema([
                        Livewire::make(UserPlanDayBrowser::class, fn (User $record): array => ['user' => $record]),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\PlansRelationManager::class,
            RelationManagers\DevicesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'view' => Pages\ViewUser::route('/{record}'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}
