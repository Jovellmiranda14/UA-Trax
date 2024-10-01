<?php

namespace App\Filament\Resources\RegularUserResource\Pages;

use App\Filament\Resources\RegularUserResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\RegularUserResource\Widgets\RegularUserOverview;

class ListRegularUsers extends ListRecords
{
    protected static string $resource = RegularUserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
            ->label('New Regular User'),
        ];
    }
    protected function getHeaderWidgets(): array
    {
        return[
            RegularUserOverview::class
        ];
    }
}
