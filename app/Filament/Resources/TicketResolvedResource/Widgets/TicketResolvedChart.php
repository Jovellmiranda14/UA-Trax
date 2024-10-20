<?php

namespace App\Filament\Resources\TicketResolvedResource\Widgets;

use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use App\Models\TicketResolved;

class TicketResolvedChart extends ChartWidget
{
    protected static ?string $heading = 'Tickets Resolved';

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
            default:
                return [now()->startOfYear(), now()->endOfYear()];
        }
    }

    protected function getData(): array
    {
        $dateRange = $this->getFilterDateRange();

        $equipmentResolvedData = Trend::query(
            TicketResolved::query()->where('concern_type', 'Laboratory and Equipment')
                ->where('status', 'Resolved')
        )
        ->between($dateRange[0], $dateRange[1])
        ->perDay()
        ->count();

        $facilityResolvedData = Trend::query(
            TicketResolved::query()->where('concern_type', 'Facility')
                ->where('status', 'Resolved')
        )
        ->between($dateRange[0], $dateRange[1])
        ->perDay()
        ->count();

        return [
            'datasets' => [
                [
                    'label' => 'Resolved Tickets - Laboratory and Equipment',
                    'data' => $equipmentResolvedData->map(fn (TrendValue $value) => $value->aggregate),
                ],
                [
                    'label' => 'Resolved Tickets - Facility',
                    'data' => $facilityResolvedData->map(fn (TrendValue $value) => $value->aggregate),
                ],
            ],
            'labels' => $equipmentResolvedData->map(fn (TrendValue $value) => $value->date),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
