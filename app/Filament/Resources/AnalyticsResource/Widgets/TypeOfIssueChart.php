<?php

namespace App\Filament\Resources\AnalyticsResource\Widgets;

use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use App\Models\Ticket;

class TypeOfIssueChart extends ChartWidget
{
    protected static ?string $heading = 'Issue Types';

    protected function getData(): array
    {
        //Dapat Lumitaw yung type of issue by concern type
        //Ibig sabihin dpt lumilitaw by Lab&Equip tsaka Facility
        //Kaso type of issue lng sa facility lumilitaw
        $issueData = Ticket::query()
            ->select('type_of_issue', \DB::raw('count(*) as total'))
            ->where('concern_type', 'Laboratory and Equipment')
            ->groupBy('type_of_issue')
            ->get();

        $facilityIssueData = Ticket::query()
            ->select('type_of_issue', \DB::raw('count(*) as total'))
            ->where('concern_type', 'Facility')
            ->groupBy('type_of_issue')
            ->get();

        $issueLabels = $issueData->pluck('type_of_issue')->merge($facilityIssueData->pluck('type_of_issue'));
        $issueValues = $issueData->pluck('total')->merge($facilityIssueData->pluck('total'));

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
