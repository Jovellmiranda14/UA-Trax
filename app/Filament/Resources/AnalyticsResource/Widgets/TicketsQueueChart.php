<?php

namespace App\Filament\Resources\AnalyticsResource\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\TicketQueue;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use App\Models\Ticket;

class TicketsQueueChart extends ChartWidget
{
    protected static ?string $heading = 'Ticket Queue';

    protected function getData(): array
    {
        $data = Trend::query(
            Ticket::query()
                ->where('concern_type', 'Laboratory and Equipment')
                ->when(request('department'), function ($query) {
                    return $query->where('department', request('department'));
                })
        )
        ->between(
            start: now()->startOfMonth(),
            end: now()->endOfMonth(),
        )
        ->perDay()
        ->count();

        $facilityData = Trend::query(
            Ticket::query()
                ->where('concern_type', 'Facility')
        )
        ->between(
            start: now()->startOfMonth(),
            end: now()->endOfMonth(),
        )
        ->perDay()
        ->count();

        return [
            'datasets' => [
                [
                    'label' => 'Laboratory and Equipment Ticket Queue',
                    'data' => $data->map(fn (TrendValue $value) => $value->aggregate),
                ],
                [
                    'label' => 'Facility Ticket Queue',
                    'data' => $facilityData->map(fn (TrendValue $value) => $value->aggregate),
                ],
            ],
            'labels' => $data->map(fn (TrendValue $value) => $value->date),
        ];
    }


    protected function getType(): string
    {
        return 'line';
    }
}
