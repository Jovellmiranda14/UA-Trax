<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Filament\Resources\TicketResource\Widgets\TicketVolumeChart;
use App\Filament\Resources\TicketsAcceptedResource\Widgets\TicketAcceptedChart;
use App\Filament\Resources\TicketResolvedResource\Widgets\TicketResolvedChart;
use App\Filament\Resources\TicketResource\Widgets\ConcernTypeChart;
use App\Filament\Resources\TicketResource\Widgets\IssueTypeChart;


class AnalyticsChart extends Page
{
    // protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static string $view = 'filament.pages.analytics-chart';
    protected static ?int $navigationSort = 4;

    public function getHeaderWidgets(): array
    {
        return [
            TicketVolumeChart::class,
            TicketAcceptedChart::class,
            TicketResolvedChart::class,
            ConcernTypeChart::class,
            IssueTypeChart::class,
        ];
    }
}
