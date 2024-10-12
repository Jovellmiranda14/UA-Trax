<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TicketsAcceptedResource\Pages;
use App\Filament\Resources\TicketsAcceptedResource\RelationManagers;
use App\Models\TicketsAccepted;
use App\Models\TicketResolved;
use App\Models\Ticket;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Actions\ActionGroup;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ViewAction;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\ImageColumn;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\TextArea;
use Filament\Forms\Components\DateTimePicker;
use Filament\Tables\Filters\MultiSelectFilter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use App\Models\TicketComment;
use Filament\Support\Colors\Color;
use Filament\Support\Facades\FilamentColor;
use App\Notifications\NewCommentNotification;
use Filament\Forms\Components\Grid;
use Illuminate\Support\Facades\Log;
use Filament\Notifications\Events\DatabaseNotificationsSent;

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

    // protected static ?string $navigationGroup = 'Tickets';
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
                    ->label('Concern')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('priority')
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
                    ->searchable()
                    ->sortable(),

                Tables\Columns\BadgeColumn::make('status')
                    ->label('Status')
                    ->sortable()
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
                            'class' => 'sticky-modal-header', // Add sticky header class
                        ])
                        ->form([
                            Card::make()
                                ->extraAttributes([
                                    'style' => 'max-height: 68vh; overflow-y: auto;', // Make the content scrollable
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
                                                    TextInput::make('issue_type')
                                                        ->label('Type of Issue')
                                                        ->disabled()
                                                        ->required(),
                                                ]),

                                            // Description and Attachment Fields
                                            Grid::make(2)
                                                ->schema([
                                                    TextInput::make('description')
                                                        ->label('Description')
                                                        ->disabled()
                                                        ->required(),
                                                    // ImageColumn::make('attachment')
                                                    // ->label('Attachment')
                                                    // ->disabled(),
                                                ]),
                                        ]),

                                    // Section: Place of Issue
                                    Card::make('Place of Issue')
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
                                                    TextInput::make('created_at')
                                                        ->label('Date Created')
                                                        ->disabled()
                                                        ->required(),
                                                ]),
                                        ]),
                                ]),
                        ]),
                    Tables\Actions\Action::make('comment')
                        ->label('Comment')
                        // ->icon('heroicon-o-chat')
                        ->modalHeading('Comment on Ticket')
                        ->modalSubheading('Provide and view comments related to this ticket.')
                        ->form(function (TicketsAccepted $record) {
                            return [
                               
                                Repeater::make('comments') // Display existing comments
                                    ->label('Previous Comments')
                                    ->schema([
                                        TextInput::make('sender')
                                            ->label('Sender')
                                            ->disabled(),
                                        TextArea::make('comment')
                                            ->label('Comment')
                                            ->disabled(),
                                        TextInput::make('commented_at') // Use DateTimePicker for date and time selection
                                            ->label('Date and Time')
                                            ->disabled(),
                                    ])

                                    ->default(function () use ($record) {
                                        // Now we use the passed record parameter instead of $this
                                        if ($record->comments) {
                                            return $record->comments->map(function ($comment) {
                                                return [
                                                    'sender' => $comment->sender,
                                                    'comment' => $comment->comment,
                                                    'commented_at' => $comment->commented_at,
                                                ];
                                            })->toArray();
                                        } else {
                                            return [];
                                        }
                                    })
                                    ->disabled(),
                                TextInput::make('new_comment')
                                    ->label('Add a Comment')
                                    ->placeholder('Write your comment here...'),

                            ];
                        })
                        ->action(function (array $data, TicketsAccepted $record) {
                            // Save the new comment to the database
                            $comment = new TicketComment();
                            $comment->ticket_id = $record->id;
                            $comment->sender = auth()->user()->name;
                            $comment->commented_at = now();
                            $comment->comment = $data['new_comment'];
                            $comment->save();
                            // ------------ Notification ------------------------------------------------ 
                            $assignedAdmin = User::where('name', $record->assigned)->first();
                            $RegularUser = User::where('name', $record->name)->first();
 
                            if ($assignedAdmin) { // Check if the assigned admin exists
                
                                $RegularUser->notify(new NewCommentNotification($comment));
                                Notification::make()
                                    ->title('Admin Comment on Ticket:')
                                    ->body('The ticket owner commented: ' . $comment->comment)
                                    ->sendToDatabase($RegularUser);
                                event(new DatabaseNotificationsSent($RegularUser));
                            } else {
                               
                                Log::warning('No admin found for assigned record ID: ' . $record->assigned);
                            }

                            if ($RegularUser) { // Check if the regular user exists
                                // Notify the regular user about the new comment
                                $assignedAdmin->notify(new NewCommentNotification($comment));
                                Notification::make()
                                    ->title('User Comment on Ticket:')
                                    ->body('The ticket owner commented: ' . $comment->comment)
                                    ->sendToDatabase($assignedAdmin);


                                event(new DatabaseNotificationsSent($assignedAdmin));
                            } else {
                                // Handle the case where the regular user is not found
                                Log::warning('No regular user found for assigned record ID: ' . $record->assigned);
                            }

                        }),

                    Action::make('resolve')
                        ->label('Resolve')
                        ->icon('heroicon-o-check')
                        ->hidden(fn() => auth()->user()->role === 'user')->action(function ($record) {
                            // Create a new entry in TicketsAccepted
                            TicketResolved::create([
                                'id' => $record->id,
                                'concern_type' => $record->concern_type,
                                'name' => $record->name,
                                'subject' => $record->subject,
                                'priority' => $record->priority,
                                'department' => $record->department,
                                'location' => $record->location,
                                'dept' => $record->dept_role,
                                'status' => 'Resolved',
                                'accepted_at' => now(),
                                'attachment' => $record->attachment,
                                'created_at' => $record->created_at,
                                'assigned' => auth()->user()->name,
                            ]);

                            // Attempt to delete the record
                            if ($record->delete()) {
                                \Log::info('Ticket resolved:', ['ticket_id' => $record->id]);
                            } else {
                                \Log::error('Failed to delete the resolved ticket:', ['ticket_id' => $record->id]);
                            }
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
            // 'create' => Pages\CreateTicketsAccepted::route('/create'),
            // 'edit' => Pages\EditTicketsAccepted::route('/{record}/edit'),
        ];
    }
}
