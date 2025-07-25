<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LocationResource\Pages;
use App\Models\Department;
use App\Models\Location;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\Select;
use Filament\Tables\Filters\SelectFilter;
use Filament\Forms\Components\Card;
use Filament\Support\Colors\Color;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Grid;
class LocationResource extends Resource
{
    protected static ?string $model = Location::class;
    protected static ?string $navigationLabel = 'Locations';
    protected static ?string $navigationGroup = 'Manage';
    protected static ?int $navigationSort = 4;
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Card::make('Location info')
                    ->description('Register a new location to the system')
                    ->schema([
                        Grid::make(4)
                            ->schema([
                                Select::make('department')
                                    ->label('Department')
                                    ->required()
                                    ->options(Department::Dept)
                                    ->placeholder('Enter department'),

                                TextInput::make('building')
                                    ->label('Building')
                                    ->required()
                                    ->placeholder('Enter building'),

                                TextInput::make('location')
                                    ->label('Room no.')
                                    ->required()
                                    ->placeholder('Enter room number'),
                                Select::make('priority')
                                    ->label('Priority')
                                    ->required()
                                    ->options(Location::Priority)
                                    ->placeholder('Select priority'),

                            ]),
                    ]),
            ]);

    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // Column for Department
                TextColumn::make('department')
                    ->label('Department')
                    ->sortable()
                    ->searchable(),

                // Column for Building
                TextColumn::make('building')
                    ->label('Building')
                    ->sortable()
                    ->searchable(),

                // Column for Room Number
                TextColumn::make('location')
                    ->label('Room no.')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('priority')
                    ->label('Priority')
                    ->sortable()
                    ->color(function ($state) {
                        return match ($state) {
                            'Urgent' => Color::Red,
                            'High' => Color::Orange,
                            'Moderate' => Color::Yellow,
                            'Low' => Color::Blue,
                            'Escalated' => Color::Purple,
                            default => null,
                        };
                    })
                    ->searchable()
            ])
            ->filters([
                SelectFilter::make('department')
                    ->label('Department')
                    ->options(Department::Dept),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ]),
            ]);

    }


    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLocations::route('/'),
        ];
    }
}
