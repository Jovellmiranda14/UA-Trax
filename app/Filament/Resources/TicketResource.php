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
use Filament\Forms\Components\Wizard\Step;
use Filament\Tables\Filters\MultiSelectFilter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;

class TicketResource extends Resource
{
    protected static ?string $navigationLabel = 'My Tickets';
    protected static ?string $model = Ticket::class;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    // Disable Function
    // public static function canCreate(): Bool
    // {
    //     return false;
    // }
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Radio::make('concern_type')
                    ->label('My concern is about:')
                    ->options([
                        'Laboratory and Equipment' => 'Laboratory and Equipment',
                        'Facility' => 'Facility', 
                    ])
                    ->reactive()
                    ->required(),              
                // TextInput::make('property_no')
                //     ->label('Property No.')
                //     ->required()
                //     ->visible(fn ($get) => $get('concern_type') === 'Laboratory and Equipment'),

                // Under Modification 
                TextInput::make('priority')
                    ->label('Priority')
                    ->default('Moderate')
                    ->visible(fn ($get) => in_array($get('concern_type'), ['Laboratory and Equipment', 'Facility']))
                    ->disabled()
                    ->hidden(),                 
                Select::make('department')
                    ->label('Department')
                    ->options([
                        'SAS' => 'SAS',
                        'CEA' => 'CEA',
                        'CONP' => 'CONP',
                        'CITCLS' => 'CITCLS',
                        'RSO' => 'RSO', // Specify the Location
                        'OFFICE' => 'OFFICE', // Specify the Location        
                    ])
                    ->reactive()
                    ->required()
                    ->visible(fn ($get) => $get('concern_type') === 'Laboratory and Equipment'),
                                 
                    Select::make('type_of_issue')
                    ->label('Type of Issue')
                    ->options([
                        'computer_issues' => 'Computer issues (e.g., malfunctioning hardware, software crashes)',
                        'lab_equipment' => 'Lab equipment malfunction (e.g., broken microscopes, non-functioning lab equipment)',
                        'connectivity_issues' => 'Connectivity issues (e.g., internet problems, network issues)',
                    ])
                    ->required()
                    ->visible(fn ($get) => $get('concern_type') === 'Laboratory and Equipment'),            
                        
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
                    ->visible(fn ($get) => $get('concern_type') === 'Facility'),

                TextArea::make('description')
                    ->label('Description')
                    ->required()
                    ->visible(fn ($get) => in_array($get('concern_type'), ['Laboratory and Equipment', 'Facility'])),

                TextInput::make('subject')
                    ->label('Subject')
                    ->required()
                    ->visible(fn ($get) => in_array($get('concern_type'), ['Laboratory and Equipment', 'Facility'])),

                    TextInput::make('name')
                    ->label('Sender')
                    ->default(fn () => Auth::user()->name)
                    ->visible(fn ($get) => in_array($get('concern_type'), ['Laboratory and Equipment', 'Facility'])),

                    TextInput::make('location')
                    ->label('Location')
                    ->required()
                    ->default('N/A')
                     ->visible(fn ($get) => $get('department') === 'RSO' || $get('department') === 'OFFICE'),
                    
        
                FileUpload::make('attachment')
                    ->label('Upload file')
                    ->acceptedFileTypes(['image/jpeg', 'image/png'])
                    ->visible(fn ($get) => in_array($get('concern_type'), ['Laboratory and Equipment', 'Facility'])),
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
                    ->size(150)
                    // ->circular() // Remove this if you prefer square images
                    ->url(fn($record) => asset('storage/' . $record->attachment)),

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
                SelectFilter::make('status')
                ->options([
                    'Accepted' => 'Accepted',
                    'Open' => 'Open Tickets',         
                ])  
            ])
            ->actions([
                Tables\Actions\DeleteAction::make(),
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
