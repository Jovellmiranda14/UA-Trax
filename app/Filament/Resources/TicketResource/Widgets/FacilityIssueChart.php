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
        $filter = $this->filter ?? 'today';

        switch ($filter) {
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
            ->whereIn('type_of_issue', array_keys($this->issueTypeMap))  // Filter for the facility issues
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('type_of_issue', 'department')
            ->get();

        // Prepare data for the chart
        $issueLabels = [];
        $data = [];

        foreach ($submittedIssues as $issue) {
            $issueLabel = $this->issueTypeMap[$issue->type_of_issue] ?? $issue->type_of_issue;
            $issueLabels[] = "{$issueLabel} - {$issue->department}";
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
                    'backgroundColor' => [
                        'rgba(54, 162, 235, 0.6)',
                        'rgba(255, 159, 64, 0.6)',
                        'rgba(75, 192, 192, 0.6)',
                        'rgba(153, 102, 255, 0.6)',
                        'rgba(255, 99, 132, 0.6)',
                    ],
                    'hoverBackgroundColor' => [
                        'rgba(54, 162, 235, 1)',
                        'rgba(255, 159, 64, 1)',
                        'rgba(75, 192, 192, 1)',
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
                    'text' => 'Facility Issues by Department',
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
