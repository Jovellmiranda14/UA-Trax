<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TicketHistoryResource\Pages;

use App\Models\TicketHistory;

use Filament\Forms\Form;
use Filament\Resources\Resource;

use Filament\Tables\Table;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Support\Colors\Color;

use Filament\Tables\Filters\SelectFilter;


// Admin = By Dept
// User = Sarili 

use Filament\Support\Facades\FilamentColor;

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
                auth()->user()->role === 'facilitysuperadmin'  ||
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
                    ->searchable()
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
                            case 'HGU OFFICE':
                            case 'OFFICE OF STUDENT AFFAIRS':
                            case 'RESEARCH PLANNING OFFICE':
                            case 'CEO':
                            case 'SOCIAL HALL':
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
                            case 'PHARMACY LECTURE ROOM':
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
                    }),
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
