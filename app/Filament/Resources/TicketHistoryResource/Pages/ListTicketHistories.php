<?php

namespace App\Filament\Resources\TicketHistoryResource\Pages;

use App\Filament\Resources\TicketHistoryResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTicketHistories extends ListRecords
{
    protected static string $resource = TicketHistoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
