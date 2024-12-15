<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LocationResource\Pages;
use App\Filament\Resources\LocationResource\RelationManagers;
use App\Models\Location;
use Filament\Forms;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Grid;
class LocationResource extends Resource
{
    protected static ?string $model = Location::class;
    protected static ?string $navigationLabel = 'Locations';
    protected static ?string $navigationGroup = 'Manage';
    protected static ?int $navigationSort = 3;
    public static function form(Form $form): Form
    {
        return $form
        ->schema([
            Card::make('Location info') // Section for the location form
                ->description('Register a new location to the system')
                ->schema([
                    Grid::make(3) // Three columns layout
                        ->schema([
                            TextInput::make('department')
                                ->label('Department')
                                ->required()
                                ->placeholder('Enter department'),
        
                            TextInput::make('building')
                                ->label('Building')
                                ->required()
                                ->placeholder('Enter building'),
        
                            TextInput::make('room_no')
                                ->label('Room no.')
                                ->required()
                                ->placeholder('Enter room number'),
                        ]),
                ]),
        ]);
        
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // Column for Department
                TextColumn::make('department_id')
                    ->label('Department')
                    ->sortable()
                    ->searchable(),
                    
                // Column for Building
                TextColumn::make('building')
                    ->label('Building')
                    ->sortable()
                    ->searchable(),
    
                // Column for Room Number
                TextColumn::make('room_no')
                    ->label('Room no.')
                    ->sortable()
                    ->searchable(),
            ])
            ->filters([
                // You can add filters if needed (e.g., by department)
            ])
            ->actions([
                // Edit action
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
           // 'create' => Pages\CreateLocation::route('/create'),
            //'edit' => Pages\EditLocation::route('/{record}/edit'),
        ];
    }
}
