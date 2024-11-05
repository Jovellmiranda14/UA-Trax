<?php

namespace App\Filament\Resources;
use Illuminate\Support\Facades\Date;
use App\Filament\Resources\TicketResource\Pages;
use App\Models\Ticket;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\FileUpload;
use Filament\Support\Colors\Color;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Notifications\TicketCreated;
use Filament\Tables\Filters\MultiSelectFilter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Columns\ImageColumn;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Card;

use Filament\Forms\Components\Button;
use Filament\Forms\Components\Modal;

use Filament\Support\Facades\FilamentColor;

class TicketColors
{
    const Gray = '#808080';
    const Black = '#000000';
}
class TicketResource extends Resource
{
    protected static ?string $navigationLabel = 'My tickets';
    protected static ?string $model = Ticket::class;
    // protected static ?string $navigationIcon = 'heroicon-s-ticket';
    // protected static ?string $label = 'Open tickets';
    //protected static ?string $navigationGroup = 'Tickets';
    protected static ?int $navigationSort = 1;

    protected static ?string $pluralModelLabel = 'Open tickets';
    // Disable Function
    // public static function canCreate(): Bool
    // {
    //     return false;
    // }
    public static function form(Form $form): Form
    {
        return $form

            ->schema([

                // Radio::make('concern_type')
                // ->label('My concern is about:')
                // ->options([
                //     'Laboratory and Equipment' => 'Laboratory and Equipment',
                //     'Facility' => 'Facility',
                // ])
                // ->reactive()
                // ->required()
                // ->default(function () {
                //     // Check the user's role and return the appropriate default value
                //     $user = Auth::user();

                //     if ($user->isEquipmentSuperAdmin()) {
                //         return 'Facility'; // Automatically set to 'Facility' for Equipment Admin or User
                //     }

                //     // Default to null or another value if needed
                //     return null;
                // }), 




                Radio::make('concern_type')
                    ->label('I want to send a ticket to:')
                    ->options(function () {
                        $user = auth()->user();
                        // Restrict options based on user role
                        // || $user ->isEquipmentAdminOmiss() || $user -> isEquipmentAdminlabcustodian()
            
                        if ($user->isEquipmentSuperAdmin()) {
                            return [
                                'Facility' => 'Facility',
                            ];
                        }
                        if ($user->isFacilitySuperAdmin()) {
                            return [
                                'Laboratory and Equipment' => 'OMISS/ Lab in-Charge',
                            ];
                        }
                        return [
                            'Laboratory and Equipment' => 'OMISS/ Lab in-Charge',
                            'Facility' => 'PPGS',
                        ];
                    })
                    ->reactive()
                    ->required()
                    ->default(function () {
                        $user = Auth::user();
                        return $user->isEquipmentSuperAdmin() ? 'Facility' : null;
                    }),

                Card::make('')
                    ->description('')
                    ->visible(fn($get) => in_array($get('concern_type'), ['Laboratory and Equipment', 'Facility']))
                    ->extraAttributes([
                        'style' => 'max-height: 40vh; max-width: 60vw; overflow: auto; position: -webkit-sticky; position: sticky; top: 10px;' // Sticky behavior
                    ])
                    ->schema([


                        // Ticket Details Card
                        Card::make('Ticket details')
                            ->description('Enter specific issues you have trouble with.')
                            ->visible(fn($get) => in_array($get('concern_type'), ['Laboratory and Equipment', 'Facility']))
                            ->schema([
                                Grid::make(3)

                                    ->schema([
                                        TextInput::make('name')
                                            ->label('Sender')
                                            ->default(fn() => auth()->user()->name)
                                            ->visible(fn($get) => in_array($get('concern_type'), ['Laboratory and Equipment', 'Facility'])),

                                        TextInput::make('subject')
                                            ->label('Concern')
                                            ->required()
                                            ->visible(fn($get) => in_array($get('concern_type'), ['Laboratory and Equipment', 'Facility'])),

                                        Select::make('type_of_issue')
                                            ->label('Type of issue')
                                            ->options([
                                                'repair' => 'Repair',
                                                'air_conditioning' => 'Air Conditioning',
                                                'plumbing' => 'Plumbing',
                                                'lighting' => 'Lighting',
                                                'electricity' => 'Electricity',
                                            ])
                                            ->required()
                                            ->reactive()
                                            ->visible(fn($get) => $get('concern_type') === 'Facility'),

                                        Select::make('type_of_issue')
                                            ->label('Type of issue')
                                            ->options([
                                                'computer_issues' => 'Computer issues (e.g., malfunctioning hardware, software crashes)',
                                                'lab_equipment' => 'Lab equipment malfunction (e.g., broken microscopes, non-functioning lab equipment)',
                                                'Other_Devices' => 'Other Devices (e.g., Printer, Projector, and TV)',
                                            ])
                                            ->required()
                                            ->visible(fn($get) => $get('concern_type') === 'Laboratory and Equipment'),
                                    ]),

                                Grid::make(2)

                                    ->schema([
                                        Textarea::make('description')
                                            ->label('Description')
                                            ->autosize()
                                            ->required()
                                            ->extraAttributes(['style' => 'max-height: 80px; overflow-y: auto;'])
                                            ->visible(fn($get) => in_array($get('concern_type'), ['Laboratory and Equipment', 'Facility'])),
                                           
                                        FileUpload::make('attachment')
                                            ->label('Upload a file (optional)')
                                            ->acceptedFileTypes(['image/jpeg', 'image/png'])
                                            ->directory('attachments')
                                            ->visible(fn($get) => in_array($get('concern_type'), ['Laboratory and Equipment', 'Facility'])),
                                    ]),
                            ]),

                        // Place of Issues Card
                        Card::make('Where did it occur ?')
                            ->description('Enter the information about the place of issue.')
                            ->visible(fn($get) => in_array($get('concern_type'), ['Laboratory and Equipment', 'Facility']))
                            ->schema([
                                Grid::make(2)

                                    ->schema([
                                        Select::make('department')
                                            ->label('Area')
                                            ->hint('Help')
                                            ->hintIcon('heroicon-s-question-mark-circle' , 
                                            tooltip: 'Select the area where the issue occurred, not your own department. For example, if a CONP member reports an issue in the CITCLS lab, choose CITCLS. Select OFFICE for issues outside these areas.')
                                            ->extraAttributes([
                                                'style' => '
                                                    position: relative; 
                                                    font-size: 0.500rem; 
                                                    cursor: pointer;
                                                ',
                                                'class' => 'hover-tooltip',
                                            ])
                                             ->options([
                                                'CEA' => 'CEA',
                                                'CONP' => 'CONP',
                                                'CITCLS' => 'CITCLS',
                                                'SAS (AB COMM)' => 'SAS (AB COMM)',
                                                'SAS (PSYCH)' => 'SAS (PSYCH)',
                                                'SAS (CRIM)' => 'SAS (CRIM)',
                                                'OFFICE' => 'OFFICES',
                                            ])
                                            ->reactive()
                                            ->required(),

                                        Select::make('location')
                                            ->searchable()
                                            ->label('Location')
                                            ->options(fn($get) => collect([
                                                'SAS (AB COMM)' => [
                                                    'AUDIO VISUAL CENTER', 'DEMO ROOM',
                                                    'DIRECTOR’S BOOTH', 'G201 - SPEECH LAB',
                                                    'G208', 'RADIO STUDIO',
                                                    'TV STUDIO',
                                                ],

                                                'CEA' => [
                                                    'ARCHITECTURE DESIGN STUDIO', 'C200 - PHYSICS LAB',
                                                    'C201 - PHYSICS LAB',  'C202 - PHYSICS LAB',
                                                    'C203A', 'C203B',
                                                    'RY302', 'RY303',
                                                    'RY304', 'RY305',
                                                    'RY306', 'RY307',
                                                    'RY308', 'RY309',
                                                ],

                                                'CITCLS' => [
                                                    'C204 - ROBOTICS LAB',  'C301 - CISCO LAB',
                                                    'C302 - SPEECH LAB',    'P308',
                                                    'P309 - COMPUTER LAB 4','P310 - COMPUTER LAB 3',
                                                    'P311 - COMPUTER LAB 2','P312 - COMPUTER LAB 1',
                                                    'P313',
                                                ],

                                                'CONP' => [
                                                    'G103 - NURSING LAB', 'G105 - NURSING LAB',
                                                    'G107 - NURSING LAB',
                                                    'NURSING ARTS LAB',
                                                    'NURSING CONFERENCE ROOM',
                                                    'PHARMACY LECTURE ROOM',
                                                    'PHARMACY STOCKROOM',
                                                ],

                                                'SAS (CRIM)' => [
                                                    'CRIME LAB',
                                                    'CRIMINOLOGY LECTURE ROOM',
                                                    'FORENSIC PHOTOGRAPHY ROOM',
                                                    'MOOT COURT',
                                                ],
                                                'OFFICE' => [
                                                    'PROPERTY CUSTODIAN',
                                                    'PHYSICAL PLANT & GENERAL SERVICES',
                                                    'EAMO',
                                                    'DENTAL/MEDICAL CLINIC',
                                                    'REGINA OFFICE',
                                                    'QUALITY MANAGEMENT OFFICE',
                                                    'OFFICE OF THE PRESIDENT',
                                                    'VPA',
                                                    'HUMAN RESOURCES & DEVELOPMENT',
                                                    'CITCLS OFFICE',
                                                    'CITCLS DEAN OFFICE',
                                                    'CEA OFFICE',
                                                    'HGU OFFICE',
                                                    'VPAA',
                                                    'RSO OFFICE',
                                                    'SAS OFFICE',
                                                    'SED OFFICE',
                                                    'SBPA OFFICE',
                                                    'CONP OFFICE',
                                                    'CHTM OFFICE',
                                                    'OFFICE OF STUDENT AFFAIRS',
                                                    'UACSC OFFICE',
                                                    'PREFECT OF DISCIPLINE',
                                                    'RESEARCH PLANNING OFFICE',
                                                    'CEO',
                                                    'GUIDANCE & ADMISSION',
                                                    'CMO',
                                                    'ITRS',
                                                    'REGISTRAR’S OFFICE',
                                                    'PHOTO LAB',
                                                    'BUSINESS OFFICE',
                                                    'FINANCE OFFICE',
                                                    'RMS OFFICE',
                                                    'VPF',
                                                    'AMPHITHEATER',
                                                    'COLLEGE AVR',
                                                    'LIBRARY MAIN LOBBY',
                                                    'NSTP',
                                                    'COLLEGE LIBRARY',
                                                    'OMISS',
                                                    'SOCIAL HALL',
                                                    'QMO',
                                                    'RPO',
                                                    'BOOKSTORE',
                                                    'HOTEL OFFICE/CAFE MARIA',
                                                    'SPORTS OFFICE',
                                                    'NURSING ARTS LAB',
                                                ],


                                                'SAS (PSYCH)' => [
                                                    'C100 - PHARMACY LAB',
                                                    'C101 - BIOLOGY LAB/STOCKROOM',
                                                    'C102',
                                                    'C103 - CHEMISTRY LAB',
                                                    'C104 - CHEMISTRY LAB',
                                                    'C105 - CHEMISTRY LAB',
                                                    'C106',
                                                    'C303',
                                                    'C304',
                                                    'C305',
                                                    'C306',
                                                    'C307 - PSYCHOLOGY LAB',
                                                ],
                                            ][$get('department')] ?? [])->mapWithKeys(fn($value) => [$value => $value]))
                                            ->required()
                                            ->reactive(),
                                    ]),
                            ])

                    ]),

            ]);

    }



    public static function table(Table $table): Table
    {
        $user = auth()->user();
       
        return $table
            // Pagination 
            // ->paginated([10, 25, 50, 100, 'all']) 
            ->query(Ticket::query()->where('name', $user->name))
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('Ticket ID')
                    ->searchable(),

                Tables\Columns\TextColumn::make('concern_type')
                    ->label('Category')
                    ->searchable(),

                Tables\Columns\TextColumn::make('subject')
                    ->label('Concern')
                    ->limit(25)
                    ->searchable(),

                // Tables\Columns\BadgeColumn::make('status')
                //     ->label('Status')
                //     ->getStateUsing(function ($record) {
                //         switch ($record->status) {
                //             case 'open':
                //                 return 'Open';
                //             case 'in progress':
                //                 return 'In progress';
                //             case 'on-hold':
                //                 return 'On-hold';
                //             case 'resolved':
                //                 return 'Resolved';
                //             case 'close':
                //                 return 'Close';
                //             default:
                //                 return $record->status;
                //         }
                //     })
                //     ->color(function ($state) {
                //         return match ($state) {
                //             'Open' => Color::Blue,
                //             'In progress' => Color::Yellow,
                //             'On-hold' => TicketColors::Black,
                //             'Resolved' => Color::Green,
                //             'Close' => TicketColors::Gray,
                //             default => null,
                //         };
                //     })
                //     ->badge()
                //     ->searchable(),
                Tables\Columns\TextColumn::make('priority')
                    ->label('Priority')
                    ->getStateUsing(function ($record) {
                        switch ($record->location) {
                            case 'OFFICE OF THE PRESIDENT': case 'CMO':
                            case 'EAMO':                    case ' QUALITY MANAGEMENT OFFICE':
                            case 'REGINA OFFICE':
                                return 'High';

                            case 'NURSING ARTS LAB':        case 'SBPA OFFICE':
                            case 'VPAA':                    case 'PREFECT OF DISCIPLINE':
                            case 'GUIDANCE & ADMISSION':    case 'CITCLS OFFICE':
                            case 'CITCLS DEAN OFFICE':      case 'CEA OFFICE':
                            case 'SAS OFFICE':              case 'SED OFFICE':
                            case 'CONP OFFICE':             case 'CHTM OFFICE':
                            case 'ITRS':                    case 'REGISTRAR’S OFFICE':
                            case 'RPO':                     case 'COLLEGE LIBRARY':
                            case 'VPF':                     case 'BUSINESS OFFICE':
                            case 'FINANCE OFFICE':          case 'RMS OFFICE':
                            case 'PROPERTY CUSTODIAN':      case 'BOOKSTORE':
                            case 'VPA':                     case 'HUMAN RESOURCES & DEVELOPMENT':
                            case 'DENTAL/MEDICAL CLINIC':   case 'PHYSICAL PLANT & GENERAL SERVICES':
                            case 'OMISS':                   case 'HOTEL OFFICE/CAFE MARIA':
                            case 'SPORTS OFFICE':           case 'QMO':
                                return 'Moderate';


                            case 'C100 - PHARMACY LAB':     case 'C101 - BIOLOGY LAB/STOCKROOM':
                            case 'C102':                    case 'C103 - CHEMISTRY LAB':
                            case 'C104 - CHEMISTRY LAB':    case 'C105 - CHEMISTRY LAB':
                            case 'C106':                    case 'C303':
                            case 'C304':                    case 'C305':
                            case 'C306':                    case 'C307 - PSYCHOLOGY LAB':

                            // SAS (AB COMM)
                            case 'G201 - SPEECH LAB':       case 'RADIO STUDIO':
                            case 'DIRECTOR’S BOOTH':        case 'AUDIO VISUAL CENTER':
                            case 'TV STUDIO':               case 'G208':
                            case 'DEMO ROOM':

                            // SAS (Crim)
                            case 'MOOT COURT':              case 'CRIMINOLOGY LECTURE ROOM':
                            case 'FORENSIC PHOTOGRAPHY ROOM':  case 'CRIME LAB':

                            // Other previously defined low priority locations
                            case 'C200 - PHYSICS LAB':      case 'C201 - PHYSICS LAB':
                            case 'C202 - PHYSICS LAB':      case 'C203A':
                            case 'C203B':                   case 'ARCHITECTURE DESIGN STUDIO':
                            case 'RY302':                   case 'RY303':
                            case 'RY304':                   case 'RY305':
                            case 'RY306':                   case 'RY307':
                            case 'RY308':                   case 'RY309':
                            case 'PHARMACY STOCKROOM':      case 'G103 - NURSING LAB':
                            case 'G105 - NURSING LAB':      case 'G107 - NURSING LAB':
                            case 'NURSING CONFERENCE ROOM': case 'C204 - ROBOTICS LAB':
                            case 'C301 - CISCO LAB':        case 'C302 - SPEECH LAB':
                            case 'P307':                    case 'P308':
                            case 'P309':                    case 'P309 - COMPUTER LAB 4':
                            case 'P310':                    case 'P310 - COMPUTER LAB 3':
                            case 'P311':                    case 'P311 - COMPUTER LAB 2':
                            case 'P312 - COMPUTER LAB 1':   case 'P312':
                            case 'P313':                    case 'RSO OFFICE':
                            case 'UACSC OFFICE':            case 'PHOTO LAB':
                            case 'AMPHITHEATER':            case 'COLLEGE AVR':
                            case 'LIBRARY MAIN LOBBY':      case 'NSTP':
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
                    Tables\Columns\TextColumn::make('department')
                    ->label('Area')
                    ->searchable(),
                Tables\Columns\TextColumn::make('location')
                    ->label('Location')
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
                    ->label('Date Created')
                    ->formatStateUsing(fn() => Date::now()->format('Y-m-d H:i:s')) // Format date and time
                    ->sortable()
                    ->date(),


            ])
            ->defaultSort('id', 'desc')
            ->filters([

                // Type of issue filter
                SelectFilter::make('concern_type')
                    ->label('Type of issue')
                    ->options([
                        'Facility' => 'Facility',
                        'Laboratory and Equipment' => 'Lab Equipment',
                    ]),
            ])
            ->actions([
                //
            ]);

    }

    public static function getRelations(): array
    {
        return [
            // Define any relations here
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTickets::route('/'),
            // 'create' => Pages\CreateTicket::route('/create'),
            // 'edit' => Pages\EditTicket::route('/{record}/edit'),
        ];
    }


}
