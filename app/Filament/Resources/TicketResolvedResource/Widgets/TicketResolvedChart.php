<?php

namespace App\Filament\Resources\TicketResolvedResource\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\TicketResolved;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class TicketResolvedChart extends ChartWidget
{
    protected int | string | array $columnSpan = 2;
    protected static ?string $heading = 'Ticket Resolved Volume';

    // Default filter to 'today'
    protected function getDefaultFilter(): ?string
    {
        return 'week';
    }

    protected function getFilters(): ?array
    {
        return [
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
                return [now()->startOfWeek(), now()->endOfWeek()];
        }
    }

    protected function getData(): array
    {
        [$startDate, $endDate] = $this->getFilterDateRange();
        $selectedDepartment = $this->filter;

        // Determine if the year filter is selected or if the selected filter is a department
        $isYearFilter = $this->filter === 'year';
        $isDepartmentFilter = in_array($selectedDepartment, $this->getDepartmentFilters());

        // Create base queries for resolved tickets
        $resolvedLabEquipmentQuery = TicketResolved::query()
            ->where('concern_type', 'Laboratory and Equipment')
            ->when($isDepartmentFilter, function ($query) use ($selectedDepartment) {
                return $query->where('department', $selectedDepartment);
            });

        $resolvedFacilityQuery = TicketResolved::query()
            ->where('concern_type', 'Facility')
            ->when($isDepartmentFilter, function ($query) use ($selectedDepartment) {
                return $query->where('department', $selectedDepartment);
            });

        // If it's the "This Year" or a department filter, aggregate data monthly
        if ($isYearFilter || $isDepartmentFilter) {
            $resolvedLabEquipmentData = Trend::query($resolvedLabEquipmentQuery)
                ->between($startDate, $endDate)
                ->perMonth() // Aggregate by month
                ->count();

            $resolvedFacilityData = Trend::query($resolvedFacilityQuery)
                ->between($startDate, $endDate)
                ->perMonth() // Aggregate by month
                ->count();
        } else {
            // For other filters, aggregate by day
            $resolvedLabEquipmentData = Trend::query($resolvedLabEquipmentQuery)
                ->between($startDate, $endDate)
                ->perDay() // Aggregate by day
                ->count();

            $resolvedFacilityData = Trend::query($resolvedFacilityQuery)
                ->between($startDate, $endDate)
                ->perDay() // Aggregate by day
                ->count();
        }

        // Create unique labels for the data
        $labels = ($isYearFilter || $isDepartmentFilter)
            ? $resolvedLabEquipmentData->groupBy(function ($item) {
                return \Carbon\Carbon::parse($item->date)->format('Y-m'); // Group by year and month
            })->keys()->map(fn($date) => \Carbon\Carbon::parse($date)->format('M Y')) // Format to "M Y"
            : $resolvedLabEquipmentData->map(fn (TrendValue $value) => \Carbon\Carbon::parse($value->date)->format('Y-m-d')); // Daily labels

        return [
            'datasets' => [
                [
                    'label' => 'Laboratory and Equipment Resolved Tickets',
                    'data' => $resolvedLabEquipmentData->map(fn (TrendValue $value) => $value->aggregate),
                    'backgroundColor' => 'rgba(75, 192, 192, 0.2)',
                    'borderColor' => 'rgba(75, 192, 192, 1)',
                    'borderWidth' => 2,
                    'fill' => true,
                    'tension' => 0.4, // Adds curve to the line
                ],
                [
                    'label' => 'Facility Resolved Tickets',
                    'data' => $resolvedFacilityData->map(fn (TrendValue $value) => $value->aggregate),
                    'backgroundColor' => 'rgba(255, 99, 132, 0.2)',
                    'borderColor' => 'rgba(255, 99, 132, 1)',
                    'borderWidth' => 2,
                    'fill' => true,
                    'tension' => 0.4, // Adds curve to the line
                ],
            ],
            'labels' => $labels, // Use the unique labels
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
                        'text' => 'Number of Resolved Tickets',
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
                    'text' => 'Tickets Resolved Volume',
                ],
            ],
        ];
    }
}
