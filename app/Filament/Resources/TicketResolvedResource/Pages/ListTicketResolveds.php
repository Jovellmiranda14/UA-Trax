<?php

namespace App\Filament\Resources\TicketResolvedResource\Pages;

use App\Filament\Resources\TicketResolvedResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTicketResolveds extends ListRecords
{
    protected static string $resource = TicketResolvedResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
