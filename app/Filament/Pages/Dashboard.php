<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Filament\Resources\TicketResource\Widgets\TicketVolumeChart;
use App\Filament\Resources\TicketsAcceptedResource\Widgets\TicketAcceptedChart;
use App\Filament\Resources\TicketResolvedResource\Widgets\TicketResolvedChart;
use App\Filament\Resources\TicketResource\Widgets\LabEquipmentIssueChart;
use App\Filament\Resources\TicketResource\Widgets\ComputerIssueChart;
use App\Filament\Resources\TicketResource\Widgets\OtherIssueChart;
use App\Filament\Resources\TicketResource\Widgets\OfficeIssueChart;
use App\Filament\Resources\TicketResource\Widgets\FacilityIssueChart;
use App\Filament\Widgets\TicketOverviewWidget;

class Dashboard extends Page
{
    protected static string $view = 'filament.pages.analytics-chart';
    public static function getNavigationSort(): ?int
    {
        if (
            auth()->check() && in_array(auth()->user()->role, [
                'equipmentsuperadmin',
                'facilitysuperadmin',
                'equipment_admin_labcustodian',
                'equipment_admin_omiss',
                'facility_admin',
            ])
        ) {
            return 3; // Assign a sort order for these roles
        }
    
        return null; // Hide or deprioritize the navigation for other roles
    }

    public function getHeaderWidgets(): array
    {
        return [
            TicketOverviewWidget::class,
            TicketVolumeChart::class,
            TicketAcceptedChart::class,
            TicketResolvedChart::class,
            ComputerIssueChart::class,
            LabEquipmentIssueChart::class,
            OtherIssueChart::class,
            OfficeIssueChart::class,
            FacilityIssueChart::class,
        ];
    }
}
