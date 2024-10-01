<?php

namespace App\Filament\Resources\TicketResource\Widgets;

use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use App\Models\Ticket;

class ConcernTypeChart extends ChartWidget
{
    protected static ?string $heading = 'Concern Types';

    protected function getData(): array
    {
        $concernData = Ticket::query()
            ->select('concern_type', \DB::raw('count(*) as total'))
            ->groupBy('concern_type')
            ->get();

        $concernLabels = $concernData->pluck('concern_type');
        $concernValues = $concernData->pluck('total');

        return [
            'datasets' => [
                [
                    'label' => 'Concern Types',
                    'data' => $concernValues,
                    'backgroundColor' => [
                        '#FF6384', // Red
                        '#36A2EB', // Blue
                        '#FFCE56', // Yellow
                        '#4BC0C0', // Teal
                        '#9966FF', // Purple
                    ],
                    'hoverBackgroundColor' => [
                        '#FF6384', // Red
                        '#36A2EB', // Blue
                        '#FFCE56', // Yellow
                        '#4BC0C0', // Teal
                        '#9966FF', // Purple
                    ],
                ],
            ],
            'labels' => $concernLabels,
        ];
    }

    protected function getType(): string
    {
        return 'doughnut'; // You can change this to 'doughnut' if preferred
    }

    protected function getOptions(): array
    {
        return [
            'responsive' => true,
            'plugins' => [
                [
                    'legend' => [
                        'display' => true,
                        'position' => 'top', // Can be 'top', 'left', 'bottom', 'right'
                    ],
                ],
                [
                    'tooltip' => [
                        'enabled' => true,
                    ],
                ],
            ],
        ];
    }
}
