<?php

namespace App\Filament\Resources\TicketResolvedResource\Pages;

use App\Filament\Resources\TicketResolvedResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTicketResolved extends EditRecord
{
    protected static string $resource = TicketResolvedResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
