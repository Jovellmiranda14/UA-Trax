<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TicketQueueResource\Pages;
use App\Models\TicketQueue;
use App\Models\Ticket;
use App\Models\TicketsAccepted;
use App\Models\User;
use App\Notifications\TicketGrabbedNotification;
use Filament\Notifications\Events\DatabaseNotificationsSent;
use Filament\Forms;
use Carbon\Carbon;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Support\Colors\Color;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Filters\SelectFilter;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\DatePicker;

class TicketQueueResource extends Resource
{
    protected static ?string $model = TicketQueue::class;
    // protected static ?string $navigationIcon = 'heroicon-s-queue-list';
    protected static ?string $navigationGroup = 'Tickets';
    protected static ?int $navigationSort = 1;

    public static function canCreate(): bool
    {
        return false; // Disable the creation of new tickets from this resource
    }

    public static function getPluralLabel(): string
    {
        return 'Ticket Queue';
    }

    public static function getLabel(): string
    {
        return 'Ticket Queue';
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            // Define form schema if needed
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->query(function () {
                $query = Ticket::query()->whereNull('assigned'); // Only show tickets not yet assigned

                if (auth()->user()->isEquipmentSuperAdmin() ||
                    auth()->user()->isEquipmentAdminOmiss() ||
                    auth()->user()->isEquipmentAdminlabcustodian()) {
                    $query->whereIn('concern_type', ['Laboratory and Equipment'])
                          ->orderBy('concern_type', 'asc');
                } elseif (auth()->user()->isFacilityAdmin() ||
                        auth()->user()->isFacilitySuperAdmin()) {
                    $query->where('concern_type', 'Facility')
                          ->orderBy('concern_type', 'asc');
                }
                return $query;
            })
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
                    ->label('Subject')
                    ->sortable()
                    ->searchable(),
                BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'primary' => 'Open',
                        'success' => 'Resolved',
                        'warning' => 'In progress',
                        'info' => 'Closed',
                    ])
                    ->searchable(),
                TextColumn::make('priority')
                    ->label('Priority')
                    ->colors([
                        'primary' => 'Low', 
                        'success' => 'Moderate',
                        'warning' => 'In progress',
                        'warning' => 'High',
                        'warning' => 'Urgent',
                        'info' => 'Closed',
                    ])
                    ->sortable()
                    ->searchable(),
                TextColumn::make('location')
                    ->label('Location')
                    ->sortable()
                    ->searchable(),
                    TextColumn::make('concern_type')
                    ->label('Concern Type')
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
            ->filters([
                SelectFilter::make('Tickets')
                    ->options([
                        'Accepted' => 'Accepted',
                        'Open' => 'Open Tickets',
                        'published' => 'Published',
                    ]),
            ])
            ->actions([
                Action::make('grab')
                    ->label('Claim')
                    ->icon('heroicon-o-rectangle-stack')
                    ->action(function ($record) {
                        try {
                            // Assign the ticket to the current user
                            $record->update(['assigned' => auth()->user()->name]);
            
                            // Move the ticket to the "Tickets Accepted" list with additional modifications
                            \App\Models\TicketsAccepted::create([
                                'id' => $record->id, // Assuming you want to keep the ticket ID
                                'concern_type' => $record->concern_type,
                                'name' => $record->name,
                                'subject' => $record->subject,
                                'priority' => $record->priority,
                                'department' => $record->department,
                                'location' => $record->location,
                                'dept' => $record->dept_role,
                                'status' => 'In progress', // Setting status to 'In progress'
                                'accepted_at' => now(),
                                'created_at' => $record->created_at,
                                'assigned' => auth()->user()->name, // Adding timestamp for when the ticket was accepted
                                // Add other fields you want to copy from the original record
                            ]);
            
                            // Delete the original ticket record
                            $record->delete();
                        } catch (\Exception $e) {
                            // Handle the exception if something goes wrong
                            // For example, log the error or notify the user
                            \Log::error('Error grabbing ticket: ' . $e->getMessage());
                        }
                    })
                    ->requiresConfirmation()
                    ->modalHeading('Confirm Grab Ticket')
                    ->modalSubheading('Are you sure you want to grab this ticket?')
                    ->color('success')
                    ->hidden(fn ($record) => $record->assigned !== null), // Hide the button if already grabbed

                ActionGroup::make([
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
                                TextInput::make('department')
                                    ->label('department')
                                    ->disabled()
                                    ->required(),
                                TextInput::make('location')
                                    ->label('Location')
                                    ->disabled()
                                    ->required(),
                                    TextInput::make('dept_role')
                                    ->label('Dept Assigned')
                                    ->disabled()
                                    ->required(),
                                DatePicker::make('created_at')
                                    ->label('Date Created')
                                    ->disabled()
                                    ->required(),
                            ]),
                        ]),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            // Define any relationships here if needed
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTicketQueues::route('/'),
            // 'create' => Pages\CreateTicketQueue::route('/create'),
            // 'edit' => Pages\EditTicketQueue::route('/{record}/edit'),
        ];
    }
}
