<?php
//Super Admin
namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use App\Models\Department;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Card;
class UserResource extends Resource
{
    protected static ?string $navigationLabel = 'Superadmins';
    protected static ?string $model = User::class;
    //protected static ?string $navigationIcon = 'heroicon-o-user';
    protected static ?string $navigationGroup = 'Manage';
    protected static ?int $navigationSort = 3;
    
    public static function canCreate(): bool
{
    $user = auth()->user();

 
    $allowedRoles = ['equipmentsuperadmin', 'facilitysuperadmin'];

    return $user && in_array($user->role, $allowedRoles);
}

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
                                    ->placeholder('username@ua.edu.ph')
                                    ->rule('regex:/^[a-zA-Z0-9._%+-]+@ua\.edu\.ph$/')
                                    ->maxLength(255),

                                Forms\Components\TextInput::make('password')
                                    ->label('Password')
                                    ->revealable(true)
                                    ->required()
                                    ->password()
                                    ->minLength(8)
                                    ->maxLength(255)
                                     ->dehydrateStateUsing(fn($state) => bcrypt($state)), // Ensure password is hashed

                                Forms\Components\Select::make('dept_role')
                                    ->label('Department Assigned')
                                    ->required()
                                    ->options(Department::Dept),

                                Forms\Components\Select::make('position')
                                    ->required()
                                    ->options(User::Pos),

                                Forms\Components\Select::make('role')
                                    ->label('Role')
                                    ->required()
                                    ->options([
                                        'equipmentsuperadmin' => 'Equipment Super Admin',
                                        'facilitysuperadmin' => 'Facility Super Admin',
                                    ]),
                            ])
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        $user = auth()->user();

        return $table
    
        ->query(
            User::query()
                ->when(
                    // Check if the user is NOT a superadmin
                    !($user->role === 'facilitysuperadmin' || $user->role === 'equipmentsuperadmin'), 
                    function ($query) {
                        // If the user is not a superadmin, only show superadmins
                        $query->whereIn('role', ['equipmentsuperadmin', 'facilitysuperadmin']);
                    }
                )
                // This will return all users for superadmins
                ->orWhere(function ($query) use ($user) {
                    if ($user->role === 'facilitysuperadmin' || $user->role === 'equipmentsuperadmin') {
                        // If the user is a superadmin, show all users
                        // Note: You may not need to add this clause, as it would show all users anyway
                        $query->whereIn('role', ['equipmentsuperadmin', 'facilitysuperadmin']);
                    }
                })
        )
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('role')
                    ->searchable(),
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
                SelectFilter::make('role')
                    ->options(User::ROLES),


            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make()
                        ->visible(fn() => auth()->user()->role === 'equipmentsuperadmin' || auth()->user()->role === 'facilitysuperadmin'),
            
                    Tables\Actions\DeleteAction::make()
                        ->visible(fn() => auth()->user()->role === 'equipmentsuperadmin' || auth()->user()->role === 'facilitysuperadmin'),
                ]),
            

            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            // 'create' => Pages\CreateUser::route('/create'),
            //'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
