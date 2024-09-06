<?php
//Amdin
namespace App\Filament\Resources;

use App\Filament\Resources\AdminUserResource\Pages;
use App\Filament\Resources\AdminUserResource\RelationManagers;
use App\Models\AdminUser;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Models\User;
class AdminUserResource extends Resource
{
    protected static ?string $model = AdminUser::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?int $navigationSort = 2;
    // public static function canCreate(): Bool
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
                ->dehydrateStateUsing(fn ($state) => bcrypt($state)), // Ensure password is hashed
    
                // This should be hidden for facility admin
                 // Visible only for Equipment admin
                 Forms\Components\Select::make('dept_role')
                 ->label('Department Role')
                 ->required()
                 ->options(User::Dept)
                 ->visible(fn () => in_array(auth()->user()->role, [ 'equipment_admin_omiss', 'equipment_admin_labcustodian']))
                 ->hidden(fn () => auth()->user()->role=== 'facility_user'),
                
                
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
                    'facility_user' => 'Facility Admin',
                ]),
        ]);
    }
    public static function table(Table $table): Table
    {
           $user = auth()->user();
        return $table
         // Pagination 
        // ->paginated([10, 25, 50, 100, 'all']) 
              // ->query(Ticket::query()->where('role', $user->role))
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
                    ->label('Department Role')
         
                     ->visible(fn () => auth()->user()->role === 'equipment_user'),

                    Tables\Columns\TextColumn::make('role')
                    ->label('Role')
                    ->searchable()
                    ->sortable(),
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
            'index' => Pages\ListAdminUsers::route('/'),
            // 'create' => Pages\CreateAdminUser::route('/create'),
            // 'edit' => Pages\EditAdminUser::route('/{record}/edit'),
        ];
    }
}
