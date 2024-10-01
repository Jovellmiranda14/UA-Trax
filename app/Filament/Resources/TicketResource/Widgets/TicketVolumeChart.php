<?php

namespace App\Filament\Resources\TicketResource\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Ticket;
use App\Models\TicketsAccepted; // Add this model if accepted tickets are tracked here
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class TicketVolumeChart extends ChartWidget
{
    protected static ?string $heading = 'Ticket Volume';

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
        ];
    }

    protected function getFilterDateRange(): array
    {
        // Get the current filter or use 'today' as the default
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
            default:
                return [now()->startOfYear(), now()->endOfYear()];
        }
    }

    protected function getData(): array
    {
        [$startDate, $endDate] = $this->getFilterDateRange();

        // Query for Laboratory and Equipment tickets (submitted tickets)
        $labEquipmentData = Trend::query(
            Ticket::query()
                ->where('concern_type', 'Laboratory and Equipment')
                ->when(request('department'), function ($query) {
                    return $query->where('department', request('department'));
                })
        )
        ->between($startDate, $endDate)
        ->perDay()
        ->count();

        // Query for Facility tickets (submitted tickets)
        $facilityData = Trend::query(
            Ticket::query()
                ->where('concern_type', 'Facility')
        )
        ->between($startDate, $endDate)
        ->perDay()
        ->count();

        // Query for accepted Laboratory and Equipment tickets from the TicketsAccepted table
        $acceptedLabEquipmentTickets = Trend::query(
            TicketsAccepted::query()
                ->where('concern_type', 'Laboratory and Equipment') // Adjust field if necessary
        )
        ->between($startDate, $endDate)
        ->perDay()
        ->count();

        // Query for accepted Facility tickets from the TicketsAccepted table
        $acceptedFacilityTickets = Trend::query(
            TicketsAccepted::query()
                ->where('concern_type', 'Facility') // Adjust field if necessary
        )
        ->between($startDate, $endDate)
        ->perDay()
        ->count();

        // Combine Laboratory and Equipment tickets (submitted + accepted)
        $combinedLabEquipmentData = $labEquipmentData->map(function (TrendValue $value, $key) use ($acceptedLabEquipmentTickets) {
            return $value->aggregate + ($acceptedLabEquipmentTickets[$key]->aggregate ?? 0);
        });

        // Combine Facility tickets (submitted + accepted)
        $combinedFacilityData = $facilityData->map(function (TrendValue $value, $key) use ($acceptedFacilityTickets) {
            return $value->aggregate + ($acceptedFacilityTickets[$key]->aggregate ?? 0);
        });

        return [
            'datasets' => [
                [
                    'label' => 'Laboratory and Equipment Ticket Volume',
                    'data' => $combinedLabEquipmentData,
                    'backgroundColor' => 'rgba(75, 192, 192, 0.2)', // Light cyan background
                    'borderColor' => 'rgba(75, 192, 192, 1)', // Cyan border
                    'borderWidth' => 2,
                    'fill' => true,
                ],
                [
                    'label' => 'Facility Ticket Volume',
                    'data' => $combinedFacilityData,
                    'backgroundColor' => 'rgba(255, 99, 132, 0.2)', // Light red background
                    'borderColor' => 'rgba(255, 99, 132, 1)', // Red border
                    'borderWidth' => 2,
                    'fill' => true,
                ],
            ],
            'labels' => $labEquipmentData->map(fn (TrendValue $value) => $value->date),
        ];
    }

    protected function getType(): string
    {
        return 'line'; // Keep it as line chart for smooth transitions
    }

    protected function getOptions(): array
    {
        return [
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'title' => [
                        'display' => true,
                        'text' => 'Number of Tickets', // Title for the Y-axis
                    ],
                ],
                'x' => [
                    'title' => [
                        'display' => true,
                        'text' => 'Date', // Title for the X-axis
                    ],
                ],
            ],
            'plugins' => [
                'legend' => [
                    'position' => 'top', // Position of the legend
                ],
                'tooltip' => [
                    'enabled' => true, // Show tooltips on hover
                    'mode' => 'index', // Show tooltips for all datasets at the same index
                ],
                'title' => [
                    'display' => true,
                    'text' => 'Daily Ticket Submission Volume', // Main title for the chart
                ],
            ],
        ];
    }
}
