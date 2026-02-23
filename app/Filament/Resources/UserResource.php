<?php

namespace App\Filament\Resources;

use App\Enums\UserSource;
use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
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
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function infolist(Schema $infolist): Schema
    {
        return $infolist
            ->schema([
                Section::make('User Details')
                    ->schema([
                        TextEntry::make('name'),
                        TextEntry::make('email'),
                        TextEntry::make('source')
                            ->badge(),
                        TextEntry::make('locale'),
                        TextEntry::make('email_verified_at')
                            ->dateTime(),
                        TextEntry::make('created_at')
                            ->dateTime(),
                    ])->columns(3),

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
                    ])->columns(4),
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
