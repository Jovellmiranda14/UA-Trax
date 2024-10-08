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
use App\Filament\Resources\UserResource\RelationManagers;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

use Filament\Support\Facades\FilamentColor;

class UTicketColors
{
    const Gray = '#808080';
    const Black = '#000000';
}

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
    
                if (
                    auth()->user()->isEquipmentSuperAdmin() ||
                    auth()->user()->isEquipmentAdminOmiss() ||
                    auth()->user()->isEquipmentAdminlabcustodian()
                ) {
                    $query->whereIn('concern_type', ['Laboratory and Equipment'])
                        ->orderBy('concern_type', 'asc');
                } elseif (
                    auth()->user()->isFacilityAdmin() ||
                    auth()->user()->isFacilitySuperAdmin()
                ) {
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
                    ->label('Concern')
                    ->sortable()
                    ->searchable(),
                BadgeColumn::make('status')
                    ->label('Status')
                    ->getStateUsing(function ($record) {
                        switch ($record->status) {
                            case 'open':
                                return 'Open';
                            case 'in progress':
                                return 'In progress';
                            case 'on-hold':
                                return 'On-hold';
                            case 'resolved':
                                return 'Resolved';
                            case 'close':
                                return 'Close';
                            default:
                                return $record->status;
                        }
                    })
                    ->color(function ($state) {
                        return match ($state) {
                            'Open' => Color::Blue,
                            'In progress' => Color::Yellow,
                            'On-hold' => UTicketColors::Black,
                            'Resolved' => Color::Green,
                            'Close' => UTicketColors::Gray,
                            default => null,
                        };
                    })
                    ->searchable(),
                BadgeColumn::make('priority')
                    ->label('Priority')
                    ->getStateUsing(function ($record) {
                        switch ($record->priority) {
                            case 'urgent':
                                return 'Urgent';
                            case 'high':
                                return 'High';
                            case 'moderate':
                                return 'Moderate';
                            case 'low':
                                return 'Low';
                            case 'escalated':
                                return 'Escalated';
                            default:
                                return $record->priority;
                        }
                    })
                    ->color(function ($state) {
                        return match ($state) {
                            'Urgent' => Color::Red,
                            'High' => Color::Orange,
                            'Moderate' => Color::Yellow,
                            'Low' => Color::Blue,
                            'Escalated' => Color::Purple,
                            default => null,
                        };
                    })
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
                    ->label('Department')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('created_at')
                    ->label('Date Created')
                    ->date()
                    ->sortable(),
            ])
            ->filters([
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
                            TicketsAccepted::create([
                                'id' => $record->id, // Keep the ticket ID
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
                                'assigned' => auth()->user()->name, // Timestamp when the ticket was accepted
                                // Add other fields you want to copy from the original record
                            ]);

                            // Delete the original ticket record
                            $record->delete();
                        } catch (\Exception $e) {
                            // Handle the exception if something goes wrong
                            \Log::error('Error grabbing ticket: ' . $e->getMessage());
                        }
                    })
                    ->requiresConfirmation()
                    ->modalHeading('Confirm Grab Ticket')
                    ->modalSubheading('Are you sure you want to grab this ticket?')
                    ->color('success')
                    ->hidden(fn($record) => $record->assigned !== null), // Hide the button if already grabbed

                ActionGroup::make([
                    Action::make('View')
                        ->icon('heroicon-o-rectangle-stack')
                        ->color('success')
                        ->modalHeading('Ticket Details')
                        ->modalSubheading('Full details of the selected ticket.')
                        ->extraAttributes([
                            'class' => 'sticky-modal-header', // Add sticky header class
                        ])
                        ->form(function ($record) {
                            return [
                                Card::make()
                                    ->extraAttributes([
                                        'style' => 'max-height: 60vh; overflow-y: auto;', // Make the content scrollable
                                    ])
                                    ->schema([

                                        // Ticket ID, Sender, Concern, etc.
                                        TextInput::make('id')
                                            ->label('Ticket ID')
                                            ->disabled()
                                            ->default($record->id)
                                            ->required(),
                                        TextInput::make('name')
                                            ->label('Sender')
                                            ->disabled()
                                            ->default($record->name)
                                            ->required(),
                                        TextInput::make('subject')
                                            ->label('Concern')
                                            ->disabled()
                                            ->default($record->subject)
                                            ->required(),
                                        TextInput::make('status')
                                            ->label('Status')
                                            ->disabled()
                                            ->default($record->status)
                                            ->required(),
                                        TextInput::make('priority')
                                            ->label('Priority')
                                            ->disabled()
                                            ->default($record->priority)
                                            ->required(),
                                        TextInput::make('department')
                                            ->label('Department')
                                            ->disabled()
                                            ->default($record->department)
                                            ->required(),
                                        TextInput::make('location')
                                            ->label('Location')
                                            ->disabled()
                                            ->default($record->location)
                                            ->required(),
                                        TextInput::make('dept_role')
                                            ->label('Dept Assigned')
                                            ->disabled()
                                            ->default($record->dept_role)
                                            ->required(),
                                        DatePicker::make('created_at')
                                            ->label('Date Created')
                                            ->disabled()
                                            ->default($record->created_at)
                                            ->required(),
                                    ]),
                            ];
                        }),
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
