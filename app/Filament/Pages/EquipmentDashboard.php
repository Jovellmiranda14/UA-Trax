<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Models\Ticket;
class EquipmentDashboard extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.equipment-dashboard';

    public function getTicketsProperty()
    {
        return Ticket::where('concern_type', 'Equipment')->get();
    }
}