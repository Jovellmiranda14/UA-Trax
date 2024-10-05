<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TicketHistoryResource\Pages;
use App\Filament\Resources\TicketHistoryResource\RelationManagers;
use App\Models\TicketHistory;
use App\Models\Ticket;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Support\Colors\Color;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Filters\SelectFilter;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\DatePicker;
// Admin = By Dept
// User = Sarili 

      
class TicketHistoryResource extends Resource
{
    protected static ?string $navigationLabel = 'Ticket history';
    protected static ?string $model = TicketHistory::class;
    
    protected static ?int $navigationSort = 3;
    // protected static ?string $navigationIcon = 'heroicon-s-rectangle-stack';
    // protected static ?string $navigationGroup = 'Users Account';
    
    // Disable Function

    public static function canCreate(): Bool
    {
        return false;
    }

    public static function form(Form $form): Form
    {

        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        // $user = auth()->user();
        return $table
        // ->query(Ticket::query()->where('name', $user->name))
         // Pagination 
        // ->paginated([10, 25, 50, 100, 'all']) 
        ->columns([
            TextColumn::make('id')
                ->label('Ticket ID')
                ->sortable()
                ->searchable(),
            TextColumn::make('name')
                ->label('Sender')
                ->sortable()
                ->searchable(),
            TextColumn::make('subject')
                ->label('Concern')
                ->sortable()
                ->searchable(),
            BadgeColumn::make('status')
                ->label('Status')
                ->colors([
                    'success' => 'Resolved',   
                    'primary' => 'Open',      
                    'warning' => 'In progress', 
                    'black' => 'On-hold', 
                    'grey' => 'Close',        
                ])
                ->searchable(),
                Tables\Columns\TextColumn::make('priority')
                ->label('Priority')
                ->sortable()
                ->searchable()
                ->colors([
                    'info' => 'Low',
                    'warning' => 'Moderate',
                    'danger'  => 'Urgent',
                    'danger'  => 'High',
                    'important' => 'Escalated',
                ]),
            TextColumn::make('location')
                ->label('Location')
                ->sortable()
                ->searchable(),
            TextColumn::make('department')
                ->label('Dept')
                ->sortable()
                ->searchable(),
            TextColumn::make('created_at')
                ->label('Date Created')
                ->date()
                ->sortable(),
        ])
                //
            
            ->filters([
                SelectFilter::make('status')
                ->options([
                        'Accepted' => 'Accepted',
                        'Open' => 'Open Tickets',
                        'Closed' => 'Closed Tickets',
                        'In progress' => 'In Progress Tickets',             
                    ])  
                ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
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
            'index' => Pages\ListTicketHistories::route('/'),
            // 'create' => Pages\CreateTicketHistory::route('/create'),
            // 'edit' => Pages\EditTicketHistory::route('/{record}/edit'),
        ];
    }
}
