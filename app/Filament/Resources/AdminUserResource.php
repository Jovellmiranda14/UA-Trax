<?php
namespace App\Filament\Resources;

use App\Filament\Resources\AdminUserResource\Pages;
use App\Models\Department;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use App\Models\User;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Card;
class AdminUserResource extends Resource
{
    protected static ?string $model = User::class;
    protected static ?string $navigationLabel = 'Admins';
    protected static ?string $navigationGroup = 'Manage';
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
                                    ->label('Full name')
                                    ->extraAttributes(['style' => 'width: 250px;'])
                                    ->required()
                                    ->maxLength(255),

                                Forms\Components\TextInput::make('email')
                                    ->label('Email')
                                    ->extraAttributes(['style' => 'width: 250px;'])
                                    ->email()
                                    ->placeholder('username@ua.edu.ph')
                                    ->rule('regex:/^[a-zA-Z0-9._%+-]+@ua\.edu\.ph$/')
                                    ->required()
                                    ->maxLength(255),

                                Forms\Components\TextInput::make('password')
                                    ->label('Password')
                                    ->extraAttributes(['style' => 'width: 250px;'])
                                    ->revealable(true)
                                    ->required()
                                    ->password()
                                    ->minLength(8)
                                    ->maxLength(255)
                                    ->dehydrateStateUsing(fn($state) => bcrypt($state)), // Ensure password is hashed

                            ]),

                        // Lower part: Department, Role
                        Grid::make(2) 
                            ->schema([
                                Forms\Components\Select::make('role')
                                    ->label('Role')
                                    ->required()
                                    ->reactive()
                                    ->options([
                                        'equipment_admin_omiss' => 'Equipment OMISS',
                                        'equipment_admin_labcustodian' => 'Equipment LabCustodian',
                                        'facility_admin' => 'Facility Admin',
                                    ])
                                    ->afterStateUpdated(function (callable $set, $state) {
                                        // Conditionally set the department based on the selected role
                                        if ($state === 'equipment_admin_omiss') {
                                            $set('dept_role', 'OFFICE'); // Automatically select 'Office'
                                        } else {
                                            $set('dept_role', ''); // Clear selection for other roles
                                        }
                                    }),
                                  
                                Forms\Components\Select::make('dept_role')
                                    ->label('Department role')
                                    ->required()
                                    ->options(Department::Dept)
                                    ->reactive()
                                    ->visible(
                                        fn() => in_array(
                                            auth()->user()->role,
                                            [
                                                'equipment_admin_omiss',
                                                'equipmentsuperadmin',
                                                'equipment_admin_labcustodian',
                                                'facility_admin',
                                                'facilitysuperadmin'
                                            ]
                                        )
                                    ),

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
                    ->label('Department role'),

                Tables\Columns\TextColumn::make('role')
                    ->label('Role')
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                // Define filters if needed
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
            'index' => Pages\ListAdminUsers::route('/'),
            // 'create' => Pages\CreateAdminUser::route('/create'),
            // 'edit' => Pages\EditAdminUser::route('/{record}/edit'),
        ];
    }
}
