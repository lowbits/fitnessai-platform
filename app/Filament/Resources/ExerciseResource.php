<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ExerciseResource\Pages;
use App\Models\Exercise;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class ExerciseResource extends Resource
{
    protected static ?string $model = Exercise::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-bolt';

    protected static string|\UnitEnum|null $navigationGroup = 'Content';

    protected static ?int $navigationSort = 5;

    public static function form(Schema $form): Schema
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                TextInput::make('type')
                    ->maxLength(50),
                TextInput::make('body_region')
                    ->maxLength(50),
                TextInput::make('difficulty')
                    ->maxLength(50),
                TextInput::make('movement_pattern')
                    ->maxLength(100),
                TextInput::make('mechanic')
                    ->maxLength(50),
                Textarea::make('description')
                    ->columnSpanFull(),
                TagsInput::make('primary_muscles'),
                TagsInput::make('secondary_muscles'),
                TagsInput::make('equipment'),
                TagsInput::make('training_places'),
                TextInput::make('video_url')
                    ->url()
                    ->maxLength(500),
                TextInput::make('image')
                    ->maxLength(500),
                Toggle::make('is_verified'),
                TagsInput::make('event_tags'),
                Textarea::make('form_cues')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image')
                    ->label('')
                    ->state(function (Exercise $record) {
                        if (! $record->image) {
                            return null;
                        }

                        return str_starts_with($record->image, 'http')
                            ? $record->image
                            : config('services.r2.public_url').'/'.$record->image;
                    })
                    ->circular()
                    ->size(40),
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('type')
                    ->badge()
                    ->sortable(),
                TextColumn::make('body_region')
                    ->sortable(),
                TextColumn::make('difficulty')
                    ->sortable(),
                IconColumn::make('is_verified')
                    ->boolean(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->options(fn () => Exercise::query()->distinct()->pluck('type', 'type')->filter()->all()),
                SelectFilter::make('difficulty')
                    ->options(fn () => Exercise::query()->distinct()->pluck('difficulty', 'difficulty')->filter()->all()),
                SelectFilter::make('body_region')
                    ->options(fn () => Exercise::query()->distinct()->pluck('body_region', 'body_region')->filter()->all()),
                TernaryFilter::make('is_verified'),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->defaultSort('name');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListExercises::route('/'),
            'create' => Pages\CreateExercise::route('/create'),
            'view' => Pages\ViewExercise::route('/{record}'),
            'edit' => Pages\EditExercise::route('/{record}/edit'),
        ];
    }
}
