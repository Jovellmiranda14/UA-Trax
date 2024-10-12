<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TicketHistoryResource\Pages;
use App\Filament\Resources\TicketHistoryResource\RelationManagers;
use App\Models\TicketHistory;
use App\Models\Ticket;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Support\Colors\Color;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Filters\SelectFilter;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\DatePicker;
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
        // $user = auth()->user();
        return $table
            // ->query(Ticket::query()->where('name', $user->name))
            // Pagination 
            // ->paginated([10, 25, 50, 100, 'all']) 
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
                            'On-hold' => UTicket::Black,
                            'Resolved' => Color::Green,
                            'Close' => UTicket::Gray,
                            default => null,
                        };
                    })
                    ->searchable(),
                Tables\Columns\TextColumn::make('priority')
                    ->label('Priority')
                    ->sortable()
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
            //

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
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
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
