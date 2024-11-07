<?php

namespace App\Filament\Resources\TicketResource\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Ticket;
use Illuminate\Support\Facades\Auth;

class OfficeIssueChart extends ChartWidget
{
    protected static ?string $heading = 'Office Issues by Department';

    // Issue types will be mapped for the chart
    protected array $issueTypeMap = [
        'Other_Devices' => 'Other Devices',
        'computer_issues' => 'Computer Issues',
        'lab_equipment' => 'Lab Equipment',
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
        // Restrict access to users with the 'equipment_admin_labcustodian' or 'equipment_superadmin' role
        if (!in_array(Auth::user()->role, ['equipment_admin_omiss', 'equipmentsuperadmin'])) {
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

        // Filter tickets for "Office" issues and by department, grouped by issue type
        $submittedIssues = Ticket::query()
            ->select('type_of_issue', \DB::raw('count(*) as total'))
            ->where('department', 'Office')
            ->whereIn('type_of_issue', ['Other_Devices', 'computer_issues', 'lab_equipment'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('type_of_issue')
            ->get();

        // Prepare data for the chart
        $issueLabels = [];
        $data = [];
        $colors = [
            'rgba(54, 162, 235, 0.6)', // Blue
            'rgba(255, 159, 64, 0.6)', // Orange
            'rgba(75, 192, 192, 0.6)', // Green
        ];

        foreach ($submittedIssues as $index => $issue) {
            $issueLabels[] = $this->issueTypeMap[$issue->type_of_issue] ?? 'Unknown Issue Type';
            $data[] = $issue->total;
        }

        // Ensure that if no data was found, a valid structure is still returned
        if (empty($data)) {
            $data = [0];
            $issueLabels = ['No Data Available'];
        }

        return [
            'labels' => $issueLabels,
            'datasets' => [
                [
                    'data' => $data,
                    'backgroundColor' => $colors,
                    'hoverBackgroundColor' => array_map(fn($color) => str_replace('0.6', '1', $color), $colors),
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
            'plugins' => [
                'legend' => [
                    'position' => 'bottom',
                ],
                'tooltip' => [
                    'enabled' => true,
                ],
                'title' => [
                    'display' => true,
                    'text' => 'Office Issues',
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

    // Make this method public as it is required to be accessed by Filament
    public static function canView(): bool
    {
        // Allow view for both equipment_admin_labcustodian and equipment_superadmin roles
        return in_array(Auth::user()->role, ['equipment_admin_omiss', 'equipmentsuperadmin']);
    }
}
