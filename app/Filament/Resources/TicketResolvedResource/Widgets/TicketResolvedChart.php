<?php

namespace App\Filament\Resources\TicketResolvedResource\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\TicketResolved;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use App\Models\User;

class TicketResolvedChart extends ChartWidget
{
    protected static ?string $heading = 'Ticket Resolved Volume';

    // Default filter to 'week'
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
            'SAS (CRIM)' => 'CRIM Department',
            'SAS (PSYCH)' => 'PSYCH Department',
            'SAS (AB COMM)' => 'AB COMM Department',
            'CEA' => 'CEA Department',
            'CONP' => 'CONP Department',
            'CITCLS' => 'CITCLS Department',
            'RSO' => 'RSO Department',
            'OFFICE' => 'OFFICE Department',
        ];
    }

    protected function getFilterDateRange(): array
    {
        $filter = $this->filter ?? 'week';

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
            User::equipment_superadmin,
            User::equipment_admin_omiss,
            User::equipment_admin_labcustodian,
        ]);

        $isFacilityRole = in_array($user->role, [
            User::facility_super_admin,
            User::facility_admin,
        ]);

        $isRegularUser = $user->role === User::regular_user;

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

        // Base query for resolved tickets
        $resolvedTicketsQuery = TicketResolved::query()
            ->when($concernType, function ($query) use ($concernType) {
                return $query->where('concern_type', $concernType);
            })
            ->when($selectedDepartment && in_array($selectedDepartment, $this->getDepartmentFilters()), function ($query) use ($selectedDepartment) {
                return $query->where('department', $selectedDepartment);
            });

        // For regular users, only filter by department if selected
        if ($isRegularUser) {
            $resolvedTicketsQuery = $resolvedTicketsQuery->when($selectedDepartment && in_array($selectedDepartment, $this->getDepartmentFilters()), function ($query) use ($selectedDepartment) {
                return $query->where('department', $selectedDepartment);
            });
        }

        // Determine aggregation method based on filter
        $aggregationMethod = ($this->filter === 'year' || in_array($selectedDepartment, $this->getDepartmentFilters())) ? 'perMonth' : 'perDay';

        // Aggregate data for resolved tickets
        $resolvedTicketsData = Trend::query($resolvedTicketsQuery)
            ->between($startDate, $endDate)
            ->{$aggregationMethod}()
            ->count();

        // Combine data into total resolved tickets volume
        $totalResolvedVolume = $resolvedTicketsData->map(fn(TrendValue $value) => $value->aggregate);

        // Create labels based on aggregation method
        $labels = ($aggregationMethod === 'perMonth')
        ? $resolvedTicketsData->groupBy(fn($item) => \Carbon\Carbon::parse($item->date)->format('Y-m'))->keys()->map(fn($date) => \Carbon\Carbon::parse($date)->format('M Y'))
        : $resolvedTicketsData->map(fn(TrendValue $value) => \Carbon\Carbon::parse($value->date)->format('M j, Y'));

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
                    'label' => "$concernType Resolved Tickets Volume",
                    'data' => $totalResolvedVolume,
                    'backgroundColor' => $lineColor,
                    'borderColor' => $borderColor,
                    'borderWidth' => 2,
                    'fill' => true,
                    'tension' => 0.4,
                ],
            ],
            'labels' => $labels,
        ];
    }

    // Get department filters
    protected function getDepartmentFilters(): array
    {
        return [
            'SAS (CRIM)',
            'SAS (PSYCH)',
            'SAS (AB COMM)',
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
            'aspectRatio' => 1.5,
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
                    'position' => 'bottom',
                ],
                'tooltip' => [
                    'enabled' => true,
                    'mode' => 'index',
                    'intersect' => false,
                ],
                'title' => [
                    'display' => true,
                    'text' => 'Tickets Resolved Volume',
                ],
            ],
        ];
    }
}
