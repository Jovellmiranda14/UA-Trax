<?php

namespace App\Filament\Resources\TicketResource\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Ticket;
use App\Models\TicketsAccepted;
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
        $selectedDepartment = $this->filter;

        // Determine if the year filter is selected
        $isYearFilter = $this->filter === 'year';
        $isDepartmentFilter = in_array($selectedDepartment, $this->getDepartmentFilters());

        // Get the trend data for tickets based on the aggregation period
        $aggregationPeriod = $isYearFilter || $isDepartmentFilter ? 'month' : 'day';

        $labEquipmentData = Trend::query(
            Ticket::query()
                ->where('concern_type', 'Laboratory and Equipment')
                ->when($isDepartmentFilter, function ($query) use ($selectedDepartment) {
                    return $query->where('department', $selectedDepartment);
                })
        )
        ->between($startDate, $endDate)
        ->perDay($aggregationPeriod)
        ->count();

        $facilityData = Trend::query(
            Ticket::query()
                ->where('concern_type', 'Facility')
                ->when($isDepartmentFilter, function ($query) use ($selectedDepartment) {
                    return $query->where('department', $selectedDepartment);
                })
        )
        ->between($startDate, $endDate)
        ->perDay($aggregationPeriod)
        ->count();

        $acceptedLabEquipmentTickets = Trend::query(
            TicketsAccepted::query()
                ->where('concern_type', 'Laboratory and Equipment')
                ->when($isDepartmentFilter, function ($query) use ($selectedDepartment) {
                    return $query->where('department', $selectedDepartment);
                })
        )
        ->between($startDate, $endDate)
        ->perDay($aggregationPeriod)
        ->count();

        $acceptedFacilityTickets = Trend::query(
            TicketsAccepted::query()
                ->where('concern_type', 'Facility')
                ->when($isDepartmentFilter, function ($query) use ($selectedDepartment) {
                    return $query->where('department', $selectedDepartment);
                })
        )
        ->between($startDate, $endDate)
        ->perDay($aggregationPeriod)
        ->count();

        // Initialize arrays for combined data and labels
        $combinedLabEquipmentData = [];
        $combinedFacilityData = [];
        $labels = [];

        // Group data by month if filtering by year or department
        if ($isYearFilter || $isDepartmentFilter) {
            $labEquipmentData->groupBy(function ($value) {
                return \Carbon\Carbon::parse($value->date)->format('Y-m'); // Group by year-month
            })->each(function ($group) use (&$combinedLabEquipmentData, &$labels) {
                $combinedLabEquipmentData[] = $group->sum('aggregate'); // Sum for the month
                $labels[] = \Carbon\Carbon::parse($group->first()->date)->format('M Y'); // Store unique month-year
            });

            $facilityData->groupBy(function ($value) {
                return \Carbon\Carbon::parse($value->date)->format('Y-m'); // Group by year-month
            })->each(function ($group) use (&$combinedFacilityData) {
                $combinedFacilityData[] = $group->sum('aggregate'); // Sum for the month
            });
        } else {
            // Original logic for daily data
            $labels = $labEquipmentData->map(fn (TrendValue $value) => \Carbon\Carbon::parse($value->date)->format('Y-m-d'));
            $combinedLabEquipmentData = $labEquipmentData->map(fn (TrendValue $value) => $value->aggregate);
            $combinedFacilityData = $facilityData->map(fn (TrendValue $value) => $value->aggregate);
        }

        return [
            'datasets' => [
                [
                    'label' => 'Laboratory and Equipment Ticket Volume',
                    'data' => $combinedLabEquipmentData,
                    'backgroundColor' => 'rgba(75, 192, 192, 0.2)',
                    'borderColor' => 'rgba(75, 192, 192, 1)',
                    'borderWidth' => 2,
                    'fill' => true,
                    'tension' => 0.4, // Adds curve to the line
                ],
                [
                    'label' => 'Facility Ticket Volume',
                    'data' => $combinedFacilityData,
                    'backgroundColor' => 'rgba(255, 99, 132, 0.2)',
                    'borderColor' => 'rgba(255, 99, 132, 1)',
                    'borderWidth' => 2,
                    'fill' => true,
                    'tension' => 0.4, // Adds curve to the line
                ],
            ],
            'labels' => $labels,
        ];
    }

    // Helper method to get department filters
    protected function getDepartmentFilters(): array
    {
        return [
            'CRIM',
            'PSYCH',
            'BS COMM',
            'CEA',
            'CONP',
            'CITCLS',
            'RSO',
            'OFFICE',
            'PPGS',
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getOptions(): array
    {
        return [
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'title' => [
                        'display' => true,
                        'text' => 'Number of Tickets',
                    ],
                ],
                'x' => [
                    'title' => [
                        'display' => true,
                        'text' => 'Date',
                    ],
                ],
            ],
            'plugins' => [
                'legend' => [
                    'position' => 'top',
                ],
                'tooltip' => [
                    'enabled' => true,
                    'mode' => 'index',
                    'intersect' => false, // Makes the tooltip display on the entire line
                ],
                'title' => [
                    'display' => true,
                    'text' => 'Daily Ticket Submission Volume',
                ],
            ],
        ];
    }
}
