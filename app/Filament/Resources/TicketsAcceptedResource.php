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

use Filament\Tables\Filters\MultiSelectFilter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;




class TicketsAcceptedResource extends Resource
{
    protected static ?string $navigationLabel = 'Tickets Accepted';
    protected static ?string $label = 'Open tickets';
    protected static ?string $model = TicketsAccepted::class;

    // protected static ?string $navigationIcon = 'heroicon-o-ticket';

    protected static ?string $navigationGroup = 'Tickets';
    protected static ?int $navigationSort = 2;



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
         // Pagination 
        // ->paginated([10, 25, 50, 100, 'all']) 
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
                    ->colors([
                        'success' => 'Low',        // Green
                        'warning' => 'Moderate',   // Yellow
                        'danger'  => 'Urgent',     // Orange
                        'danger'  => 'High',       // Red (same as Urgent in this case)
                    ]),
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Status')
                    ->sortable()
                    ->searchable()
                    // ->colors([
                        // 'info'     => 'Open',        // Light Blue for Open
                        // 'danger'   => 'In progress', // Orange for In progress
                        // 'success'  => 'Resolved',    // Green for Resolved
                        // 'muted'    => 'Closed',      // Dark Gray for Closed
                    // ]),
                    ->colors([
                        'primary'  => 'Open',        // Blue for Open
                        'warning'  => 'In progress', // Yellow for In progress
                        'success'  => 'Resolved',    // Green for Resolved
                        'secondary'=> 'Closed',      // Gray for Closed
                    ]), 
                Tables\Columns\TextColumn::make('location')
                    ->label('Location')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('department')
                    ->label('Department')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('assigned')
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
                // Tickets filter
                SelectFilter::make('status')
                    ->label('Tickets')
                    ->options([
                        'Open' => 'Open tickets',
                        'Accepted' => 'Accepted',
                    ]),
                
                // Type of issue filter
                SelectFilter::make('concern_type')
                    ->label('Type of issue')
                    ->options([
                        'Facility' => 'Facility',
                        'Laboratory and Equipment' => 'Laboratory and Equipment',
                    ]),
            
                // Department filter with checkboxes
                MultiSelectFilter::make('department')
                    ->label('Department')
                    ->options([
                        'CITCLS' => 'CITCLS',
                        'CEA' => 'CEA',
                        'SAS' => 'SAS',
                        'CONP' => 'CONP',
                        'Other offices' => 'Other offices',
                    ]),
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
                        // Tables\Actions\DeleteAction::make(),
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
