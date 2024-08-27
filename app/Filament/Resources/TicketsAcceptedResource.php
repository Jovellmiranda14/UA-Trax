<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TicketsAcceptedResource\Pages;
use App\Filament\Resources\TicketsAcceptedResource\RelationManagers;
use App\Models\TicketsAccepted;
use App\Models\Ticket;

use Filament\Forms;

use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TicketsAcceptedResource extends Resource
{
    protected static ?string $navigationLabel = 'Tickets Accepted';
    
    protected static ?string $model = TicketsAccepted::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    
    public static function canCreate(): bool
    {
       return false;
    }
    public static function getPluralLabel(): string
    {
        return 'Accepted Tickets';
    }
    public static function getLabel(): string
    {
        return 'Accepted Tickets';
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
        //  ->query(Ticket::query()->where('role', $user->role))
        ->columns([
            Tables\Columns\TextColumn::make('id')
                ->label('Ticket ID')
                ->sortable()
                ->searchable(),
            Tables\Columns\TextColumn::make('subject')
                ->label('Subject')
                ->sortable()
                ->searchable(),
                Tables\Columns\TextColumn::make('priority')
                ->label('Priority')
                ->sortable()
                ->searchable(),
            Tables\Columns\TextColumn::make('status')
                ->label('Status')
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
            Tables\Columns\TextColumn::make('name')
                ->label('Grabbed by')
                ->sortable()
                ->searchable(),

                Tables\Columns\TextColumn::make('created_at')
                ->label('Date created')
                ->date()
                ->sortable()
                ->searchable(),

                // Unfinished
                Tables\Columns\TextColumn::make('accepted_at')
                ->label('Date Accepted')
                ->date()
                ->sortable()
                ->searchable(),
        ])
        
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('grab')
                ->label('')
                ->icon('heroicon-o-rectangle-stack'),
                Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ListTicketsAccepteds::route('/'),
            // 'create' => Pages\CreateTicketsAccepted::route('/create'),
            // 'edit' => Pages\EditTicketsAccepted::route('/{record}/edit'),
        ];
    }
}
