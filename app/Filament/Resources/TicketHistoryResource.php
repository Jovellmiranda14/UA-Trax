<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TicketHistoryResource\Pages;

use App\Models\TicketHistory;
use App\Models\TicketResolved;
use App\Models\TicketsAccepted;

use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\ButtonAction;

use Filament\Tables\Table;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Support\Colors\Color;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ViewAction;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Tables\Filters\SelectFilter;


use Filament\Forms\Components\Grid;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;


// Admin = By Dept
// User = Sarili 



class UTicket
{
    const Gray = '#808080';
    const Black = '#000000';
}

class TicketHistoryResource extends Resource
{
    protected static ?string $navigationLabel = 'Ticket history';
    protected static ?string $model = TicketHistory::class;

    protected static ?string $navigationGroup = 'Tickets';

    public static function getNavigationGroup(): ?string
    {
        // Check if the authenticated user has one of the specific roles
        if (
            auth()->check() && (
                auth()->user()->role === 'equipmentsuperadmin' ||
                auth()->user()->role === 'facilitysuperadmin' ||
                auth()->user()->role === 'equipment_admin_labcustodian' ||
                auth()->user()->role === 'equipment_admin_omiss' ||
                auth()->user()->role === 'facility_admin'
            )
        ) {
            return 'Tickets'; // Only visible to users with specific admin roles
        }

        return null; // No navigation group for other users
    }
    protected static ?int $navigationSort = 4;
    // protected static ?string $navigationIcon = 'heroicon-s-rectangle-stack';
    // protected static ?string $navigationGroup = 'Users Account';

    // Disable Function

    public static function canCreate(): bool
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
        $user = auth()->user(); // Retrieve the currently authenticated user

        // Build the base query for TicketHistory
        $query = TicketHistory::query();

        // Check for Equipment Super Admin role
        if ($user->isEquipmentSuperAdmin()) {
            $query->whereIn('concern_type', ['Laboratory and Equipment'])
                ->orderBy('concern_type', 'asc');
        }
        // Check for Equipment Admin roles
        else if ($user->isEquipmentAdminOmiss() || $user->isEquipmentAdminLabCustodian()) {
            // Filter by 'Laboratory and Equipment' concerns and by assigned department
            $query->whereIn('concern_type', ['Laboratory and Equipment'])
                ->where('department', $user->dept_role)
                ->orderBy('concern_type', 'asc');
        }
        // Check for Facility Admin roles
        else if ($user->isFacilityAdmin() || $user->isFacilitySuperAdmin()) {
            $query->where('concern_type', 'Facility')
                // ->where('assigned', $user->name)
                ->orderBy('concern_type', 'asc');
        }
        // For regular users, filter by the user's own tickets
        else {
            $query->where('name', $user->name);  // Regular users query
        }


        return $table
            ->query($query)
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
                            'On-hold' => UTicket::Black,
                            'Resolved' => Color::Green,
                            'Close' => UTicket::Gray,
                            default => null,
                        };
                    })
                    ->sortable()
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
                TextColumn::make('assigned')
                    ->label('Assigned')
                    ->sortable(),
                ImageColumn::make('attachment')
                    ->label('Image')
                    ->size(50)
                    ->circular()
                    ->getStateUsing(fn($record) => $record->attachment ? asset('storage/' . $record->attachment) : url('/images/XCircleOutline.png'))
                    ->url(fn($record) => $record->attachment ? asset('storage/' . $record->attachment) : null)
                    ->extraAttributes(function ($record) {
                        return $record->attachment ? ['class' => 'clickable-image'] : [];
                    })
                    ->openUrlInNewTab(),


                TextColumn::make('created_at')
                    ->label('Date created')
                    ->date()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('id', 'desc')
            ->actions([
                ActionGroup::make([
                    ViewAction::make('View')
                        ->modalHeading('Ticket details')
                        ->modalSubHeading('')
                        ->modalActions([
                            ButtonAction::make('close')
                                ->label('Close')
                                ->button()
                                ->close(),
                        ])
                        ->extraAttributes([
                            'class' => 'sticky-modal-header', // Add sticky header class
                        ])
                        ->form([
                            Card::make()

                                ->extraAttributes([
                                    'style' => 'max-height: 68vh; overflow-y: auto;',
                                ])

                                ->schema([

                                    // Top Row: Ticket ID, Status, Priority
                                    Grid::make(3)
                                        ->schema([
                                            TextInput::make('id')
                                                ->label('Ticket id')
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
                                        ]),

                                    // Section: Issue Information
                                    Card::make('Issue information')
                                        ->description('View the information about the ticket.')
                                        ->schema([
                                            Grid::make(3)
                                                ->schema([
                                                    TextInput::make('name')
                                                        ->label('Sender')
                                                        ->disabled()
                                                        ->required(),
                                                    TextInput::make('subject')
                                                        ->label('Concern')
                                                        ->disabled()
                                                        ->required(),
                                                    Select::make('type_of_issue')
                                                        ->label('Type of Issue')
                                                        ->options([
                                                            'computer_issues' => 'Computer issues (e.g., malfunctioning hardware, software crashes)',
                                                            'lab_equipment' => 'Lab equipment malfunction (e.g., broken microscopes, non-functioning lab equipment)',
                                                            'other_devices' => 'Other Devices (e.g., Printer, Projector, and TV)',
                                                            'repair' => 'Repair',
                                                            'air_conditioning' => 'Air Conditioning',
                                                            'plumbing' => 'Plumbing',
                                                            'lighting' => 'Lighting',
                                                            'electricity' => 'Electricity',
                                                        ])
                                                        ->required()
                                                        ->disabled(),
                                                ]),

                                            // Description and Attachment Fields
                                            Grid::make(2)
                                                ->schema([
                                                    Textarea::make('description')
                                                        ->label('Description')
                                                        ->disabled()
                                                        ->required()
                                                        ->autosize(),
                                                    FileUpload::make('attachment')
                                                        ->label('Attachment')
                                                        ->disabled()
                                                        ->default(fn($record) => $record->attachment)
                                                        ->required(),
                                                ]),
                                        ]),

                                    // Section: Place of Issue
                                    Card::make('Place of issue')
                                        ->description('Select where the equipment is currently located.')
                                        ->schema([
                                            Grid::make(3)
                                                ->schema([
                                                    TextInput::make('department')
                                                        ->label('Department')
                                                        ->disabled()
                                                        ->required(),
                                                    TextInput::make('location')
                                                        ->label('Location')
                                                        ->disabled()
                                                        ->required(),
                                                    DatePicker::make('created_at')
                                                        ->label('Date created')
                                                        ->disabled()
                                                        ->required(),
                                                ]),
                                        ]),
                                ]),
                        ]),

                        Action::make('comment-list')
                        ->label('Comment list')
                        ->icon('heroicon-o-chat-bubble-left-right')
                        ->modalHeading('')
                        ->modalSubheading('')
                        ->modalActions([
                            ButtonAction::make('done')
                                ->label('Done')
                                ->button()
                                ->close(),
                        ])

                        ->form(function (TicketHistory $record) {
                            $comments = $record->comments->map(function ($comment) {
                                return [
                                    'sender' => $comment->sender,
                                    'comment' => $comment->comment,
                                    'commented_at' => $comment->commented_at,
                                ];
                            })->toArray();  
                            return [
                                Grid::make(4)
                                    ->schema([
                                        TextInput::make('id')
                                            ->label('Ticket ID')
                                            ->default($record->id)
                                            ->disabled()
                                            ->required(),
                                        TextInput::make('subject')
                                            ->label('Concern')
                                            ->default($record->subject)
                                            ->disabled()
                                            ->required(),
                                    ]),
                                Repeater::make('comments')
                                    ->label('Comments')
                                    ->extraAttributes([
                                        'style' => 'max-height: 38vh; overflow-y: auto;',
                                    ])
                                    ->schema([
                                        Grid::make(3)
                                            ->schema([
                                                TextInput::make('sender')
                                                    ->label('Sender')
                                                    ->disabled(),
                                                TextInput::make('date_sent')
                                                    ->label('Date Sent')
                                                    ->default($record->created_at->format('M d, Y'))
                                                    ->disabled()
                                                    ->required(),
                                                    TextInput::make('time_created')
                                                    ->label('Time Created')
                                                    ->default($record->created_at->format('g:i A'))
                                                    ->disabled()
                                                    ->required(),
                                            ]),
                                        Textarea::make('comment')
                                            ->label('Description')
                                            ->autosize()
                                            ->disabled(),
                                    ])
                              


                                    ->default(function () use ($record) {
                                        return $record->comments->map(function ($comment) {
                                            return [
                                                'sender' => $comment->sender,
                                                'comment' => $comment->comment,
                                                'commented_at' => $comment->commented_at,
                                            ];
                                        })->toArray();
                                    })
                                    ->disabled(),

                                    Card::make()
                                    ->visible(empty($comments))
                                    ->extraAttributes(['class' => 'd-flex justify-content-center align-items-center', 'style' => 'height: 100px;'])
                                    ->schema([
                                        Placeholder::make('')
                                        ->content('No available comment')
                                        ->extraAttributes(['style' => 'text-align: center; color: #808080; font-weight: bold; font-size: 15px;']),
                                    ]), 
                            ];
                        }),
                ]),
            ])

            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'Accepted' => 'Accepted',
                        'Open' => 'Open Tickets',
                        'Closed' => 'Closed Tickets',
                        'In progress' => 'In Progress Tickets',
                    ])
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
