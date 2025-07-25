<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TicketResolvedResource\Pages;
use App\Models\TicketResolved;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\ButtonAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\Actions\ViewAction;
use Filament\Forms\Components\Card;
use Filament\Tables\Actions\Action;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Support\Colors\Color;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Placeholder;
class ConcernSpectrum
{
    const Gray = '#808080';
    const Black = '#000000';
}
class TicketResolvedResource extends Resource
{
    protected static ?string $navigationLabel = 'Closed tickets';

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
        $user = auth()->user();

        $query = TicketResolved::query();

        // Check for Equipment Super Admin role
        if ($user->isEquipmentSuperAdmin()) {
            $query->whereIn('concern_type', ['Laboratory and Equipment'])
                ->where('assigned_id', $user->assigned_id)
                ->orderBy('concern_type', 'asc');
        }

        if ($user->isEquipmentAdminOmiss() || $user->isEquipmentAdminLabCustodian()) {
            // Adjust the query for 'Laboratory and Equipment' concerns and filter by user's department
            $query->whereIn('concern_type', ['Laboratory and Equipment'])
                ->where('assigned_id', $user->assigned_id)
                ->orderBy('concern_type', 'asc');
        }


        if ($user->isFacilityAdmin() || $user->isFacilitySuperAdmin()) {
            $query->where('concern_type', 'Facility')
                ->where('assigned_id', $user->assigned_id)
                ->orderBy('concern_type', 'asc');
        }

        return $table
            ->query($query)
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('Ticket id')
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
                            'On-hold' => ConcernSpectrum::Black,
                            'Resolved' => Color::Green,
                            'Close' => ConcernSpectrum::Gray,
                            default => null,
                        };
                    })
                    ->searchable(),
                TextColumn::make('priority')
                    ->label('Priority')
                    ->sortable()
                    ->getStateUsing(callback: function ($record) {
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
                Tables\Columns\ImageColumn::make('attachment')
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
                    ->label('Created on')
                    ->date()
                    ->sortable()
                    ->searchable(),

                // Unfinished
                TextColumn::make('accepted_at')
                    ->label('Accepted on')
                    ->date()
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('id', 'desc')
            ->filters([
                //
            ])
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
                                                        ->default(fn($record) => $record->created_at)
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

                    Action::make('ViewComment')
                        ->label('Comment list')
                        ->icon('heroicon-o-chat-bubble-left-right')
                        ->modalHeading('Comments')
                        ->modalActions([
                            ButtonAction::make('close')
                                ->label('Close')
                                ->button()
                                ->close(),
                        ])

                        ->form(function (TicketResolved $record) {
                            return [
                                Grid::make(3)
                                    ->schema([
                                        TextInput::make('id')
                                            ->label('Ticket id')
                                            ->default($record->id)
                                            ->disabled()
                                            ->required(),
                                        TextInput::make('subject')
                                            ->label('Concern')
                                            ->default($record->subject)
                                            ->disabled()
                                            ->required(),
                                        DatePicker::make('created_at')
                                            ->label('Date created')
                                            ->default(fn($record) => $record->created_at->format('M d, Y'))
                                            ->disabled()
                                            ->required(),
                                    ]),
                                Repeater::make('resolved_comments')
                                    ->label('Resolved Comments')
                                    ->extraAttributes([
                                        'style' => 'max-height: 38vh; overflow-y: auto;',
                                    ])
                                    ->schema([
                                        Grid::make(3)
                                            ->schema([
                                                TextInput::make('sender')
                                                    ->label('Sender')
                                                    ->disabled(),
                                                TextInput::make('commented_at')
                                                    ->label('Date and time')
                                                    ->disabled(),
                                            ]),
                                        Textarea::make('comment')
                                            ->label('Description')
                                            ->autosize()
                                            ->disabled(),
                                    ])
                                    ->default(function () use ($record) {
                                        return $record->resolvedComments->map(function ($comment) {
                                            return [
                                                'sender' => $comment->sender,
                                                'commented_at' => $comment->created_at->format('Y-m-d H:i:s'), // Format as needed
                                                'comment' => $comment->comment,
                                            ];
                                        })->toArray();
                                    })
                                    ->disabled(),

                                Card::make()
                                    ->visible($record->resolvedComments->isEmpty())
                                    ->extraAttributes(['class' => 'd-flex justify-content-center align-items-center', 'style' => 'height: 100px;'])
                                    ->schema([
                                        Placeholder::make('')
                                            ->content('No comments has been made.')
                                            ->extraAttributes(['style' => 'text-align: center; color: #808080; font-weight: bold; font-size: 15px;']),
                                    ]),
                            ];
                        }),
                ]),
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
