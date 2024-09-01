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
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\ViewAction;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;

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
                // Add form fields here if needed in the future
            ]);
    }

    public static function table(Table $table): Table
    {
        // $user = auth()->user();
       
        return $table
            // ->query(Ticket::query()->where('role', $user->role))
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
                Tables\Columns\TextColumn::make('accepted_at')
                    ->label('Date Accepted')
                    ->date()
                    ->sortable()
                    ->searchable(),
            ])
            ->filters([
                // Add filters here if needed
            ])
            ->actions([  
                Tables\Actions\ActionGroup::make([
                        ViewAction::make('View')
                            ->modalHeading('Ticket Details')
                            ->modalSubheading('Full details of the selected ticket.')
                            ->form([
                                Card::make([
                                    TextInput::make('id')
                                        ->label('Ticket ID')
                                        ->disabled()
                                        ->required(),
                                    TextInput::make('name')
                                        ->label('Sender')
                                        ->disabled()
                                        ->required(),
                                    TextInput::make('subject')
                                        ->label('Subject')
                                        ->disabled()
                                        ->required(),
                                    TextInput::make('status')
                                        ->label('Status')
                                        ->disabled()
                                        ->required(),
                                    TextInput::make('priority')
                                        ->label('Priority')
                                        ->disabled()
                                        ->required(),
                                    TextInput::make('location')
                                        ->label('Location')
                                        ->disabled()
                                        ->required(),
                                    TextInput::make('department')
                                        ->label('Department')
                                        ->disabled()
                                        ->required(),
                                    TextInput::make('dept_role')
                                        ->label('Dept')
                                        ->disabled()
                                        ->required(),
                                    DatePicker::make('created_at')
                                        ->label('Date Created')
                                        ->disabled()
                                        ->required(),
                                ]),
                            ]),
                        Tables\Actions\Action::make('comment')
                            ->label('Comment')
                            ->icon('heroicon-o-rectangle-stack'),
                        Tables\Actions\DeleteAction::make(),
                    ])
                        ]);
            
            // ->bulkActions([
            //     // Tables\Actions\BulkActionGroup::make([
            //     //     Tables\Actions\DeleteBulkAction::make(),
            //     ])
            // ]);
    }

    public static function getRelations(): array
    {
        return [
            // Define relations here if needed
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
