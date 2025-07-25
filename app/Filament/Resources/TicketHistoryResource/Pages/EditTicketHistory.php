<?php

namespace App\Filament\Resources\TicketHistoryResource\Pages;

use App\Filament\Resources\TicketHistoryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTicketHistory extends EditRecord
{
    protected static string $resource = TicketHistoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
