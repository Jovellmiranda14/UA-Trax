<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TicketQueueResource\Pages;
use App\Models\TicketQueue;
use App\Models\Ticket;
use App\Models\TicketsAccepted;
use App\Models\User;
use App\Models\TicketHistory;
use App\Notifications\TicketGrabbedNotification;
use Filament\Notifications\Events\DatabaseNotificationsSent;
use Filament\Forms;
use Illuminate\Support\Facades\Storage;
use Filament\Notifications\Actions\Action as NotificationAction;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Support\Colors\Color;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Grid;
use Filament\Notifications\Notification;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
class UTicketColors
{
    const Gray = '#808080';
    const Black = '#000000';
}

class TicketQueueResource extends Resource
{
    protected static ?string $model = TicketQueue::class;
    // protected static ?string $navigationIcon = 'heroicon-s-queue-list';
    // protected static ?string $navigationGroup = 'Tickets';

    protected static ?string $navigationLabel = 'Ticket queue';
    protected static ?int $navigationSort = 1;

    public static function canCreate(): bool
    {
        return false; // Disable the creation of new tickets from this resource
    }

    public static function getPluralLabel(): string
    {
        return 'Ticket queue';
    }

    public static function getLabel(): string
    {
        return 'Ticket queue';
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
                $user = auth()->user();

                if ($user->isEquipmentSuperAdmin()) {
                    $query->whereIn('concern_type', ['Laboratory and Equipment'])
                        ->orderBy('concern_type', 'asc');

                } elseif (
                    $user->isEquipmentAdminOmiss() ||
                    $user->isEquipmentAdminlabcustodian()
                ) {
                    $query->whereIn('concern_type', ['Laboratory and Equipment'])
                        ->where('department', $user->dept_role)
                        ->orderBy('concern_type', 'asc');

                } elseif (
                    $user->isFacilityAdmin() ||
                    $user->isFacilitySuperAdmin()
                ) {
                    $query->where('concern_type', 'Facility')
                        ->orderBy('concern_type', 'asc');
                }

                return $query;
            })
            ->columns([
                TextColumn::make('id')
                    ->label('Ticket id')
                    ->searchable(),
                TextColumn::make('name')
                    ->label('Sender')
                    ->searchable(),
                TextColumn::make('subject')
                    ->label('Concern')
                    ->limit(25)
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

                    TextColumn::make('priority')
                    ->label('Priority')
                    ->sortable()
                    ->getStateUsing(function ($record) {
                        // Fetch the priority from the related Location model
                        return \App\Models\Location::where('location', $record->location)->value('priority');
                    })
                    ->color(function ($state, $record) {
                        switch ($state) {
                            case 'Urgent':
                                return Color::Red;
                            case 'High':
                                return Color::Orange;
                            case 'Moderate':
                                return Color::Yellow;
                            case 'Low':
                                return Color::Blue;
                            case 'Escalated':
                                return Color::Purple;
                            default:
                                return null; // Default if no matching priority
                        }
                    })
                    ->searchable(),
                   TextColumn::make('department')
                    ->label('Area')
                    ->searchable(),
               TextColumn::make('location')
                    ->label('Location')
                    ->searchable(),
                TextColumn::make('created_at')
                    ->label('Date created')
                    ->date()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('id', 'desc')
            ->filters([
            ])
            ->actions([
                Action::make('grab')
                    ->label('Claim')
                    ->icon('heroicon-o-ticket')
                    ->action(function ($record) {
                        try {
                            // Assign the ticket to the current user
                            $record->update(['assigned' => auth()->user()->name]);
                            $priority = $record->priority; // Default value
            
                            // Move the ticket to the "Tickets Accepted" list with additional modifications
                            TicketsAccepted::create([
                                'id' => $record->id, // Keep the ticket ID
                                'user_id' => $record->user_id, // Assuming $record contains the user object or the relationship
                                'concern_type' => $record->concern_type,
                                'type_of_issue' => $record->type_of_issue,
                                'description' => $record->description,
                                'name' => $record->name,
                                'subject' => $record->subject,
                                'priority' => $priority, // Set the priority determined above
                                'department' => $record->department,
                                'location' => $record->location,
                                'dept' => $record->dept_role,
                                'status' => 'In progress',
                                'accepted_at' => now(),
                                'attachment' => $record->attachment,
                                'created_at' => $record->created_at,
                                'assigned_id'=> Auth::id(),
                                'assigned' => auth()->user()->name,
                            ]);
                            TicketHistory::where('id', $record->id)->update([

                                'priority' => $record->priority,
                                'status' => 'In progress',
                                'assigned_id'=> Auth::id(),
                                'assigned' => auth()->user()->name,
                                'accepted_at' => now(),
                            ]);

                            $user = User::where('name', $record->name)->first();
                            if ($user) {
                                $user->notify(new TicketGrabbedNotification($record));
                                Notification::make()
                                    ->title('Admin accepted your ticket: (#' . $record->id . ')')
                                    ->body('Concern: "' . Str::words($record->subject, 10, '...') . '"')
                                    ->actions([
                                        NotificationAction::make('view')
                                        ->label('View Ticket')
                                    ])
                                    ->sendToDatabase($user);

                                event(new DatabaseNotificationsSent($user));
                            }
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
                        ->icon('heroicon-s-eye')
                        ->modalHeading('Ticket Details')
                        ->modalSubheading('Full details of the selected ticket.')
                        ->modalActions([
                            // Tables\Actions\Modal\Actions\ButtonAction::make('submit')
                            //     ->label('Submit') 
                            //     ->button()
                            //     ->close(),

                            Tables\Actions\Modal\Actions\ButtonAction::make('close')
                            ->label('Close')
                            ->button()
                            ->close(),
                        ])
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
                                        Grid::make(3)
                                            ->schema([
                                                // Ticket ID, Sender, Concern, etc.
                                                TextInput::make('id')
                                                    ->label('Ticket id')
                                                    ->disabled()
                                                    ->default($record->id)
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
                                            ]),

                                        Card::make('Issue Information')
                                            ->description('View the information about the ticket.')
                                            ->schema([
                                                Grid::make(3)
                                                    ->schema([
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
                                                        Select::make('type_of_issue')
                                                            ->label('Type of Issue')
                                                            ->options([
                                                                'computer_issues' => 'Computer issues (e.g., malfunctioning hardware, software crashes)',
                                                                'lab_equipment' => 'Lab equipment malfunction (e.g., broken microscopes, non-functioning lab equipment)',
                                                                'Other_Devices' => 'Other Devices (e.g., Printer, Projector, and TV)',
                                                                'repair' => 'Repair',
                                                                'air_conditioning' => 'Air Conditioning',
                                                                'plumbing' => 'Plumbing',
                                                                'lighting' => 'Lighting',
                                                                'electricity' => 'Electricity',
                                                            ])
                                                            ->default($record->type_of_issue)
                                                            ->required()
                                                            ->disabled(),
                                                    ]),
                                                Grid::make(2)
                                                    ->schema([
                                                        Textarea::make('description')
                                                            ->label('Description')
                                                            ->default($record->description)
                                                            ->autosize()
                                                            ->disabled()
                                                            ->required(),
                                                        FileUpload::make('attachment')
                                                            ->label('Attachment(optional)')
                                                            ->image()
                                                            ->default($record->attachment)
                                                            ->disabled(),
                                                    ]),
                                            ]),

                                        Card::make('Where did it occur ?')
                                            ->description('Enter the information about the place of issue.')
                                            ->schema([
                                                Grid::make(3)
                                                    ->schema([
                                                        TextInput::make('department')
                                                            ->label('Area')
                                                            ->disabled()
                                                            ->default($record->department)
                                                            ->required(),
                                                        TextInput::make('location')
                                                            ->label('Location')
                                                            ->disabled()
                                                            ->default($record->location)
                                                            ->required(),
                                                        DatePicker::make('created_at')
                                                            ->label('Date created')
                                                            ->disabled()
                                                            ->default($record->created_at->format('M d, Y'))
                                                            ->required(),
                                                    ]),
                                            ]),
                                    ])
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
