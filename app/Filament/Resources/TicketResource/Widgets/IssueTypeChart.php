<?php

namespace App\Filament\Resources\TicketResource\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Ticket;
use Illuminate\Support\Collection;

class IssueTypeChart extends ChartWidget
{
    protected static ?string $heading = 'Issue Types';

    protected function getData(): array
    {
        // Fetch the data grouped by type_of_issue and department
        $issueData = Ticket::query()
            ->select('type_of_issue', 'department', \DB::raw('count(*) as total'))
            ->groupBy('type_of_issue', 'department')
            ->get();

        // Initialize collections for labels and values
        $issueLabels = Collection::make();
        $issueValues = Collection::make();

        // Organize the data for charting
        foreach ($issueData as $issue) {
            $issueLabels->push("{$issue->type_of_issue} - {$issue->department}");
            $issueValues->push($issue->total);
        }

        return [
            'datasets' => [
                [
                    'label' => 'Issue Types',
                    'data' => $issueValues,
                ],
            ],
            'labels' => $issueLabels,
        ];
    }

    protected function getType(): string
    {
        return 'bar'; // Bar chart for Issue Types
    }
}
