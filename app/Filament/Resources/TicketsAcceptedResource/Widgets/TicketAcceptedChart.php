<?php

namespace App\Filament\Resources\TicketsAcceptedResource\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\TicketsAccepted;
use App\Models\TicketResolved;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use App\Models\User;

class TicketAcceptedChart extends ChartWidget
{
    protected static ?string $heading = 'Tickets Accepted Volume';

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
    $user = auth()->user();

    // Check user roles to determine concern type
    $isEquipmentRole = in_array($user->role, [
        User::EquipmentSUPER_ADMIN,
        User::EQUIPMENT_ADMIN_Omiss,
        User::EQUIPMENT_ADMIN_labcustodian,
    ]);

    $isFacilityRole = in_array($user->role, [
        User::FacilitySUPER_ADMIN,
        User::FACILITY_ADMIN,
    ]);

    $isRegularUser = $user->role === User::REGULAR_USER;

    // Determine concern type based on role
    $concernType = null;
    if ($isEquipmentRole) {
        $concernType = 'Laboratory and Equipment';
    } elseif ($isFacilityRole) {
        $concernType = 'Facility';
    }

    // If the user is a regular user, no need to filter by concern type
    if (!$concernType && !$isRegularUser) {
        return []; // No data if user role does not match
    }

    // Base queries for accepted and resolved tickets
    $acceptedTicketsQuery = TicketsAccepted::query()
        ->when($isRegularUser, function ($query) use ($selectedDepartment) {
            // For regular users, don't filter by concern_type, only by department
            return $query->when($selectedDepartment && in_array($selectedDepartment, $this->getDepartmentFilters()), function ($query) use ($selectedDepartment) {
                return $query->where('department', $selectedDepartment);
            });
        })
        ->when($concernType, function ($query) use ($concernType) {
            // For equipment or facility roles, filter by concern_type
            return $query->where('concern_type', $concernType);
        });

    $resolvedTicketsQuery = TicketResolved::query()
        ->when($isRegularUser, function ($query) use ($selectedDepartment) {
            // For regular users, don't filter by concern_type, only by department
            return $query->when($selectedDepartment && in_array($selectedDepartment, $this->getDepartmentFilters()), function ($query) use ($selectedDepartment) {
                return $query->where('department', $selectedDepartment);
            });
        })
        ->when($concernType, function ($query) use ($concernType) {
            // For equipment or facility roles, filter by concern_type
            return $query->where('concern_type', $concernType);
        });

    // Determine aggregation method based on filter
    $aggregationMethod = ($this->filter === 'year' || in_array($selectedDepartment, $this->getDepartmentFilters())) ? 'perMonth' : 'perDay';

    // Aggregate data for accepted and resolved tickets
    $acceptedTicketsData = Trend::query($acceptedTicketsQuery)
        ->between($startDate, $endDate)
        ->{$aggregationMethod}()
        ->count();

    $resolvedTicketsData = Trend::query($resolvedTicketsQuery)
        ->between($startDate, $endDate)
        ->{$aggregationMethod}()
        ->count();

    // Combine accepted and resolved data for total volume
    $totalTicketVolume = $acceptedTicketsData->map(function (TrendValue $accepted, $key) use ($resolvedTicketsData) {
        return $accepted->aggregate + ($resolvedTicketsData[$key]->aggregate ?? 0);
    });

    // Create labels based on aggregation method
    $labels = ($aggregationMethod === 'perMonth')
    ? $acceptedTicketsData->groupBy(fn($item) => \Carbon\Carbon::parse($item->date)->format('Y-m'))->keys()->map(fn($date) => \Carbon\Carbon::parse($date)->format('M Y'))
    : $acceptedTicketsData->map(fn(TrendValue $value) => \Carbon\Carbon::parse($value->date)->format('M j, Y'));


    // Determine the line color based on user role
    if ($isRegularUser) {
        $lineColor = 'rgba(255, 255, 0, 0.2)'; // Yellow for Regular Users
        $borderColor = 'rgba(255, 255, 0, 1)';
    } elseif ($isFacilityRole) {
        $lineColor = 'rgba(255, 0, 0, 0.2)'; // Red for Facility Super Admin and Admin
        $borderColor = 'rgba(255, 0, 0, 1)';
    } else {
        $lineColor = 'rgba(75, 192, 192, 0.2)'; // Default color for Equipment Roles
        $borderColor = 'rgba(75, 192, 192, 1)';
    }

    return [
        'datasets' => [
            [
                'label' => "$concernType Tickets Accepted Volume",
                'data' => $totalTicketVolume,
                'backgroundColor' => $lineColor,
                'borderColor' => $borderColor,
                'borderWidth' => 2,
                'fill' => true,
                'tension' => 0.4, // Adds curve to the line
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
        return 'bar';
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
                    'position' => 'bottom',
                ],
                'tooltip' => [
                    'enabled' => true,
                    'mode' => 'index',
                    'intersect' => false, // Makes the tooltip display on the entire line
                ],
                'title' => [
                    'display' => true,
                    'text' => 'Ticket Accepted Volume',
                ],
            ],
        ];
    }
}
