<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TicketsAcceptedResource\Pages;
use App\Models\TicketsAccepted;
use App\Models\TicketResolved;
use App\Models\TicketHistory;
use App\Models\User;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Notifications\Actions\Action as NotificationAction;
use Filament\Tables;
use Filament\Tables\Table;
use App\Notifications\TicketResolvedNotification;
use Illuminate\Support\Str;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ViewAction;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Tables\Filters\MultiSelectFilter;
use Filament\Tables\Filters\SelectFilter;
use App\Models\TicketComment;
use App\Models\ResolvedComment;
use Filament\Support\Colors\Color;
use App\Notifications\NewCommentNotification;
use Filament\Forms\Components\Grid;
use Illuminate\Support\Facades\Log;
use Filament\Notifications\Events\DatabaseNotificationsSent;
use Filament\Notifications\Notification;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;

class IssuePalette
{
    const Gray = '#808080';
    const Black = '#000000';
}


class TicketsAcceptedResource extends Resource
{
    protected static ?string $navigationLabel = 'Accepted tickets';
    // protected static ?string $label = 'Open tickets';
    protected static ?string $model = TicketsAccepted::class;

    // protected static ?string $navigationIcon = 'heroicon-o-ticket';

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
        $user = auth()->user();
        // Regular users see only their assigned tickets
        if ($user->role === 'user') {

            $query = TicketsAccepted::query()->where('user_id', auth()->id());
        } else {
            // Admin users can see all tickets
            $query = TicketsAccepted::query()->where('assigned_id', auth()->id());
        }

        return $table
            ->query($query)
            // Pagination
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('Ticket ID')
                    ->searchable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Sender')
                    ->searchable(),
                Tables\Columns\TextColumn::make('subject')
                    ->label('Concern')
                    ->limit(25)
                    ->searchable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Status')
                    ->searchable()
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
                            'On-hold' => IssuePalette::Black,
                            'Resolved' => Color::Green,
                            'Close' => IssuePalette::Gray,
                            default => null,
                        };
                    }),
                Tables\Columns\TextColumn::make('priority')
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
                Tables\Columns\TextColumn::make('department')
                    ->label('Area')
                    ->searchable(),
                Tables\Columns\TextColumn::make('location')
                    ->label('Location')
                    ->searchable(),
                Tables\Columns\TextColumn::make('assigned')
                    ->label('Assigned')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created On')
                    ->date()
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('accepted_at')
                    ->label('Accepted On')
                    ->date()
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('id', 'desc')
            ->filters([
                // Tickets filter
                SelectFilter::make('status')
                    ->label('Tickets')
                    ->options([
                        'Open' => 'Open Tickets',
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
                        ->modalSubHeading('')
                        ->extraAttributes([
                            'class' => 'sticky-modal-header',
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
                                                ->label('Ticket ID')
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
                                    Card::make('Issue Information')
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
                                                        ->autosize()
                                                        ->disabled()
                                                        ->required(),
                                                    FileUpload::make('attachment')
                                                        ->label('Attachment (optional)')
                                                        ->image()
                                                        ->default(fn($record) => $record->attachment)
                                                        ->disabled(),
                                                ]),
                                        ]),


                                    // Section: Place of Issue
                                    Card::make('Where did it occur ?')
                                        ->description('Enter the information about the place of issue.')
                                        ->schema([
                                            Grid::make(3)
                                                ->schema([
                                                    TextInput::make('department')
                                                        ->label('Area')
                                                        ->disabled()
                                                        ->required(),
                                                    TextInput::make('location')
                                                        ->label('Location')
                                                        ->disabled()
                                                        ->required(),
                                                    DatePicker::make('created_at')
                                                        ->label('Date Created')

                                                        ->default(fn($record) => $record->created_at->format('M d, Y'))
                                                        ->disabled()
                                                        ->required(),
                                                ]),
                                        ]),
                                ]),
                        ]),

                    Tables\Actions\Action::make('comment-list')
                        ->label('Comment list')
                        ->icon('heroicon-o-chat-bubble-left-right')
                        ->modalHeading('')
                        ->modalSubheading('')
                        ->modalActions([
                            Tables\Actions\Modal\Actions\ButtonAction::make('done')
                                ->label('Done')
                                ->button()
                                ->close(),
                        ])

                        ->form(function (TicketsAccepted $record) {
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
                                        // TextInput::make('date_created')
                                        //     ->label('Date Created')
                                        //     ->default($record->created_at->format('M d, Y')) 
                                        //     ->disabled()
                                        //     ->required(),
                                        // TextInput::make('time_created')
                                        //     ->label('Time Created')
                                        //     ->default($record->created_at->format('g:i A')) 
                                        //     ->disabled()
                                        //     ->required(),
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
                                                TextInput::make('commented_at')
                                                    ->label('Time Sent')
                                                    ->default(now()->timezone('Asia/Manila')->format('g:i A'))
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

                    Tables\Actions\Action::make('send_comment')
                        ->icon('heroicon-o-chat-bubble-left-ellipsis')
                        ->label('Send Comment')
                        ->form(function (TicketsAccepted $record) {
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
                                        TextInput::make('date_created')
                                            ->label('Date Created')
                                            ->default($record->created_at->format('M d, Y'))
                                            ->disabled()
                                            ->required(),
                                        TextInput::make('time_created')
                                            ->label('Time Created')
                                            ->default($record->created_at->format('g:i A'))
                                            ->disabled()
                                            ->required(),
                                    ]),
                                Card::make('')
                                    ->schema([
                                        Textarea::make('new_comment')
                                            ->label('Comment')
                                            ->autosize()
                                            ->placeholder('Write something to the administrator...')
                                            ->required(),
                                    ])
                            ];
                        })
                        ->action(function (array $data, TicketsAccepted $record) {
                            $authUser = auth()->user();

                            // Save the comment
                            $comment = new TicketComment();
                            $comment->ticket_id = $record->id;
                            $comment->sender = $authUser->name;
                            $comment->commented_at = now();
                            $comment->comment = $data['new_comment'];
                            $comment->save();

                            // Get involved users
                            $assignedAdmin = User::where('name', $record->assigned)->first();
                            $regularUser = User::where('name', $record->name)->first();

                            // Notify the *other* person
                            if ($authUser->name === $record->assigned && $regularUser) {
                                // Admin sent comment → notify user
                                $regularUser->notify(new NewCommentNotification($comment));
                                Notification::make()
                                    ->title('Admin commented on your ticket (#' . $record->id . ')')
                                    ->body('Comment: "' . Str::limit($comment->comment, 10) . '"')
                                    ->actions([
                                        NotificationAction::make('view')
                                            ->label('View Ticket')
                                    ])
                                    ->sendToDatabase($regularUser);
                                event(new DatabaseNotificationsSent($regularUser));

                            } elseif ($authUser->name === $record->name && $assignedAdmin) {
                                // User sent comment → notify admin
                                $assignedAdmin->notify(new NewCommentNotification($comment));
                                Notification::make()
                                    ->title($authUser->name . ' commented on ticket (#' . $record->id . ')')
                                    ->body('Comment: "' . Str::limit($comment->comment, 10) . '"')
                                    ->actions([
                                        NotificationAction::make('view')
                                            ->label('View Ticket')
                                    ])
                                    ->sendToDatabase($assignedAdmin);
                                event(new DatabaseNotificationsSent($assignedAdmin));
                            } else {
                                Log::warning('Unable to determine recipient for comment on ticket ID: ' . $record->id);
                            }
                        }),

                    Tables\Actions\Action::make('resolve')
                        ->label('Resolve')
                        ->icon('heroicon-o-check')
                        ->action(function ($record) {
                            // Determine the priority based on the location
                            $priority = $record->priority; // Default value
                
                            // Create a new entry in TicketsResolved
                            TicketResolved::create([
                                'id' => $record->id,
                                'concern_type' => $record->concern_type,
                                'name' => $record->name,
                                'type_of_issue' => $record->type_of_issue,
                                'description' => $record->description,
                                'subject' => $record->subject,
                                'priority' => $priority, // Set the priority determined above
                                'department' => $record->department,
                                'location' => $record->location,
                                'dept' => $record->dept_role,
                                'status' => 'Resolved',
                                'accepted_at' => $record->accepted_at,
                                'resolved_at' => now(),
                                'attachment' => $record->attachment,
                                'created_at' => $record->created_at,
                                'assigned' => auth()->user()->name,
                                'assigned_id' => auth()->id(),
                            ]);
                            // Move comments to resolved_comments
                            $comments = TicketComment::where('ticket_id', $record->id)->get();
                            foreach ($comments as $comment) {
                                // Log the ticket ID before inserting into resolved_comments
                                \Log::info('Inserting comment into resolved_comments for ticket ID: ' . $record->id);

                                // Create a resolved comment
                                ResolvedComment::create([
                                    'ticket_id' => $record->id,
                                    'comment' => $comment->comment,
                                    'sender' => $comment->sender,
                                ]);
                            }

                            // Delete original comments after moving them
                            TicketComment::where('ticket_id', $record->id)->delete();

                            // Update TicketHistory for the resolved ticket
                            TicketHistory::where('id', $record->id)->update([
                                'priority' => $record->priority,
                                'status' => 'Resolved',
                                'assigned' => auth()->user()->name,
                                'assigned_id' => auth()->id(),
                            ]);

                            // Notify the user about the ticket resolution
                            $user = User::where('name', $record->name)->first();
                            if ($user) {
                                $user->notify(new TicketResolvedNotification($record));
                                Notification::make()
                                    ->title('Admin resolved the Ticket: (#' . $record->id . ')')
                                    ->body('Concern: "' . Str::limit($record->subject, 10) . '"')
                                    ->actions([
                                        NotificationAction::make('view')
                                            ->label('View Ticket')
                                    ])
                                    ->sendToDatabase($user);
                                event(new DatabaseNotificationsSent($user));
                            }

                            // Attempt to delete the original ticket record
                            if ($record->delete()) {
                                \Log::info('Ticket resolved and deleted:', ['ticket_id' => $record->id]);
                            } else {
                                \Log::error('Failed to delete the resolved ticket:', ['ticket_id' => $record->id]);
                            }
                        })
                        ->requiresConfirmation()
                        ->modalHeading('Confirm Resolve Ticket')
                        ->modalSubheading('Are you sure you want to resolve this ticket?')
                        ->hidden(function () {
                            // Get the authenticated user
                            $user = auth()->user();
                            // Hide the modal only for users with the 'user' role
                            return $user && in_array($user->role, ['user']); // Returns true to hide the modal for 'user' role
                        })
                ]),
            ]);
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
        ];
    }
}
