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
use Filament\Tables\Actions\Action;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Support\Colors\Color;
use Filament\Support\Facades\FilamentColor;
use Filament\Forms\Components\Grid;
class ConcernSpectrum
{
    const Gray = '#808080';
    const Black = '#000000';
}
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
        $user = auth()->user(); // Retrieve the currently authenticated user

        // Build the base query for TicketHistory
        $query = TicketResolved::query();

        // Check for Equipment Super Admin role
        if ($user->isEquipmentSuperAdmin()) {
            $query->whereIn('concern_type', ['Laboratory and Equipment'])
                ->orderBy('concern_type', 'asc');
        }



        if ($user->isEquipmentAdminOmiss() || $user->isEquipmentAdminLabCustodian()) {
            // Adjust the query for 'Laboratory and Equipment' concerns and filter by user's department
            $query->whereIn('concern_type', ['Laboratory and Equipment'])
                ->where('assigned', $user->name)
                ->orderBy('concern_type', 'asc');
        }


        if ($user->isFacilityAdmin() || $user->isFacilitySuperAdmin()) {
            $query->where('concern_type', 'Facility')
                ->orderBy('concern_type', 'asc');
        }

        return $table
            ->query($query)
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
                Tables\Columns\TextColumn::make('priority')
                    ->label('Priority')
                    ->getStateUsing(function ($record) {
                        switch ($record->location) {
                            case 'OFFICE OF THE PRESIDENT':
                            case 'CMO':
                            case 'EAMO':
                            case ' QUALITY MANAGEMENT OFFICE':
                            case 'REGINA OFFICE':
                                return 'High';

                            case 'NURSING ARTS LAB':
                            case 'SBPA OFFICE':
                            case 'VPAA':
                            case 'PREFECT OF DISCIPLINE':
                            case 'GUIDANCE & ADMISSION':
                            case 'CITCLS OFFICE':
                            case 'CITCLS DEAN OFFICE':
                            case 'CEA OFFICE':
                            case 'SAS OFFICE':
                            case 'SED OFFICE':
                            case 'CONP OFFICE':
                            case 'CHTM OFFICE':
                            case 'ITRS':
                            case 'REGISTRAR’S OFFICE':
                            case 'RPO':
                            case 'COLLEGE LIBRARY':
                            case 'VPF':
                            case 'BUSINESS OFFICE':
                            case 'FINANCE OFFICE':
                            case 'RMS OFFICE':
                            case 'PROPERTY CUSTODIAN':
                            case 'BOOKSTORE':
                            case 'VPA':
                            case 'HUMAN RESOURCES & DEVELOPMENT':
                            case 'DENTAL/MEDICAL CLINIC':
                            case 'PHYSICAL PLANT & GENERAL SERVICES':
                            case 'OMISS':
                            case 'HOTEL OFFICE/CAFE MARIA':
                            case 'SPORTS OFFICE':
                            case 'QMO':
                                return 'Moderate';


                            case 'C100 - PHARMACY LAB':
                            case 'C101 - BIOLOGY LAB/STOCKROOM':
                            case 'C102':
                            case 'C103 - CHEMISTRY LAB':
                            case 'C104 - CHEMISTRY LAB':
                            case 'C105 - CHEMISTRY LAB':
                            case 'C106':
                            case 'C303':
                            case 'C304':
                            case 'C305':
                            case 'C306':
                            case 'C307 - PSYCHOLOGY LAB':

                            // SAS (AB COMM)
                            case 'G201 - SPEECH LAB':
                            case 'RADIO STUDIO':
                            case 'DIRECTOR’S BOOTH':
                            case 'AUDIO VISUAL CENTER':
                            case 'TV STUDIO':
                            case 'G208':
                            case 'DEMO ROOM':

                            // SAS (Crim)
                            case 'MOOT COURT':
                            case 'CRIMINOLOGY LECTURE ROOM':
                            case 'FORENSIC PHOTOGRAPHY ROOM':
                            case 'CRIME LAB':

                            // Other previously defined low priority locations
                            case 'C200 - PHYSICS LAB':
                            case 'C201 - PHYSICS LAB':
                            case 'C202 - PHYSICS LAB':
                            case 'C203A':
                            case 'C203B':
                            case 'ARCHITECTURE DESIGN STUDIO':
                            case 'RY302':
                            case 'RY303':
                            case 'RY304':
                            case 'RY305':
                            case 'RY306':
                            case 'RY307':
                            case 'RY308':
                            case 'RY309':
                            case 'PHARMACY STOCKROOM':
                            case 'G103 - NURSING LAB':
                            case 'G105 - NURSING LAB':
                            case 'G107 - NURSING LAB':
                            case 'NURSING CONFERENCE ROOM':
                            case 'C204 - ROBOTICS LAB':
                            case 'C301 - CISCO LAB':
                            case 'C302 - SPEECH LAB':
                            case 'P307':
                            case 'P308':
                            case 'P309':
                            case 'P309 - COMPUTER LAB 4':
                            case 'P310':
                            case 'P310 - COMPUTER LAB 3':
                            case 'P311':
                            case 'P311 - COMPUTER LAB 2':
                            case 'P312 - COMPUTER LAB 1':
                            case 'P312':
                            case 'P313':
                            case 'RSO OFFICE':
                            case 'UACSC OFFICE':
                            case 'PHOTO LAB':
                            case 'AMPHITHEATER':
                            case 'COLLEGE AVR':
                            case 'LIBRARY MAIN LOBBY':
                            case 'NSTP':
                                return 'Low';

                            // Add more cases for other locations as needed
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
                    ->searchable(),


                Tables\Columns\TextColumn::make('location')
                    ->label('Location')
                    ->searchable(),
                Tables\Columns\TextColumn::make('department')
                    ->label('Department')
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
                        ->modalSubHeading('')
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
                                                    TextInput::make('type_of_issue')
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
                                                    TextInput::make('attachment')
                                                        ->label('Attachment')
                                                        ->disabled()
                                                        ->required(),
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
                                                    DatePicker::make('created_at')
                                                        ->label('Date Created')
                                                        ->disabled()
                                                        ->required(),
                                                ]),
                                        ]),
                                ]),
                        ]),

                    Action::make('ViewComment')
                        ->label('Comment list')
                        ->icon('heroicon-o-rectangle-stack')
                        ->modalHeading('Comments')
                        ->form(function (TicketResolved $record) {
                            return [
                                Grid::make(3)
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
                                        TextInput::make('created_at')
                                            ->label('Date Created')
                                            ->default($record->created_at)
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
                                                    ->label('Date and Time')
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
