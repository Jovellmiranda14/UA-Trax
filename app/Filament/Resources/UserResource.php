<?php
//Super Admin
namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;
class UserResource extends Resource
{
    protected static ?string $navigationLabel = 'Super Admin';
    protected static ?string $model = User::class;
    protected static ?string $navigationIcon = 'heroicon-o-user';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('password')
                    ->password()
                    ->maxLength(255)
                    ->dehydrateStateUsing(fn ($state) => bcrypt($state)),// Ensure password is hashed

                Forms\Components\Select::make('dept_role')
                ->label('Department Assigned')
                    ->required()
                    ->options(User::Dept), 
                Forms\Components\Select::make('position')
                ->required()
                ->options(User::Pos),
                Forms\Components\Select::make('role')
                ->required()
                ->options([
                    'equipmentsuperadmin' => 'Equipment Super Admin',
                    'facilitysuperadmin' => 'Facility Super Admin',
                ]),
                //Equipment Admin Omiss - > Offcie (secretary) and RSO 
                //Equipment Admin LabCustodian -> Faculty (All Depts)
                // Display Position IF MERON SYA
                // Default value for role
            ]);
    }

    public static function table(Table $table): Table
    {   
        $user = auth()->user();
       
       return $table
        // Pagination 
        // ->paginated([10, 25, 50, 100, 'all']) 
       ->query(User::query()
       ->when($user->role === 'equipmentsuperadmin' || $user->role === 'facilitysuperadmin', function ($query) {
           $query->whereIn('role', ['equipmentsuperadmin', 'facilitysuperadmin']);
       })
   )
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                    // Tables\Columns\TextColumn::make('dept_role')
                    // ->sortable()
                    // ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                    Tables\Columns\TextColumn::make('role')
                    ->searchable(),
                    // Tables\Columns\TextColumn::make('position')
                    // ->sortable()
                    // ->toggleable(isToggledHiddenByDefault: true),
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
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]),

            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
