<?php

namespace App\Filament\Resources\TicketQueueResource\Pages;

use App\Filament\Resources\TicketQueueResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTicketQueue extends EditRecord
{
    protected static string $resource = TicketQueueResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
