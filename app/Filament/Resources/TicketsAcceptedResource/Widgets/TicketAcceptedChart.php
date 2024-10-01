<?php

namespace App\Filament\Resources\TicketsAcceptedResource\Widgets;

use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use App\Models\TicketsAccepted;

class TicketAcceptedChart extends ChartWidget
{
    protected static ?string $heading = 'Tickets Accepted';

    protected function getData(): array
    {
        // Visible only for Laboratory and Equipment
        $ticketsAcceptedData = Trend::query(
            TicketsAccepted::query()
                ->where('concern_type', 'Laboratory and Equipment')
        )
        ->between(now()->startOfYear(), now()->endOfYear())
        ->perDay()
        ->count();

        // Visible only for Facility
        $facilityAcceptedData = Trend::query(
            TicketsAccepted::query()
                ->where('concern_type', 'Facility')
        )
        ->between(now()->startOfYear(), now()->endOfYear())
        ->perDay()
        ->count();

        return [
            'datasets' => [
                [
                    'label' => 'Accepted Tickets - Laboratory and Equipment',
                    'data' => $ticketsAcceptedData->map(fn (TrendValue $value) => $value->aggregate),
                    'backgroundColor' => 'rgba(75, 192, 192, 0.6)', // Light cyan
                    'borderColor' => 'rgba(75, 192, 192, 1)', // Cyan
                    'borderWidth' => 2,
                ],
                [
                    'label' => 'Accepted Tickets - Facility',
                    'data' => $facilityAcceptedData->map(fn (TrendValue $value) => $value->aggregate),
                    'backgroundColor' => 'rgba(255, 99, 132, 0.6)', // Light red
                    'borderColor' => 'rgba(255, 99, 132, 1)', // Red
                    'borderWidth' => 2,
                ],
            ],
            'labels' => $ticketsAcceptedData->map(fn (TrendValue $value) => $value->date),
        ];
    }

    protected function getType(): string
    {
        return 'bar'; // Bar chart
    }

    protected function getOptions(): array
    {
        return [
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'title' => [
                        'display' => true,
                        'text' => 'Number of Accepted Tickets', // Y-axis title
                    ],
                ],
                'x' => [
                    'title' => [
                        'display' => true,
                        'text' => 'Date', // X-axis title
                    ],
                ],
            ],
            'plugins' => [
                'legend' => [
                    'position' => 'top', // Position of the legend
                ],
                'tooltip' => [
                    'enabled' => true, // Enable tooltips
                    'mode' => 'index', // Show tooltips for all datasets at the same index
                ],
                'title' => [
                    'display' => true,
                    'text' => 'Daily Accepted Ticket Volume', // Main chart title
                ],
            ],
        ];
    }
}
