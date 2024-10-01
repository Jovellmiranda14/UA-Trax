<?php

namespace App\Filament\Resources\TicketResolvedResource\Widgets;

use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use App\Models\Ticket;
use App\Models\TicketResolved;

class TicketResolvedChart extends ChartWidget
{
    //Wala pang lumilitaw sa tickets resolved baka dahil empty pa yung data
    protected static ?string $heading = 'Tickets Resolved';

    protected function getData(): array
    {
        $ticketsResolvedData = Trend::query(
            Ticket::query()->where('concern_type', 'Equipment')
                ->where('status', 'Resolved')
        )
        ->between(now()->startOfMonth(), now()->endOfMonth())
        ->perDay()
        ->count();

        $facilityResolvedData = Trend::query(
            Ticket::query()->where('concern_type', 'Facility')
                ->where('status', 'Resolved')
        )
        ->between(now()->startOfMonth(), now()->endOfMonth())
        ->perDay()
        ->count();

        return [
            'datasets' => [
                [
                    'label' => 'Resolved Tickets - Equipment',
                    'data' => $ticketsResolvedData->map(fn (TrendValue $value) => $value->aggregate),
                ],
                [
                    'label' => 'Resolved Tickets - Facility',
                    'data' => $facilityResolvedData->map(fn (TrendValue $value) => $value->aggregate),
                ],
            ],
            'labels' => $ticketsResolvedData->map(fn (TrendValue $value) => $value->date),
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
