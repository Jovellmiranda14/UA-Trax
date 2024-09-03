<?php

namespace App\Filament\Resources;

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

use Filament\Tables\Filters\MultiSelectFilter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;

use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Card;



class TicketResource extends Resource
{
    protected static ?string $navigationLabel = 'My Tickets';
    protected static ?string $model = Ticket::class;
    //protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $label = 'Open tickets';
    protected static ?string $navigationGroup = 'Tickets';
    protected static ?int $navigationSort = 4;
    
    // Disable Function
    // public static function canCreate(): Bool
    // {
    //     return false;
    // }
    public static function form(Form $form): Form
{
    return $form
        ->schema([
            Card::make('Ticket details')
            ->description('Enter specfic issues you have trouble with.')
                ->schema([
                    Grid::make(2)
                        ->schema([
                            TextInput::make('subject')
                                ->label('Subject')
                                ->required(),

                                TextInput::make('property_no')
                                ->label('Property No.')
                                ->required(),
                              

                            TextInput::make('property_no')
                    ->label('Property No.')
                    ->visible(fn ($get) => $get('concern_type') === 'Laboratory and Equipment'),
                                   
                        ]),

                         
                    Grid::make(2)
                        ->schema([
                            Textarea::make('description')
                                ->label('Description')
                                ->required(),
                            
                            FileUpload::make('attachment')
                                ->label('Upload a file')
                                ->acceptedFileTypes(['image/jpeg', 'image/png'])
                                ->directory('attachments'),
                        ]),
                ])
                ->label('Ticket details'),

                Card::make('Place of issues')
                ->description('Select where the equipment is currently located.')
                ->schema([
                    Grid::make(2)
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
                                ->options(fn ($get) => [
                                    'SAS' => ['SAS Building', 'SAS Lab'],
                                    'CEA' => ['CEA Hall', 'CEA Workshop'],
                                    'CONP' => ['CONP Room 1', 'CONP Room 2'],
                                    'CITCLS' => ['CITCLS Area A', 'CITCLS Area B'],
                                ][$get('department')] ?? [])
                                ->required()
                                ->reactive()
                                ->visible(fn ($get) => !in_array($get('department'), ['RSO', 'OFFICE'])),
                        ]),
                ])
               
        ]);
}
    

    public static function table(Table $table): Table
    {
        $user = auth()->user();
       

        return $table
        ->query(Ticket::query()->where('name', $user->name))
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('Ticket ID')
                    ->sortable()
                    ->searchable(),
                    // ->extraAttributes(['class' => 'border-blue-500 bg-gray-100']),
                Tables\Columns\TextColumn::make('concern_type')
                    ->label('Category')
                    ->sortable()
                    ->searchable(),

                    Tables\Columns\TextColumn::make('subject')
                    ->label('Subject')
                    ->searchable(),

                    Tables\Columns\TextColumn::make('priority')
                    ->label('Priority')
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
                    ->circular() // Keep the image circular, remove if you want square images
                    ->getStateUsing(fn($record) => $record->attachment ? asset('storage/' . $record->attachment) : url('/images/equipment.png'))
                    ->url(fn($record) => $record->attachment ? asset('storage/' . $record->attachment) : null)
                    ->extraAttributes(function ($record) {
                        return $record->attachment ? ['class' => 'clickable-image'] : [];
                    })
                    ->openUrlInNewTab(),

                 


                Tables\Columns\TextColumn::make('created_at')
                    ->label('Date Created')
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
                

                // Tables\Columns\BadgeColumn::make('status')
                //     ->label('Status')
                //     ->colors([
                //         'primary' => 'Open', Color::Blue,
                //         'success' => 'Resolved',
                //         'warning' => 'In progress',
                //         'info' => 'Closed',
                //     ])
                //     ->searchable(),

               
                    
                // Tables\Columns\TextColumn::make('updated_at')
                // ->label('Date Updated')
                // ->date()
                // ->searchable()
                // ->toggleable(isToggledHiddenByDefault: true),

            ])
            ->filters([
                // Tickets filter
                SelectFilter::make('status')
                    ->label('Tickets')
                    ->options([
                        'Open' => 'Open tickets',
                        'Accepted' => 'Accepted',
                    ]),
                
                // Type of issue filter
                SelectFilter::make('issue_type')
                    ->label('Type of issue')
                    ->options([
                        'Facility' => 'Facility',
                        'Equipment' => 'Equipment',
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
