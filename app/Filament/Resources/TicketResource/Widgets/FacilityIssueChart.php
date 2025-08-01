<?php

namespace App\Filament\Resources\TicketResource\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Ticket;
use App\Models\TicketsAccepted;
use App\Models\TicketResolved;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class FacilityIssueChart extends ChartWidget
{
    protected static ?string $heading = 'Facility Issues by Department';

    protected array $issueTypeMap = [
        'repair' => 'Repair Issues',
        'air_conditioning' => 'Air Conditioning Issues',
        'plumbing' => 'Plumbing Issues',
        'lighting' => 'Lighting Issues',
        'electricity' => 'Electricity Issues',
    ];

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
        //Default today filter
        $this->filter = $this->filter ?? 'today';

        switch ($this->filter) {
            case 'today':
                return [now()->startOfDay(), now()->endOfDay()];
            case 'week':
                return [now()->startOfWeek(), now()->endOfWeek()];
            case 'last_week':
                return [now()->subWeek()->startOfWeek(), now()->subWeek()->endOfWeek()];
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
        // Restrict access to users with the 'facilityadmin' or 'facilitiesuperadmin' role
        if (!in_array(Auth::user()->role, ['facility_admin', 'facilitysuperadmin'])) {
            return [
                'labels' => ['No Access'],
                'datasets' => [
                    [
                        'data' => [0],
                        'backgroundColor' => ['rgba(211, 211, 211, 0.6)'],
                    ],
                ],
            ];
        }

        [$startDate, $endDate] = $this->getFilterDateRange();

        // Filter tickets for facility-related issues
        $submittedIssues = Ticket::query()
            ->select('type_of_issue', 'department', \DB::raw('count(*) as total'))
            ->whereIn('type_of_issue', array_keys($this->issueTypeMap))
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('type_of_issue', 'department')
            ->get();

        // Map colors to departments
        $departmentColors = [
            'CEA' => 'rgba(207, 32, 43, 1)',
            'CITCLS' => 'rgba(77, 104, 201, 1)',
            'CONP' => 'rgba(73, 184, 71, 1)',
            'SAS (AB COMM)' => 'rgba(230, 175, 1, 1)',
            'SAS (CRIM)' => 'rgba(230, 175, 100, 1)',
            'SAS (PSYCH)' => 'rgba(230, 175, 70, 1)',
            'OFFICE' => 'rgba(103, 177, 209, 1)',
        ];

        // Prepare data for the chart
        $issueLabels = [];
        $data = [];
        $colors = [];

        foreach ($submittedIssues as $issue) {
            $issueLabel = $this->issueTypeMap[$issue->type_of_issue] ?? $issue->type_of_issue;
            $issueLabels[] = "{$issueLabel} - {$issue->department}";
            $data[] = $issue->total;

            // Use the mapped color for each department, or default color if not defined
            $colors[] = $departmentColors[$issue->department] ?? 'rgba(100, 100, 100, 0.6)';
        }

        // Ensure that if no data was found, a valid structure is still returned
        if (empty($data)) {
            $data = [0];
            $issueLabels = ['No Data Available'];
            $colors = ['rgba(211, 211, 211, 0.6)'];
        }

        return [
            'labels' => $issueLabels,
            'datasets' => [
                [
                    'data' => $data,
                    'backgroundColor' => $colors,
                    'hoverBackgroundColor' => $colors,
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
            'aspectRatio' => 1.61,
            'plugins' => [
                'legend' => [
                    'position' => 'bottom',
                ],
                'tooltip' => [
                    'enabled' => true,
                ],
                'title' => [
                    'display' => true,
                    'text' => 'Facility Issues',
                ],
            ],
        ];
    }

    public function getDescription(): string
    {
        [$startDate, $endDate] = $this->getFilterDateRange();

        // Show a single date for the 'today' filter
        if ($this->filter === 'today') {
            return "Data from " . $startDate->format('M j, Y');
        }

        // Show a date range for other filters
        return "Data from " . $startDate->format('M j, Y') . " to " . $endDate->format('M j, Y');
    }

    public static function canView(): bool
    {
        // Allow view for both equipment_admin_labcustodian and equipment_superadmin roles
        return in_array(Auth::user()->role, ['facility_admin', 'facilitysuperadmin']);
    }
}
