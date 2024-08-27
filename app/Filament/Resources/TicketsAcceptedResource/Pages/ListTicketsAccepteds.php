<?php

namespace App\Filament\Resources\TicketsAcceptedResource\Pages;

use App\Filament\Resources\TicketsAcceptedResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use App\Models\TicketsAccepted;
use Illuminate\Database\Eloquent\Builder;

class ListTicketsAccepteds extends ListRecords
{
    protected static string $resource = TicketsAcceptedResource::class;

    protected function query(): Builder
    {
        return TicketsAccepted::query()->whereNotNull('assigned');
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(), // Include CreateAction if needed
        ];
    }
}
