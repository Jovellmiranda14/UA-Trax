<?php
//Amdin
namespace App\Filament\Resources;

use App\Filament\Resources\AdminUserResource\Pages;
use App\Filament\Resources\AdminUserResource\RelationManagers;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Models\User;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\TextInput;


class AdminUserResource extends Resource
{
    protected static ?string $model = User::class;
    protected static ?string $label = 'Admin User';

    // protected static ?string $navigationGroupIcon = 'heroicon-o-user-group';
    protected static ?string $navigationGroup = 'Users Account';
    protected static ?int $navigationSort = 2;


    public static function form(Form $form): Form
    {
        return $form
        ->schema([
            Card::make('User info')
            ->description('Register a user in the system.')
            ->schema([
                Grid::make(3)
                ->schema([
            Forms\Components\TextInput::make('name')
                ->label('Full Name')
                ->extraAttributes(['style' => 'width: 250px;'])
                ->required()
                ->maxLength(255),
    
            Forms\Components\TextInput::make('email')
            ->label('Email')
            ->extraAttributes(['style' => 'width: 250px;'])
                ->email()
                ->unique()
                ->required()
                ->maxLength(255),
    
                Forms\Components\TextInput::make('password')
                ->label('Password')
                ->extraAttributes(['style' => 'width: 250px;'])
                ->revealable(true)
                ->required()
                ->password()
                ->maxLength(255)
                ->dehydrateStateUsing(fn ($state) => bcrypt($state)), // Ensure password is hashed
                    
            ]),

            // Lower part: Department, Role
            Grid::make(3) // Split into two columns for the bottom part
                ->schema([
                // This should be hidden for facility admin
                 // Visible only for Equipment admin
                 
                // Uncomment if needed
            // Forms\Components\Select::make('position')
            //     ->required()
            //     ->options(User::Pos), // Ensure User::Pos provides an associative array or similar structure
    
            Forms\Components\Select::make('role')
                ->label('Role')
                ->required()
                ->options([
                    'equipment_admin_omiss' => 'Equipment OMISS',
                    'equipment_admin_labcustodian' => 'Equipment LabCustodian',
                    'facility_admin' => 'Facility Admin',
                ]),
                Forms\Components\Select::make('dept_role')
                ->label('Department Role')
                ->required()
                ->options(User::Dept)
                ->reactive()  // Make the field reactive to trigger location updates
                ->visible(fn () => in_array(auth()->user()->role, ['equipment_admin_omiss', 'equipmentsuperadmin', 'equipment_admin_labcustodian']))
                ->hidden(fn () => auth()->user()->role === 'facility_admin'),
                
                // Select::make('location')
                //     ->label('Location')
                //     ->options(fn ($get) => collect([
                //         'SAS' => ['SAS Building', 'SAS Lab'],
                //         'CEA' => ['CEA Hall', 'CEA Workshop'],
                //         'CONP' => ['CONP Room 1', 'CONP Room 2'],
                //         'CITCLS' => ['CITCLS Area A', 'CITCLS Area B'],
                //     ][$get('dept_role')] ?? [])->mapWithKeys(fn($value) => [$value => $value]))
                //     ->required()
                //     ->multiple()
                //     ->reactive()
                //     ->visible(fn ($get) => !in_array($get('dept_role'), ['RSO', 'OFFICE'])),

                // TextInput::make('location') // Changed field name to avoid conflict
                //     ->label('Location')
                //     ->required()
                //     ->default('N/A')
                //     ->visible(fn ($get) => in_array($get('dept_role'), ['RSO', 'OFFICE'])),

            
                ])
                
                ]),
               


        ]);
    }

    public static function table(Table $table): Table
    {
        $user = auth()->user(); // Get the authenticated user

        $query = User::query();
    
        if (in_array($user->role, ['equipmentsuperadmin', 'facilitysuperadmin'])) {
            // If the user is either 'equipmentsuperadmin' or 'facilitysuperadmin', show all three roles
            $query->whereIn('role', [
                'facility_admin', 
                'equipment_admin_omiss', 
                'equipment_admin_labcustodian'
            ]);
        } elseif ($user->role === 'facility_admin') {
            // If the user is 'facility_admin', only show 'facility_admin'
            $query->where('role', 'facility_admin')
             ->where('dept_role', $user->dept_role);
        } elseif ($user->role === 'equipment_admin_omiss') {
            // Department Role for 'equipment_admin_omiss'
            $query->where('role', 'equipment_admin_omiss')
                  ->where('dept_role', $user->dept_role);
        } elseif ($user->role === 'equipment_admin_labcustodian') {
            // Department Role for 'equipment_admin_labcustodian'
            $query->where('role', 'equipment_admin_labcustodian')
                  ->where('dept_role', $user->dept_role);
        }
            return $table
            ->query($query) 
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Name')
                    ->searchable()
                    ->sortable(),
    
                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->sortable(),
    
                Tables\Columns\TextColumn::make('dept_role')
                    ->label('Department Role'),
    
                Tables\Columns\TextColumn::make('role')
                    ->label('Role')
                    ->searchable()
                    ->sortable(),
                    Tables\Columns\TextColumn::make('location')
                    ->label('location')
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                // Define filters if needed
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                // Tables\Actions\BulkActionGroup::make([
                //     Tables\Actions\DeleteBulkAction::make(),
                // ]),
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
            'index' => Pages\ListAdminUsers::route('/'),
            // 'create' => Pages\CreateAdminUser::route('/create'),
            // 'edit' => Pages\EditAdminUser::route('/{record}/edit'),
        ];
    }
}
