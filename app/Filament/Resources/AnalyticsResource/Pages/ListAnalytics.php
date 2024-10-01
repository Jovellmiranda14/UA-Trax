<?php

namespace App\Filament\Resources\AnalyticsResource\Pages;

use App\Filament\Resources\AnalyticsResource;
use App\Filament\Resources\AnalyticsResource\Widgets\TicketsQueueChart;
use App\Filament\Resources\AnalyticsResource\Widgets\TicketsAcceptedChart;
use App\Filament\Resources\AnalyticsResource\Widgets\TicketsResolvedChart;
use App\Filament\Resources\AnalyticsResource\Widgets\ConcernTypeChart;
use App\Filament\Resources\AnalyticsResource\Widgets\TypeOfIssueChart;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAnalytics extends ListRecords
{
    protected static string $resource = AnalyticsResource::class;

    //protected function getHeaderActions(): array
    //{
      //  return [
        //    Actions\CreateAction::make(),
        //];
    //}

    protected function getHeaderWidgets(): array
    {
        return[
            TicketsQueueChart::class,
            TicketsAcceptedChart::class,
            TicketsResolvedChart::class,
            ConcernTypeChart::class,
            TypeOfIssueChart::class,
        ];
    }
}
