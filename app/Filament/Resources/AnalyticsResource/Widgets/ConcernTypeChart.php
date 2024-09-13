<?php

namespace App\Filament\Resources\AnalyticsResource\Widgets;

use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use App\Models\Ticket;

class ConcernTypeChart extends ChartWidget
{
    protected static ?string $heading = 'Concern Types';

    protected function getData(): array
    {
        //Dapat Lumitaw yung concern type sa Laboratory and Equipment pero facility lng lumilitaw
        $concernData = Ticket::query()
            ->select('concern_type', \DB::raw('count(*) as total'))
            ->groupBy('concern_type')
            ->get();

        $concernLabels = $concernData->pluck('concern_type');
        $concernValues = $concernData->pluck('total');

        return [
            'datasets' => [
                [
                    'label' => 'Concern Types',
                    'data' => $concernValues,
                ],
            ],
            'labels' => $concernLabels,
        ];
    }

    protected function getType(): string
    {
        return 'pie';
    }
}
