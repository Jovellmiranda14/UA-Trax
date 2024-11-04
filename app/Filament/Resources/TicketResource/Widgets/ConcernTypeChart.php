<?php

namespace App\Filament\Resources\TicketResource\Widgets;

use Filament\Widgets\ChartWidget;
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
        [$startDate, $endDate] = $this->getFilterDateRange();

        // Initialize the data array
        $data = [];

        // Fetch data from Ticket
        $submittedConcerns = Ticket::query()
            ->select('concern_type', \DB::raw('count(*) as total'))
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('concern_type')
            ->get();

        // Fetch data from TicketsAccepted
        $acceptedConcerns = TicketsAccepted::query()
            ->select('concern_type', \DB::raw('count(*) as total'))
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('concern_type')
            ->get();

        // Fetch data from TicketResolved
        $resolvedConcerns = TicketResolved::query()
            ->select('concern_type', \DB::raw('count(*) as total'))
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('concern_type')
            ->get();

        // Initialize array to hold labels and values
        $concernLabels = [];
        $totalCounts = [];

        // Helper method to aggregate data
        $aggregateData = function ($dataCollection) use (&$totalCounts) {
            foreach ($dataCollection as $concern) {
                if (!isset($totalCounts[$concern->concern_type])) {
                    $totalCounts[$concern->concern_type] = 0;
                }
                $totalCounts[$concern->concern_type] += $concern->total;
            }
        };

        // Aggregate submitted, accepted, and resolved concerns
        $aggregateData($submittedConcerns);
        $aggregateData($acceptedConcerns);
        $aggregateData($resolvedConcerns);

        // Prepare labels and data for the chart
        foreach ($totalCounts as $label => $count) {
            $concernLabels[] = $label;
            $data[] = $count; // Ensure this is populated even if no data exists
        }

        // Ensure that if no data was found, we still return a valid structure
        if (empty($data)) {
            $data = [0]; // Default to zero if there are no issues
            $concernLabels = ['No Data Available']; // Set a label to indicate no data
        }

        return [
            'labels' => $concernLabels,
            'datasets' => [
                [
                    'data' => $data, // Now always defined
                    'backgroundColor' => [
                        '#FF6384', // Color for submitted
                        '#36A2EB', // Color for accepted
                        '#FFCE56', // Color for resolved
                    ],
                    'hoverBackgroundColor' => [
                        '#FF6384',
                        '#36A2EB',
                        '#FFCE56',
                    ],
                ],
            ],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut'; // Set to doughnut chart
    }

    protected function getOptions(): array
    {
        return [
            'responsive' => true,
            'plugins' => [
                [
                    'legend' => [
                        'display' => true,
                        'position' => 'top',
                    ],
                ],
                [
                    'tooltip' => [
                        'enabled' => true,
                    ],
                ],
                [
                    'title' => [
                        'display' => true,
                        'text' => 'Concern Types Distribution',
                    ],
                ],
            ],
        ];
    }

    // Method to get the description with date range
    public function getDescription(): string
    {
        [$startDate, $endDate] = $this->getFilterDateRange();
        return "Data from " . $startDate->format('Y-m-d') . " to " . $endDate->format('Y-m-d');
    }
}
