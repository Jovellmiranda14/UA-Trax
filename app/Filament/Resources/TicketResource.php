<?php

namespace App\Filament\Resources;
use Illuminate\Support\Facades\Date;
use App\Filament\Resources\TicketResource\Pages;
use App\Models\Ticket;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\FileUpload;
use Filament\Support\Colors\Color;
use Illuminate\Support\Facades\Auth;
use Filament\Tables\Filters\SelectFilter;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Card;

class TicketColors
{
    const Gray = '#808080';
    const Black = '#000000';
}
class TicketResource extends Resource
{
    protected static ?string $navigationLabel = 'My tickets';
    protected static ?string $model = Ticket::class;
    protected static ?int $navigationSort = 1;

    protected static ?string $pluralModelLabel = 'Open tickets';
    public static function form(Form $form): Form
    {
        return $form

            ->schema([
                Radio::make('concern_type')
                    ->label('I want to send a ticket to:')
                    ->options(function () {
                        $user = auth()->user();

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
                                            ->required()
                                            ->disabled()
                                            ->extraAttributes([
                                                'style' => '
                                                    position: relative; 
                                                    font-size: 0.500rem; 
                                                    cursor: pointer;
                                                ',
                                                'class' => 'hover-tooltip',
                                            ])
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
                                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/jpg', 'image/tiff', 'image/JPEG', 'image/JPG', 'image/PNG'])
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
                                            ->hintIcon('heroicon-s-question-mark-circle', tooltip: 'Select the area where the issue occurred, not your own department. For example, if a CONP member reports an issue in the CITCLS lab, choose CITCLS. Select OFFICE for issues outside these areas.')
                                            ->extraAttributes([
                                                'style' => '
                                                position: relative; 
                                                font-size: 0.500rem; 
                                                cursor: pointer;
                                            ',
                                                'class' => 'hover-tooltip',
                                            ])
                                            ->options(fn($get) => \App\Models\Department::where('code', '!=', 'PPGS')->pluck('name', 'code'))  // Exclude "PPGS"
                                            ->reactive()  // Ensures the location field updates on department change
                                            ->required(),

                                        Select::make('location')
                                            ->searchable()
                                            ->label('Location')
                                            ->options(
                                                fn($get) => \App\Models\Location::query()
                                                    ->when(
                                                        $get('department'),
                                                        fn($query, $department) => $query->where('department', $department) // Filter by selected department
                                                    )
                                                    ->pluck('location') // Get room numbers with their corresponding IDs
                                                    ->mapWithKeys(fn($roomNo) => [$roomNo => $roomNo]) // Format options
                                                    ->toArray() // Convert to array for Select component
                                            )
                                            ->required()
                                            ->reactive()
                                            ->afterStateUpdated(fn($state, $set) => $set(
                                                'priority',
                                                \App\Models\Location::where('location', $state)->value('priority') // Fetch associated priority
                                            )),

                                    ]),
                            ])

                    ]),

            ]);

    }



    public static function table(Table $table): Table
    {
        $user = auth()->user();

        return $table
            ->query(Ticket::query()->where('user_id', auth()->id()))
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
                    ->searchable()
                    ->sortable()
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
