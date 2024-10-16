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

    protected static ?int $navigationSort = 3;
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
        if ($user->isEquipmentAdminOmiss() || $user->isEquipmentAdminlabcustodian()) {

            $query->where('assigned', $user->dept_role);
        }


        if ($user->isFacilityAdmin() || $user->isFacilitySuperAdmin()) {
            $query->where('concern_type', 'Facility')
                ->orderBy('concern_type', 'asc');
        }


        // Pagination 
        // ->paginated([10, 25, 50, 100, 'all']) 
        return $table
            ->query($query)
            ->columns([
                TextColumn::make('id')
                    ->label('Ticket ID')
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
                    }),
                TextColumn::make('location')
                    ->label('Location')
                    ->searchable(),
                TextColumn::make('department')
                    ->label('Department')
                    ->searchable(),
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
                    ->label('Date Created')
                    ->date()
                    ->sortable(),
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
            ])
            ->actions([

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
