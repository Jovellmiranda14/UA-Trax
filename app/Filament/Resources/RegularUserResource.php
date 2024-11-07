<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RegularUserResource\Pages;

use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Card;

class RegularUserResource extends Resource
{
    protected static ?int $navigationSort = 1;
    protected static ?string $model = User::class;
    protected static ?string $navigationLabel = 'Regular user';
    //protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Users';
    // Uncomment the following method if you want to disable the creation of new Regular Users
    // public static function canCreate(): bool
    // {
    //     return false;
    // }

    public static function form(Form $form): Form
    {
        return $form

            ->schema([
                Card::make('User info')
                    ->description('Register a user in the system.')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->label('Full name')
                                    ->required()
                                    ->maxLength(255),

                                Forms\Components\TextInput::make('email')
                                    ->label('Email')
                                    ->email()
                                    ->required()
                                    ->maxLength(255),

                                Forms\Components\TextInput::make('password')
                                    ->label('Password')
                                    ->revealable(true)
                                    ->required()
                                    ->password()
                                    ->minLength(8)
                                    ->maxLength(255)
                                    ->dehydrateStateUsing(fn($state) => bcrypt($state)),  // Ensure password is hashed

                                Forms\Components\Select::make('dept_role')
                                    ->label('Department')
                                    ->required()
                                    ->options(User::Dept),

                                Forms\Components\Select::make('position')
                                    ->label('Position')
                                    ->required()
                                    ->options(User::Pos),

                                Forms\Components\TextInput::make('role')
                                    ->label('Role')
                                    ->required()
                                    ->disabled()
                                    ->default('user'), // Default value for Regular User
                            ])
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {


        return $table
            // Pagination 
            // ->paginated([10, 25, 50, 100, 'all']) 
            ->query(User::query()->where('role', 'user'))
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('dept_role')
                    ->label('Department'), // Display the label for department role

                Tables\Columns\TextColumn::make('position')
                    ->label('Position')
                    ->sortable(),

                Tables\Columns\TextColumn::make('role')
                    ->label('Role')
                    ->sortable(), // Display the label for role

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
                // Add filters here if needed
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
            // Define relation managers here if needed
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRegularUsers::route('/'),
            // Uncomment the following lines if you want to enable the create and edit pages
            // 'create' => Pages\CreateRegularUser::route('/create'),
            // 'edit' => Pages\EditRegularUser::route('/{record}/edit'),
        ];
    }
}
