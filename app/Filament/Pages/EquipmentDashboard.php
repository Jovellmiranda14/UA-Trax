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
        return Ticket::where('concern_type', 'Laboratory and Equipment')->get();
    }

    public static function canView(): bool
    {
        $user = auth()->user();

        // If you are using Spatie's roles package, use hasAnyRole method
        return $user->hasAnyRole(['equipmentsuperadmin', 'equipment_user', 'regular user']) &&
               !$user->hasRole('faciltysuperadmin');

        // If you are manually handling roles, ensure 'role' is correctly checked
        // return in_array($user->role, ['equipmentsuperadmin', 'equipment_user', 'regular user']) &&
        //        $user->role !== 'faciltiyequipment';
    }

    public static function canViewNavigation(): bool
    {
        $user = auth()->user();
    
        // If you are using Spatie's roles package, use hasAnyRole method
        return static::canView() &&
               $user->hasAnyRole(['equipmentsuperadmin', 'equipment_user', 'regular user']) &&
               !$user->hasRole('facilitysuperadmin');
    }
}
