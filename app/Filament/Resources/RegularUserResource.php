<?php
//
namespace App\Filament\Resources;

use App\Filament\Resources\RegularUserResource\Pages;
use App\Filament\Resources\RegularUserResource\RelationManagers;
use App\Models\RegularUser;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Models\User;
class RegularUserResource extends Resource
{
    protected static ?int $navigationSort = 1;
    protected static ?string $model = RegularUser::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    //     public static function canCreate(): Bool
    // {
    //     return false;
    // }

    public static function form(Form $form): Form
    {
        return $form
        
        ->schema([
            Forms\Components\TextInput::make('name')
                ->required()
                ->maxLength(255),
            
            Forms\Components\TextInput::make('email')
                ->email()
                ->unique()
                ->required()
                ->maxLength(255),
            Forms\Components\TextInput::make('password')
                ->password()
                ->maxLength(255)
                ->dehydrateStateUsing(fn ($state) => bcrypt($state)),// Ensure password is hashed

            Forms\Components\Select::make('dept_role')
            ->label('Department')
            ->required()
            ->options(User::Dept), 
            Forms\Components\Select::make('position')
            ->required()
            ->options(User::Pos),
            Forms\Components\Select::make('role')
            ->required()
            ->options(['user' =>'Regular User']), // Default value for Regular User
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
        ->query(RegularUser::query())
        ->columns([
            Tables\Columns\TextColumn::make('name')
                ->searchable()
                ->sortable(),
                
            Tables\Columns\TextColumn::make('email')
                ->searchable()
                ->sortable(),
        
                
            Tables\Columns\TextColumn::make('dept_role')
                ->label('Department'),// Display the label for department role
                
            Tables\Columns\TextColumn::make('position')
            ->label('Position')
            ->sortable(),

                
            Tables\Columns\TextColumn::make('role')
            ->label('Role')
            ->sortable(),
             // Display the label for role
                
            Tables\Columns\TextColumn::make('created_at')
                ->dateTime()
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),
                
            Tables\Columns\TextColumn::make('updated_at')
                ->dateTime()
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),
        ])
            ->filters([
                //
            ])
            ->actions([
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
            'index' => Pages\ListRegularUsers::route('/'),
            // 'create' => Pages\CreateRegularUser::route('/create'),
            // 'edit' => Pages\EditRegularUser::route('/{record}/edit'),
        ];
    }
}
