<?php

namespace App\Filament\Resources\TicketsAcceptedResource\Pages;

use App\Filament\Resources\TicketsAcceptedResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTicketsAccepted extends EditRecord
{
    protected static string $resource = TicketsAcceptedResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
