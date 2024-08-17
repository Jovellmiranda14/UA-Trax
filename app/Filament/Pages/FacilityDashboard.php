<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Models\Ticket;
class FacilityDashboard extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.facility-dashboard';
    
    public function getTicketsProperty()
    {
        return Ticket::all();
    }
}
