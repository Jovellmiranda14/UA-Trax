<?php

namespace App\Filament\Resources\TicketResource\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Ticket;
use App\Models\TicketsAccepted;
use App\Models\TicketResolved;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use App\Models\User;

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
        // Retrieve and validate the date range from filters
        [$startDate, $endDate] = $this->getFilterDateRange();

        // Ensure start and end dates are set properly to avoid uninitialized access
        if (!$startDate || !$endDate) {
            // Default to the last 7 days if no valid date range is provided
            $startDate = now()->subDays(7);
            $endDate = now();
        }

        $selectedDepartment = $this->filter;
        $user = auth()->user();

        // Check user roles
        $isEquipmentRole = in_array($user->role, [
            User::EquipmentSUPER_ADMIN,
            User::EQUIPMENT_ADMIN_Omiss,
            User::EQUIPMENT_ADMIN_labcustodian,
        ]);
        $isFacilityRole = in_array($user->role, [
            User::FacilitySUPER_ADMIN,
            User::FACILITY_ADMIN,
        ]);

        // Determine concern type based on role
        $concernType = $isEquipmentRole ? 'Laboratory and Equipment' : ($isFacilityRole ? 'Facility' : null);
        if (!$concernType && $user->role !== User::REGULAR_USER) {
            return []; // Return empty data if no matching concern type
        }

        // Base query for tickets, filtering by user `name` for regular users or by concern type for admins
        $query = Ticket::query()
            ->when($selectedDepartment && in_array($selectedDepartment, $this->getDepartmentFilters()), function ($query) use ($selectedDepartment) {
                return $query->where('department', $selectedDepartment);
            })
            ->when($user->role === User::REGULAR_USER, function ($query) use ($user) {
                return $query->where('name', $user->name);
            }, function ($query) use ($concernType) {
                return $query->where('concern_type', $concernType);
            });

        // Determine aggregation method
        $aggregationMethod = ($this->filter === 'year' || in_array($selectedDepartment, $this->getDepartmentFilters())) ? 'perMonth' : 'perDay';

        // Aggregate data for total ticket volume using Trend
        $ticketData = Trend::query($query)
            ->between($startDate, $endDate) // Set the date range explicitly
            ->{$aggregationMethod}('created_at')
            ->count();

        // Format labels based on aggregation
        $labels = $ticketData->map(fn(TrendValue $value) => \Carbon\Carbon::parse($value->date)->format($aggregationMethod === 'perMonth' ? 'M Y' : 'M j, Y'));

        // Set the color based on user role
        if ($user->role === User::REGULAR_USER) {
            $lineColor = 'rgba(255, 255, 0, 0.2)'; // Yellow for Regular Users
            $borderColor = 'rgba(255, 255, 0, 1)';
        } elseif (in_array($user->role, [User::FacilitySUPER_ADMIN, User::FACILITY_ADMIN])) {
            $lineColor = 'rgba(255, 0, 0, 0.2)'; // Red for Facility Super Admin and Admin
            $borderColor = 'rgba(255, 0, 0, 1)';
        } else {
            $lineColor = 'rgba(75, 192, 192, 0.2)'; // Default color for Equipment Roles
            $borderColor = 'rgba(75, 192, 192, 1)';
        }

        return [
            'datasets' => [
                [
                    'label' => $concernType ? "$concernType Tickets Volume" : "User Ticket Volume",
                    'data' => $ticketData->pluck('aggregate'),
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
            'SAS (AB COMM)',
            'SAS (PSYCH)',
            'CEA',
            'CONP',
            'CITCLS',
            'RSO',
            'OFFICE',
        ];
    }

    protected function getType(): string
    {
        return 'bar';
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
                    'intersect' => false,
                ],
                'title' => [
                    'display' => true,
                    'text' => 'Ticket Submission Volume',
                ],
            ],
        ];
    }
}
