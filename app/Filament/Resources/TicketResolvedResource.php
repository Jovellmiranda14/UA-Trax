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
use Filament\Tables\Actions\ViewAction;
use Filament\Forms\Components\Card;
use Filament\Tables\Actions\ActionGroup;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
class TicketResolvedResource extends Resource
{
    protected static ?string $navigationLabel = 'Closed Tickets';

    protected static ?string $model = TicketResolved::class;

    // protected static ?string $navigationIcon = 'heroicon-s-ticket';
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
                    ->label('Concern')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->sortable()
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
                    ->colors([
                        'info' => 'Low',
                        'warning' => 'Moderate',
                        'danger' => 'Urgent',
                        'primary' => 'High', // Temporary color
                        'important' => 'Escalated',
                    ])
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
                                    ->label('Concern')
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
                                // TextInput::make('dept_role')
                                //     ->label('Dept')
                                //     ->disabled()
                                //     ->required(),
                                DatePicker::make('created_at')
                                    ->label('Date Created')
                                    ->disabled()
                                    ->required(),
                            ]),
                        ]),
                    Tables\Actions\Action::make('comment')
                        ->label('Comment')
                        ->icon('heroicon-o-rectangle-stack'),
                    // Tables\Actions\DeleteAction::make(),
                ])
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
            'index' => Pages\ListTicketResolveds::route('/'),
            // 'create' => Pages\CreateTicketResolved::route('/create'),
            // 'edit' => Pages\EditTicketResolved::route('/{record}/edit'),
        ];
    }
}
