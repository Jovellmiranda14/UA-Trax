<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TicketResource\Pages;
use App\Models\Ticket;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

use Filament\Tables\Actions\Action; 
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

use Filament\Tables\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\FileUpload;
use Filament\Support\Colors\Color;

use Filament\Forms\Components\Button;
use App\Models\User;

class TicketResource extends Resource
{
    protected static ?string $model = Ticket::class;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    
      public static function form(Form $form): Form
{
    return $form
        ->schema([ 
            TextInput::make('property_no')
                ->label('Property No.')
                ->required(),

            Select::make('department')
                ->label('Department')
                ->options([
                    'IT' => 'IT',
                    'HR' => 'HR',
                    'Maintenance' => 'Maintenance',
                    // Add more departments here
                ])
                ->required(),

            TextInput::make('subject')
                ->label('Subject')
                ->required(),


                TextInput::make('email')
                ->label('Customer')
                ->default(auth()->user()->email)
                 ->required(),
                
                TextArea::make('description')
                ->label('Description')
                ->required(),

                TextInput::make('location')
                ->label('Location')
                ->required(),



                TextInput::make('email')
                ->label('Customer')
                ->default(auth()->user()->email)
                 ->required(),
                
                TextArea::make('description')
                ->label('Description')
                ->required(),

                TextInput::make('location')
                ->label('Location')
                ->required(),


            FileUpload::make('attachment')
                ->label('Upload file'),
        ]);
}
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->label('Ticket ID')
                ->sortable()
                ->searchable(),
                Tables\Columns\TextColumn::make('subject')->label('Subject')
                ->searchable(),   
                Tables\Columns\TextColumn::make('email')->label('Administrator')
                ->searchable(),
                Tables\Columns\TextColumn::make('department')->label('Department')
                ->searchable(),
                Tables\Columns\TextColumn::make('attachment')->label('Attachment')
                ->searchable()
                ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'primary' => 'Open', Color::Blue,
                        'success' => 'Resolved',
                        'warning' => 'In progress',
                        'info' => 'Closed',
                    ])
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')->label('Date created')
                ->date()
                ->searchable()
                ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                // Define any filters you need
            ])
            ->actions([
                //Tables\Actions\EditAction::make(),
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
            // Define any relations here
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTickets::route('/'),
             //'create' => Pages\CreateTicket::route('/create'),
            // 'edit' => Pages\EditTicket::route('/{record}/edit'),
        ];
    }
}
