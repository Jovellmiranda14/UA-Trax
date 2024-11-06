<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Filament\Resources\TicketResource\Widgets\TicketVolumeChart;
use App\Filament\Resources\TicketsAcceptedResource\Widgets\TicketAcceptedChart;
use App\Filament\Resources\TicketResolvedResource\Widgets\TicketResolvedChart;
use App\Filament\Resources\TicketResource\Widgets\LabEquipmentIssueChart;
use App\Filament\Resources\TicketResource\Widgets\ComputerIssueChart;
use App\Filament\Resources\TicketResource\Widgets\OtherIssueChart;
use App\Filament\Resources\TicketResource\Widgets\FacilityIssueChart;
use App\Filament\Widgets\TicketOverviewWidget;

class AnalyticsChart extends Page
{
    protected static string $view = 'filament.pages.analytics-chart';
    protected static ?int $navigationSort = 4;

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
            FacilityIssueChart::class,
        ];
    }
}
