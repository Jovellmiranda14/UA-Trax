<?php

namespace App\Filament\Resources\AnalyticsResource\Widgets;

use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use App\Models\Ticket;
use App\Models\TicketsAccepted;

class TicketsAcceptedChart extends ChartWidget
{
    //Wlng lumilitaw na data sa tickets accepted di ko alm pano kunin LOL
    protected static ?string $heading = 'Tickets Accepted';

    protected function getData(): array
    {
        $ticketsAcceptedData = Trend::query(
            TicketsAccepted::query()
                ->where('department', 'Equipment')
                //concern_type dapat then by department
                ->where('accepted_at', 'Accepted')
        )
        ->between(now()->startOfMonth(), now()->endOfMonth())
        ->perDay()
        ->count();

        $facilityAcceptedData = Trend::query(
            TicketsAccepted::query()
                ->where('department', 'Facility')
                //concern_type dapat
                ->where('accepted_at', 'Accepted')
        )
        ->between(now()->startOfMonth(), now()->endOfMonth())
        ->perDay()
        ->count();

        return [
            'datasets' => [
                [
                    'label' => 'Accepted Tickets - Equipment',
                    'data' => $ticketsAcceptedData->map(fn (TrendValue $value) => $value->aggregate),
                ],
                [
                    'label' => 'Accepted Tickets - Facility',
                    'data' => $facilityAcceptedData->map(fn (TrendValue $value) => $value->aggregate),
                ],
            ],
            'labels' => $ticketsAcceptedData->map(fn (TrendValue $value) => $value->date),
        ];
    }

    protected function getType(): string
    {
        return 'pie';
    }
}
