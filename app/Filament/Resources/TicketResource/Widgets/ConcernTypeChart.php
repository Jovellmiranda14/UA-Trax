<?php

namespace App\Filament\Resources\TicketResource\Widgets;

use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use App\Models\Ticket;
use App\Models\TicketsAccepted;
use App\Models\TicketResolved;

class ConcernTypeChart extends ChartWidget
{
    protected static ?string $heading = 'Concern Types';

    // Default filter to 'today'
    protected function getDefaultFilter(): ?string
    {
        return 'today';
    }

    // Define available filters
    protected function getFilters(): ?array
    {
        return [
            'today' => 'Today',
            'week' => 'This Week',
            'last_week' => 'Last Week',
            'month' => 'Last Month',
            'year' => 'This Year',
            'CRIM' => 'CRIM Department',
            'PSYCH' => 'PSYCH Department',
            'BS COMM' => 'BS COMM Department',
            'CEA' => 'CEA Department',
            'CONP' => 'CONP Department',
            'CITCLS' => 'CITCLS Department',
            'RSO' => 'RSO Department',
            'OFFICE' => 'OFFICE Department',
            'PPGS' => 'PPGS Department',
        ];
    }

    // Get the date range based on the selected filter
    protected function getFilterDateRange(): array
    {
        $filter = $this->filter ?? 'today';

        switch ($filter) {
            case 'today':
                return [now()->startOfDay(), now()->endOfDay()];
            case 'week':
                return [now()->startOfWeek(), now()->endOfWeek()];
            case 'last_week':
                return [
                    now()->subWeek()->startOfWeek(),
                    now()->subWeek()->endOfWeek(),
                ];
            case 'month':
                return [now()->subMonth()->startOfMonth(), now()->subMonth()->endOfMonth()];
            case 'year':
                return [now()->startOfYear(), now()->endOfYear()];
            default:
                return [now()->startOfYear(), now()->endOfYear()];
        }
    }

    protected function getData(): array
    {
        // Get the date range based on the filter
        [$startDate, $endDate] = $this->getFilterDateRange();

        // Initialize an array to hold the aggregated data
        $concernData = [];

        // Fetch concern data from Ticket
        $submittedConcerns = Ticket::query()
            ->select('concern_type', \DB::raw('count(*) as total'))
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('concern_type')
            ->get();

        // Fetch concern data from TicketsAccepted
        $acceptedConcerns = TicketsAccepted::query()
            ->select('concern_type', \DB::raw('count(*) as total'))
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('concern_type')
            ->get();

        // Fetch concern data from TicketResolved
        $resolvedConcerns = TicketResolved::query()
            ->select('concern_type', \DB::raw('count(*) as total'))
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('concern_type')
            ->get();

        // Aggregate data from all three models
        $aggregateConcerns = function ($dataCollection) use (&$concernData) {
            foreach ($dataCollection as $concern) {
                if (!isset($concernData[$concern->concern_type])) {
                    $concernData[$concern->concern_type] = 0;
                }
                $concernData[$concern->concern_type] += $concern->total;
            }
        };

        // Apply aggregation for each dataset
        $aggregateConcerns($submittedConcerns);
        $aggregateConcerns($acceptedConcerns);
        $aggregateConcerns($resolvedConcerns);

        // Prepare labels and values for the chart
        $concernLabels = array_keys($concernData);
        $concernValues = array_values($concernData);

        // Ensure that if no data was found, we still return a valid structure
        if (empty($concernValues)) {
            $concernValues = [0]; // Default to zero if there are no issues
            $concernLabels = ['No Data Available']; // Set a label to indicate no data
        }

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
        return 'doughnut'; // Doughnut chart for Concern Types
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
