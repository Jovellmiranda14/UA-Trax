<?php

namespace App\Filament\Resources\TicketResource\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Ticket;
use App\Models\TicketsAccepted;
use App\Models\TicketResolved;
use Illuminate\Support\Collection;

class IssueTypeChart extends ChartWidget
{
    protected static ?string $heading = 'Issue Types by Department';

    protected array $issueTypeMap = [
        'computer_issues' => 'Computer Issues',
        'lab_equipment' => 'Laboratory Equipment Issues',
        // Add other mappings as needed
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

    // Initialize the data array to avoid undefined variable error
    $data = [];

    // Fetch data from Tickets
    $submittedIssues = Ticket::query()
        ->select('type_of_issue', 'department', 'concern_type', \DB::raw('count(*) as total'))
        ->whereBetween('created_at', [$startDate, $endDate])
        ->groupBy('type_of_issue', 'department', 'concern_type')
        ->get();

    // Fetch data from TicketsAccepted
    $acceptedIssues = TicketsAccepted::query()
        ->select('type_of_issue', 'department', 'concern_type', \DB::raw('count(*) as total'))
        ->whereBetween('created_at', [$startDate, $endDate])
        ->groupBy('type_of_issue', 'department', 'concern_type')
        ->get();

    // Fetch data from TicketResolved
    $resolvedIssues = TicketResolved::query()
        ->select('type_of_issue', 'department', 'concern_type', \DB::raw('count(*) as total'))
        ->whereBetween('created_at', [$startDate, $endDate])
        ->groupBy('type_of_issue', 'department', 'concern_type')
        ->get();

    // Initialize arrays to hold labels and values for the chart
    $issueLabels = [];
    $totalCounts = [];

    // Helper method to aggregate data
    $aggregateData = function ($dataCollection) use (&$totalCounts) {
        foreach ($dataCollection as $issue) {
            // Use the mapping to get a user-friendly issue type label
            $issueLabel = $this->issueTypeMap[$issue->type_of_issue] ?? $issue->type_of_issue; // Default to raw value if no mapping found

            // Create a unique key for each department and issue type
            $key = "{$issueLabel} - {$issue->department} ({$issue->concern_type})";

            if (!isset($totalCounts[$key])) {
                $totalCounts[$key] = 0;
            }

            $totalCounts[$key] += $issue->total;
        }
    };

    // Aggregate submitted, accepted, and resolved issues
    $aggregateData($submittedIssues);
    $aggregateData($acceptedIssues);
    $aggregateData($resolvedIssues);

    // Prepare the labels and data for the doughnut chart
    foreach ($totalCounts as $label => $count) {
        $issueLabels[] = $label;
        $data[] = $count; // Ensure this is populated even if no data exists
    }

    // Ensure that if no data was found, we still return a valid structure
    if (empty($data)) {
        $data = [0]; // Default to zero if there are no issues
        $issueLabels = ['No Data Available']; // Set a label to indicate no data
    }

    return [
        'labels' => $issueLabels,
        'datasets' => [
            [
                'data' => $data, // Now always defined
                'backgroundColor' => [
                    'rgba(54, 162, 235, 0.6)', // Submitted color
                    'rgba(255, 159, 64, 0.6)', // Accepted color
                    'rgba(75, 192, 192, 0.6)', // Resolved color
                    'rgba(153, 102, 255, 0.6)',
                    'rgba(255, 99, 132, 0.6)',
                ],
                'hoverBackgroundColor' => [
                    'rgba(54, 162, 235, 1)', // Submitted hover color
                    'rgba(255, 159, 64, 1)', // Accepted hover color
                    'rgba(75, 192, 192, 1)', // Resolved hover color
                    'rgba(153, 102, 255, 1)',
                    'rgba(255, 99, 132, 1)',
                ],
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
                    'position' => 'top',
                ],
                'tooltip' => [
                    'enabled' => true,
                ],
                'title' => [
                    'display' => true,
                    'text' => 'Issue Types Distribution',
                ],
            ],
        ];
    }

    public function getDescription(): string
    {
        [$startDate, $endDate] = $this->getFilterDateRange();
        return "Data from " . $startDate->format('Y-m-d') . " to " . $endDate->format('Y-m-d');
    }
}
