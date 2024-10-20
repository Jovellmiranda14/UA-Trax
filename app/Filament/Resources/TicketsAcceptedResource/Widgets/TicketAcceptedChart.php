<?php

namespace App\Filament\Resources\TicketsAcceptedResource\Widgets;

use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use App\Models\TicketsAccepted;
use App\Models\TicketResolved;

class TicketAcceptedChart extends ChartWidget
{
    protected static ?string $heading = 'Tickets Accepted';

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
                return [now()->startOfDay(), now()->endOfDay()]; // Fallback
        }
    }

    protected function getData(): array
    {
        [$startDate, $endDate] = $this->getFilterDateRange();
        $selectedDepartment = $this->filter;

        // Determine if we're filtering by year
        $isYearFilter = $this->filter === 'year';
        $isDepartmentFilter = in_array($selectedDepartment, $this->getDepartmentFilters());

        // Set the time period for data aggregation
        $timePeriod = $isYearFilter ? 'perMonth' : 'perDay';

        // Lab Equipment Data
        $labEquipmentData = Trend::query(
            TicketsAccepted::query()
                ->where('concern_type', 'Laboratory and Equipment')
                ->when($isDepartmentFilter, function ($query) use ($selectedDepartment) {
                    return $query->where('department', $selectedDepartment);
                })
        )
        ->between($startDate, $endDate)
        ->$timePeriod()
        ->count();

        // Facility Data
        $facilityData = Trend::query(
            TicketsAccepted::query()
                ->where('concern_type', 'Facility')
                ->when($isDepartmentFilter, function ($query) use ($selectedDepartment) {
                    return $query->where('department', $selectedDepartment);
                })
        )
        ->between($startDate, $endDate)
        ->$timePeriod()
        ->count();

        // Prepare resolved ticket data for both categories
        $resolvedLabEquipmentData = Trend::query(
            TicketResolved::query()
                ->where('concern_type', 'Laboratory and Equipment')
                ->when($isDepartmentFilter, function ($query) use ($selectedDepartment) {
                    return $query->where('department', $selectedDepartment);
                })
        )
        ->between($startDate, $endDate)
        ->$timePeriod()
        ->count();

        $resolvedFacilityData = Trend::query(
            TicketResolved::query()
                ->where('concern_type', 'Facility')
                ->when($isDepartmentFilter, function ($query) use ($selectedDepartment) {
                    return $query->where('department', $selectedDepartment);
                })
        )
        ->between($startDate, $endDate)
        ->$timePeriod()
        ->count();

        // Prepare labels based on the selected time period
        $labels = [];
        if ($isYearFilter || $isDepartmentFilter) {
            // We will use the data's date for the labels
            $labEquipmentData->each(function (TrendValue $value) use (&$labels) {
                $date = \Carbon\Carbon::parse($value->date)->format('M Y');
                if (!in_array($date, $labels)) {
                    $labels[] = $date; // Unique month/year labels
                }
            });
        } else {
            $labels = $labEquipmentData->map(function (TrendValue $value) {
                return \Carbon\Carbon::parse($value->date)->format('Y-m-d');
            });
        }

        // Combine the data
        return [
            'datasets' => [
                [
                    'label' => 'Accepted Tickets - Laboratory and Equipment',
                    'data' => $labEquipmentData->map(function (TrendValue $value) use ($resolvedLabEquipmentData) {
                        $resolved = $resolvedLabEquipmentData->firstWhere('date', $value->date);
                        return $value->aggregate + ($resolved->aggregate ?? 0);
                    }),
                    'backgroundColor' => 'rgba(75, 192, 192, 0.6)',
                    'borderColor' => 'rgba(75, 192, 192, 1)',
                    'borderWidth' => 2,
                ],
                [
                    'label' => 'Accepted Tickets - Facility',
                    'data' => $facilityData->map(function (TrendValue $value) use ($resolvedFacilityData) {
                        $resolved = $resolvedFacilityData->firstWhere('date', $value->date);
                        return $value->aggregate + ($resolved->aggregate ?? 0);
                    }),
                    'backgroundColor' => 'rgba(255, 99, 132, 0.6)',
                    'borderColor' => 'rgba(255, 99, 132, 1)',
                    'borderWidth' => 2,
                ],
            ],
            'labels' => $labels,
        ];
    }

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
                        'text' => 'Number of Accepted Tickets',
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
                ],
                'title' => [
                    'display' => true,
                    'text' => 'Ticket Volume Overview',
                ],
            ],
        ];
    }
}
