<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TicketResolvedResource\Pages;
use App\Filament\Resources\TicketResolvedResource\RelationManagers;
use App\Models\TicketResolved;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TicketResolvedResource extends Resource
{
    protected static ?string $navigationLabel = 'Closed Tickets';

    protected static ?string $model = TicketResolved::class;

    //protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Tickets';
    protected static ?int $navigationSort = 3;
    public static function canCreate(): bool
    {
        return false;
    }
    public static function getPluralLabel(): string
    {
        return 'Closed Tickets';
    }
    public static function getLabel(): string
    {
        return 'Closed Tickets';
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
        
            return $table
             // Pagination 
        // ->paginated([10, 25, 50, 100, 'all']) 
            // ->query(function (Builder $query) {
            //     // Filter to show only tickets that are accepted
            //     $query->whereNotNull('assigned');
            // })
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('Ticket ID')
                    ->sortable()
                    ->searchable(),

                    Tables\Columns\TextColumn::make('name')
                    ->label('Sender')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('subject')
                    ->label('Subject')
                    ->sortable()
                    ->searchable(),
                    
                    Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->sortable()
                    ->searchable(),
                    Tables\Columns\TextColumn::make('priority')
                    ->label('Priority')
                    ->sortable()
                    ->searchable(),
                    
               
               
                Tables\Columns\TextColumn::make('location')
                    ->label('Location')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('department')
                    ->label('Department')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('assigned_to')
                    ->label('Assigned To')
                    ->sortable()
                    ->searchable(),
    
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created On')
                    ->date()
                    ->sortable()
                    ->searchable(),
    
                    // Unfinished
                Tables\Columns\TextColumn::make('accepted_at')
                    ->label('Accepted On')
                    ->date()
                    ->sortable()
                    ->searchable(),
            ])
            
                ->filters([
                    //
                ])
                ->actions([
                    Tables\Actions\Action::make('grab')
                    ->label('')
                    ->icon('heroicon-o-rectangle-stack')
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
            'index' => Pages\ListTicketResolveds::route('/'),
            // 'create' => Pages\CreateTicketResolved::route('/create'),
            // 'edit' => Pages\EditTicketResolved::route('/{record}/edit'),
        ];
    }
}
