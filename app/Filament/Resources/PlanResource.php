<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PlanResource\Pages;
use App\Filament\Resources\PlanResource\RelationManagers;
use App\Models\Plan;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class PlanResource extends Resource
{
    protected static ?string $model = Plan::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static string|\UnitEnum|null $navigationGroup = 'Plans';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Section::make('Plan Details')
                    ->schema([
                        TextInput::make('plan_name')
                            ->required()
                            ->maxLength(255),
                        Select::make('status')
                            ->options([
                                'active' => 'Active',
                                'paused' => 'Paused',
                                'completed' => 'Completed',
                                'cancelled' => 'Cancelled',
                            ])
                            ->required(),
                        DatePicker::make('start_date')
                            ->required(),
                        DatePicker::make('end_date')
                            ->required(),
                        TextInput::make('duration_days')
                            ->numeric()
                            ->required(),
                    ])->columns(3),

                Section::make('Macros & Training')
                    ->schema([
                        TextInput::make('daily_calories')
                            ->label('Daily Calories')
                            ->numeric(),
                        TextInput::make('daily_protein_g')
                            ->label('Protein (g)')
                            ->numeric(),
                        TextInput::make('daily_carbs_g')
                            ->label('Carbs (g)')
                            ->numeric(),
                        TextInput::make('daily_fat_g')
                            ->label('Fat (g)')
                            ->numeric(),
                        TextInput::make('workouts_per_week')
                            ->numeric(),
                    ])->columns(5),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->sortable(),
                TextColumn::make('user.name')
                    ->label('User')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('plan_name')
                    ->label('Plan')
                    ->searchable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'paused' => 'warning',
                        'completed' => 'info',
                        'cancelled' => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('start_date')
                    ->date()
                    ->sortable(),
                TextColumn::make('end_date')
                    ->date(),
                TextColumn::make('duration_days')
                    ->label('Days'),
                TextColumn::make('daily_calories'),
                TextColumn::make('workouts_per_week'),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'active' => 'Active',
                        'paused' => 'Paused',
                        'completed' => 'Completed',
                        'cancelled' => 'Cancelled',
                    ]),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function infolist(Schema $infolist): Schema
    {
        return $infolist
            ->schema([
                Section::make('Plan Details')
                    ->schema([
                        TextEntry::make('user.name')
                            ->label('User'),
                        TextEntry::make('user.email')
                            ->label('Email'),
                        TextEntry::make('plan_name'),
                        TextEntry::make('status')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'active' => 'success',
                                'paused' => 'warning',
                                'completed' => 'info',
                                'cancelled' => 'danger',
                                default => 'gray',
                            }),
                        TextEntry::make('start_date')
                            ->date(),
                        TextEntry::make('end_date')
                            ->date(),
                        TextEntry::make('duration_days'),
                    ])->columns(4),

                Section::make('Macros')
                    ->schema([
                        TextEntry::make('daily_calories')
                            ->label('Calories'),
                        TextEntry::make('daily_protein_g')
                            ->label('Protein (g)'),
                        TextEntry::make('daily_carbs_g')
                            ->label('Carbs (g)'),
                        TextEntry::make('daily_fat_g')
                            ->label('Fat (g)'),
                        TextEntry::make('workouts_per_week'),
                    ])->columns(5),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\WorkoutPlansRelationManager::class,
            RelationManagers\MealPlansRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPlans::route('/'),
            'view' => Pages\ViewPlan::route('/{record}'),
            'edit' => Pages\EditPlan::route('/{record}/edit'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}
