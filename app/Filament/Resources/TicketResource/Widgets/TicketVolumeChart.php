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
    protected int | string | array $columnSpan = 2;
    protected static ?string $heading = 'Ticket Volume';

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
            'SAS (AB COMM)' => 'AB COMM Department',
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
        if (!$concernType) {
            return []; // No data if user role does not match
        }

        // Base queries for created, accepted, and resolved tickets
        $createdTicketsQuery = Ticket::query()
            ->where('concern_type', $concernType)
            ->when($selectedDepartment && in_array($selectedDepartment, $this->getDepartmentFilters()), function ($query) use ($selectedDepartment) {
                return $query->where('department', $selectedDepartment);
            });

        $acceptedTicketsQuery = TicketsAccepted::query()
            ->where('concern_type', $concernType)
            ->when($selectedDepartment && in_array($selectedDepartment, $this->getDepartmentFilters()), function ($query) use ($selectedDepartment) {
                return $query->where('department', $selectedDepartment);
            });

        $resolvedTicketsQuery = TicketResolved::query()
            ->where('concern_type', $concernType)
            ->when($selectedDepartment && in_array($selectedDepartment, $this->getDepartmentFilters()), function ($query) use ($selectedDepartment) {
                return $query->where('department', $selectedDepartment);
            });

        // Determine aggregation method based on filter
        $aggregationMethod = ($this->filter === 'year' || in_array($selectedDepartment, $this->getDepartmentFilters())) ? 'perMonth' : 'perDay';

        // Aggregate data for created, accepted, and resolved tickets
        $createdTicketsData = Trend::query($createdTicketsQuery)
            ->between($startDate, $endDate)
            ->{$aggregationMethod}()
            ->count();

        $acceptedTicketsData = Trend::query($acceptedTicketsQuery)
            ->between($startDate, $endDate)
            ->{$aggregationMethod}()
            ->count();

        $resolvedTicketsData = Trend::query($resolvedTicketsQuery)
            ->between($startDate, $endDate)
            ->{$aggregationMethod}()
            ->count();

        // Combine all ticket data for total volume
        $totalTicketVolume = $createdTicketsData->map(function (TrendValue $created, $key) use ($acceptedTicketsData, $resolvedTicketsData) {
            return $created->aggregate
                + ($acceptedTicketsData[$key]->aggregate ?? 0)
                + ($resolvedTicketsData[$key]->aggregate ?? 0);
        });

        // Create labels based on aggregation method
        $labels = ($aggregationMethod === 'perMonth')
            ? $createdTicketsData->groupBy(fn($item) => \Carbon\Carbon::parse($item->date)->format('Y-m'))->keys()->map(fn($date) => \Carbon\Carbon::parse($date)->format('M Y'))
            : $createdTicketsData->map(fn(TrendValue $value) => \Carbon\Carbon::parse($value->date)->format('Y-m-d'));

        return [
            'datasets' => [
                [
                    'label' => "$concernType Tickets Volume",
                    'data' => $totalTicketVolume,
                    'backgroundColor' => $isEquipmentRole ? 'rgba(75, 192, 192, 0.2)' : 'rgba(255, 99, 132, 0.2)',
                    'borderColor' => $isEquipmentRole ? 'rgba(75, 192, 192, 1)' : 'rgba(255, 99, 132, 1)',
                    'borderWidth' => 2,
                    'fill' => true,
                    'tension' => 0.4,
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
                    'text' => 'Ticket Submission Volume',
                ],
            ],
        ];
    }
}
