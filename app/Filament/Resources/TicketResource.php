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

use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Card;

use Filament\Forms\Components\Button;
use Filament\Forms\Components\Modal;


class TicketResource extends Resource
{
    protected static ?string $navigationLabel = 'My tickets';
    protected static ?string $model = Ticket::class;
    // protected static ?string $navigationIcon = 'heroicon-s-ticket';
    protected static ?string $label = 'Open tickets';
    //protected static ?string $navigationGroup = 'Tickets';
    protected static ?int $navigationSort = 1;
    
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
                ->label('My concern is about:')
                ->options(function () {
                    $user = auth()->user();
                    // Restrict options based on user role
                    // || $user ->isEquipmentAdminOmiss() || $user -> isEquipmentAdminlabcustodian()

                    if ($user->isEquipmentSuperAdmin() ) {
                        return [
                            'Facility' => 'Facility',
                        ];
                    }
                    if ($user->isFacilitySuperAdmin() ) {
                        return [
                            'Laboratory and Equipment' => 'Laboratory and Equipment',
                        ];
                    }
                    return [
                        'Laboratory and Equipment' => 'Laboratory and Equipment',
                        'Facility' => 'Facility',
                    ];
                })
                ->reactive()
                ->required()
                ->default(function () {
                    $user = Auth::user();
                    return $user->isEquipmentSuperAdmin() ? 'Facility' : null;
                }),
             
                
            // Ticket Details Card
            Card::make('Ticket details')
                ->description('Enter specific issues you have trouble with.')
                ->extraAttributes([
                    'class' => 'sticky-card', // Add a class to apply sticky styles
                    'style' => 'max-height: 25vh; max-width: 60vw; overflow: auto; position: -webkit-sticky; position: sticky; top: 10px;' // Sticky behavior
                ]) 
                ->visible(fn ($get) => in_array($get('concern_type'), ['Laboratory and Equipment', 'Facility']))
                ->schema([
                    Grid::make(3)
                    ->extraAttributes(['class' => 'scrollable-content'])
                        ->schema([
                            TextInput::make('name')
                                ->label('Sender')
                                ->default(fn () => auth()->user()->name)
                                ->visible(fn ($get) => in_array($get('concern_type'), ['Laboratory and Equipment', 'Facility'])),
                            
                            TextInput::make('subject')
                                ->label('Concern')
                                ->required()
                                
                                ->visible(fn ($get) => in_array($get('concern_type'), ['Laboratory and Equipment', 'Facility'])),

                            // TextInput::make('property_no')
                                // ->label('Property No.')
                                // ->visible(fn ($get) => $get('concern_type') === 'Laboratory and Equipment'),

                            Select::make('type_of_issue')
                                ->label('Type of Issue')
                                ->options([
                                    'repair' => 'Repair',
                                    'air_conditioning' => 'Air Conditioning',
                                    'plumbing' => 'Plumbing',
                                    'lighting' => 'Lighting',
                                    'electricity' => 'Electricity',
                                ])
                                ->required()
                                ->reactive()
                                
                                ->visible(fn ($get) => $get('concern_type') === 'Facility'),
                                
                                Select::make('type_of_issue')
                                ->label('Type of Issue')
                                ->options([
                                    'computer_issues' => 'Computer issues (e.g., malfunctioning hardware, software crashes)',
                                    'lab_equipment' => 'Lab equipment malfunction (e.g., broken microscopes, non-functioning lab equipment)',
                                    'Other_Devices' => 'Other Devices (e.g., Printer, Projector, and TV)',
                                ])
                                ->required()
                                
                                ->visible(fn ($get) => $get('concern_type') === 'Laboratory and Equipment'),   
                        ]),

                    Grid::make(2)
                    ->extraAttributes(['class' => 'scrollable-content'])
                        ->schema([  
                            Textarea::make('description')
                                ->label('Description')
                                ->required()
                                
                                ->visible(fn ($get) => in_array($get('concern_type'), ['Laboratory and Equipment', 'Facility'])),
                            
                            FileUpload::make('attachment')
                                ->label('Upload a file')
                                ->acceptedFileTypes(['image/jpeg', 'image/png'])
                                ->directory('attachments')
                                ->visible(fn ($get) => in_array($get('concern_type'), ['Laboratory and Equipment', 'Facility'])),
                        ]),
                ]),

            // Place of Issues Card
            Card::make('Place of issues')
                ->description('Select where the equipment is currently located.')
                ->extraAttributes([
                    'class' => 'sticky-card', // Add a class to apply sticky styles
                    'style' => 'max-height: 25vh; max-width: 60vw; overflow: auto; position: -webkit-sticky; position: sticky; top: 10px;' // Sticky behavior
                ]) 
                ->visible(fn ($get) => in_array($get('concern_type'), ['Laboratory and Equipment', 'Facility']))
                ->schema([
                    Grid::make(2)
                    ->extraAttributes(['class' => 'scrollable-content'])
                        ->schema([
                            Select::make('department')
                                ->label('Department')
                                ->options([
                                    'SAS' => 'SAS',
                                    'CEA' => 'CEA',
                                    'CONP' => 'CONP',
                                    'CITCLS' => 'CITCLS',
                                    'RSO' => 'RSO',
                                    'OFFICE' => 'OFFICE',
                                ])
                                ->reactive()
                                
                                ->required(),
                            
                                Select::make('location')
                                ->label('Location')
                                ->options(fn ($get) => collect([
                                    'SAS' => ['SAS Building', 'SAS Lab'],
                                    'CEA' => ['CEA Hall', 'CEA Workshop'],
                                    'CONP' => ['CONP Room 1', 'CONP Room 2'],
                                    'CITCLS' => ['CITCLS Area A', 'CITCLS Area B'],
                                    // mapWithKeys
                                ][$get('department')] ?? [])->mapWithKeys(fn($value) => [$value => $value]))
                                ->required()
                                
                                ->reactive()
                                // ->afterStateUpdated(function ($state, $set) {
                                //     if (is_array($state)) {
                                //         $locationString = implode(', ', $state);
                                //         $set('location', $locationString);
                                //     }
                                // })
                                ->visible(fn ($get) => !in_array($get('department'), ['RSO', 'OFFICE'])),
                            
                            TextInput::make('location')
                                ->label('Location')
                                ->required()
                                
                                ->default('N/A')
                                ->visible(fn ($get) => in_array($get('department'), ['RSO', 'OFFICE'])),
                                // TextInput::make('created_at')
                                // ->label('Date Created')
                                // ->default(Date::now()->format('Y-m-d')) // Set default value to current date
                                // ->hidden(),
                            
                            // TextInput::make('property_no')
                                // ->label('Property No.')
                                
                                // ->visible(fn ($get) => $get('concern_type') === 'Laboratory and Equipment'),
                        ]),
                ]),
        ]) ;
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
                    ->sortable()
                    ->searchable(),
        
                Tables\Columns\TextColumn::make('concern_type')
                    ->label('Category')
                    ->sortable()
                    ->searchable(),

                    Tables\Columns\TextColumn::make('subject')
                    ->label('Concern')
                    ->searchable(),

                    Tables\Columns\BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'primary' => 'Open', 
                        'success' => 'Resolved',
                        'warning' => 'In progress',
                        'info' => 'Closed',
                    ])
                    ->searchable(),
                    Tables\Columns\TextColumn::make('priority')
                    ->label('Priority')
                    ->colors([
                        'success' => 'Low',        // Green
                        'warning' => 'Moderate',   // Yellow
                        'danger'  => 'Urgent',     // Orange
                        'danger'  => 'High',       // Red (same as Urgent in this case)
                    ])
                    ->searchable(),

                Tables\Columns\TextColumn::make('location')
                    ->label('Location')
                    ->searchable(),

                Tables\Columns\TextColumn::make('department')
                    ->label('Dept')
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
                    ->formatStateUsing(fn () => Date::now()->format('Y-m-d H:i:s')) // Format date and time
                    ->date(),

                // Tables\Columns\TextColumn::make('name')
                //     ->label('Sender')
                //     ->sortable()
                //     ->searchable(),

                    // Tables\Columns\TextColumn::make('type_of_issue')
                    // ->label('Type of Issue')
                    // ->sortable()
                    // ->searchable(),


                    // Tables\Columns\TextColumn::make('attachment')
                    // ->label('Attachment')
                    // ->searchable()
                    // ->toggleable(isToggledHiddenByDefault: true),
                    // ->formatStateUsing(fn ($state) => $state ? '<img src="' . $state . '" alt="Attachment" style="max-width: 100px; max-height: 100px;" />' : 'No Attachment')
                    // ->html(), // Use HTML formatting to render the image
                


               
                    
                // Tables\Columns\TextColumn::make('updated_at')
                // ->label('Date Updated')
                // ->date()
                // ->searchable()
                // ->toggleable(isToggledHiddenByDefault: true),

            ])
            ->filters([
                
                // Type of issue filter
                SelectFilter::make('concern_type')
                    ->label('Type of issue')
                    ->options([
                        'Facility' => 'Facility',
                        'Laboratory and Equipment' => 'Laboratory and Equipment',
                    ]),
            ])
            ->actions([
     //
            ]);
            // ->bulkActions([
            //     Tables\Actions\BulkActionGroup::make([
            //         Tables\Actions\DeleteBulkAction::make(),
                    
            //     ]),
                
        
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
